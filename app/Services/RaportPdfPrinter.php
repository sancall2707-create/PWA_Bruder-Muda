<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class RaportPdfPrinter
{
    public function print(string $html): string
    {
        $chrome = '/usr/bin/google-chrome-stable';
        if (! is_executable($chrome)) {
            throw new RuntimeException('Peramban untuk membuat PDF raport tidak tersedia.');
        }

        $directory = storage_path('app/raport-pdf');
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Folder sementara PDF raport tidak dapat dibuat.');
        }

        $token = uniqid('raport-', true);
        $htmlPath = $directory.'/'.$token.'.html';
        $pdfPath = $directory.'/'.$token.'.pdf';
        $profile = $directory.'/'.$token.'-chrome';

        file_put_contents($htmlPath, $this->localAssets($html));

        try {
            $result = Process::timeout(40)->run([
                $chrome,
                '--headless=new',
                '--disable-gpu',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--no-first-run',
                '--disable-background-networking',
                '--host-rules=MAP * ~NOTFOUND',
                '--user-data-dir='.$profile,
                '--virtual-time-budget=8000',
                '--no-pdf-header-footer',
                '--print-to-pdf='.$pdfPath,
                'file://'.$htmlPath,
            ]);

            if (! $result->successful() || ! is_file($pdfPath) || filesize($pdfPath) < 500) {
                throw new RuntimeException('PDF raport gagal dibuat.');
            }

            return (string) file_get_contents($pdfPath);
        } finally {
            @unlink($htmlPath);
            @unlink($pdfPath);
            $this->deleteDirectory($profile);
        }
    }

    private function localAssets(string $html): string
    {
        $public = rtrim(public_path(), '/');
        $html = preg_replace('#<link[^>]+fonts\.(?:googleapis|gstatic)\.com[^>]*>#', '', $html) ?? $html;
        $html = preg_replace(
            '#https?://[^"\']+(/css/[^"\']+|/images/[^"\']+|/favicon[^"\']*)#',
            'file://'.$public.'$1',
            $html
        ) ?? $html;

        $html = preg_replace(
            '#(?<=["\'])(/(?:css|images|favicon)[^"\']*)#',
            'file://'.$public.'$1',
            $html
        ) ?? $html;

        // Hapus elemen navigasi/header dan img remote yang tidak dicetak untuk mencegah deadlock request internal
        return preg_replace('#<img[^>]+src=["\']https?://[^"\']+["\'][^>]*>#i', '', $html) ?? $html;
    }

    private function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = scandir($path);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $target = $path.'/'.$item;
            if (is_dir($target)) {
                $this->deleteDirectory($target);
            } else {
                @unlink($target);
            }
        }

        @rmdir($path);
    }
}
