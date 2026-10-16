<?php

namespace App\Services;

class PlainPdf
{
    /**
     * @param  list<string>  $lines
     */
    public function render(array $lines): string
    {
        $wrapped = [];
        foreach ($lines as $index => $line) {
            $encoded = $this->encode($line);
            $chunks = $encoded === '' ? [''] : explode("\n", wordwrap($encoded, 90, "\n", true));
            foreach ($chunks as $chunk) {
                $wrapped[] = [
                    'text' => $this->escape($chunk),
                    'title' => $index === 0,
                ];
            }
        }

        $pages = [];
        $current = [];
        $remaining = 46;

        foreach ($wrapped as $line) {
            if ($remaining <= 0) {
                $pages[] = $current;
                $current = [];
                $remaining = 46;
            }
            $current[] = $line;
            $remaining--;
        }

        if ($current !== [] || $pages === []) {
            $pages[] = $current;
        }

        return $this->document($pages);
    }

    /**
     * @param  list<list<array{text: string, title: bool}>>  $pages
     */
    private function document(array $pages): string
    {
        $pageCount = count($pages);
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        $kids = [];
        $nextId = 3;
        $pageIds = [];
        $contentIds = [];

        for ($i = 0; $i < $pageCount; $i++) {
            $pageIds[] = $nextId;
            $contentIds[] = $nextId + 1;
            $kids[] = $nextId.' 0 R';
            $nextId += 2;
        }

        $fontId = $nextId;
        $objects[2] = '<< /Type /Pages /Count '.$pageCount.' /Kids ['.implode(' ', $kids).'] >>';

        foreach ($pages as $index => $lines) {
            $pageId = $pageIds[$index];
            $contentId = $contentIds[$index];
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents '.$contentId.' 0 R /Resources << /Font << /F1 '.$fontId.' 0 R /F2 '.($fontId + 1).' 0 R >> >> >>';
            $stream = $this->stream($lines);
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
        }

        $objects[$fontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[$fontId + 1] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$body."\nendobj\n";
        }

        $xref = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 ".$count."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id < $count; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
        }

        $pdf .= "trailer\n<< /Size ".$count." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }

    /**
     * @param  list<array{text: string, title: bool}>  $lines
     */
    private function stream(array $lines): string
    {
        $commands = ['BT', '/F2 14 Tf', '36 800 Td'];
        $first = true;

        foreach ($lines as $line) {
            if (! $first) {
                $commands[] = '0 -16 Td';
            }
            $first = false;
            $commands[] = ($line['title'] ? '/F2 14 Tf' : '/F1 10 Tf');
            $commands[] = '('.$line['text'].') Tj';
        }

        $commands[] = 'ET';

        return implode("\n", $commands);
    }

    private function encode(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $text);
        $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);

        if ($converted === false) {
            $converted = iconv('UTF-8', 'Windows-1252//IGNORE', $text) ?: '';
        }

        return $converted;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
