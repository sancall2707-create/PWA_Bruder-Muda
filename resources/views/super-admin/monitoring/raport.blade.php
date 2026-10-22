@extends('layouts.super-admin')

@section('content')
<style>
    .raport-paper {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #D0D5DD;
        box-shadow: 0 4px 16px rgba(16, 24, 40, 0.06);
        padding: 24px;
        margin-top: 14px;
        font-size: 12px;
        color: #101828;
        line-height: 1.45;
    }
    .raport-section-title {
        background: #D9E8F9;
        border-left: 4px solid #0B3F91;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 12px;
        color: #0B3F91;
        text-transform: uppercase;
        margin: 18px 0 8px;
        letter-spacing: 0.4px;
    }
    .raport-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 11px;
    }
    .raport-table th, .raport-table td {
        border: 1px solid #9CA3AF;
        padding: 6px 8px;
        vertical-align: top;
    }
    .raport-table th {
        background: #F8FAFC;
        font-weight: 700;
        color: #1E293B;
    }
    .raport-label {
        background: #F1F5F9;
        font-weight: 700;
        width: 130px;
        color: #334155;
    }
    .status-box-v {
        background: #16A34A;
        color: #ffffff;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 4px;
    }
    .status-box-x {
        background: #DC2626;
        color: #ffffff;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 4px;
    }
    @media print {
        body { background: #fff !important; }
        .sa-header, .sa-bottom-nav, .sa-menu-toggle, .no-print { display: none !important; }
        .raport-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .page-break { page-break-after: always; }
        tr { page-break-inside: avoid; }
    }
</style>

<div class="no-print">
    @include('super-admin.monitoring._toolbar', ['exportKind' => 'raport'])

    <div style="margin-top: 12px; display: flex; justify-content: flex-end;">
        <button onclick="window.print()" style="background:#0B3F91; color:#fff; border:none; padding:10px 16px; border-radius:10px; font-weight:700; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(11,63,145,0.25);">
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
    <div id="raportContainer" class="raport-paper">
        <!-- KOP RESMI -->
        <div style="display:flex; align-items:center; gap:16px; border-bottom:2px solid #0B3F91; padding-bottom:12px; margin-bottom:16px;">
            <img src="/images/logo-fic.png" alt="Logo Kongregasi FIC" style="width:68px; height:68px; object-fit:contain;">
            <div style="flex:1;">
                <div style="font-size:15px; font-weight:800; color:#0B3F91; letter-spacing:0.4px; text-transform:uppercase;">RAPORT PENDAMPINGAN BRUDER MUDA</div>
                <div style="font-size:13px; font-weight:700; color:#101828;">Kongregasi Bruder FIC Indonesia</div>
                <div style="font-size:11px; color:#475467;">Tim Pemantap Panggilan Bruder Muda (TPPBM)</div>
            </div>
        </div>

        <!-- A. DATA DIRI -->
        <div class="raport-section-title">A. DATA DIRI</div>
        <table class="raport-table">
            <tr>
                <td class="raport-label">Nama Lengkap</td>
                <td><strong>{{ $raport['data_diri']['nama'] }}</strong></td>
                <td class="raport-label">Username Akses</td>
                <td>{{ $raport['data_diri']['username'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Email</td>
                <td>{{ $raport['data_diri']['email'] }}</td>
                <td class="raport-label">Tanggal Lahir</td>
                <td>{{ $raport['data_diri']['birth_date'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Tempat Lahir</td>
                <td>{{ $raport['data_diri']['birth_place'] }}</td>
                <td class="raport-label">No. Telepon / WA</td>
                <td>{{ $raport['data_diri']['phone'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Kata Sandi</td>
                <td>[Dilindungi Sistem]</td>
                <td class="raport-label">Asal Paroki</td>
                <td>{{ $raport['data_diri']['parish_origin'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Alamat Rumah Asal</td>
                <td colspan="3">{{ $raport['data_diri']['home_address'] }}</td>
            </tr>
        </table>

        <!-- B. DATA KELUARGA & KONTAK DARURAT -->
        <div class="raport-section-title">B. DATA KELUARGA &amp; KONTAK DARURAT</div>
        <div style="font-size:11px; font-weight:800; color:#334155; margin:6px 0 4px; text-transform:uppercase;">Data Orang Tua</div>
        <table class="raport-table">
            <tr>
                <td class="raport-label">Nama Ayah</td>
                <td>{{ $raport['keluarga']['father_name'] }}</td>
                <td class="raport-label">Tahun Lahir</td>
                <td style="width:70px;">{{ $raport['keluarga']['father_birth_year'] }}</td>
                <td class="raport-label">Pekerjaan Ayah</td>
                <td>{{ $raport['keluarga']['father_job'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Nama Ibu</td>
                <td>{{ $raport['keluarga']['mother_name'] }}</td>
                <td class="raport-label">Tahun Lahir</td>
                <td>{{ $raport['keluarga']['mother_birth_year'] }}</td>
                <td class="raport-label">Pekerjaan Ibu</td>
                <td>{{ $raport['keluarga']['mother_job'] }}</td>
            </tr>
        </table>

        <div style="font-size:11px; font-weight:800; color:#334155; margin:6px 0 4px; text-transform:uppercase;">Data Saudara &amp; Narahubung</div>
        <table class="raport-table">
            <tr>
                <td class="raport-label">Jumlah Saudara</td>
                <td colspan="3">{{ $raport['keluarga']['siblings_text'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">Narahubung Darurat</td>
                <td>{{ $raport['keluarga']['emergency_name'] }}</td>
                <td class="raport-label">Status Keluarga</td>
                <td>{{ $raport['keluarga']['emergency_relation'] }}</td>
            </tr>
            <tr>
                <td class="raport-label">No. HP Narahubung</td>
                <td>{{ $raport['keluarga']['emergency_phone'] }}</td>
                <td class="raport-label">Alamat Narahubung</td>
                <td>{{ $raport['keluarga']['emergency_address'] }}</td>
            </tr>
        </table>

        <!-- C. RIWAYAT PENDIDIKAN -->
        <div class="raport-section-title">C. RIWAYAT PENDIDIKAN</div>
        <table class="raport-table">
            <thead>
                <tr>
                    <th style="width:70px;">Jenjang</th>
                    <th>Nama Institusi</th>
                    <th>Jurusan / Prodi</th>
                    <th style="width:140px; text-align:center;">Tahun</th>
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
                    <th>Tahapan</th>
                    <th style="width:90px; text-align:center;">Tahun</th>
                    <th style="width:120px; text-align:center;">Jumlah Angkatan</th>
                    <th>Magister / Provinsial</th>
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
        <table class="raport-table">
            <thead>
                <tr>
                    <th style="width:80px; text-align:center;">Tugas Ke-</th>
                    <th>Nama Komunitas</th>
                    <th style="width:110px; text-align:center;">Masa Tugas</th>
                    <th>Tugas Pokok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($raport['perutusan'] as $p)
                    <tr>
                        <td style="text-align:center; font-weight:700;">{{ $p['tugas_ke'] }}</td>
                        <td>{{ $p['komunitas'] }}</td>
                        <td style="text-align:center;">{{ $p['masa'] }}</td>
                        <td>{{ $p['pokok'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- F. LAPORAN PELAKSANAAN BIMBINGAN ROHANI -->
        <div class="raport-section-title">F. LAPORAN PELAKSANAAN BIMBINGAN ROHANI</div>
        @foreach($raport['bimbingan_bulan'] as $b)
            <table class="raport-table" style="margin-bottom:10px;">
                <tr>
                    <td rowspan="3" style="width:90px; vertical-align:middle; text-align:center; background:#F8FAFC; font-weight:800; color:#0B3F91;">
                        BULAN<br>{{ $b['bulan'] }}
                    </td>
                    <th style="width:40px; text-align:center;">Tgl</th>
                    <th>Pembimbing</th>
                    <th>Tempat / Ruangan</th>
                    <th style="width:40px; text-align:center;">Status</th>
                </tr>
                <tr>
                    <td style="text-align:center; font-weight:700;">{{ $b['date'] }}</td>
                    <td>{{ $b['mentor'] }}</td>
                    <td>{{ $b['location'] }}</td>
                    <td style="text-align:center;">
                        @if($b['done'])
                            <span class="status-box-v">✓</span>
                        @else
                            <span class="status-box-x">✗</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="background:#FAFAFA;">
                        <div style="font-size:10px; font-weight:700; color:#64748B; text-transform:uppercase; margin-bottom:2px;">Isi Bimbingan / Catatan Refleksi / Keterangan</div>
                        <div style="font-style: italic; color:#1E293B;">{{ $b['content'] }}</div>
                    </td>
                </tr>
            </table>
        @endforeach

        <!-- G. LAPORAN PELAKSANAAN WAWANHATI PIMPINAN LOKAL -->
        <div class="raport-section-title">G. LAPORAN PELAKSANAAN WAWANHATI PIMPINAN LOKAL</div>
        @foreach($raport['wawanhati_bulan'] as $w)
            <table class="raport-table" style="margin-bottom:10px;">
                <tr>
                    <td rowspan="3" style="width:90px; vertical-align:middle; text-align:center; background:#F8FAFC; font-weight:800; color:#0B3F91;">
                        BULAN<br>{{ $w['bulan'] }}
                    </td>
                    <th style="width:40px; text-align:center;">Tgl</th>
                    <th>Pimpinan Lokal</th>
                    <th>Tempat / Ruangan</th>
                    <th style="width:40px; text-align:center;">Status</th>
                </tr>
                <tr>
                    <td style="text-align:center; font-weight:700;">{{ $w['date'] }}</td>
                    <td>{{ $w['leader'] }}</td>
                    <td>{{ $w['location'] }}</td>
                    <td style="text-align:center;">
                        @if($w['done'])
                            <span class="status-box-v">✓</span>
                        @else
                            <span class="status-box-x">✗</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="background:#FAFAFA;">
                        <div style="font-size:10px; font-weight:700; color:#64748B; text-transform:uppercase; margin-bottom:2px;">Isi Bimbingan / Catatan Refleksi / Keterangan</div>
                        <div style="font-style: italic; color:#1E293B;">{{ $w['content'] }}</div>
                    </td>
                </tr>
            </table>
        @endforeach

        <!-- H. LAPORAN KEGIATAN PENGEMBANGAN DIRI -->
        <div class="raport-section-title">H. LAPORAN KEGIATAN PENGEMBANGAN DIRI</div>
        @forelse($raport['pengembangan_diri'] as $pd)
            <div style="font-size:11px; font-weight:800; color:#0B3F91; margin:8px 0 4px;">{{ $pd['label'] }}</div>
            <table class="raport-table" style="margin-bottom:12px;">
                <tr>
                    <td class="raport-label">Tanggal Kegiatan</td>
                    <td>{{ $pd['date'] }}</td>
                    <td class="raport-label">Penyelenggara</td>
                    <td>{{ $pd['organizer'] }}</td>
                </tr>
                <tr>
                    <td class="raport-label">Nama Kegiatan</td>
                    <td><strong>{{ $pd['name'] }}</strong></td>
                    <td class="raport-label">Narasumber</td>
                    <td>{{ $pd['speaker'] }}</td>
                </tr>
                <tr>
                    <td class="raport-label">Deskripsi Kegiatan</td>
                    <td colspan="3">{{ $pd['description'] }}</td>
                </tr>
                <tr>
                    <td class="raport-label">Refleksi Singkat</td>
                    <td colspan="3" style="font-style:italic;">{{ $pd['reflection'] }}</td>
                </tr>
            </table>
        @empty
            <div style="padding:10px; border:1px dashed #CBD5E1; border-radius:8px; text-align:center; color:#64748B; margin-bottom:10px;">
                Belum ada kegiatan pengembangan diri yang dilaporkan pada periode ini.
            </div>
        @endforelse

        <!-- I. CATATAN PENDAMPING BRUDER MUDA (TPPBM) -->
        <div class="raport-section-title">I. CATATAN PENDAMPING BRUDER MUDA (TPPBM)</div>
        <div style="display:flex; flex-direction:column; gap:10px; font-size:11px; line-height:1.5;">
            <div>
                <strong>1. Catatan Pribadi</strong>
                @if(!empty($raport['catatan_tppbm']['pribadi']))
                    <ul style="margin:4px 0 0 18px; padding:0;">
                        @foreach($raport['catatan_tppbm']['pribadi'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 18px; padding:0; color:#334155;">
                        <li>Proses pengenalan diri adalah fondasi utama dalam panggilan. Teruslah belajar menerima kelebihan dan kelemahan diri secara jujur dan terbuka. Jangan lupa untuk selalu menjaga keseimbangan antara kesehatan fisik, mental, dan emosional (cura personalis).</li>
                        <li>Kematangan pribadi dan kemauan untuk terus dievaluasi akan sangat membantu kelancaran proses formasi Bruder ke depannya.</li>
                    </ol>
                @endif
            </div>

            <div>
                <strong>2. Catatan Hidup Rohani/Doa</strong>
                @if(!empty($raport['catatan_tppbm']['rohani']))
                    <ul style="margin:4px 0 0 18px; padding:0;">
                        @foreach($raport['catatan_tppbm']['rohani'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 18px; padding:0; color:#334155;">
                        <li>Hidup rohani adalah jangkar dari seluruh peziarahan panggilan kita. Tetaplah setia dan bertekun dalam doa-doa harian, meditasi pribadi, serta perayaan Ekaristi.</li>
                        <li>Usahakan agar waktu-waktu hening tidak hanya menjadi rutinitas wajib yang kering, melainkan menjadi perjumpaan yang intim untuk sungguh-sungguh mendengarkan kehendak Tuhan setiap harinya.</li>
                    </ol>
                @endif
            </div>

            <div>
                <strong>3. Catatan Hidup Komunitas</strong>
                @if(!empty($raport['catatan_tppbm']['komunitas']))
                    <ul style="margin:4px 0 0 18px; padding:0;">
                        @foreach($raport['catatan_tppbm']['komunitas'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 18px; padding:0; color:#334155;">
                        <li>Komunitas adalah tempat di mana kasih persaudaraan diuji sekaligus dihidupi secara nyata. Berusahalah untuk selalu proaktif mengambil bagian dalam tugas-tugas harian dan kegiatan bersama (seperti rekreasi atau doa komunitas).</li>
                        <li>Tumbuhkan kepekaan terhadap kebutuhan sesama bruder, serta belajarlah untuk senantiasa lapang dada dalam menghadapi perbedaan karakter antaranggota komunitas.</li>
                    </ol>
                @endif
            </div>

            <div>
                <strong>4. Catatan Hidup Kerasulan</strong>
                @if(!empty($raport['catatan_tppbm']['kerasulan']))
                    <ul style="margin:4px 0 0 18px; padding:0;">
                        @foreach($raport['catatan_tppbm']['kerasulan'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 18px; padding:0; color:#334155;">
                        <li>Tugas perutusan dan kerasulan kita menuntut dedikasi dan kasih yang tulus. Layanilah setiap jiwa yang dipercayakan kepada Bruder dengan hati yang gembira dan penuh totalitas.</li>
                        <li>Selalu ingat bahwa karya kerasulan bukanlah sekadar soal pencapaian kerja profesional, melainkan wujud kesaksian nyata akan kehadiran Allah yang peduli di tengah-tengah umat dan kaum muda.</li>
                    </ol>
                @endif
            </div>

            <div>
                <strong>5. Catatan Hidup Triprasetia (Kaul-kaul)</strong>
                @if(!empty($raport['catatan_tppbm']['triprasetia']))
                    <ul style="margin:4px 0 0 18px; padding:0;">
                        @foreach($raport['catatan_tppbm']['triprasetia'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                @else
                    <ol type="a" style="margin:4px 0 0 18px; padding:0; color:#334155;">
                        <li>Menghidupi Triprasetia (Kemiskinan, Kemurnian, dan Ketaatan) adalah jalan pembebasan sejati, bukan pengekangan.</li>
                        <li>Hiduplah dalam kesederhanaan yang solider dengan mereka yang lemah, jagalah kemurnian hati dalam setiap relasi pastoral, dan belajarlah taat dengan semangat iman kepada pimpinan serta keputusan komunitas. Biarkan kaul-kaul ini sungguh mewarnai cara berpikir dan bertindak Bruder sehari-hari.</li>
                    </ol>
                @endif
            </div>
        </div>

        <!-- PENGESAHAN & TANDA TANGAN -->
        <table style="width:100%; margin-top:28px; font-size:11px; border-collapse:collapse; border:none;">
            <tr>
                <td style="width:50%; vertical-align:top; border:none;">
                    Mengetahui/Mengesahkan,<br>
                    <strong>Ketua TPPBM</strong>
                    <div style="height:60px;"></div>
                    <u><strong>Br. F.A. Galih Sih Hartanta, FIC</strong></u>
                </td>
                <td style="width:50%; vertical-align:top; border:none; text-align:right;">
                    Dibuat di Semarang, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>
                    <strong>Administrator Data</strong>
                    <div style="height:60px;"></div>
                    <u><strong>Br. Andrias Purwanto, FIC</strong></u>
                </td>
            </tr>
        </table>
    </div>
@endif
@endsection
