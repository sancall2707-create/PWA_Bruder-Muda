<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileImageCompressor
{
    public const MAX_EDGE = 400;

    public const QUALITY = 80;

    private const LIGHT_IMAGE_BYTES = 80 * 1024;

    public function store(UploadedFile $file): string
    {
        $binary = $file->getContent();
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $encoded = $this->encode($binary, $extension);

        if ($encoded === null) {
            return $file->store('profil', 'public');
        }

        $path = 'profil/'.Str::random(40).'.'.$encoded['extension'];
        Storage::disk('public')->put($path, $encoded['bytes']);

        return $path;
    }

    /**
     * Layani foto profil dengan StreamedResponse dan Cache-Control no-cache (selalu revalidasi ETag).
     */
    public static function response(string $path, Request $request): StreamedResponse
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);
        $lastModified = filemtime($fullPath);
        $etag = '"'.md5($path.$lastModified).'"';

        return $disk->response($path, null, [
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
        ]);
    }

    /**
     * Kompres foto profil yang sudah tersimpan di disk public.
     *
     * @return array{path: string, before: int, after: int, rewritten: bool}
     */
    public function rewrite(string $relativePath): array
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($relativePath)) {
            return ['path' => $relativePath, 'before' => 0, 'after' => 0, 'rewritten' => false];
        }

        $before = (int) $disk->size($relativePath);
        $binary = $disk->get($relativePath);
        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        $encoded = is_string($binary) ? $this->encode($binary, $extension) : null;

        if ($encoded === null) {
            return [
                'path' => $relativePath,
                'before' => $before,
                'after' => $before,
                'rewritten' => false,
            ];
        }

        $filename = pathinfo($relativePath, PATHINFO_FILENAME);
        $newPath = 'profil/'.$filename.'.'.$encoded['extension'];
        $disk->put($newPath, $encoded['bytes']);

        if ($newPath !== $relativePath) {
            $disk->delete($relativePath);
        }

        return [
            'path' => $newPath,
            'before' => $before,
            'after' => strlen($encoded['bytes']),
            'rewritten' => true,
        ];
    }

    /**
     * @return array{bytes: string, extension: string}|null
     */
    private function encode(string $binary, string $extension): ?array
    {
        if ($binary === '' || ! function_exists('imagecreatetruecolor') || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $extension = strtolower($extension);
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $info = @getimagesizefromstring($binary);
        if (is_array($info)) {
            $w = (int) ($info[0] ?? 0);
            $h = (int) ($info[1] ?? 0);
            if ($w > 0 && $h > 0 && max($w, $h) <= self::MAX_EDGE && strlen($binary) <= self::LIGHT_IMAGE_BYTES) {
                return null;
            }
        }

        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width < 1 || $height < 1) {
            imagedestroy($source);

            return null;
        }

        $longest = max($width, $height);
        $scale = $longest > self::MAX_EDGE ? self::MAX_EDGE / $longest : 1;
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $resized = $scale < 1;

        if (! $resized && strlen($binary) <= self::LIGHT_IMAGE_BYTES) {
            imagedestroy($source);

            return null;
        }

        $hasAlpha = ! in_array($extension, ['jpg', 'jpeg'], true) && $this->hasTransparency($source);
        $format = in_array($extension, ['jpg', 'jpeg'], true) ? 'jpeg' : ($hasAlpha ? 'png' : 'jpeg');

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($format === 'jpeg') {
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $white);
        } else {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        $bytes = $this->write($canvas, $format);
        imagedestroy($canvas);

        if ($bytes === null) {
            return null;
        }

        if (! $resized && strlen($bytes) >= strlen($binary)) {
            return null;
        }

        return [
            'bytes' => $bytes,
            'extension' => $format === 'jpeg' ? 'jpg' : $format,
        ];
    }

    private function hasTransparency(\GdImage $image): bool
    {
        if (imagecolortransparent($image) >= 0) {
            return true;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $alpha = (imagecolorat($image, $x, $y) >> 24) & 0x7F;
                if ($alpha > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private function write(\GdImage $image, string $format): ?string
    {
        $temporary = tempnam(sys_get_temp_dir(), 'profil');
        if ($temporary === false) {
            return null;
        }

        $target = $temporary.'.'.$format;
        @unlink($temporary);

        $written = match ($format) {
            'jpeg' => imagejpeg($image, $target, self::QUALITY),
            'webp' => function_exists('imagewebp') && imagewebp($image, $target, self::QUALITY),
            'png' => imagepng($image, $target, 8),
            default => false,
        };

        if (! $written || ! is_file($target)) {
            @unlink($target);

            return null;
        }

        $bytes = file_get_contents($target);
        @unlink($target);

        return is_string($bytes) && $bytes !== '' ? $bytes : null;
    }
}
