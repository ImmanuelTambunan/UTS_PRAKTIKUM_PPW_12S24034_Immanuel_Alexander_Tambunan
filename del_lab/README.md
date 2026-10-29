# DEL-LEATS (DEL Lab Equipment & Asset Tracking System)
### UTS Praktikum Pemrograman & Pengujian Aplikasi Web (12S3101)

- **Nama**: Immanuel Alexander Tambunan
- **NIM**: 12S24034
- **Dosen Pengampu**: Chandro Pardede, S.Kom., M.Sc.

---

### Cara Menjalankan Aplikasi
1. Salin konfigurasi environment jika belum ada:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
2. Hubungkan link storage:
   ```bash
   php artisan storage:link
   ```
3. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
4. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```
   Akses di browser melalui `http://127.0.0.1:8000`.
