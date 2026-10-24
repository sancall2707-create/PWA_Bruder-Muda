<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProvincialMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProvincialMessageController extends Controller
{
    /**
     * Tampilkan halaman Sapaan Provinsial & Pengumuman lengkap.
     */
    public function index(): View
    {
        return view('user.sapaan-provinsial', [
            'message' => $this->present(ProvincialMessage::currentSapaan()),
            'announcements' => ProvincialMessage::currentAnnouncements(),
        ]);
    }

    public function photo(ProvincialMessage $message, Request $request): StreamedResponse
    {
        abort_unless($this->canViewPhoto($message), 404);

        $path = $message->image;
        $disk = Storage::disk('public');
        $fullPath = $disk->path($path);
        $lastModified = filemtime($fullPath);
        $etag = '"'.md5($path.$lastModified).'"';

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=604800, must-revalidate',
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified).' GMT',
        ]);
    }

    private function canViewPhoto(ProvincialMessage $message): bool
    {
        if (! $this->isSapaanImage($message->image)) {
            return false;
        }

        $user = auth()->user();

        return $message->is_active || $user?->role === 'super_admin';
    }

    private function isSapaanImage(?string $path): bool
    {
        return is_string($path)
            && str_starts_with($path, 'sapaan/')
            && Storage::disk('public')->exists($path);
    }

    private function present(?ProvincialMessage $message): ?object
    {
        if ($message === null) {
            return null;
        }

        $paragraphs = preg_split("/\r\n|\n|\r/", $message->content) ?: [];

        return (object) [
            'id' => $message->id,
            'title' => $message->title,
            'author_name' => $message->author_name ?: 'Provinsi FIC Indonesia',
            'author_role' => $message->author_role ?: 'TPPBM',
            'label' => $message->sapaanLabel(),
            'initials' => $this->initials($message->author_name ?: 'FIC'),
            'published_at' => $message->publishedLabel(),
            'image_url' => $message->image ? route('sapaan-provinsial.foto', $message) : null,
            'content' => array_values(array_filter(array_map('trim', $paragraphs), fn (string $line) => $line !== '')),
        ];
    }

    private function initials(string $name): string
    {
        $letters = '';

        foreach (preg_split('/\s+/', trim($name)) ?: [] as $word) {
            $clean = trim($word, '.');
            if (in_array(strtolower($clean), ['br', 'rm', 'sr'], true)) {
                continue;
            }
            $letters .= mb_strtoupper(mb_substr($clean, 0, 1));
            if (mb_strlen($letters) >= 2) {
                break;
            }
        }

        return $letters !== '' ? $letters : 'FIC';
    }
}
