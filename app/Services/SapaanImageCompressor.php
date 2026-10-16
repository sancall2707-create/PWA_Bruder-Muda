<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SapaanImageCompressor
{
    public const MAX_EDGE = 1280;

    public const QUALITY = 75;

    /**
     * PNG yang sudah muat dalam bingkai dan berukuran ringan tidak ditulis ulang.
     */
    private const LIGHT_PNG_BYTES = 120 * 1024;

    public function store(UploadedFile $file): string
    {
        $binary = $file->getContent();
        $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        $encoded = $this->encode($binary, $extension);

        if ($encoded === null) {
            return $file->store('sapaan', 'public');
        }

        $path = 'sapaan/'.Str::random(40).'.'.$encoded['extension'];
        Storage::disk('public')->put($path, $encoded['bytes']);

        return $path;
    }

    /**
     * Kompres berkas yang sudah ada di disk public.
     * Path dikembalikan sama bila nama berkas tidak berubah.
     *
     * @return array{path: string, before: int, after: int, rewritten: bool}
     */
    public function rewrite(string $relativePath): array
    {
        $disk = Storage::disk('public');
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
        $newPath = 'sapaan/'.$filename.'.'.$encoded['extension'];
        $disk->put($newPath, $encoded['bytes']);

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

        if ($this->isLightPng($binary, $extension)) {
            return null;
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
        $hasAlpha = ! in_array($extension, ['jpg', 'jpeg'], true) && $this->hasTransparency($source);
        $format = $this->targetFormat($extension, $hasAlpha);

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

    private function isLightPng(string $binary, string $extension): bool
    {
        if ($extension !== 'png' || strlen($binary) > self::LIGHT_PNG_BYTES) {
            return false;
        }

        $info = @getimagesizefromstring($binary);
        if (! is_array($info)) {
            return false;
        }

        return max((int) $info[0], (int) $info[1]) <= self::MAX_EDGE;
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

    private function targetFormat(string $extension, bool $hasAlpha): string
    {
        if (in_array($extension, ['jpg', 'jpeg'], true)) {
            return 'jpeg';
        }

        if ($extension === 'webp' && function_exists('imagewebp')) {
            return 'webp';
        }

        if ($hasAlpha && function_exists('imagewebp')) {
            return 'webp';
        }

        if ($hasAlpha) {
            return 'png';
        }

        return 'jpeg';
    }

    private function write(\GdImage $image, string $format): ?string
    {
        $temporary = tempnam(sys_get_temp_dir(), 'sapaan');
        if ($temporary === false) {
            return null;
        }

        $target = $temporary.'.'.$format;
        @unlink($temporary);

        $written = match ($format) {
            'jpeg' => imagejpeg($image, $target, self::QUALITY),
            'webp' => function_exists('imagewebp') && imagewebp($image, $target, self::QUALITY),
            'png' => imagepng($image, $target, 9),
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
