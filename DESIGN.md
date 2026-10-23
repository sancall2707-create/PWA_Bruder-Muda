# DOKUMEN SPESIFIKASI DESAIN SISTEM (DESIGN.MD)
## PWA PENDAMPINGAN FORMATIF BRUDER MUDA FIC INDONESIA
**Kongregasi Bruder Santo Aloysius Gonzaga (FIC) Indonesia**  
*Tim Pemantap Panggilan Bruder Muda (TPPBM)*  
Versi Dokumen: 1.3 (Pemutakhiran Sistem Terpadu) — Oktober 2026

---

## 1. RINGKASAN EKSEKUTIF & TUJUAN SISTEM
Aplikasi **PWA Bruder Muda** adalah platform *Progressive Web App* berbasis mobile-first yang dirancang khusus untuk memfasilitasi pendampingan formatif para Bruder Muda di lingkungan Kongregasi Bruder FIC Indonesia.

Aplikasi ini mengintegrasikan seluruh instrumen formasi religius bruder ke dalam satu sistem terpusat:
1. **Pencatatan Refleksi Formatif**: Pelaksanaan Bimbingan Rohani bulanan dan Wawanhati Pimpinan Lokal (Pilok) dengan otomatisasi penyelesaian agenda jadwal.
2. **Jurnal Pengembangan Diri**: Dokumentasi seminar, retret, rekoleksi, dan pembelajaran mandiri lengkap dengan refleksi terstruktur serta fitur ubah (*edit*) dan hapus.
3. **Agenda & Kalender Formasi**: Penjadwalan kronologis pertemuan pembinaan dan kegiatan pribadi dengan dukungan penambahan, pengubahan, dan pembatalan agenda.
4. **Monitoring & Evaluasi TPPBM**: Pemantauan tingkat kepatuhan bulanan, rekam jejak (*track record*), pencatatan & penarikan catatan formatif, serta penerbitan **Raport Pendampingan Bruder Muda**.
5. **Komunikasi & Arahan Formatif**: Publikasi Sapaan Provinsial bulanan, rilis pengumuman kegiatan, dan pengiriman Pesan Khusus Privat satu arah dari pimpinan/TPPBM.

---

## 2. ARSITEKTUR TEKNOLOGI
* **Framework Backend**: Laravel 11.x (PHP 8.3+)
* **Basis Data**: SQLite (`database/database.sqlite`) dengan schema migrations dan foreign-key constraints aktif.
* **Frontend Templating**: Laravel Blade Views dengan arsitektur Layout modular (`layouts/user-app.blade.php`, `layouts/super-admin.blade.php`).
* **Styling & CSS Framework**: Kustom CSS murni berbobot ringan (`public/css/fic-pwa.css`) dipadukan dengan utility styling inline terstruktur.
* **Mesin Rendering PDF**: Google Chrome headless (`google-chrome-stable` / `RaportPdfPrinter.php`) untuk ekspor dokumen PDF server-side yang presisi piksel.
* **PWA & Offline Capability**:
  * Web App Manifest: `public/manifest.json` (Ikon FIC 192x192 & 512x512, background `#003E97`, theme `#1455C0`, display `standalone`).
  * Service Worker: `public/sw.js` (Cache-first untuk aset statis, font Google, ikon SVG, dan logo; Network-first untuk alur formulir data).
* **Integrasi & Deployment**:
  * Port Dev: `8022` (`php artisan serve --host=127.0.0.1 --port=8022`)
  * Akses Publik: Cloudflare Quick Tunnel (`cloudflared tunnel`)
  * Repositori Git: `git@github.com:sancall2707-create/PWA_Bruder-Muda.git` (Branch `main`).

---

## 3. DESIGN SYSTEM & IDENTITAS VISUAL

### 3.1. Filosofi & Tata Letak Mobile-First
* **Orientasi**: Mobile App Container (Maksimal `max-width: 480px` rata tengah di layar besar dengan bayangan halus `box-shadow: 0 0 30px rgba(0,62,151,0.08)`). Pada tampilan cetak/raport lebar, kontainer melebar proporsional (`wideLayout` hingga `880px`).
* **Efek Glassmorphism**: Permukaan kartu semi-transparan `rgba(255, 255, 255, 0.72)` dengan `backdrop-filter: blur(14px)` dan border halus `rgba(255, 255, 255, 0.85)`.
* **Kerapian Antarmuka**:
  * Avatar profil dan judul seksi formulir selalu diposisikan **rata tengah (center simetris)**.
  * Form berdimensi panjang mengaktifkan flag `$hideBottomNav = true` agar bilah navigasi bawah tidak menumpuk elemen input.
  * Tombol kembali navigasi terintegrasi rapi di dalam subheader resmi PWA.

### 3.2. Sistem Navigasi Ganda (Top Header + Bottom Navigation)
1. **Navigasi Atas (Top Header & Slide Menu)**:
   - Header aplikasi memuat lencana peran (`SUPER ADMIN`, `KETUA TPPBM`, `TIM PENDAMPING TPPBM`), avatar profil mandiri, dan tombol hamburger menu.
   - Menu panel geser memuat tautan rute lengkap dan tombol *Logout*.
   - Khusus akun Admin dan Admin Ketua TPPBM, tersedia tautan cepat **"Beranda Bruder Muda"** untuk melihat antarmuka sisi bruder.
2. **Navigasi Bawah (Bottom Navigation Bar)**:
   - **Super Admin & Para Admin (`sa-bottom-nav`)**: 5 menu utama permanen di layar ponsel:
     1. `Beranda`: Dashboard metrik & ikhtisar.
     2. `Bruder`: Manajemen Data Bruder & Akun Staff.
     3. `Raport`: Modul Raport Pendampingan Cetak/PDF.
     4. `Monitoring`: Monitoring Laporan & Rekapitulasi Agenda.
     5. `Sapaan`: Manajemen Sapaan & Pengumuman.
   - **Bruder Muda (`bottom-nav`)**: 4 menu utama:
     1. `Beranda`: Metrik progres & sapaan.
     2. `Kalender`: Jadwal formasi & agenda pribadi.
     3. `Jurnal`: Dokumentasi pengembangan diri.
     4. `Profil`: Biodata lengkap pribadi & formasi.

### 3.3. Palet Warna Resmi
| Variabel / Token | Kode Hex / Nilai | Penggunaan |
| :--- | :--- | :--- |
| `--fic-primary` | `#1455C0` | Warna tema utama, tombol aksi primer, header tab aktif |
| `--fic-primary-dark` | `#003E97` | Gradien tombol, kop header, border seksi resmi |
| `--fic-navy-deep` | `#0E3366` | Header tabel Raport, judul KOP, border dokumen resmi |
| `--fic-bg-light` | `#E7F0FB` – `#F7F9FC` | Latar belakang gradien lembut aplikasi |
| `--fic-gold` | `#B57900` – `#D97706` | Tombol Kirim Pesan, Agenda Lainnya, Ekspor Excel |
| `--fic-cyan` | `#00838F` – `#0284C7` | Modul Wawanhati Pimpinan Lokal |
| `--fic-green-soft` | `#ECFDF3` / `#16A34A` | Badge status "Selesai", lencana hijau "TELAH TERLAKSANA" |
| `--fic-danger` | `#B42318` / `#DC2626` | Tombol hapus, lencana merah "BELUM / TIDAK TERLAKSANA" |

---

## 4. PERAN PENGGUNA & HAK AKSES (RBAC)

Aplikasi menerapkan sistem otentikasi sesi berbasis 4 peran terstruktur:

```
                  ┌─────────────────────────────────────────┐
                  │          SUPER ADMIN (super_admin)      │
                  │  Kontrol Akun, Biodata Lengkap, Raport  │
                  └────────────────────┬────────────────────┘
                                       │
        ┌──────────────────────────────┴──────────────────────────────┐
        ▼                                                             ▼
┌───────────────────────────────────────┐             ┌───────────────────────────────────────┐
│     ADMIN KETUA TPPBM (admin_ketua)   │             │          ADMIN TPPBM (admin)          │
│  Tanda Tangan Ketua, Monitoring       │             │  Pendampingan, Catatan, Monitoring    │
└──────────────────┬────────────────────┘             └──────────────────┬────────────────────┘
                   │                                                     │
                   └──────────────────────────┬──────────────────────────┘
                                              ▼
                             ┌─────────────────────────────────┐
                             │       BRUDER MUDA (user)        │
                             │  Laporan, Refleksi, Jurnal      │
                             └─────────────────────────────────┘
```

1. **Super Admin (`role: super_admin`)**:
   - Kontrol mutlak data pengguna: pembuatan akun baru dengan pilihan peran (`user`, `admin`, `admin_ketua_tppbm`).
   - Editor data diri lengkap seluruh bruder (Data Pokok, Keluarga, Pendidikan, Formasi, Penugasan).
   - Pengelolaan foto profil akun, reset sandi, aktivasi/nonaktivasi, dan penghapusan akun.
   - Penandatangan resmi pada dokumen Raport Pendampingan sebagai **Administrator Data**.
2. **Admin Ketua TPPBM (`role: admin_ketua_tppbm`)**:
   - Memiliki seluruh hak akses manajerial TPPBM (Monitoring, Raport, Catatan, Pesan Khusus).
   - Namanya secara otomatis ditautkan ke lembar pengesahan Raport Pendampingan sebagai **Ketua TPPBM**.
3. **Admin TPPBM (`role: admin`)**:
   - Beranda pendampingan formatif (pemantauan kepatuhan bulanan).
   - Direktori Bruder Muda, Rekam Jejak (*Track Record*), input & penarikan Catatan Pembinaan 5 Aspek.
   - Pengiriman dan penarikan Pesan Khusus Privat satu arah.
   - Monitoring laporan bimbingan/wawanhati/jurnal dan ekspor data (Excel & PDF).
4. **Bruder Muda (`role: user`)**:
   - Beranda personal dengan cincin progres kepatuhan bulanan.
   - Notifikasi pesan khusus privat dari TPPBM.
   - Pengisian laporan formasi (Bimbingan Rohani & Wawanhati) dengan validasi refleksi 200–500 karakter.
   - Manajemen agenda pribadi di kalender (tambah, ubah, hapus).
   - Manajemen jurnal pengembangan diri (tambah, ubah, hapus).
   - Pembaruan mandiri data profil dan biodata lengkap.

---

## 5. SKEMA BASIS DATA (DATA ARCHITECTURE)

```
┌─────────────────────────────────┐
│             users               │
├─────────────────────────────────┤
│ id (PK)                         │
│ name, username, email, password │
│ role (super_admin/admin_ketua/  │
│       admin/user)               │
│ is_active (boolean)             │
│ birth_place, birth_date, phone  │
│ community_location, bio, photo  │
│ family_data (JSON)              │
│ education_data (JSON)           │
│ formation_data (JSON)           │
│ assignment_data (JSON)          │
└────────┬───────────────┬────────┘
         │               │
         ▼               ▼
┌──────────────────┐   ┌───────────────────────────┐
│  bruder_notes    │   │      direct_messages      │
├──────────────────┤   ├───────────────────────────┤
│ id (PK)          │   │ id (PK)                   │
│ user_id (FK)     │   │ recipient_id (FK)         │
│ author_id (FK)   │   │ sender_id (FK)            │
│ category (Enum5) │   │ title, content            │
│ content (text)   │   │ read_at (datetime)        │
└──────────────────┘   └───────────────────────────┘
         │
         ▼
┌───────────────────────────────┐     ┌───────────────────────────────┐
│ spiritual_guidance_schedules  │◄────┤  spiritual_guidance_reports   │
├───────────────────────────────┤     ├───────────────────────────────┤
│ id (PK)                       │     │ id (PK)                       │
│ user_id (FK)                  │     │ user_id (FK)                  │
│ mentor_name, scheduled_at     │     │ schedule_id (FK, Nullable)    │
│ location, topic_notes         │     │ mentor_name, meeting_at       │
│ status (scheduled/completed)  │     │ location, report_content      │
└───────────────────────────────┘     │ status                        │
                                      └───────────────────────────────┘
```

---

## 6. SPESIFIKASI DOKUMEN: RAPORT PENDAMPINGAN BRUDER MUDA

Modul Raport Pendampingan mengimplementasikan standardisasi format dokumen resmi TPPBM 4 Halaman Berkelanjutan (*Continuous Flow*):

### 6.1. Standar Cetak & Margin Kertas A4
* **Ukuran Kertas**: A4 Portrait (`210 mm x 297 mm`).
* **Margin Presisi**:
  * Atas: `1.5 cm`
  * Kanan: `1.5 cm`
  * Bawah: `1.8 cm`
  * Kiri: `2.0 cm` (ruang jilid resmi)
* **Tata Letak Bersambung Padat**: Konten mengalir alami (*continuous flow*) tanpa jeda halaman paksa yang membuang kertas.

### 6.2. Struktur 9 Seksi Komprehensif
1. **Kop Dokumen Resmi**:
   - Sisi Kiri: Logo resmi Kongregasi Bruder FIC (`logo-fic.png`).
   - Tengah: `RAPORT PENDAMPINGAN BRUDER MUDA` & `Tim Pemantap Panggilan Bruder Muda (TPPBM)`.
   - Sisi Kanan: Logo resmi TPPBM periode 2024–2030 (`logo-tppbm.png`).
   - Garis Pembatas: Garis ganda warna Navy (`#0E3366`).
   - Subheader: Periode Bulan & Tahun Terpilih (contoh: *Periode Januari - Oktober 2026*).
2. **Seksi A (Data Diri)**: Kartu grid 2 kolom (Nama, Username, TTL, WhatsApp, Paroki, Alamat Asal).
3. **Seksi B (Data Keluarga & Narahubung)**: Kartu grid 2 kolom (Ayah, Ibu, Saudara, Kontak Darurat).
4. **Seksi C (Riwayat Pendidikan)**: Tabel institusi formal dari jenjang SD hingga S2.
5. **Seksi D (Tahapan Formasi FIC)**: Tabel kronologis tahapan dari Postulat (satuan: *Frater*), Novisiat, Prasetia, hingga Pembaruan (satuan: *Bruder*).
6. **Seksi E (Komunitas Tinggal & Perutusan)**: Riwayat perutusan dan tugas pokok komunitas tinggal.
7. **Seksi F (Laporan Bimbingan Rohani Bulanan)**: Kartu bulanan dengan lencana kapsul:
   - Terlaksana: Lencana hijau `✔ TELAH TERLAKSANA` dengan aksen border Navy.
   - Belum Terlaksana: Lencana merah `✘ BELUM / TIDAK TERLAKSANA` dengan aksen border merah.
8. **Seksi G (Laporan Wawanhati Pilok Bulanan)**: Kartu bulanan dengan format identik Seksi F.
9. **Seksi H (Laporan Pengembangan Diri)**: Kartu ringkasan jurnal seminar/pelatihan dengan badge `TERCATAT`.
10. **Seksi I (Catatan Pendamping TPPBM)**: 5 aspek pembinaan formatif (Pribadi, Rohani/Doa, Komunitas, Kerasulan, Triprasetia).
11. **Lembar Pengesahan Tanda Tangan Dinamis**:
   - Kolom Kiri: **Ketua TPPBM** (otomatis mengambil nama akun `admin_ketua_tppbm`).
   - Kolom Kanan: **Administrator Data** (otomatis mengambil nama akun `super_admin`).
12. **Running Footer**: Garis pembatas abu-abu di dasar setiap halaman dengan teks kiri `Raport Bruder Muda - [Nama]` dan teks kanan `Halaman [N]`.

---

## 7. OTOMATISASI STATUS & FITUR PENDUKUNG

1. **Otomatisasi Status Agenda (`scheduled` → `completed`)**:
   - Saat laporan bimbingan atau wawanhati dikirimkan, jadwal terdekat yang relevan otomatis berubah status dari `Menunggu` menjadi `Selesai`.
2. **Filter Rentang Bulan Fleksibel (1–12 Bulan)**:
   - Mendukung pemilihan bulan awal dan akhir untuk rekapitulasi semesteran, triwulan, maupun tahunan.
3. **Manajemen Akun Terpadu**:
   - Menampilkan akun bruder maupun admin pada daftar manajemen dengan menu aksi adaptif (tanpa tombol track record untuk peran admin).
   - Pengunggahan foto profil dilengkapi auto-kompresi peramban (Canvas) dan batas ukuran hingga 10 MB.
4. **Fitur Pengelolaan Konten Formatif**:
   - Tarik & ubah catatan pembinaan TPPBM.
   - Edit & hapus agenda pribadi bruder di kalender.
   - Edit & hapus jurnal pengembangan diri.
   - Hapus sapaan & pengumuman provinsial beserta pembersihan berkas gambar lampiran.

---

## 8. INTEGRASI SISTEM & KUALITAS PERANGKAT LUNAK
* **Pembersihan Cache Sistem**: `php artisan optimize:clear`
* **Suite Pengujian Otomatis**: Dilengkapi pengujian unit & fitur komprehensif (`php artisan test`) dengan total **74 skenario pengujian lolos (712 assertions PASS)**.
* **Standar Kode**: Mematuhi PSR-12, Laravel 11 conventions, zero-warning test suite, dan komunikasi sistem 100% Bahasa Indonesia baku.
