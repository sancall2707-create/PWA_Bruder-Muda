<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MonitoringExport;
use App\Services\MonitoringQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MonitoringController extends Controller
{
    public function __construct(
        private readonly MonitoringQuery $monitoring,
        private readonly MonitoringExport $exporter,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->monitoring->filters($request);

        return view('super-admin.monitoring.index', [
            'filters' => $filters,
            'bruders' => $this->bruders(),
            'reports' => $this->monitoring->reports($filters),
            'headerTitle' => 'Monitoring Laporan',
            'headerSubtitle' => $filters['period_label'],
        ]);
    }

    public function agenda(Request $request): View
    {
        $filters = $this->monitoring->filters($request);
        $agendas = $this->monitoring->agendas($filters);
        $tampilan = $request->input('tampilan') === 'kalender' ? 'kalender' : 'daftar';

        return view('super-admin.monitoring.agenda', [
            'filters' => $filters,
            'bruders' => $this->bruders(),
            'agendas' => $agendas,
            'tampilan' => $tampilan,
            'calendar' => $this->monitoring->calendar($agendas, $filters['tahun'], $filters['bulan'], $request->input('tanggal')),
            'headerTitle' => 'Rekapitulasi Agenda',
            'headerSubtitle' => $filters['period_label'],
        ]);
    }

    public function export(Request $request): Response
    {
        $filters = $this->monitoring->filters($request);
        $kind = $request->input('jenis') === 'agenda' ? 'agenda' : 'laporan';
        $format = $request->input('format') === 'pdf' ? 'pdf' : 'excel';
        $rows = $kind === 'agenda'
            ? $this->monitoring->agendas($filters)
            : $this->monitoring->reports($filters);

        $filename = $this->exporter->filename($kind, $format, $filters);
        $body = $format === 'pdf'
            ? $this->exporter->pdf($kind, $filters, $rows)
            : $this->exporter->excel($kind, $filters, $rows);

        $contentType = $format === 'pdf'
            ? 'application/pdf'
            : 'application/vnd.ms-excel; charset=UTF-8';

        return response($body, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function bruders()
    {
        return User::query()->where('role', 'user')->orderBy('name')->get();
    }
}
