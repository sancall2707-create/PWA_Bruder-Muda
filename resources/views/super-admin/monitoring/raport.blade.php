@extends('layouts.super-admin')

@php
    $wideLayout = true;
@endphp

@section('content')
<style>
    @page {
        size: A4 portrait;
        margin: 1.5cm 1.5cm 1.5cm 2cm;
    }

    /* KONTEN PREVIEW LAYAR & PRINT */
    .raport-sheet {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #D1D5DB;
        box-shadow: 0 4px 20px rgba(14, 51, 102, 0.08);
        padding: 24px 28px 32px;
        margin-top: 14px;
        font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
        color: #1F2937;
        font-size: 11px;
        line-height: 1.5;
        box-sizing: border-box;
        width: 100%;
    }

    .raport-section-title {
        color: #0E3366;
        font-weight: 800;
        font-size: 12px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        margin: 16px 0 6px 0;
        padding-bottom: 2px;
        page-break-after: avoid;
        break-after: avoid;
    }

    /* KARTU FORMULIR DATA (SEKSI A & B) */
    .raport-card-box {
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        background: #ffffff;
        overflow: hidden;
        margin-bottom: 10px;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .raport-grid-row {
        display: flex;
        border-bottom: 1px solid #E5E7EB;
    }
    .raport-grid-row:last-child {
        border-bottom: none;
    }
    .raport-cell {
        flex: 1;
        padding: 6px 10px;
        border-right: 1px solid #E5E7EB;
        box-sizing: border-box;
    }
    .raport-cell:last-child {
        border-right: none;
    }
    .raport-cell-label {
        font-size: 9.5px;
        font-weight: 800;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    .raport-cell-value {
        font-size: 11px;
        font-weight: 600;
        color: #1F2937;
    }

    /* TABEL STANDAR DENGAN HEADER NAVY */
    .raport-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        font-size: 11px;
        page-break-inside: auto;
    }
    .raport-table th {
        background-color: #0E3366 !important;
        color: #ffffff !important;
        font-weight: 700;
        padding: 6px 8px;
        border: 1px solid #0E3366;
        text-align: left;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .raport-table td {
        border: 1px solid #D1D5DB;
        padding: 6px 8px;
        color: #1F2937;
        vertical-align: top;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .raport-table tr {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* KARTU FORMULIR PER BULAN (SEKSI F & G) & PENGEMBANGAN (SEKSI H) */
    .raport-log-card {
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        background: #ffffff;
        padding: 8px 12px;
        margin-bottom: 8px;
        box-sizing: border-box;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        line-height: 1.2;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .badge-terlaksana {
        background-color: #DCFCE7 !important;
        color: #166534 !important;
        border: 1px solid #86EFAC !important;
    }
    .badge-belum {
        background-color: #FEE2E2 !important;
        color: #991B1B !important;
        border: 1px solid #FCA5A5 !important;
    }
    .badge-tercatat {
        background-color: #E0F2FE !important;
        color: #0369A1 !important;
        border: 1px solid #BAE6FD !important;
    }

    /* FOOTER DOKUMEN */
    .raport-doc-footer {
        border-top: 1px solid #D1D5DB;
        padding-top: 8px;
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 9.5px;
        color: #6B7280;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .raport-signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 32px;
        font-size: 11px;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    /* PRINT RULES */
    @media print {
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 2cm;
        }

        html, body {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .sa-container {
            max-width: 100% !important;
            width: 100% !important;
            min-height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .sa-header, .sa-bottom-nav, .sa-menu-toggle, .sa-menu, .no-print, header, nav {
            display: none !important;
        }

        .sa-body {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            gap: 0 !important;
        }

        .raport-sheet {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .raport-table th {
            background-color: #0E3366 !important;
            color: #ffffff !important;
        }
    }
</style>

<div class="no-print">
    @include('super-admin.monitoring._toolbar', ['exportKind' => 'raport'])

    <div style="margin-top: 12px; display: flex; justify-content: flex-end;">
        <button onclick="window.print()" style="background:#0E3366; color:#fff; border:none; padding:10px 18px; border-radius:10px; font-weight:700; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 6px rgba(14,51,102,0.25);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak / Simpan PDF Raport
        </button>
    </div>
</div>

@if(!$selectedBruder)
    <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:24px; text-align:center; color:#667085; margin-top:14px;">
        Silakan pilih salah satu Bruder Muda pada filter di atas untuk menampilkan Raport Pendampingan.
    </div>
@else
    <div id="raportContainer" class="raport-sheet">

        <!-- KOP RESMI DOKUMEN -->
        <div style="display:flex; align-items:center; justify-content:center; gap:16px; margin-bottom:10px;">
            <img src="/images/logo-fic.png" alt="Logo FIC" style="width:58px; height:58px; object-fit:contain;">
            <div style="text-align:center;">
                <div style="font-size:16px; font-weight:800; color:#0E3366; letter-spacing:0.8px; line-height:1.2; text-transform:uppercase;">
                    RAPORT PENDAMPINGAN BRUDER MUDA
                </div>
                <div style="font-size:12px; font-weight:700; color:#1F2937; margin-top:2px;">Kongregasi Bruder FIC Indonesia</div>
                <div style="font-size:10.5px; color:#6B7280;">Tim Pemantap Panggilan Bruder Muda (TPPBM)</div>
            </div>
            <div style="width:58px;"></div>
        </div>

        <!-- GARIS GANDA PEMISAH KOP -->
        <div style="border-top: 2.5px solid #0E3366; border-bottom: 0.8px solid #0E3366; height: 2px; margin-bottom: 12px;"></div>

        <!-- A. DATA DIRI -->
        <div class="raport-section-title">A. DATA DIRI</div>
        <div class="raport-card-box">
            <div class="raport-grid-row">
                <div class="raport-cell">
                    <div class="raport-cell-label">NAMA LENGKAP</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['nama'] }}</div>
                </div>
                <div class="raport-cell">
                    <div class="raport-cell-label">USERNAME / EMAIL</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['username'] }} / {{ $raport['data_diri']['email'] }}</div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell">
                    <div class="raport-cell-label">TEMPAT, TANGGAL LAHIR</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['birth_place'] }}, {{ $raport['data_diri']['birth_date'] }}</div>
                </div>
                <div class="raport-cell">
                    <div class="raport-cell-label">NO. TELEPON / WHATSAPP</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['phone'] }}</div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell" style="width:100%;">
                    <div class="raport-cell-label">ASAL PAROKI</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['parish_origin'] }}</div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell" style="width:100%;">
                    <div class="raport-cell-label">ALAMAT RUMAH ASAL</div>
                    <div class="raport-cell-value">{{ $raport['data_diri']['home_address'] }}</div>
                </div>
            </div>
        </div>

        <!-- B. DATA KELUARGA & KONTAK DARURAT -->
        <div class="raport-section-title">B. DATA KELUARGA &amp; KONTAK DARURAT</div>
        <div class="raport-card-box">
            <div class="raport-grid-row">
                <div class="raport-cell">
                    <div class="raport-cell-label">NAMA AYAH</div>
                    <div class="raport-cell-value">
                        {{ $raport['keluarga']['father_name'] }}
                        @if(!empty($raport['keluarga']['father_birth_year']) && $raport['keluarga']['father_birth_year'] !== '-')
                            (Lahir: {{ $raport['keluarga']['father_birth_year'] }})
                        @endif
                        @if(!empty($raport['keluarga']['father_job']) && $raport['keluarga']['father_job'] !== '-')
                            - {{ $raport['keluarga']['father_job'] }}
                        @endif
                    </div>
                </div>
                <div class="raport-cell">
                    <div class="raport-cell-label">NAMA IBU</div>
                    <div class="raport-cell-value">
                        {{ $raport['keluarga']['mother_name'] }}
                        @if(!empty($raport['keluarga']['mother_birth_year']) && $raport['keluarga']['mother_birth_year'] !== '-')
                            (Lahir: {{ $raport['keluarga']['mother_birth_year'] }})
                        @endif
                        @if(!empty($raport['keluarga']['mother_job']) && $raport['keluarga']['mother_job'] !== '-')
                            - {{ $raport['keluarga']['mother_job'] }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell" style="width:100%;">
                    <div class="raport-cell-label">JUMLAH SAUDARA</div>
                    <div class="raport-cell-value">{{ $raport['keluarga']['siblings_text'] }}</div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell">
                    <div class="raport-cell-label">NARAHUBUNG DARURAT</div>
                    <div class="raport-cell-value">
                        {{ $raport['keluarga']['emergency_name'] }}
                        @if(!empty($raport['keluarga']['emergency_relation']) && $raport['keluarga']['emergency_relation'] !== '-')
                            ({{ $raport['keluarga']['emergency_relation'] }})
                        @endif
                    </div>
                </div>
                <div class="raport-cell">
                    <div class="raport-cell-label">NO. HP NARAHUBUNG</div>
                    <div class="raport-cell-value">{{ $raport['keluarga']['emergency_phone'] }}</div>
                </div>
            </div>
            <div class="raport-grid-row">
                <div class="raport-cell" style="width:100%;">
                    <div class="raport-cell-label">ALAMAT NARAHUBUNG</div>
                    <div class="raport-cell-value">{{ $raport['keluarga']['emergency_address'] }}</div>
                </div>
            </div>
        </div>

        <!-- C. RIWAYAT PENDIDIKAN -->
        <div class="raport-section-title">C. RIWAYAT PENDIDIKAN</div>
        <table class="raport-table">
            <thead>
                <tr>
                    <th style="width:10%; text-align:center;">Jenjang</th>
                    <th style="width:42%;">Nama Institusi</th>
                    <th style="width:26%;">Jurusan / Prodi</th>
                    <th style="width:22%; text-align:center;">Tahun</th>
                </tr>
            </thead>
            <tbody>
                @foreach($raport['pendidikan'] as $edu)
                    <tr>
                        <td style="text-align:center; font-weight:700;">{{ $edu['jenjang'] }}</td>
                        <td>{{ $edu['nama'] }}</td>
                        <td>{{ $edu['jurusan'] }}</td>
                        <td style="text-align:center;">{{ $edu['tahun'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- D. TAHUN MASUK & TAHAPAN FORMASI -->
        <div class="raport-section-title">D. TAHUN MASUK &amp; TAHAPAN FORMASI</div>
        <table class="raport-table">
            <thead>
                <tr>
                    <th style="width:28%;">Tahapan</th>
                    <th style="width:14%; text-align:center;">Tahun</th>
                    <th style="width:24%; text-align:center;">Jumlah Angkatan</th>
                    <th style="width:34%;">Magister / Provinsial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($raport['formasi'] as $form)
                    <tr>
                        <td style="font-weight:600;">{{ $form['tahapan'] }}</td>
                        <td style="text-align:center;">{{ $form['tahun'] }}</td>
                        <td style="text-align:center;">{{ $form['angkatan'] }}</td>
                        <td>{{ $form['magister'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- E. KOMUNITAS TINGGAL & PERUTUSAN -->
        <div class="raport-section-title">E. KOMUNITAS TINGGAL &amp; PERUTUSAN</div>
        <div class="raport-card-box" style="padding: 6px 12px;">
            @foreach($raport['perutusan'] as $p)
                <div style="display:flex; gap:16px; padding:6px 0; {{ !$loop->last ? 'border-bottom:1px dotted #D1D5DB;' : '' }}">
                    <div style="width:110px; font-weight:800; color:#0E3366; font-size:11px; white-space:nowrap;">
                        {{ $p['masa'] }}
                    </div>
                    <div style="flex:1; font-size:11px; color:#1F2937;">
                        <strong>{{ $p['komunitas'] }}</strong> &mdash; Tugas Pokok: {{ $p['pokok'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- F. LAPORAN PELAKSANAAN BIMBINGAN ROHANI -->
        <div class="raport-section-title">F. LAPORAN PELAKSANAAN BIMBINGAN ROHANI</div>
        @foreach($raport['bimbingan_bulan'] as $b)
            <div class="raport-log-card" style="border-left: 4px solid {{ $b['done'] ? '#0E3366' : '#DC2626' }};">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                    <div style="font-size:11.5px; font-weight:800; color:#0E3366;">
                        Bulan: {{ $b['bulan'] }}
                    </div>
                    @if($b['done'])
                        <span class="badge-pill badge-terlaksana">✔ TELAH TERLAKSANA</span>
                    @else
                        <span class="badge-pill badge-belum">✘ BELUM / TIDAK TERLAKSANA</span>
                    @endif
                </div>
                <div style="display:flex; flex-wrap:wrap; gap:14px; font-size:10px; margin-bottom:5px; color:#4B5563;">
                    <div><strong>Tgl:</strong> {{ $b['date'] !== '-' ? $b['date'] . ' ' . ucfirst(strtolower($b['bulan'])) : '-' }}</div>
                    <div><strong>Pembimbing:</strong> {{ $b['mentor'] }}</div>
                    <div><strong>Tempat:</strong> {{ $b['location'] }}</div>
                </div>
                <div style="font-size:10.5px; color:#1F2937; line-height:1.45;">
                    @if($b['done'])
                        <strong>Refleksi:</strong> {{ $b['content'] }}
                    @else
                        <span style="font-style:italic; color:#6B7280;">(Tidak ada catatan atau refleksi untuk periode ini)</span>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- G. LAPORAN WAWANHATI PIMPINAN LOKAL -->
        <div class="raport-section-title" style="margin-top:16px;">G. LAPORAN WAWANHATI PIMPINAN LOKAL</div>
        @foreach($raport['wawanhati_bulan'] as $w)
            <div class="raport-log-card" style="border-left: 4px solid {{ $w['done'] ? '#0E3366' : '#DC2626' }};">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
                    <div style="font-size:11.5px; font-weight:800; color:#0E3366;">
                        Bulan: {{ $w['bulan'] }}
                    </div>
                    @if($w['done'])
                        <span class="badge-pill badge-terlaksana">✔ TELAH TERLAKSANA</span>
                    @else
                        <span class="badge-pill badge-belum">✘ BELUM / TIDAK TERLAKSANA</span>
                    @endif
                </div>
                <div style="display:flex; flex-wrap:wrap; gap:14px; font-size:10px; margin-bottom:5px; color:#4B5563;">
                    <div><strong>Tgl:</strong> {{ $w['date'] !== '-' ? $w['date'] . ' ' . ucfirst(strtolower($w['bulan'])) : '-' }}</div>
                    <div><strong>Pimpinan Lokal:</strong> {{ $w['leader'] }}</div>
                    <div><strong>Tempat:</strong> {{ $w['location'] }}</div>
                </div>
                <div style="font-size:10.5px; color:#1F2937; line-height:1.45;">
                    @if($w['done'])
                        <strong>Refleksi:</strong> {{ $w['content'] }}
                    @else
                        <span style="font-style:italic; color:#6B7280;">(Tidak ada catatan atau refleksi untuk periode ini)</span>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- H. LAPORAN KEGIATAN PENGEMBANGAN DIRI -->
        <div class="raport-section-title" style="margin-top:16px;">H. LAPORAN KEGIATAN PENGEMBANGAN DIRI</div>
        @forelse($raport['pengembangan_diri'] as $pd)
            <div class="raport-log-card" style="border-left: 4px solid #0E3366; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <div style="font-size:11.5px; font-weight:800; color:#0E3366;">
                        {{ $pd['label'] }}
                    </div>
                    <span class="badge-pill badge-tercatat">TERCATAT</span>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:4px 16px; font-size:10px; margin-bottom:6px; color:#4B5563;">
                    <div><strong>Kegiatan:</strong> {{ $pd['name'] }}</div>
                    <div><strong>Tanggal:</strong> {{ $pd['date'] }}</div>
                    <div><strong>Penyelenggara:</strong> {{ $pd['organizer'] }}</div>
                    <div><strong>Narasumber:</strong> {{ $pd['speaker'] }}</div>
                </div>
                <div style="font-size:10.5px; color:#1F2937; margin-bottom:4px; line-height:1.45;">
                    <strong>Deskripsi:</strong> {{ $pd['description'] }}
                </div>
                <div style="font-size:10.5px; color:#1F2937; line-height:1.45;">
                    <strong>Refleksi:</strong> {{ $pd['reflection'] }}
                </div>
            </div>
        @empty
            <div style="padding:10px; border:1px dashed #CBD5E1; border-radius:6px; text-align:center; color:#64748B; font-size:10.5px;">
                Belum ada kegiatan pengembangan diri yang dilaporkan pada periode ini.
            </div>
        @endforelse

        <!-- I. CATATAN PENDAMPING BRUDER MUDA (TPPBM) -->
        <div class="raport-section-title" style="border-bottom: 1.5px solid #0E3366; padding-bottom: 4px; margin-top:20px;">
            I. CATATAN PENDAMPING BRUDER MUDA (TPPBM)
        </div>

        <div style="display:flex; flex-direction:column; gap:12px; font-size:11px; line-height:1.55; margin-top:8px;">
            <div>
                <div style="font-weight:800; color:#1F2937;">Catatan Pribadi</div>
                @if(!empty($raport['catatan_tppbm']['pribadi']))
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach($raport['catatan_tppbm']['pribadi'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 16px; padding:0; color:#374151;">
                        <li>Proses pengenalan diri adalah fondasi utama dalam panggilan. Teruslah belajar menerima kelebihan dan kelemahan diri secara jujur dan terbuka. Jangan lupa untuk selalu menjaga keseimbangan antara kesehatan fisik, mental, dan emosional (cura personalis).</li>
                        <li>Kematangan pribadi dan kemauan untuk terus dievaluasi akan sangat membantu kelancaran proses formasi Bruder ke depannya.</li>
                    </ol>
                @endif
            </div>

            <div>
                <div style="font-weight:800; color:#1F2937;">Catatan Hidup Rohani / Doa</div>
                @if(!empty($raport['catatan_tppbm']['rohani']))
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach($raport['catatan_tppbm']['rohani'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 16px; padding:0; color:#374151;">
                        <li>Hidup rohani adalah jangkar dari seluruh peziarahan panggilan kita. Tetaplah setia dan bertekun dalam doa-doa harian, meditasi pribadi, serta perayaan Ekaristi.</li>
                        <li>Usahakan agar waktu-waktu hening tidak hanya menjadi rutinitas wajib yang kering, melainkan menjadi perjumpaan yang intim untuk sungguh-sungguh mendengarkan kehendak Tuhan setiap harinya.</li>
                    </ol>
                @endif
            </div>

            <div>
                <div style="font-weight:800; color:#1F2937;">Catatan Hidup Komunitas</div>
                @if(!empty($raport['catatan_tppbm']['komunitas']))
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach($raport['catatan_tppbm']['komunitas'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 16px; padding:0; color:#374151;">
                        <li>Komunitas adalah tempat di mana kasih persaudaraan diuji sekaligus dihidupi secara nyata. Berusahalah untuk selalu proaktif mengambil bagian dalam tugas-tugas harian dan kegiatan bersama (seperti rekreasi atau doa komunitas).</li>
                        <li>Tumbuhkan kepekaan terhadap kebutuhan sesama bruder, serta belajarlah untuk senantiasa lapang dada dalam menghadapi perbedaan karakter antaranggota komunitas.</li>
                    </ol>
                @endif
            </div>

            <div>
                <div style="font-weight:800; color:#1F2937;">Catatan Hidup Kerasulan</div>
                @if(!empty($raport['catatan_tppbm']['kerasulan']))
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach($raport['catatan_tppbm']['kerasulan'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 16px; padding:0; color:#374151;">
                        <li>Tugas perutusan dan kerasulan kita menuntut dedikasi dan kasih yang tulus. Layanilah setiap jiwa yang dipercayakan kepada Bruder dengan hati yang gembira dan penuh totalitas.</li>
                        <li>Selalu ingat bahwa karya kerasulan bukanlah sekadar soal pencapaian kerja profesional, melainkan wujud kesaksian nyata akan kehadiran Allah yang peduli di tengah-tengah umat dan kaum muda.</li>
                    </ol>
                @endif
            </div>

            <div>
                <div style="font-weight:800; color:#1F2937;">Catatan Hidup Triprasetia (Kaul-kaul)</div>
                @if(!empty($raport['catatan_tppbm']['triprasetia']))
                    <ul style="margin:4px 0 0 16px; padding:0;">
                        @foreach($raport['catatan_tppbm']['triprasetia'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 16px; padding:0; color:#374151;">
                        <li>Menghidupi Triprasetia (Kemiskinan, Kemurnian, dan Ketaatan) adalah jalan pembebasan sejati, bukan pengekangan.</li>
                        <li>Hiduplah dalam kesederhanaan yang solider dengan mereka yang lemah, jagalah kemurnian hati dalam setiap relasi pastoral, dan belajarlah taat dengan semangat iman kepada pimpinan serta keputusan komunitas. Biarkan kaul-kaul ini sungguh mewarnai cara berpikir dan bertindak Bruder sehari-hari.</li>
                    </ol>
                @endif
            </div>
        </div>

        <!-- TANDA TANGAN PENGESAHAN -->
        <div class="raport-signatures">
            <div style="text-align:center; width:220px;">
                Mengetahui/Mengesahkan,<br>
                <strong>Ketua TPPBM</strong>
                <div style="height:60px;"></div>
                <span style="font-weight:800; text-decoration:underline;">Br. F.A. Galih Sih Hartanta, FIC</span>
            </div>
            <div style="text-align:center; width:220px;">
                Semarang, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
                <strong>Administrator Data</strong>
                <div style="height:60px;"></div>
                <span style="font-weight:800; text-decoration:underline;">Br. Andrias Purwanto, FIC</span>
            </div>
        </div>

        <!-- FOOTER DOKUMEN -->
        <div class="raport-doc-footer">
            <div>Raport Pendampingan - {{ $raport['data_diri']['nama'] }}</div>
            <div>Tim Pemantap Panggilan Bruder Muda (TPPBM)</div>
        </div>

    </div>
@endif
@endsection
