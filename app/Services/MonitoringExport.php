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

        $widths = $kind === 'agenda'
            ? [130, 160, 120, 95, 160, 140, 220, 100]
            : [140, 160, 120, 160, 100, 260];

        $columns = collect($widths)->map(
            fn (int $width) => '<Column ss:AutoFitWidth="0" ss:Width="'.$width.'"/>'
        )->implode('');

        $headerRow = $this->excelRow($headers, 'Header', 28);
        $dataRows = $body->map(fn (array $cells) => $this->excelRow($cells, 'Data'))->implode('');
        $sheet = $kind === 'agenda' ? 'Agenda' : 'Laporan';

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Styles>'
            .'<Style ss:ID="Header">'
            .'<Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>'
            .'<Font ss:Bold="1" ss:Color="#FFFFFF"/>'
            .'<Interior ss:Color="#1455C0" ss:Pattern="Solid"/>'
            .'<Borders>'
            .'<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0B3F91"/>'
            .'<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0B3F91"/>'
            .'<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0B3F91"/>'
            .'<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0B3F91"/>'
            .'</Borders>'
            .'</Style>'
            .'<Style ss:ID="Data">'
            .'<Alignment ss:Vertical="Center" ss:WrapText="1"/>'
            .'<Borders>'
            .'<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D5DD"/>'
            .'<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D5DD"/>'
            .'<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D5DD"/>'
            .'<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D5DD"/>'
            .'</Borders>'
            .'</Style>'
            .'</Styles>'
            .'<Worksheet ss:Name="'.$sheet.'"><Table>'.$columns.$headerRow.$dataRows.'</Table></Worksheet>'
            .'</Workbook>';
    }

    /**
     * @param  array<int, mixed>  $cells
     */
    private function excelRow(array $cells, string $styleId, ?int $height = null): string
    {
        $rendered = collect($cells)->map(function ($cell) use ($styleId) {
            $value = htmlspecialchars((string) $cell, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            return '<Cell ss:StyleID="'.$styleId.'"><Data ss:Type="String">'.$value.'</Data></Cell>';
        })->implode('');

        $size = $height === null
            ? ' ss:AutoFitHeight="1"'
            : ' ss:AutoFitHeight="0" ss:Height="'.$height.'"';

        return '<Row'.$size.'>'.$rendered.'</Row>';
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
