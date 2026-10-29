# UTS Praktikum Pemrograman & Pengujian Aplikasi Web (12S3101)
## DEL-LEATS (DEL Lab Equipment & Asset Tracking System)

Repositori ini berisi kode sumber pengerjaan Ujian Tengah Semester (UTS) Praktikum mata kuliah Pemrograman dan Pengujian Aplikasi Web (PPW), Institut Teknologi Del.

### Identitas Mahasiswa
- **Nama**: Immanuel Alexander Tambunan
- **NIM**: 12S24034
- **Dosen Pengampu**: Chandro Pardede, S.Kom., M.Sc.

---

### Deskripsi Proyek
DEL-LEATS adalah aplikasi web berbasis Laravel dengan arsitektur Model-View-Controller (MVC) yang digunakan untuk mengelola inventaris perangkat laboratorium dan transaksi pengajuan peminjaman aset oleh mahasiswa Institut Teknologi Del.

### Fitur yang Dikerjakan
1. **Katalog & Penjelajahan Aset (`/equipments`)**:
   - Menampilkan daftar perangkat laboratorium dalam kartu responsif (desktop 3 kolom, tablet 2 kolom, smartphone 1 kolom).
   - Paginasi Eloquent sebanyak 5 data per halaman dengan query string persisten (`withQueryString()`).
   - Pencarian kata kunci fleksibel pada nama atau kode aset.
   - Filter dropdown berdasarkan kategori dan status ketersediaan (tersedia, dipinjam, dalam perbaikan).
   - Indikator status badge visual dengan warna kontras (Hijau = Tersedia, Kuning = Dipinjam, Merah = Dalam Perbaikan).

2. **Pendaftaran Perangkat Baru (`/equipments/create`)**:
   - Formulir pendaftaran inventaris baru dengan proteksi `@csrf` dan `enctype="multipart/form-data"`.
   - Validasi server-side untuk kode aset unik, nama minimal 5 karakter, kategori, tarif harian, dan upload berkas foto (JPG/PNG/WEBP max 2MB).
   - Penyimpanan gambar ke storage publik dengan penamaan unik berbasis timestamp.
   - Retensi input formulir menggunakan helper `old()` dan pesan galat spesifik di bawah field.
   - Notifikasi flash session hijau setelah perangkat berhasil didaftarkan.

3. **Detail Spesifikasi Perangkat (`/equipments/{id}`)**:
   - Menampilkan foto fisik, spesifikasi teknis lengkap, kondisi fisik, tarif sewa harian, dan tombol aksi pengajuan pinjam.
   - Menampilkan tabel riwayat peminjaman untuk perangkat terkait.

4. **Pengajuan Peminjaman (`/loans/create`)**:
   - Validasi aturan bisnis: perangkat harus berstatus tersedia (ditolak jika sedang dipinjam atau dalam perbaikan).
   - Validasi format NIM mahasiswa tepat 8 digit angka/huruf institusi.
   - Validasi tanggal pinjam tidak boleh sebelum hari ini (`after_or_equal:today`).
   - Validasi durasi peminjaman maksimal 7 hari kalender.
   - Kalkulasi otomatis estimasi total biaya sewa (durasi hari x tarif harian).
   - Mutasi otomatis status perangkat menjadi `dipinjam` setelah permohonan diajukan.

5. **Riwayat Peminjaman (`/loans`)**:
   - Menampilkan tabel daftar seluruh transaksi pengajuan peminjaman inventaris laboratorium.

---

### Skema Basis Data & Relasi Model
1. **Tabel `categories`** (Model: `Category`):
   - Atribut: `id`, `name`, `code`, `created_at`, `updated_at`.
   - Relasi: `hasMany(Equipment::class)`.
2. **Tabel `equipments`** (Model: `Equipment`):
   - Atribut: `id`, `category_id`, `asset_code`, `name`, `specification`, `condition`, `status`, `daily_rental_fee`, `image_path`, `created_at`, `updated_at`.
   - Relasi: `belongsTo(Category::class)` dan `hasMany(LoanRequest::class)`.
3. **Tabel `loan_requests`** (Model: `LoanRequest`):
   - Atribut: `id`, `equipment_id`, `borrower_name`, `borrower_nim`, `borrower_email`, `loan_date`, `expected_return_date`, `purpose`, `status`, `total_estimated_fee`, `created_at`, `updated_at`.
   - Relasi: `belongsTo(Equipment::class)`.

---

### Cara Menjalankan Proyek
1. Buka terminal dan masuk ke direktori proyek:
   ```bash
   cd del_lab
   ```
2. Pastikan file `.env` sudah ada (atau salin dari `.env.example`):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Buat symlink storage untuk berkas foto:
   ```bash
   php artisan storage:link
   ```
4. Jalankan migrasi basis data dan seeder:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
6. Buka aplikasi di peramban (browser):
   ```
   http://127.0.0.1:8000
   ```

---

### Tautan Pengumpulan
- **Repository GitHub**: https://github.com/ImmanuelTambunan/UTS_PRAKTIKUM_PPW_12S24034_Immanuel_Alexander_Tambunan
- **Video Dokumentasi & Output (YouTube)**: *[Masukkan tautan video YouTube di sini]*
