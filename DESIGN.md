# DOKUMEN SPESIFIKASI DESAIN SISTEM (DESIGN.MD)
## PWA PENDAMPINGAN FORMATIF BRUDER MUDA FIC INDONESIA
**Kongregasi Bruder Santo Aloysius Gonzaga (FIC) Indonesia**  
*Tim Pemantap Panggilan Bruder Muda (TPPBM)*  
Versi Dokumen: 1.2 (Tahap 1 Terpadu) — Oktober 2026

---

## 1. RINGKASAN EKSEKUTIF & TUJUAN SISTEM
Aplikasi **PWA Bruder Muda** adalah platform *Progressive Web App* berbasis mobile-first yang dirancang khusus untuk memfasilitasi pendampingan formatif para Bruder Muda di lingkungan Kongregasi Bruder FIC Indonesia.

Aplikasi ini mengintegrasikan seluruh instrumen formasi religius bruder ke dalam satu sistem terpusat:
1. **Pencatatan Refleksi Formatif**: Pelaksanaan Bimbingan Rohani bulanan dan Wawanhati Pimpinan Lokal (Pilok).
2. **Jurnal Pengembangan Diri**: Dokumentasi seminar, retret, rekoleksi, dan pembelajaran mandiri beserta evaluasi refleksinya.
3. **Agenda & Kalender Formasi**: Penjadwalan kronologis pertemuan pembinaan dan kegiatan pribadi.
4. **Monitoring & Evaluasi TPPBM**: Pemantauan tingkat kepatuhan bulanan, rekam jejak (*track record*), dan penerbitan **Raport Pendampingan Bruder Muda**.
5. **Komunikasi & Arahan Formatif**: Publikasi Sapaan Provinsial bulanan dan pengiriman Pesan Khusus Privat satu arah dari pimpinan/TPPBM.

---

## 2. ARSITEKTUR TEKNOLOGI
* **Framework Backend**: Laravel 11.x (PHP 8.3+)
* **Basis Data**: SQLite (`database/database.sqlite`) dengan schema migrations dan foreign-key constraints aktif.
* **Frontend Templating**: Laravel Blade Views dengan arsitektur Layout modular (`layouts/user-app.blade.php`, `layouts/super-admin.blade.php`).
* **Styling & CSS Framework**: Kustom CSS murni berbobot ringan (`public/css/fic-pwa.css`) dipadukan dengan utility styling inline terstruktur.
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
* **Orientasi**: Mobile App Container (Maksimal `max-width: 480px` rata tengah di layar besar dengan bayangan halus `box-shadow: 0 0 30px rgba(0,62,151,0.08)`).
* **Efek Glassmorphism**: Permukaan kartu semi-transparan `rgba(255, 255, 255, 0.72)` dengan `backdrop-filter: blur(14px)` dan border halus `rgba(255, 255, 255, 0.85)`.
* **Kerapian Viewport**:
  * Avatar profil dan judul seksi formulir selalu diposisikan **rata tengah (center simetris)**.
  * Form berdimensi panjang mengaktifkan flag `$hideBottomNav = true` agar navigasi bawah tidak menumpuk elemen input.
  * Tombol kembali navigasi terintegrasi rapi di dalam subheader resmi (bukan tombol melayang bebas).

### 3.2. Palet Warna Resmi
| Variabel / Token | Kode Hex / Nilai | Penggunaan |
| :--- | :--- | :--- |
| `--fic-primary` | `#1455C0` | Warna tema utama, tombol aksi primer, header tab aktif |
| `--fic-primary-dark` | `#003E97` | Gradien tombol, kop header, border seksi resmi |
| `--fic-navy-deep` | `#0B3F91` | Judul Raport Pendampingan, border formal kop A4 |
| `--fic-bg-light` | `#E7F0FB` – `#F7F9FC` | Latar belakang gradien lembut aplikasi |
| `--fic-gold` | `#B57900` – `#D97706` | Tombol Kirim Pesan, Agenda Lainnya, Ekspor Excel |
| `--fic-cyan` | `#00838F` – `#0284C7` | Modul Wawanhati Pimpinan Lokal |
| `--fic-green-soft` | `#ECFDF3` / `#16A34A` | Badge status "Selesai", banner sukses, centang raport |
| `--fic-danger` | `#B42318` / `#DC2626` | Tombol hapus, penanda silang laporan belum ada |

### 3.3. Tipografi
* **Font Utama**: *Plus Jakarta Sans* (Heading & Identitas) dan *Inter* (Body Teks & Formulir).
* **Hierarki Font**:
  * Title Layar: 18px – 20px, Font Weight 800
  * Subheader & Label Seksi: 12px – 14px, Font Weight 700 / 800 (Uppercase, letter-spacing 0.5px)
  * Body & Isi Catatan: 13px – 14px, Line Height 1.45 – 1.55
  * Metadata & Keterangan: 11px – 12px, Color `#667085`

---

## 4. PERAN PENGGUNA & HAK AKSES (RBAC)

Aplikasi menerapkan sistem otentikasi sesi berbasis 3 peran tegas:

```
                  ┌─────────────────────────────────────────┐
                  │          SUPER ADMIN (super_admin)      │
                  │  Kontrol Akun, Biodata Lengkap, Raport  │
                  └────────────────────┬────────────────────┘
                                       │
                    ┌──────────────────┴──────────────────┐
                    ▼                                     ▼
┌───────────────────────────────────────┐ ┌───────────────────────────────────────┐
│          ADMIN TPPBM (admin)          │ │         BRUDER MUDA (user)            │
│  Monitoring, Raport, Catatan, Pesan   │ │  Laporan, Refleksi, Jurnal, Agenda    │
└───────────────────────────────────────┘ └───────────────────────────────────────┘
```

1. **Super Admin (`role: super_admin`)**:
   - Pembuatan akun baru dengan penentuan peran awal (Bruder Muda / Admin TPPBM).
   - Pengeditan data diri lengkap bruder (Data Akun, Keluarga, Pendidikan, Formasi, Penugasan).
   - Reset kata sandi dan pengaturan status aktif/nonaktif akun.
   - Manajemen publikasi Sapaan Provinsial & Pengumuman (termasuk penggantian foto).
   - Profil dan ganti kata sandi mandiri khusus Super Admin.
   - Akses Raport Pendampingan, Monitoring Laporan, dan Rekapitulasi Agenda.

2. **Admin TPPBM (`role: admin`)**:
   - Beranda khusus TPPBM (Metrik kepatuhan bruder, feed pembinaan terbaru).
   - Akses penuh ke Direktori Bruder Muda dan Rekam Jejak (*Track Record*).
   - Menambahkan **Catatan Pembinaan TPPBM** (5 Aspek Formatif).
   - Mengirim dan menarik **Pesan Khusus Privat Satu Arah** ke bruder individu.
   - Akses Modul **Raport Pendampingan** dan **Laporan Jurnal & Bimbingan**.

3. **Bruder Muda (`role: user`)**:
   - Beranda personal dengan progress ring indikator kepatuhan formasi bulanan.
   - Notifikasi khusus jika menerima Pesan Privat dari TPPBM / Provinsial.
   - Pengisian dan pengunggahan bukti formasi:
     - Form Laporan Bimbingan Rohani (Catatan refleksi 200–500 karakter).
     - Form Laporan Wawanhati Pimpinan Lokal (Catatan 200–500 karakter).
     - Form Jurnal Pengembangan Diri (Kegiatan, narasumber, refleksi).
     - Penjadwalan Agenda Pribadi & Bimbingan di Kalender.
   - Pembaruan mandiri data profil dan biodata keluarga/pendidikan/formasi.

---

## 5. SKEMA BASIS DATA (DATA ARCHITECTURE)

```
┌─────────────────────────────────┐
│             users               │
├─────────────────────────────────┤
│ id (PK)                         │
│ name, username, email, password │
│ role (super_admin/admin/user)   │
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
└───────────────────────────────┘     │ proof_file, status            │
                                      └───────────────────────────────┘
```

### Relasi Kunci:
* `User` 1 — N `SpiritualGuidanceReport` (Laporan Bimbingan)
* `User` 1 — N `WawanhatiReport` (Laporan Wawanhati)
* `User` 1 — N `SelfDevelopmentJournal` (Jurnal Pengembangan Diri)
* `User` 1 — N `BruderNote` (Catatan Evaluasi Pembimbing TPPBM)
* `User` 1 — N `DirectMessage` (Pesan Masuk Privat)
* `SpiritualGuidanceSchedule` 1 — 1 `SpiritualGuidanceReport` (Keterkaitan jadwal dengan laporan penyelesaian)

---

## 6. SPESIFIKASI DOKUMEN: RAPORT PENDAMPINGAN BRUDER MUDA

Modul Raport Pendampingan mengimplementasikan standardisasi dokumen evaluasi resmi TPPBM yang mencakup 9 seksi komprehensif:

```
┌─────────────────────────────────────────────────────────────────┐
│               RAPORT PENDAMPINGAN BRUDER MUDA                   │
│          Kongregasi Bruder FIC Indonesia - TPPBM                │
├─────────────────────────────────────────────────────────────────┤
│ A. DATA DIRI                                                    │
│    Nama Lengkap, Username, Email, Lahir, Kontak, Paroki, Alamat │
├─────────────────────────────────────────────────────────────────┤
│ B. DATA KELUARGA & KONTAK DARURAT                               │
│    Data Orang Tua (Ayah/Ibu), Saudara, Narahubung Darurat       │
├─────────────────────────────────────────────────────────────────┤
│ C. RIWAYAT PENDIDIKAN                                           │
│    SD, SMP, SMA/SMK, Sarjana (S1), Magister (S2)                │
├─────────────────────────────────────────────────────────────────┤
│ D. TAHUN MASUK & TAHAPAN FORMASI                                │
│    Postulat, Kanonik, Apostolik, Prasetia I, Pembaruan I s.d VI │
├─────────────────────────────────────────────────────────────────┤
│ E. KOMUNITAS TINGGAL & PERUTUSAN                                │
│    Riwayat Tugas Pertama hingga Tugas Keenam                    │
├─────────────────────────────────────────────────────────────────┤
│ F. LAPORAN PELAKSANAAN BIMBINGAN ROHANI                         │
│    Matriks Bulanan: Tgl, Pembimbing, Tempat, Status (✓/✗), Isi  │
├─────────────────────────────────────────────────────────────────┤
│ G. LAPORAN PELAKSANAAN WAWANHATI PIMPINAN LOKAL                 │
│    Matriks Bulanan: Tgl, Pimpinan, Tempat, Status (✓/✗), Isi    │
├─────────────────────────────────────────────────────────────────┤
│ H. LAPORAN KEGIATAN PENGEMBANGAN DIRI                           │
│    Entri Jurnal: Tgl, Penyelenggara, Kegiatan, Narasumber, dsb. │
├─────────────────────────────────────────────────────────────────┤
│ I. CATATAN PENDAMPING BRUDER MUDA (TPPBM)                       │
│    1. Hidup Pribadi     2. Hidup Rohani/Doa  3. Komunitas       │
│    4. Hidup Kerasulan   5. Hidup Triprasetia (Kaul)             │
├─────────────────────────────────────────────────────────────────┤
│ PENGESAHAN & TANDA TANGAN                                       │
│ Mengetahui Ketua TPPBM           Semarang, [Tgl Cetak]          │
│ (Br. F.A. Galih Sih Hartanta)    Administrator Data             │
│                                  (Br. Andrias Purwanto, FIC)    │
└─────────────────────────────────────────────────────────────────┘
```

* **Fitur Cetak & PDF**: Didukung stylesheet `@media print` sehingga saat tombol *"Cetak / Simpan PDF Raport"* ditekan, browser merender tata letak dokumen bersih format kertas A4 tanpa elemen navigasi aplikasi.

---

## 7. OTOMATISASI STATUS SELESAI & FILTER RENTANG WAKTU

1. **Otomatisasi Status (Scheduled → Completed)**:
   - Saat agenda didaftarkan, sistem mencatat status `Menunggu` (`scheduled`).
   - Begitu bruder mengunggah refleksi/catatan bimbingan rohani atau wawanhati, kontroler secara otomatis:
     1. Menautkan foreign key `schedule_id` ke agenda yang bersangkutan.
     2. Memperbarui status jadwal agenda menjadi **`Selesai` (`completed`)**.
     3. Mengubah badge pada rekapitulasi agenda menjadi warna hijau **`Selesai`**.

2. **Filter Rentang Waktu Fleksibel (1 s.d. 12 Bulan)**:
   - Panel monitoring dilengkapi kontrol dropdown: `DARI BULAN` [1–12], `SAMPAI BULAN` [1–12], dan `TAHUN`.
   - Mengakomodasi rekapitulasi semesteran (misal: Januari – Juni), periode tahun berjalan (Januari – Agustus), maupun rekapitulasi tahunan penuh (Januari – Desember).
   - Ekspor Excel dan PDF secara otomatis memuat data agregat sesuai rentang bulan yang dipilih.

---

## 8. INTEGRASI SISTEM & PETUNJUK PENGEMBANGAN
* **Pembersihan Cache Sistem**: Jalankan `php artisan optimize:clear` setiap melakukan perubahan konfigurasi route atau view Blade.
* **Testing Suite**: Jalankan `php artisan test` untuk memastikan 64+ skenario pengujian fitur lolos (assertions PASS).
* **Git Workflow**: Setiap penambahan fitur atau perbaikan antarmuka dikomit ke branch `main` dan dipush ke remote `origin/main`.
