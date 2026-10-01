# SCANIC TRACE

Sistem **Lost & Found** untuk warga sekolah (siswa, guru, staf, admin) —
lapor barang hilang/ditemukan, cari & filter laporan, ajukan klaim
kepemilikan, dan proses verifikasi + serah terima oleh admin.

Dibangun dengan **Laravel 12** (Blade + Tailwind CSS), **Supabase
(PostgreSQL)** sebagai database, dan **Eloquent ORM**.

---

## ⚠️ Catatan penting soal file ini

Project ini ditulis manual (tanpa menjalankan `composer create-project`)
karena environment yang membuatnya tidak punya akses ke Packagist/Composer.
Artinya:

- Folder `vendor/` **tidak disertakan** (memang tidak boleh, sesuai standar).
- Kode aplikasi (migration, model, controller, request, policy, view) sudah
  lengkap dan saling terhubung, tapi **belum pernah dijalankan/dites secara
  langsung** di server sungguhan. Ikuti langkah instalasi di bawah, lalu
  kabari kalau ada error saat `composer install` atau saat mengakses
  halaman — supaya bisa langsung diperbaiki.

---

## Struktur Folder

```
scanic-trace/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Auth, ItemReport, Claim, Admin/*
│   │   ├── Middleware/      # EnsureUserHasRole (cek role di route)
│   │   └── Requests/        # Validasi Form Request per aksi
│   ├── Models/               # User, ItemReport, Claim, Handover
│   ├── Policies/             # ItemReportPolicy, ClaimPolicy (anti-IDOR)
│   ├── Providers/            # AppServiceProvider (daftar Policy)
│   └── Services/             # ItemReportService, ClaimService (logika bisnis)
├── database/
│   ├── migrations/           # Struktur tabel (lihat bagian ERD di bawah)
│   ├── seeders/               # Data demo
│   └── factories/             # Factory untuk testing & seeding
├── resources/
│   ├── views/                # Blade views (layouts, components, items, claims, admin)
│   ├── css/app.css           # Entry Tailwind
│   └── js/app.js             # Entry JS (Vite)
├── routes/web.php            # Semua route aplikasi
└── tests/                    # Feature test (termasuk test anti-IDOR)
```

---

## ERD Ringkas & Relasi

- **users** (`id, name, email, password, role`)
  role: `student | teacher | staff | admin`

- **item_reports** (`id, user_id, type, title, description, category,
  location, incident_date, photo_path, status`)
  - `belongsTo User` (pelapor)
  - `type`: `lost | found`
  - `status`: `open | claimed | returned | closed`

- **claims** (`id, item_report_id, claimant_id, proof_details, status,
  reviewed_by, review_note, reviewed_at`)
  - `belongsTo ItemReport`
  - `belongsTo User` sebagai `claimant`
  - `belongsTo User` sebagai `reviewer` (admin)
  - `status`: `pending | approved | rejected | cancelled`
  - `proof_details` **privat** — hanya claimant sendiri & admin yang boleh
    melihatnya (lihat `ClaimPolicy@view`)

- **handovers** (`id, claim_id, handed_over_by, received_by, notes,
  handed_over_at`)
  - `belongsTo Claim` (1 klaim approved = maksimal 1 handover)
  - `handed_over_by` & `received_by` → `users.id`

Alur: **Laporan dibuat → User lain ajukan Claim → Admin approve/reject
Claim → Jika approved, admin catat Handover → Laporan jadi "returned".**

---

## Instalasi (Windows + XAMPP + Supabase)

1. **Prasyarat**
   - XAMPP dengan PHP ≥ 8.2 (MySQL bawaan XAMPP TIDAK dipakai lagi, tapi
     Apache/PHP-nya tetap dipakai)
   - Ekstensi PHP `pdo_pgsql` aktif — cek/edit `php.ini` di
     `C:\xamppNewVersion\php\php.ini`, cari baris `;extension=pdo_pgsql`
     dan `;extension=pgsql`, hapus tanda `;` di depannya, lalu restart
     terminal/Apache.
   - Akun [Supabase](https://supabase.com) + 1 project sudah dibuat
   - [Composer](https://getcomposer.org/) sudah terpasang
   - Node.js ≥ 18 (untuk build Tailwind lewat Vite)

2. **Ekstrak project**
   Ekstrak `scanic-trace.zip` langsung ke folder tujuan, misalnya
   `C:\xamppNewVersion\htdocs\scanic-trace` — pastikan hasil ekstrak TIDAK
   nested (composer.json harus langsung ada di root folder ini, bukan di
   dalam subfolder `scanic-trace` lagi).

3. **Install dependency PHP**
   ```bash
   cd scanic-trace
   composer install
   ```

4. **Ambil kredensial dari Supabase**
   Buka project Supabase lo → **Project Settings > Database > Connection
   info**. Pilih mode **Session pooler** (port `5432`), catat:
   - Host (contoh: `aws-0-ap-southeast-1.pooler.supabase.com`)
   - Database name (biasanya `postgres`)
   - User (format `postgres.xxxxxxxxxxxx`)
   - Password (password project Supabase lo, atau reset dulu kalau lupa)

5. **Konfigurasi environment**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   Buka `.env`, isi bagian database sesuai punya lo:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-xxxxxxx.pooler.supabase.com
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres.xxxxxxxxxxxx
   DB_PASSWORD=isi_password_supabase_lo
   DB_SSLMODE=require
   ```

6. **Jalankan migration + seeder (data demo)**
   ```bash
   php artisan migrate --seed
   ```
   Kalau berhasil, buka tab **Table Editor** di dashboard Supabase — tabel
   `users`, `item_reports`, `claims`, `handovers`, dll harus sudah muncul.

7. **Buat symlink storage** (supaya foto laporan bisa diakses lewat browser)
   ```bash
   php artisan storage:link
   ```

8. **Install & build asset frontend**
   ```bash
   npm install
   npm run build
   ```
   (Untuk development dengan hot-reload, pakai `npm run dev` di terminal
   terpisah selagi server Laravel jalan.)

9. **Jalankan server**
   ```bash
   php artisan serve
   ```
   Buka `http://127.0.0.1:8000` di browser.

> **Catatan:** foto yang diupload (`storage/app/public/item-reports`) tetap
> disimpan di disk lokal server, BUKAN di Supabase Storage — itu di luar
> cakupan brief awal. Kalau nanti mau foto ikut disimpan di Supabase
> Storage juga, itu perlu tambahan terpisah (ganti filesystem disk pakai
> package S3-compatible client, karena Supabase Storage kompatibel S3).

### Akun Demo (dari seeder)

| Role  | Email                        | Password |
|-------|-------------------------------|----------|
| Admin | admin@scanictrace.local       | password |
| Staf  | staff@scanictrace.local       | password |
| Siswa | budi@scanictrace.local        | password |
| Siswa | sari@scanictrace.local        | password |

---

## Deploy ke Vercel (Docker + FrankenPHP)

Project ini sudah dilengkapi `Dockerfile.vercel`, `Caddyfile`, dan
`vercel.json` mengikuti [panduan resmi Vercel untuk Laravel](https://vercel.com/kb/guide/laravel-php-with-docker)
(pakai FrankenPHP, dijalankan sebagai Container Function di Vercel — bukan
runtime PHP komunitas yang lama/tidak resmi).

### Yang perlu disiapkan dulu

- [Vercel CLI](https://vercel.com/docs/cli): `npm install -g vercel`
- Docker Desktop (atau Docker daemon lain) terpasang & jalan — dipakai
  Vercel buat build image, dan buat `vercel dev` kalau mau tes lokal
  sebelum deploy.
- Bucket baru di Supabase Storage (kalau mau fitur upload foto tetap
  jalan setelah di-deploy — lihat bagian "Storage" di bawah).

### Langkah deploy

1. **Login Vercel**: `vercel login`

2. **Generate APP_KEY** (kalau belum ada dari setup lokal):
   ```bash
   php artisan key:generate --show
   ```

3. **Set environment variables di Vercel** (lewat CLI atau dashboard
   Project Settings > Environment Variables):
   ```
   APP_KEY=base64:hasil_dari_langkah_2
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-project-lo.vercel.app

   DB_CONNECTION=pgsql
   DB_HOST=aws-0-xxxxxxx.pooler.supabase.com
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=postgres.xxxxxxxxxxxx
   DB_PASSWORD=isi_password_supabase_lo
   DB_SSLMODE=require

   CACHE_STORE=database
   SESSION_DRIVER=database
   QUEUE_CONNECTION=sync

   FILESYSTEM_DISK=supabase
   SUPABASE_S3_ACCESS_KEY_ID=...
   SUPABASE_S3_SECRET_ACCESS_KEY=...
   SUPABASE_S3_REGION=ap-southeast-1
   SUPABASE_S3_BUCKET=scanic-trace
   SUPABASE_S3_ENDPOINT=https://xxxxxxxxxxxx.supabase.co/storage/v1/s3
   SUPABASE_S3_PUBLIC_URL=https://xxxxxxxxxxxx.supabase.co/storage/v1/object/public/scanic-trace
   ```
   Contoh pakai CLI untuk satu variabel: `vercel env add APP_KEY`.

   > **Kenapa port `6543` (Transaction pooler), bukan `5432` (Session
   > pooler) seperti di setup lokal XAMPP?** Container di Vercel bisa
   > jalan sebagai banyak instance sekaligus saat traffic naik, masing-masing
   > buka koneksi database sendiri-sendiri. Transaction pooler dirancang
   > buat menampung banyak koneksi pendek seperti ini. Untuk lokal XAMPP
   > (cuma 1 proses `php artisan serve`), Session pooler (`5432`) tetap
   > lebih pas.

4. **Jalankan migration ke database production** (dari komputer lo,
   BUKAN dari dalam container — sesuai rekomendasi resmi Vercel supaya
   nggak ada 2 instance yang migrate bersamaan):
   ```bash
   php artisan migrate --force
   ```
   (pastikan `.env` lokal lo sementara nunjuk ke database Supabase yang
   sama dengan yang dipakai di Vercel saat menjalankan ini)

5. **Deploy**:
   ```bash
   vercel deploy --prod
   ```
   Atau hubungkan repo GitHub `Scanic-Found` ke Vercel dashboard supaya
   auto-deploy tiap `git push`.

### Batasan yang perlu lo tahu

- **Upload foto**: kalau `FILESYSTEM_DISK` nggak diset ke `supabase`,
  foto yang diupload akan hilang begitu container Vercel di-recycle
  (filesystem-nya nggak permanen). Wajib pakai disk `supabase` di atas
  kalau mau upload foto beneran persisten di production.
- **Antrian/queue**: `QUEUE_CONNECTION=sync` berarti job dijalankan
  langsung saat request, bukan di background — cukup untuk project ini
  karena belum ada fitur yang butuh antrian sungguhan (misalnya kirim
  email async). Kalau nanti butuh, perlu setup terpisah (Vercel Queues).
- **Cold start**: permintaan pertama setelah container "tidur" bisa
  terasa lebih lambat dari biasanya — ini karakteristik normal
  serverless/container function, bukan bug di kode.
- Ini bukan cara "resmi satu-satunya" buat hosting Laravel (Laravel
  sendiri biasanya di-hosting di VPS/Forge/Vapor), tapi karena lo
  memang mau pakai Vercel, ini pendekatan paling stabil & terbaru yang
  Vercel dukung untuk PHP/Laravel per saat ini.

---



```bash
php artisan test
```

Test mencakup: akses publik ke daftar laporan, larangan guest membuat
laporan, larangan user mengedit laporan orang lain (anti-IDOR), dan
larangan user non-admin mengakses endpoint verifikasi klaim.

---

## Fitur yang Sudah Diimplementasikan

- ✅ Autentikasi (register/login/logout) + role: student, teacher, staff, admin
- ✅ CRUD laporan Lost & Found + upload foto opsional
- ✅ Search (judul/deskripsi) + filter (jenis, kategori, lokasi) + pagination
- ✅ Sistem klaim: ajukan, lihat detail (privat), batalkan
- ✅ Admin: dashboard statistik, verifikasi klaim (approve/reject otomatis
  menolak klaim lain), catat serah terima, kelola laporan, kelola role user
- ✅ Keamanan: Form Request validation, CSRF (bawaan Blade `@csrf`), Policy
  anti-IDOR untuk laporan & klaim, middleware role untuk area admin,
  password di-hash otomatis (`casts(): password => hashed`)
- ✅ Database Supabase (PostgreSQL) — bukan MySQL/XAMPP lagi
- ✅ Siap deploy ke Vercel (Docker + FrankenPHP) dengan opsi upload foto
  ke Supabase Storage supaya persisten di production

## Yang Belum / Bisa Dikembangkan Lanjut

- Notifikasi email saat klaim diverifikasi (`MAIL_MAILER=log` untuk saat ini)
- Riwayat aktivitas admin dalam bentuk log terpisah (audit trail)
- Rate limiting khusus untuk pengajuan klaim
- Export laporan ke Excel/PDF

Kalau lo mau lanjut ke bagian mana dulu, kasih tahu aja — misalnya mau gw
tambahin fitur notifikasi email, atau perbaiki bagian tertentu setelah lo
coba jalankan di XAMPP.
