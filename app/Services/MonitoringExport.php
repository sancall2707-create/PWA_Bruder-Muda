<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MonitoringExport
{
    /**
     * @param  array{period_label: string, bulan: int, tahun: int}  $filters
     */
    public function excel(string $kind, array $filters, Collection $rows): string
    {
        $headers = $kind === 'agenda'
            ? ['Jenis Agenda', 'Nama Bruder', 'Tanggal', 'Waktu', 'Lawan Bicara', 'Lokasi', 'Keterangan', 'Status']
            : ['Tipe Laporan', 'Nama Bruder', 'Tanggal', 'Lawan Bicara', 'Status', 'Isi'];

        $body = $rows->map(function (object $row) use ($kind) {
            if ($kind === 'agenda') {
                return [
                    $row->type,
                    $row->bruder,
                    $row->date_label,
                    $row->time_label,
                    $row->counterpart,
                    $row->location,
                    $row->summary,
                    $row->status_label,
                ];
            }

            return [
                $row->type,
                $row->bruder,
                $row->date_label,
                $row->counterpart,
                $row->status_label,
                $row->summary,
            ];
        });

        $xmlRows = collect([$headers])->merge($body)->map(function (array $cells) {
            $rendered = collect($cells)->map(function ($cell) {
                $value = htmlspecialchars((string) $cell, ENT_XML1 | ENT_QUOTES, 'UTF-8');

                return '<Cell><Data ss:Type="String">'.$value.'</Data></Cell>';
            })->implode('');

            return '<Row>'.$rendered.'</Row>';
        })->implode('');

        $sheet = $kind === 'agenda' ? 'Agenda' : 'Laporan';

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="'.$sheet.'"><Table>'.$xmlRows.'</Table></Worksheet>'
            .'</Workbook>';
    }

    /**
     * @param  array{period_label: string}  $filters
     */
    public function pdf(string $kind, array $filters, Collection $rows): string
    {
        $title = ($kind === 'agenda' ? 'Rekap Agenda' : 'Rekap Laporan').' '.$filters['period_label'];
        $lines = [$title, ''];

        if ($rows->isEmpty()) {
            $lines[] = 'Belum ada data pada periode ini.';
        }

        foreach ($rows as $row) {
            $lines[] = $row->bruder.' | '.$row->type;
            $lines[] = $kind === 'agenda'
                ? $row->date_label.' '.$row->time_label.' | '.$row->status_label
                : $row->date_label.' | '.$row->status_label;
            if ($row->counterpart !== '') {
                $lines[] = $row->counterpart;
            }
            if ($row->location !== '') {
                $lines[] = $row->location;
            }
            if ($row->summary !== '') {
                $lines[] = $row->summary;
            }
            $lines[] = '';
        }

        return (new PlainPdf)->render($lines);
    }

    public function filename(string $kind, string $format, array $filters): string
    {
        $slug = $kind === 'agenda' ? 'rekap-agenda' : 'rekap-laporan';
        $extension = $format === 'pdf' ? 'pdf' : 'xls';

        return sprintf('%s-%04d-%02d.%s', $slug, $filters['tahun'], $filters['bulan'], $extension);
    }
}
