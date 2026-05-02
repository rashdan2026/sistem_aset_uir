# Analisa Teknis Sistem Informasi Aset Universitas Islam Riau (UIR)

Dokumen acuan untuk AI Agent Programmer, System Analyst, Solution Architect, Database Designer, UI/UX Analyst, dan Developer CodeIgniter 4.

Versi: 1.5
Tanggal penyusunan: 29 April 2026
Tanggal update: 02 May 2026 - Aturan hapus lantai dengan decrement jumlah_lantai dan transaction di LantaiController::delete()
Target implementasi: Web-based application menggunakan CodeIgniter 4, MySQL/InnoDB, Bootstrap modern
Database DEV: `uir_aset` pada `localhost`, user `root`, password kosong

## 1. Ringkasan Eksekutif

Sistem Informasi Aset Universitas Islam Riau adalah aplikasi web untuk mengelola data aset kampus dengan fokus utama pada kejelasan posisi aset, hubungan aset dengan unit kerja, hubungan lokasi fisik Gedung-Lantai-Ruangan, penanggung jawab, kondisi barang, serta kesiapan audit. Dokumen PRD/Blueprint dan SDD menegaskan bahwa sistem lama eInventaris sudah memiliki cakupan modul yang luas, tetapi model datanya masih mencampurkan organisasi dan lokasi fisik, sehingga pencarian posisi aset tidak optimal ketika satu unit kerja menempati banyak ruangan atau gedung.

Untuk tahap awal, sistem harus dipersempit agar implementasi aman dan tidak gagal karena cakupan terlalu besar. Fokus tahap awal adalah login multiuser, otorisasi bertingkat, input/output master data, relasi Unit/Fakultas-Sub Unit, relasi Gedung-Lantai-Ruangan, relasi Ruangan-Sub Unit-Penanggung Jawab, dan persiapan struktur aset non-bangunan/non-ruangan seperti kendaraan, elektronik, furniture, peralatan, dan mesin.

Keputusan desain paling penting adalah memisahkan domain organisasi dan domain lokasi fisik. `tbl_unit_kerja` tetap dipakai sebagai tabel referensi Unit/Fakultas/Unit Kerja yang bersifat read-only. `ylpi_karyawan` tetap dipakai sebagai tabel referensi Penanggung Jawab yang juga read-only. Tabel aplikasi lain dibuat di database `uir_aset` dengan foreign key dan index yang jelas, tetapi relasi ke dua tabel referensi tersebut perlu dirancang hati-hati karena pada production keduanya akan dipindah ke server berbeda.

AI Agent Programmer harus membangun aplikasi secara bertahap. Urutan paling aman adalah auth/login, struktur role-permission, master data lookup, Sub Unit, Gedung, Lantai, Ruangan, relasi penanggung jawab ruang, output/listing, lalu persiapan tabel aset non-bangunan. Modul transaksi besar seperti mutasi, opname, perawatan, penghapusan, dan laporan manajerial belum menjadi fokus coding awal, tetapi struktur database harus disiapkan agar tidak perlu redesign besar.

## 2. Ruang Lingkup Sistem Tahap Awal

### 2.1 Tujuan Sistem

Tujuan sistem tahap awal adalah menyediakan fondasi data aset yang rapi, tervalidasi, dan siap diperluas. Sistem harus mampu menjawab struktur dasar berikut: unit mana yang memiliki gedung, gedung memiliki lantai apa saja, lantai memiliki ruangan apa saja, ruangan dipakai oleh sub unit mana, siapa penanggung jawab ruangan, dan master referensi apa saja yang diperlukan untuk pencatatan aset non-bangunan.

Secara bisnis, aplikasi harus menjadi single source of truth untuk master data aset UIR. Secara teknis, aplikasi harus menjadi fondasi CodeIgniter 4 yang modular, aman, mudah dipelihara, dan tidak mencampurkan logika bisnis langsung di view.

### 2.2 Scope Masuk Tahap Awal

Scope tahap awal adalah:

| Area | Masuk Tahap Awal | Keterangan |
|---|---|---|
| Autentikasi | Ya | Login multiuser, session, password hash modern, logout, basic profile |
| Otorisasi | Ya | Role, permission, scope akses minimal |
| Unit/Fakultas | Read-only dari `tbl_unit_kerja` | Tidak boleh insert/update/delete dari aplikasi |
| Penanggung Jawab | Read-only dari `ylpi_karyawan` | Hanya kolom tertentu yang digunakan |
| Sub Unit | Ya | Tabel baru, minimal satu Sub Unit per Unit/Fakultas |
| Gedung | Ya | Tabel baru, satu Gedung dimiliki satu Unit/Fakultas |
| Lantai | Ya | Tabel baru, satu Gedung memiliki banyak Lantai |
| Ruangan | Ya | Tabel baru, satu Lantai memiliki banyak Ruangan; satu Ruangan dimiliki satu Sub Unit |
| Penanggung Jawab Ruangan | Ya | Satu Ruangan memiliki satu Penanggung Jawab utama |
| Kategori | Ya | Master lookup aset |
| Sub Kategori | Ya | Terkait ke Kategori |
| Golongan | Ya | Master lookup, khususnya untuk klasifikasi aset/bangunan |
| Merk | Ya | Master lookup untuk aset non-bangunan |
| Type | Ya | Master lookup, idealnya terkait Merk |
| Kondisi Barang | Ya | Master lookup kondisi aset |
| Sumber Dana | Disarankan masuk tahap awal | Dibutuhkan untuk aset non-bangunan sesuai aturan tambahan |
| Output/List Data | Ya | Listing, detail, filter, export sederhana CSV/PDF nanti |
| Aset non-bangunan | Persiapan struktur | Minimal desain tabel dan field, coding penuh boleh fase setelah master stabil |

### 2.3 Scope Tidak Masuk Tahap Awal

Modul berikut belum menjadi prioritas coding tahap awal:

| Modul | Status | Alasan |
|---|---|---|
| Mutasi aset penuh | Ditunda | Perlu approval workflow, histori lokasi, dan dokumen pendukung |
| Opname aset | Ditunda | Perlu QR/scan, daftar aset aktif, dan rekonsiliasi |
| Perawatan/perbaikan | Ditunda | Perlu transaksi biaya, vendor, status servis |
| Kehilangan/penghapusan | Ditunda | Perlu approval dan dokumen resmi |
| Dashboard manajerial kompleks | Ditunda | Data transaksi belum cukup pada tahap awal |
| Integrasi server production lintas database | Disiapkan desainnya | Saat DEV masih lokal, production akan dipisah |
| QR publik detail aset | Ditunda | Perlu kebijakan data umum yang boleh dilihat publik |
| Migrasi data aset lama | Ditunda | Perlu mapping, cleansing, dan validasi lapangan |

### 2.4 Asumsi Aman

Asumsi yang dipakai dalam desain ini adalah:

1. `tbl_unit_kerja.id_unit_kerja` adalah primary key atau unique identifier yang bisa dijadikan referensi logis.
2. `ylpi_karyawan.id_kpe` adalah primary key atau unique identifier untuk data penanggung jawab.
3. Kolom `ylpi_karyawan.unit_kerja` dapat direlasikan secara logis ke `tbl_unit_kerja.id_unit_kerja`, tetapi jika tidak cocok maka ditampilkan kosong.
4. Karena `tbl_unit_kerja` dan `ylpi_karyawan` kelak berada di server berbeda, aplikasi tidak boleh bergantung penuh pada foreign key fisik lintas database/server untuk dua tabel tersebut.
5. Sub Unit adalah entitas internal aplikasi aset, bukan mengganti `tbl_unit_kerja`.
6. Satu Ruangan hanya memiliki satu penanggung jawab utama untuk memudahkan kontrol jika terjadi kehilangan.
7. Nomor aset lama nanti disimpan sebagai referensi historis, sedangkan nomor aset baru dibuat otomatis.
8. QR viewer nantinya bersifat publik, tetapi hanya menampilkan data umum.
9. Mutasi wajib approval, kecuali skenario sementara/peminjaman yang kelak dapat dibuat modul terpisah.

## 3. Daftar Aktor dan Role

### 3.1 Aktor Bisnis

| Aktor | Peran Bisnis | Kebutuhan Utama |
|---|---|---|
| Pimpinan UIR | Sponsor dan pengambil keputusan | Melihat ringkasan aset, kondisi, dan distribusi |
| Biro Sarana/Prasarana | Owner operasional aset | Mengelola data aset dan validasi master |
| Fakultas/Rektorat/Unit | Pemilik/pengguna aset | Melihat dan mengelola data dalam scope unit |
| Sub Unit/Prodi/Biro/Bagian | Pengguna operasional | Mengetahui ruangan dan aset yang digunakan |
| Penanggung Jawab | Pihak bertanggung jawab atas ruang/aset | Tercatat pada ruangan atau aset |
| Petugas Opname | Verifikator lapangan | Nanti memeriksa aset berdasarkan lokasi |
| Auditor/SPI | Pengawas | Melihat histori, audit trail, dan konsistensi |
| Tim IT/Developer | Pengembang dan pemelihara sistem | Struktur teknis jelas dan aman |

### 3.2 Role Minimum Sistem

Role minimum yang disarankan:

| Role | Deskripsi | Scope Data |
|---|---|---|
| `super_admin` | Pengelola global sistem, role, permission, konfigurasi | Seluruh data |
| `admin_aset_pusat` | Pengelola master dan validasi data aset pusat | Seluruh unit |
| `admin_unit` | Pengelola data terbatas pada unit/fakultas tertentu | Unit yang diberikan |
| `operator_unit` | Input data master operasional sesuai unit | Unit dan sub unit tertentu |
| `viewer_pimpinan` | Melihat dashboard/listing/laporan tanpa ubah data | Sesuai jabatan/scope |
| `auditor` | Melihat audit trail, data historis, dan exception | Seluruh atau scope audit |

### 3.3 User Administrator Awal

Akun awal wajib tersedia:

```text
username: admin
password: myUIR2026
role: super_admin
```

Catatan penting: password tersebut hanya boleh digunakan sebagai seed awal DEV atau saat instalasi pertama. Saat aplikasi pertama kali login, sistem sebaiknya memaksa perubahan password. Password di database wajib disimpan menggunakan `password_hash()` PHP dengan algoritma `PASSWORD_DEFAULT`, bukan plain text dan bukan MD5.

## 4. Daftar Fitur / Modul

### 4.1 Modul Tahap Awal

| Prioritas | Modul | Fungsi Utama | Output |
|---|---|---|---|
| P0 | Auth/Login | Login, logout, session, password hash | User bisa masuk sesuai role |
| P0 | Role & Permission | Hak akses bertingkat | Menu dan aksi sesuai role |
| P0 | Referensi Unit Kerja | Baca `tbl_unit_kerja` | Dropdown/list unit |
| P0 | Referensi Penanggung Jawab | Baca `ylpi_karyawan` | Dropdown/list PJ |
| P0 | Master Sub Unit | CRUD sub unit internal aset | Sub unit valid per unit |
| P0 | Master Gedung | CRUD gedung | Gedung terkait Unit/Fakultas |
| P0 | Master Lantai | CRUD lantai | Lantai terkait Gedung |
| P0 | Master Ruangan | CRUD ruangan | Ruangan terkait Lantai, Sub Unit, PJ |
| P0 | Master Kategori | CRUD kategori | Lookup kategori aset |
| P0 | Master Sub Kategori | CRUD subkategori | Lookup subkategori aset |
| P0 | Master Kondisi | CRUD kondisi barang | Lookup kondisi |
| P1 | Master Golongan | CRUD golongan | Lookup golongan |
| P1 | Master Merk | CRUD merk | Lookup merk |
| P1 | Master Type | CRUD type | Lookup type terkait merk |
| P1 | Master Sumber Dana | CRUD sumber dana | Lookup sumber dana |
| P1 | Output/List Master | Filter, search, pagination | Data master mudah dicari |
| P1 | Persiapan Aset Non-Bangunan | Struktur tabel dasar aset | Siap fase registrasi aset |

### 4.2 Modul Lanjutan Setelah Tahap Awal

Modul lanjutan meliputi registrasi aset penuh, penempatan awal aset, histori lokasi aset, mutasi, serah terima, opname, perawatan, kehilangan, penghapusan, QR code publik, dashboard, audit analytics, dan integrasi production lintas sistem. Desain awal tidak boleh menutup kemungkinan modul-modul ini, tetapi coding tahap awal tidak perlu memaksa semua transaksi selesai sekaligus.

## 5. Aturan Bisnis

### 5.1 Aturan Organisasi dan Lokasi

1. `tbl_unit_kerja` adalah referensi Unit/Fakultas/Unit Kerja yang dibaca oleh aplikasi, bukan dikelola oleh aplikasi aset.
2. Satu Unit/Fakultas wajib memiliki minimal satu Sub Unit aktif untuk kebutuhan operasional aset.
3. Satu Unit/Fakultas dapat memiliki banyak Sub Unit.
4. Satu Gedung dimiliki oleh satu Unit/Fakultas.
5. Satu Gedung memiliki satu atau banyak Lantai.
6. Satu Lantai memiliki satu atau banyak Ruangan.
7. Satu Ruangan dimiliki oleh satu Sub Unit.
8. Satu Sub Unit dapat memiliki satu atau banyak Ruangan.
9. Satu Ruangan memiliki satu penanggung jawab utama dari `ylpi_karyawan`.
10. Jika data penanggung jawab tidak memiliki relasi unit kerja yang valid, kolom unit kerja pada tampilan dikosongkan, bukan dipaksa cocok.

### 5.2 Aturan Referensi Read-Only

1. Aplikasi tidak boleh menyediakan fitur insert, update, delete untuk `tbl_unit_kerja`.
2. Aplikasi tidak boleh menyediakan fitur insert, update, delete untuk `ylpi_karyawan`.
3. Semua model CodeIgniter 4 untuk dua tabel tersebut harus dibuat sebagai read-only model.
4. Controller tidak boleh memiliki method `create`, `store`, `edit`, `update`, atau `delete` untuk kedua tabel referensi tersebut.
5. Jika diperlukan sinkronisasi production, buat adapter/service read-only, bukan langsung menulis ke sumber data.

### 5.3 Aturan Master Data

1. Kode master baru harus unik pada ruang lingkup yang tepat.
2. Data master tidak disarankan dihapus fisik jika sudah pernah direferensikan; gunakan `is_active = 0` atau `deleted_at`.
3. Kategori dan Sub Kategori harus aktif agar bisa dipilih pada input aset.
4. Sub Kategori wajib memiliki Kategori.
5. Type idealnya terkait ke Merk, tetapi tetap boleh dibuat generik jika type belum diketahui.
6. Kondisi Barang minimal memuat Baik, Rusak Ringan, Rusak Berat, Tidak Ditemukan, dan Dihapus/Nonaktif jika diperlukan.
7. **Golongan wajib mengikuti Kategori. Kolom `kelompok` pada `aset_golongan` harus cocok dengan kolom `jenis_aset` pada `aset_kategori`.** Saat user memilih Kategori, hanya Golongan dengan kelompok yang sesuai yang ditampilkan. Backend memvalidasi konsistensi kombinasi kategori–golongan saat simpan aset.
8. **Aturan Hapus Lantai:** Saat menghapus lantai via `LantaiController::delete()`:
   - Gunakan soft delete (set `deleted_at` dan `is_active = 0`)
   - Decrement `aset_gedung.jumlah_lantai` sebanyak 1
   - Gunakan database transaction untuk atomicity
   - Check `jumlah_lantai > 0` sebelum decrement untuk cegah nilai negatif

### 5.4 Aturan Aset Non-Bangunan / Non-Ruangan

Untuk kendaraan, elektronik, furniture, peralatan, dan mesin, struktur data harus mendukung:

1. Aset wajib memiliki kategori dan sub kategori.
2. Aset wajib memiliki merk, type, kondisi, dan sumber dana jika masuk kelompok non-bangunan.
3. Aset boleh dimiliki Unit Kerja/Unit Fakultas tanpa Sub Unit.
4. Aset boleh dimiliki Unit Kerja dan Sub Unit.
5. Aset boleh memiliki penanggung jawab khusus per aset yang berbeda dari penanggung jawab ruangan.
6. Jika aset ditempatkan di ruangan, `ruangan_id` dapat diisi.
7. Jika aset tidak berbasis ruangan, misalnya kendaraan, `ruangan_id` boleh kosong tetapi lokasi deskriptif atau unit pemegang harus jelas.

### 5.5 Aturan Approval dan Mutasi Lanjutan

Mutasi aset penuh tidak menjadi modul awal, tetapi aturan desainnya perlu disiapkan:

1. Mutasi wajib melalui approval, kecuali peminjaman/sementara yang nanti didefinisikan sebagai modul berbeda.
2. Perubahan lokasi aktif aset tidak boleh dilakukan langsung melalui edit aset.
3. Semua perpindahan aset wajib menghasilkan histori lokasi.
4. QR publik hanya menampilkan data umum, bukan data sensitif seperti nilai perolehan, nomor HP, atau email penanggung jawab.

## 6. Analisa Database

### 6.1 Prinsip Desain Database

Database menggunakan MySQL/InnoDB dengan prinsip:

1. Tabel aplikasi menggunakan nama konsisten berbasis prefix domain: `sys_`, `ref_`, `aset_`, `trx_`.
2. `sys_` untuk user, role, permission, session/audit.
3. `ref_` untuk referensi eksternal/read-only wrapper atau lookup sederhana.
4. `aset_` untuk master data yang dikelola aplikasi.
5. `trx_` untuk transaksi/histori.
6. Primary key menggunakan `INT(6) UNSIGNED AUTO_INCREMENT` dengan nama domain-specific (misalnya `gd_id`, `kt_id`), kecuali `aset_all_uir.all_id` yang menggunakan `VARCHAR(15)`.
7. Kolom referensi eksternal ke `tbl_unit_kerja` dan `ylpi_karyawan` menggunakan tipe yang mengikuti tipe asli tabel sumber.
8. Foreign key fisik hanya diterapkan antar tabel aplikasi yang berada di database/server yang sama.
9. Untuk referensi yang kelak lintas server, gunakan index biasa dan validasi di service layer.
10. Semua tabel aplikasi memiliki `created_at`, `updated_at`, `created_by`, `updated_by`, dan opsional `deleted_at`.

### 6.2 Tabel Existing Read-Only di Database `uir_aset`

#### 6.2.1 `tbl_unit_kerja`

Fungsi: referensi Unit Kerja/Unit/Fakultas dari sistem kampus.

Status aplikasi: read-only.

Kolom yang dibaca aplikasi:

| Kolom | Fungsi di Aplikasi |
|---|---|
| `id_unit_kerja` | ID referensi Unit/Fakultas/Unit Kerja |
| `nama_unit` | Nama Unit/Fakultas/Unit Kerja yang ditampilkan pada UI |
| `flag_aktif` | Penanda status aktif; hanya data dengan `flag_aktif = 1` yang ditampilkan di UI |

Aturan UI: seluruh dropdown, listing, pencarian, dan pilihan relasi Unit/Fakultas hanya menampilkan `nama_unit` dari record `tbl_unit_kerja` dengan `flag_aktif = 1`. Record dengan `flag_aktif != 1` tidak ditampilkan sebagai pilihan baru, tetapi jika sudah pernah direferensikan oleh data historis, sistem dapat menampilkannya sebagai teks read-only dengan label nonaktif.

#### 6.2.2 `ylpi_karyawan`

Fungsi: referensi Penanggung Jawab.

Status aplikasi: read-only.

Kolom yang dipakai:

| Kolom | Fungsi |
|---|---|
| `id_kpe` | ID penanggung jawab |
| `npk` | Nomor pegawai/karyawan |
| `nama_gelar` | Nama lengkap dengan gelar |
| `jenkel` | Jenis kelamin |
| `kategori` | Kategori pegawai/karyawan |
| `no_hp1` | Nomor HP, sebaiknya hanya tampil untuk role tertentu |
| `no_wa` | Nomor WhatsApp, sebaiknya hanya tampil untuk role tertentu |
| `email` | Email |
| `unit_kerja` | Relasi logis ke `tbl_unit_kerja.id_unit_kerja` |
| `flag_karyawan` | Penanda karyawan aktif; hanya data dengan `flag_karyawan = 1` yang ditampilkan di UI |

Aturan UI: seluruh listing, dropdown, pencarian, dan pilihan Penanggung Jawab hanya menampilkan data `ylpi_karyawan` dengan `flag_karyawan = 1`. Data dengan `flag_karyawan = 0` tidak boleh tampil sebagai pilihan baru di UI. Jika `ylpi_karyawan.unit_kerja` tidak cocok dengan `tbl_unit_kerja.id_unit_kerja`, aplikasi tetap menampilkan data pegawai aktif tetapi unit kerjanya kosong.

### 6.3 Tabel Baru yang Perlu Dibuat

#### 6.3.1 Tabel Sistem dan Auth

| Tabel | Fungsi |
|---|---|
| `sys_users` | Akun login aplikasi |
| `sys_roles` | Daftar role |
| `sys_permissions` | Daftar permission |
| `sys_user_roles` | Relasi user-role |
| `sys_role_permissions` | Relasi role-permission |
| `sys_user_unit_scopes` | Batas akses user ke unit kerja |
| `sys_login_attempts` | Proteksi brute force login |
| `sys_audit_logs` | Audit trail perubahan penting |

#### 6.3.2 Tabel Master Lokasi dan Organisasi Internal

| Tabel | Fungsi |
|---|---|
| `aset_sub_units` | Sub Unit kerja internal aplikasi aset |
| `aset_gedung` | Master gedung |
| `aset_lantai` | Master lantai per gedung |
| `aset_ruangan` | Master ruangan per lantai |

#### 6.3.3 Tabel Master Klasifikasi Aset

| Tabel | Fungsi |
|---|---|
| `aset_kategori` | Master kategori aset |
| `aset_sub_kategori` | Master sub kategori aset |
| `aset_golongan` | Master golongan aset/bangunan |
| `aset_merk` | Master merk |
| `aset_type` | Master type/model |
| `aset_kondisi_barang` | Master kondisi barang |
| `aset_sumber_dana` | Master sumber dana |

#### 6.3.4 Tabel Persiapan Aset Non-Bangunan

| Tabel | Fungsi |
|---|---|
| `aset_all_uir` | Tabel inti aset untuk fase registrasi aset berikutnya |
| `trx_aset_penanggung_jawab` | Histori/PJ aset jika diperlukan |
| `trx_histori_lokasi_aset` | Disiapkan untuk histori lokasi |

Untuk tahap awal, `aset_all_uir` boleh dibuat tetapi form registrasi penuh dapat ditunda. Jika dibuat, gunakan status `draft` atau batasi input untuk aset non-bangunan dasar.

### 6.4 Detail Rancangan Tabel

#### 6.4.1 `sys_users`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID user |
| `username` | VARCHAR(50) | UNIQUE | Username login |
| `password_hash` | VARCHAR(255) |  | Hash password |
| `full_name` | VARCHAR(150) |  | Nama user |
| `email` | VARCHAR(150) | INDEX | Email |
| `id_kpe` | INT(6) UNSIGNED NULL | INDEX | Relasi logis ke `ylpi_karyawan.id_kpe` |
| `default_unit_kerja_id` | INT(6) UNSIGNED NULL | INDEX | Relasi logis ke `tbl_unit_kerja.id_unit_kerja` |
| `is_active` | TINYINT(2) | INDEX | Status aktif |
| `force_password_change` | TINYINT(2) |  | Paksa ganti password |
| `last_login_at` | DATETIME NULL |  | Login terakhir |
| `created_at` | DATETIME |  | Waktu buat |
| `updated_at` | DATETIME |  | Waktu ubah |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Seed awal:

```text
username = admin
password_hash = password_hash('myUIR2026', PASSWORD_DEFAULT)
role = super_admin
force_password_change = 1
```

#### 6.4.2 `sys_roles`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID role |
| `role_code` | VARCHAR(50) | UNIQUE | Contoh `super_admin` |
| `role_name` | VARCHAR(100) |  | Nama role |
| `description` | TEXT NULL |  | Deskripsi |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.3 `sys_permissions`

Permission dibuat granular dengan pola `module.action`, misalnya `gedung.view`, `gedung.create`, `gedung.update`, `gedung.delete`, `gedung.export`.

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID permission |
| `permission_code` | VARCHAR(100) | UNIQUE | Kode permission |
| `module_code` | VARCHAR(50) | INDEX | Modul |
| `action_code` | VARCHAR(50) | INDEX | Aksi |
| `description` | VARCHAR(255) NULL |  | Deskripsi |

#### 6.4.4 `sys_user_unit_scopes`

Tabel ini membatasi user hanya pada unit tertentu.

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID |
| `user_id` | INT(6) UNSIGNED | FK | Ke `sys_users.id` |
| `unit_kerja_id` | INT(6) UNSIGNED | INDEX | Relasi logis ke `tbl_unit_kerja.id_unit_kerja` |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.4a `sys_user_roles`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID |
| `user_id` | INT(6) UNSIGNED | INDEX | Ke `sys_users.id` |
| `role_id` | INT(6) UNSIGNED | INDEX | Ke `sys_roles.id` |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.4b `sys_role_permissions`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID |
| `role_id` | INT(6) UNSIGNED | INDEX | Ke `sys_roles.id` |
| `permission_id` | INT(6) UNSIGNED | INDEX | Ke `sys_permissions.id` |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.4c `sys_login_attempts`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID |
| `user_id` | INT(6) UNSIGNED NULL | INDEX | Ke `sys_users.id`, NULL jika user tidak ditemukan |
| `ip_address` | VARCHAR(45) |  | Alamat IP |
| `attempt_time` | DATETIME |  | Waktu percobaan |
| `is_success` | TINYINT(2) |  | Berhasil atau gagal |

#### 6.4.4d `sys_audit_logs`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `id` | INT(6) UNSIGNED | PK | ID |
| `user_id` | INT(6) UNSIGNED | INDEX | Ke `sys_users.id` |
| `action` | VARCHAR(50) |  | Jenis aksi |
| `description` | TEXT NULL |  | Deskripsi perubahan |
| `ip_address` | VARCHAR(45) |  | Alamat IP |
| `created_at` | DATETIME |  | Waktu |

#### 6.4.5 `aset_sub_units`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `su_id` | INT(6) UNSIGNED | PK | ID sub unit |
| `unit_kerja_id` | INT(6) UNSIGNED | INDEX | Relasi logis ke `tbl_unit_kerja.id_unit_kerja` |
| `kode_sub_unit` | VARCHAR(30) | INDEX | Kode sub unit |
| `nama_sub_unit` | VARCHAR(150) | INDEX | Nama sub unit |
| `jenis_sub_unit` | VARCHAR(50) NULL | INDEX | Prodi, biro, laboratorium, bagian |
| `keterangan` | TEXT NULL |  | Catatan |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Unique key: `uk_sub_unit_per_unit (unit_kerja_id, kode_sub_unit)`.

Aturan: setiap `unit_kerja_id` minimal memiliki satu sub unit aktif. Jika belum ada struktur rinci, buat sub unit default dengan nama sama seperti unit, misalnya `Sub Unit Utama Fakultas Teknik`.

#### 6.4.6 `aset_gedung`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `gd_id` | INT(6) UNSIGNED | PK | ID gedung |
| `unit_kerja_id` | INT(6) UNSIGNED | INDEX | Pemilik gedung, relasi logis ke `tbl_unit_kerja` |
| `kode_gedung` | VARCHAR(30) | UNIQUE | Kode gedung |
| `nama_gedung` | VARCHAR(150) | INDEX | Nama gedung |
| `alamat_ringkas` | VARCHAR(255) NULL |  | Lokasi ringkas |
| `jumlah_lantai` | INT(10) UNSIGNED NOT NULL DEFAULT 1 |  | Jumlah lantai, otomatis membuat record di `aset_lantai` saat input gedung baru |
| `keterangan` | TEXT NULL |  | Catatan |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Aturan: satu Gedung dimiliki oleh satu Unit/Fakultas. Jika gedung dipakai bersama, pemilik administratif tetap satu, sedangkan pemakaian ruangan diatur melalui Sub Unit dan Ruangan.

**Auto-create Lantai:** Saat membuat Gedung baru, kolom `jumlah_lantai` wajib diisi (minimal 1). Sistem otomatis membuat sebanyak N record di `aset_lantai` dengan format:
- `kode_lantai` = `LT_{gd_id}_{nomor}` (contoh: `LT_1_1`, `LT_1_2`, dst)
- `nama_lantai` = `Lantai {nomor}` (contoh: `Lantai 1`, `Lantai 2`, dst)
- `nomor_lantai` = nomor urut (1, 2, 3, dst)
- `gedung_id` = ID gedung yang baru disimpan
- `is_active` = 1

Kolom `jumlah_lantai` tidak dapat diubah setelah gedung dibuat (readonly pada form edit). Jika perlu menambah/mengurangi lantai, gunakan menu Master Lantai untuk pengelolaan manual.

#### 6.4.7 `aset_lantai`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `lt_id` | INT(6) UNSIGNED | PK | ID lantai |
| `gedung_id` | INT(6) UNSIGNED | FK | Ke `aset_gedung.gd_id` |
| `kode_lantai` | VARCHAR(30) | INDEX | Format `LT_{gedung_id}_{nomor}`, contoh `LT_1_1`, `LT_1_2` |
| `nama_lantai` | VARCHAR(100) |  | Contoh `Lantai 1` |
| `nomor_lantai` | INT(11) | INDEX | Urutan lantai |
| `keterangan` | TEXT NULL |  | Catatan |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Foreign key: `gedung_id` references `aset_gedung(gd_id)` on update cascade, on delete restrict.  
Unique key: `uk_lantai_per_gedung (gedung_id, kode_lantai)`.

Catatan: Lantai dapat terbuat secara otomatis saat input Gedung baru (berdasarkan kolom `jumlah_lantai`), atau ditambahkan/edit secara manual melalui menu Master Lantai.

#### 6.4.8 `aset_ruangan`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `rg_id` | INT(6) UNSIGNED | PK | ID ruangan |
| `lantai_id` | INT(6) UNSIGNED | FK | Ke `aset_lantai.lt_id` |
| `sub_unit_id` | INT(6) UNSIGNED | FK | Ke `aset_sub_units.su_id` |
| `kode_ruangan` | VARCHAR(30) | INDEX | Kode ruang |
| `nama_ruangan` | VARCHAR(150) | INDEX | Nama ruang |
| `jenis_ruangan` | VARCHAR(50) NULL | INDEX | Kelas, lab, kantor, gudang |
| `penanggung_jawab_id_kpe` | VARCHAR(16) NULL | INDEX | Relasi logis ke `ylpi_karyawan.id_kpe` |
| `kapasitas` | INT(10) UNSIGNED NULL |  | Opsional |
| `luas_m2` | DECIMAL(10,2) NULL |  | Opsional |
| `keterangan` | TEXT NULL |  | Catatan |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Foreign key:

```text
lantai_id -> aset_lantai.lt_id
sub_unit_id -> aset_sub_units.su_id
```

Relasi ke `ylpi_karyawan.id_kpe` cukup index logis, bukan FK fisik, agar aman saat production berbeda server.

Unique key: `uk_ruangan_per_lantai (lantai_id, kode_ruangan)`.  
Index: `idx_ruangan_sub_unit (sub_unit_id)`, `idx_ruangan_pj (penanggung_jawab_id_kpe)`.

#### 6.4.9 `aset_kategori`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `kt_id` | INT(6) UNSIGNED | PK | ID kategori |
| `kode_kategori` | VARCHAR(30) | UNIQUE | Kode |
| `nama_kategori` | VARCHAR(150) | UNIQUE | Nama |
| `jenis_aset` | ENUM('bangunan','ruangan','non_bangunan','lainnya') | INDEX | Klasifikasi utama |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Contoh kategori: Tanah, Bangunan & Gedung, Kendaraan, Peralatan & Mesin, Peralatan Elektronik & IT, Furniture & Inventaris, Peralatan Laboratorium, Infrastruktur & Utilitas, Software & Lisensi.

#### 6.4.10 `aset_sub_kategori`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `sk_id` | INT(6) UNSIGNED | PK | ID sub kategori |
| `kategori_id` | INT(6) UNSIGNED | FK | Ke `aset_kategori.kt_id` |
| `kode_sub_kategori` | VARCHAR(30) | INDEX | Kode |
| `nama_sub_kategori` | VARCHAR(150) | INDEX | Nama |
| `wajib_merk` | TINYINT(2) |  | Flag validasi aset, default 0 |
| `wajib_type` | TINYINT(2) |  | Flag validasi aset, default 0 |
| `wajib_ruangan` | TINYINT(2) |  | Kendaraan bisa 0, default 0 |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Unique key: `uk_sub_kategori_per_kategori (kategori_id, kode_sub_kategori)`.

#### 6.4.11 `aset_golongan`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `gl_id` | INT(6) UNSIGNED | PK | ID golongan |
| `kode_golongan` | VARCHAR(30) | UNIQUE | Kode |
| `nama_golongan` | VARCHAR(150) | UNIQUE | Nama |
| `kelompok` | VARCHAR(50) NULL | INDEX | Bangunan/non-bangunan/lainnya |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.12 `aset_merk`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `mr_id` | INT(6) UNSIGNED | PK | ID merk |
| `kode_merk` | VARCHAR(30) | UNIQUE | Kode |
| `nama_merk` | VARCHAR(100) | UNIQUE | Nama |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

#### 6.4.13 `aset_type`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `ty_id` | INT(6) UNSIGNED | PK | ID type |
| `merk_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_merk.mr_id` |
| `kode_type` | VARCHAR(30) | INDEX | Kode |
| `nama_type` | VARCHAR(100) | INDEX | Nama type/model |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

Unique key: `uk_type_per_merk (merk_id, kode_type)`.

#### 6.4.14 `aset_kondisi_barang`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `kd_id` | INT(6) UNSIGNED | PK | ID kondisi |
| `kode_kondisi` | VARCHAR(30) | UNIQUE | Kode |
| `nama_kondisi` | VARCHAR(100) | UNIQUE | Nama |
| `level_kondisi` | INT(10) UNSIGNED | INDEX | Urutan kualitas, default 1 |
| `is_available_for_use` | TINYINT(2) | INDEX | Bisa digunakan, default 1 |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

Seed awal disarankan: Baik, Rusak Ringan, Rusak Berat, Tidak Ditemukan, Dalam Perbaikan, Dihapus/Nonaktif.

#### 6.4.15 `aset_sumber_dana`

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `sd_id` | INT(6) UNSIGNED | PK | ID sumber dana |
| `kode_sumber_dana` | VARCHAR(30) | UNIQUE | Kode |
| `nama_sumber_dana` | VARCHAR(150) | UNIQUE | Nama |
| `keterangan` | TEXT NULL |  |  |
| `is_active` | TINYINT(2) | INDEX | Status |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |

Seed awal: Anggaran UIR, Hibah, Bantuan Pemerintah, CSR, Dana Fakultas/Unit, Lainnya.

#### 6.4.16 `aset_all_uir` untuk Persiapan Fase Berikutnya

| Kolom | Tipe | Key | Keterangan |
|---|---:|---|---|
| `all_id` | VARCHAR(15) | PK | ID aset, format `all_xxx-yyyyyyy` |
| `nomor_aset_baru` | VARCHAR(80) | UNIQUE | Auto generate |
| `nomor_aset_lama` | VARCHAR(80) NULL | INDEX | Nomor lama disimpan saja |
| `nama_aset` | VARCHAR(200) | INDEX | Nama aset |
| `kategori_id` | INT(6) UNSIGNED | FK | Ke `aset_kategori.kt_id` |
| `sub_kategori_id` | INT(6) UNSIGNED | FK | Ke `aset_sub_kategori.sk_id` |
| `golongan_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_golongan.gl_id` |
| `merk_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_merk.mr_id` |
| `type_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_type.ty_id` |
| `kondisi_id` | INT(6) UNSIGNED | FK | Ke `aset_kondisi_barang.kd_id` |
| `sumber_dana_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_sumber_dana.sd_id` |
| `unit_kerja_id` | INT(6) UNSIGNED | INDEX | Relasi logis ke `tbl_unit_kerja` |
| `sub_unit_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_sub_units.su_id` |
| `ruangan_id` | INT(6) UNSIGNED NULL | FK | Ke `aset_ruangan.rg_id` |
| `penanggung_jawab_id_kpe` | VARCHAR(16) NULL | INDEX | Relasi logis ke `ylpi_karyawan` |
| `serial_number` | VARCHAR(100) NULL | INDEX | Nomor seri |
| `spesifikasi` | TEXT NULL |  | Spesifikasi |
| `tahun_perolehan` | YEAR(4) NULL | INDEX | Tahun |
| `tanggal_perolehan` | DATE NULL |  | Tanggal |
| `nilai_perolehan` | DECIMAL(18,2) NULL |  | Nilai |
| `status_aset` | ENUM('draft','aktif','nonaktif','hilang','dihapus') | INDEX | Status |
| `is_active` | TINYINT(1) | INDEX | Status aktif |
| `created_at` | DATETIME |  |  |
| `updated_at` | DATETIME |  |  |
| `deleted_at` | DATETIME NULL | INDEX | Soft delete |

Aturan validasi:

1. `unit_kerja_id` wajib.
2. `sub_unit_id` opsional karena aset boleh dimiliki Unit Kerja tanpa Sub Unit.
3. `ruangan_id` opsional untuk aset non-ruangan seperti kendaraan.
4. `penanggung_jawab_id_kpe` opsional tetapi sangat disarankan untuk aset bernilai tinggi.
5. `merk_id`, `type_id`, `kondisi_id`, dan `sumber_dana_id` wajib untuk kategori non-bangunan sesuai aturan tambahan.

### 6.5 Relasi Utama

Relasi teks inti:

```text
tbl_unit_kerja (read-only)
  1 ──< aset_sub_units
  1 ──< aset_gedung
  1 ──< aset_all_uir

aset_gedung
  1 ──< aset_lantai

aset_lantai
  1 ──< aset_ruangan

aset_sub_units
  1 ──< aset_ruangan
  1 ──< aset_all_uir

ylpi_karyawan (read-only)
  1 ──< aset_ruangan.penanggung_jawab_id_kpe
  1 ──< aset_all_uir.penanggung_jawab_id_kpe

aset_kategori
  1 ──< aset_sub_kategori
  1 ──< aset_all_uir

aset_sub_kategori
  1 ──< aset_all_uir

aset_merk
  1 ──< aset_type
  1 ──< aset_all_uir

aset_type
  1 ──< aset_all_uir

aset_kondisi_barang
  1 ──< aset_all_uir

aset_sumber_dana
  1 ──< aset_all_uir

aset_golongan
  1 ──< aset_all_uir
```

### 6.6 ERD dalam Bentuk Teks

```text
[tbl_unit_kerja]  read-only
    id_unit_kerja PK
    ...

[ylpi_karyawan]  read-only
    id_kpe PK
    unit_kerja -> tbl_unit_kerja.id_unit_kerja (logis)
    ...

[aset_sub_units]
    su_id PK
    unit_kerja_id -> tbl_unit_kerja.id_unit_kerja (logis)

[aset_gedung]
    gd_id PK
    unit_kerja_id -> tbl_unit_kerja.id_unit_kerja (logis)

[aset_lantai]
    lt_id PK
    gedung_id FK -> aset_gedung.gd_id

[aset_ruangan]
    rg_id PK
    lantai_id FK -> aset_lantai.lt_id
    sub_unit_id FK -> aset_sub_units.su_id
    penanggung_jawab_id_kpe -> ylpi_karyawan.id_kpe (logis, VARCHAR(16))

[aset_kategori]
    kt_id PK

[aset_sub_kategori]
    sk_id PK
    kategori_id FK -> aset_kategori.kt_id

[aset_merk]
    mr_id PK

[aset_type]
    ty_id PK
    merk_id FK -> aset_merk.mr_id

[aset_kondisi_barang]
    kd_id PK

[aset_sumber_dana]
    sd_id PK

[aset_golongan]
    gl_id PK

[aset_all_uir]
    all_id PK (VARCHAR(15))
    kategori_id FK -> aset_kategori.kt_id
    sub_kategori_id FK -> aset_sub_kategori.sk_id
    merk_id FK -> aset_merk.mr_id
    type_id FK -> aset_type.ty_id
    kondisi_id FK -> aset_kondisi_barang.kd_id
    sumber_dana_id FK -> aset_sumber_dana.sd_id
    golongan_id FK -> aset_golongan.gl_id
    unit_kerja_id -> tbl_unit_kerja.id_unit_kerja (logis)
    sub_unit_id FK -> aset_sub_units.su_id
    ruangan_id FK -> aset_ruangan.rg_id
    penanggung_jawab_id_kpe -> ylpi_karyawan.id_kpe (logis, VARCHAR(16))
```

### 6.7 View yang Disarankan

#### `vw_penanggung_jawab`

View ini mengambil data `ylpi_karyawan` dan menampilkan nama unit kerja jika relasi cocok.

```sql
CREATE OR REPLACE VIEW vw_penanggung_jawab AS
SELECT
    k.id_kpe,
    k.npk,
    k.nama_gelar,
    k.jenkel,
    k.kategori,
    k.no_hp1,
    k.no_wa,
    k.email,
    k.unit_kerja AS unit_kerja_id,
    u.nama_unit
FROM ylpi_karyawan k
LEFT JOIN tbl_unit_kerja u
    ON u.id_unit_kerja = k.unit_kerja
   AND u.flag_aktif = 1
WHERE k.flag_karyawan = 1;
```

Catatan: Penanggung Jawab hanya tampil bila `ylpi_karyawan.flag_karyawan = 1`. `nama_unit` hanya ditampilkan bila `tbl_unit_kerja.flag_aktif = 1`. Jika relasi pegawai mengarah ke unit yang tidak aktif atau tidak cocok, nama unit ditampilkan kosong.

#### `vw_ruangan_lengkap`

View ini memudahkan listing ruangan.

```sql
CREATE OR REPLACE VIEW vw_ruangan_lengkap AS
SELECT
    r.rg_id AS ruangan_id,
    r.kode_ruangan,
    r.nama_ruangan,
    r.jenis_ruangan,
    r.is_active,
    l.lt_id AS lantai_id,
    l.nama_lantai,
    l.nomor_lantai,
    g.gd_id AS gedung_id,
    g.nama_gedung,
    g.unit_kerja_id AS unit_pemilik_gedung_id,
    su.su_id AS sub_unit_id,
    su.nama_sub_unit,
    r.penanggung_jawab_id_kpe
FROM aset_ruangan r
JOIN aset_lantai l ON l.lt_id = r.lantai_id
JOIN aset_gedung g ON g.gd_id = l.gedung_id
JOIN aset_sub_units su ON su.su_id = r.sub_unit_id
WHERE r.deleted_at IS NULL;
```

### 6.8 Normalisasi

Desain minimal memenuhi 3NF:

1. Kategori dan sub kategori dipisah agar tidak terjadi duplikasi nama kategori di setiap aset.
2. Merk dan type dipisah agar type dapat difilter berdasarkan merk.
3. Gedung, Lantai, Ruangan dipisah agar lokasi fisik tidak disimpan sebagai teks bebas.
4. Sub Unit dipisah dari Unit Kerja karena Unit Kerja read-only dan tidak boleh diubah oleh aplikasi aset.
5. Kondisi dan Sumber Dana dibuat lookup agar konsisten dalam pelaporan.
6. Penanggung jawab tidak disalin ke tabel ruangan/aset selain ID; nama, email, dan kontak diambil dari view referensi.

### 6.9 Strategi Foreign Key

Gunakan FK fisik untuk relasi internal:

```text
aset_lantai.gedung_id -> aset_gedung.gd_id
aset_ruangan.lantai_id -> aset_lantai.lt_id
aset_ruangan.sub_unit_id -> aset_sub_units.su_id
aset_sub_kategori.kategori_id -> aset_kategori.kt_id
aset_type.merk_id -> aset_merk.mr_id
aset_all_uir.* internal lookup -> tabel aset_ terkait
```

Jangan gunakan FK fisik untuk relasi yang kelak lintas server:

```text
aset_sub_units.unit_kerja_id -> tbl_unit_kerja.id_unit_kerja
aset_gedung.unit_kerja_id -> tbl_unit_kerja.id_unit_kerja
aset_ruangan.penanggung_jawab_id_kpe -> ylpi_karyawan.id_kpe
aset_all_uir.unit_kerja_id -> tbl_unit_kerja.id_unit_kerja
aset_all_uir.penanggung_jawab_id_kpe -> ylpi_karyawan.id_kpe
```

Untuk relasi logis tersebut, buat index dan validasi melalui service layer.

## 7. Analisa CodeIgniter 4

### 7.1 Struktur Folder yang Disarankan

Gunakan struktur modular tetapi tetap sesuai CodeIgniter 4 standar:

```text
app/
  Config/
    Routes.php
    Filters.php
    Database.php
  Controllers/
    Auth/
      LoginController.php
      PasswordController.php
    DashboardController.php
    Master/
      UnitKerjaController.php
      PenanggungJawabController.php
      SubUnitController.php
      GedungController.php
      LantaiController.php
      RuanganController.php
      KategoriController.php
      SubKategoriController.php
      GolonganController.php
      MerkController.php
      TypeController.php
      KondisiBarangController.php
      SumberDanaController.php
    System/
      UserController.php
      RoleController.php
      PermissionController.php
  Models/
    System/
      UserModel.php
      RoleModel.php
      PermissionModel.php
      UserRoleModel.php
      RolePermissionModel.php
      UserUnitScopeModel.php
      AuditLogModel.php
    Reference/
      UnitKerjaReadOnlyModel.php
      PenanggungJawabReadOnlyModel.php
    Master/
      SubUnitModel.php
      GedungModel.php
      LantaiModel.php
      RuanganModel.php
      KategoriModel.php
      SubKategoriModel.php
      GolonganModel.php
      MerkModel.php
      TypeModel.php
      KondisiBarangModel.php
      SumberDanaModel.php
      AsetModel.php
  Libraries/
    AuthService.php
    AuthorizationService.php
    AuditService.php
    ReferenceDataService.php
    MasterDataService.php
    AssetNumberService.php
  Filters/
    AuthFilter.php
    RolePermissionFilter.php
    GuestFilter.php
  Helpers/
    auth_helper.php
    format_helper.php
    menu_helper.php
  Views/
    layouts/
      main.php
      auth.php
      partials/
        sidebar.php
        topbar.php
        breadcrumb.php
        flash_message.php
    auth/
      login.php
      change_password.php
    dashboard/
      index.php
    master/
      sub_units/
      gedung/
      lantai/
      ruangan/
      kategori/
      sub_kategori/
      golongan/
      merk/
      type/
      kondisi_barang/
      sumber_dana/
      unit_kerja/
      penanggung_jawab/
    system/
      users/
      roles/
      permissions/
```

### 7.2 Model yang Diperlukan

> ⚠️ **Peringatan: Namespace model tidak konsisten di codebase.** Beberapa model ada di `App\Models\` root, beberapa di `App\Models\Master\`, dan beberapa di `App\Models\Reference\`. Lihat kolom Namespace pada tabel di bawah.

| Model | Namespace | Tabel/View | PK | SoftDeletes | Timestamps | Catatan |
|---|---|---|---|---|---|---|
| `UserModel` | `App\Models` | `sys_users` | `id` | ✅ true | false | Login, `getUserRole()` |
| `RoleModel` | `App\Models` | `sys_roles` | `id` | ✅ true | false | |
| `PermissionModel` | — (belum ada) | `sys_permissions` | — | — | — | Stub |
| `UserRoleModel` | — (belum ada) | `sys_user_roles` | — | — | — | Stub |
| `RolePermissionModel` | — (belum ada) | `sys_role_permissions` | — | — | — | Stub |
| `UserUnitScopeModel` | — (belum ada) | `sys_user_unit_scopes` | — | — | — | Stub |
| `AuditLogModel` | — (belum ada) | `sys_audit_logs` | — | — | — | Stub |
| `UnitKerjaReadOnlyModel` | `App\Models\Reference` | `tbl_unit_kerja` | `id_unit_kerja` | false | false | Write blocked |
| `PenanggungJawabReadOnlyModel` | `App\Models\Reference` | `vw_penanggung_jawab` | `id_kpe` | false | false | Write blocked |
| `SubUnitModel` | `App\Models` (bukan Master) | `aset_sub_units` | `su_id` | ✅ true | ✅ true | |
| `GedungModel` | `App\Models` (bukan Master) | `aset_gedung` | `gd_id` | ✅ true | ✅ true | |
| `LantaiModel` | `App\Models\Master` | `aset_lantai` | `lt_id` | ✅ true | ✅ true | |
| `RuanganModel` | `App\Models\Master` | `aset_ruangan` | `rg_id` | ✅ true | ✅ true | `getAllWithRelationsPaginated()`, `countAllWithRelations()`, **`getJenisRuanganOptions()`** untuk baca ENUM database |
| `KategoriModel` | `App\Models\Master` | `aset_kategori` | `kt_id` | ✅ true | false | ⚠️ useTimestamps belum true |
| `SubKategoriModel` | `App\Models\Master` | `aset_sub_kategori` | `sk_id` | ✅ true | ✅ true | |
| `GolonganModel` | `App\Models\Master` | `aset_golongan` | `gl_id` | false | ✅ true | |
| `MerkModel` | `App\Models\Master` | `aset_merk` | `mr_id` | false | ✅ true | |
| `TypeModel` | `App\Models\Master` | `aset_type` | `ty_id` | false | ✅ true | |
| `KondisiBarangModel` | `App\Models\Master` | `aset_kondisi_barang` | `kd_id` | false | ✅ true | |
| `SumberDanaModel` | `App\Models\Master` | `aset_sumber_dana` | `sd_id` | false | ✅ true | |
| `AsetModel` | `App\Models\Master` | `aset_all_uir` | `all_id` | ✅ true | ✅ true | Full JOINs via `withDetails()` |

**Catatan:**
- ⚠️ `aset_sub_units` dan `aset_gedung` (SubUnitModel, GedungModel) namespace-nya `App\Models\` (root), BUKAN `App\Models\Master\` — konsisten dengan struktur file (`app/Models/SubUnitModel.php`, `app/Models/GedungModel.php`)
- ⚠️ `sys_users.id_kpe` tipenya `INT(6) UNSIGNED` tetapi `ylpi_karyawan.id_kpe` tipenya `VARCHAR(16)` — relasi logis, bukan FK fisik
- ⚠️ System model (Permission, UserRole, RolePermission, UserUnitScope, AuditLog) **belum diimplementasikan** — stub

### 7.3 Controller yang Ada (Aktual)

> ⚠️ **DUPLICATE CONTROLLERS: Terdapat controller duplikat di `app/Controllers/` dan `app/Controllers/Master/`** yang menyebabkan ambiguitas. Ini adalah artifact dari pengembangan dan perlu di-cleanup. Controller di `app/Controllers/Master/` adalah yang aktif. Controller di root `app/Controllers/` (gedungController, SubUnitController, UnitKerjaController, PenanggungJawabController) adalah legacy.

#### `app/Controllers/Master/` (aktif)

| Controller | Extends | Models | Methods | Catatan |
|---|---|---|---|---|
| `AsetController` | Controller | AsetModel, KategoriModel, SubKategoriModel, GolonganModel, MerkModel, TypeModel, KondisiBarangModel, SumberDanaModel, SubUnitModel (App\Models), UnitKerjaReadOnlyModel, AssetNumberService | `index, new, create, edit, update, delete, show` + 6 AJAX | **REGISTRASI ASET LENGKAP** |
| `BaseMasterController` | BaseController | dinamis $this->model | `index, show, create, store, edit, update, delete, toggle` | Generic base, belum dipakai |
| `GedungController` | Controller | GedungModel, LantaiModel, UnitKerjaReadOnlyModel | `index, show, new, create, edit, update, delete` | Auto-create lantai di create() |
| `GolonganController` | Controller | GolonganModel | `index, show, new, create, edit, update, delete` | |
| `KategoriController` | Controller | KategoriModel | `index, show, new, create, edit, update, delete` | |
| `KondisiBarangController` | Controller | KondisiBarangModel | `index, new, create, edit, update, delete` | |
| `LantaiController` | Controller | LantaiModel, GedungModel | `index, new, create, edit, update, delete, byGedung` | |
| `MerkController` | Controller | MerkModel | `index, new, create, edit, update, delete` | |
| `PenanggungJawabController` | Controller | PenanggungJawabReadOnlyModel | `index, show` | Read-only, query ke `ylpi_karyawan` langsung |
| `RuanganController` | Controller | RuanganModel, LantaiModel, SubUnitModel, PenanggungJawabReadOnlyModel | `index, new, create, edit, update, delete` | Pagination 20/halaman, 5 kolom ringkas. **Edit preload**: melempar `$pjData` dan `$jenisRuanganOptions` ke view |
| `SubKategoriController` | Controller | SubKategoriModel, KategoriModel | `index, new, create, edit, update, delete` | |
| `SubUnitController` | Controller | SubUnitModel, UnitKerjaReadOnlyModel | `index, show, new, create, edit, update, delete` | ⚠️ duplikat dari app/Controllers/ |
| `SumberDanaController` | Controller | SumberDanaModel | `index, new, create, edit, update, delete` | |
| `TypeController` | Controller | TypeModel, MerkModel | `index, new, create, edit, update, delete` | |
| `UnitKerjaController` | Controller | UnitKerjaReadOnlyModel | `index, show` | Read-only. ⚠️ duplikat dari app/Controllers/ |

#### `app/Controllers/Auth/`

| Controller | Models | Methods |
|---|---|---|
| `LoginController` | UserModel | `index, login, logout` |
| `PasswordController` | UserModel | `change, update` |

#### `app/Controllers/System/` (STUB — belum berfungsi)

| Controller | Status |
|---|---|
| `UserController` | Stub — render view, redirect success, tanpa model |
| `RoleController` | Stub — render view, redirect success, tanpa model |
| `PermissionController` | Stub — render view, redirect success, tanpa model |

#### `app/Controllers/` (Legacy / duplikat)

⚠️ **Controller berikut adalah duplikat dari `app/Controllers/Master/`. Segera hapus atau deprecate:**

| Controller | File | Status |
|---|---|---|
| `gedungController` | `app/Controllers/gedungController.php` | Duplikat aktif (Windows case-insensitive) |
| `SubUnitController` | `app/Controllers/SubUnitController.php` | Duplikat |
| `UnitKerjaController` | `app/Controllers/UnitKerjaController.php` | Duplikat |
| `PenanggungJawabController` | `app/Controllers/PenanggungJawabController.php` | Duplikat |

#### `app/Controllers/` (Search / AJAX)

| Controller | Models | Methods |
|---|---|---|
| `SearchController` | PenanggungJawabReadOnlyModel | `searchPenanggungJawab` |

#### Pola Method Controller

**Referensi (read-only) — Unit Kerja, Penanggung Jawab:**
```text
index()        -> listing dengan pagination
show($id)      -> detail (JSON)
```

**Master CRUD:**
```text
index()    -> listing
show($id)  -> detail (view)
new()      -> form tambah
create()   -> proses simpan (redirect atau back with errors)
edit($id)  -> form edit
update($id)-> proses update (redirect)
delete($id)-> soft delete (redirect)
```

**AsetController (Registrasi):** sama dengan Master CRUD + AJAX lookups.

### 7.4 View yang Diimplementasikan

> ⚠️ **Semua view dibuat dalam pola `master/<nama_lower>/index.php` dan `master/<nama_lower>/form.php`** (huruf kecil, bukan underscore/spasi). Show/detail ditampilkan dalam modal Bootstrap di halaman index, BUKAN sebagai view terpisah. Exception: `gedung/show.php` ada sebagai view terpisah (modal belum dibuat).

| View | File | Build Status |
|---|---|---|
| Sub Unit | `master/sub-units/index.php`, `form.php` | ✅ Selesai |
| Gedung | `master/gedung/index.php`, `form.php`, `show.php` | ✅ Selesai |
| Lantai | `master/lantai/index.php`, `form.php` | ✅ Selesai |
| Ruangan | `master/ruangan/index.php`, `form.php` | ✅ Selesai (5 kolom ringkas: Kode, Nama Ruangan, Gedung/Lantai, Sub Unit, Aksi). **Form edit preload**: Select2 dengan `initSelection` untuk PJ, dropdown ENUM dari `$jenisRuanganOptions` |
| Kategori | `master/kategori/index.php`, `form.php` | ✅ Selesai |
| Sub Kategori | `master/sub-kategori/index.php`, `form.php` | ✅ Selesai |
| Golongan | `master/golongan/index.php`, `form.php` | ✅ Selesai |
| Merk | `master/merk/index.php`, `form.php` | ✅ Selesai |
| Type | `master/type/index.php`, `form.php` | ✅ Selesai |
| Kondisi Barang | `master/kondisi-barang/index.php`, `form.php` | ✅ Selesai |
| Sumber Dana | `master/sumber-dana/index.php`, `form.php` | ✅ Selesai |
| Registrasi Aset | `master/aset/index.php`, `form.php` | ✅ Selesai |
| Referensi: Unit Kerja | `referensi/unit-kerja/index.php` (modal) | ✅ Selesai |
| Referensi: Penanggung Jawab | `referensi/penanggung-jawab/index.php` (modal) | ✅ Selesai |

**Catatan Pola View:**
- Index menampilkan listing tabel + filter sidebar
- Form adalah halaman penuh (bukan modal) untuk create/edit
- Detail/ruangan show menggunakan view terpisah (`gedung/show.php` sudah dibuat, ruangan show belum)
- Semua form menggunakan `csrf_field()` dan `base_url()` untuk action
- Cascading dropdown (kategori→sub-kategori, merk→type, unit→sub-unit→ruangan) diimplementasikan dengan JavaScript vanilla + AJAX call ke endpoint `/master/aset/get-*`

### 7.5 Routing Aktual

```php
// Auth
$routes->get('auth/login', 'Auth\LoginController::index', ['filter' => 'guest']);
$routes->post('auth/login', 'Auth\LoginController::attemptLogin', ['filter' => 'guest']);
$routes->post('auth/logout', 'Auth\LoginController::logout', ['filter' => 'auth']);
$routes->get('auth/change-password', 'Auth\PasswordController::change', ['filter' => 'auth']);
$routes->post('auth/change-password', 'Auth\PasswordController::update', ['filter' => 'auth']);

// Dashboard
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

// Master resources
$routes->resource('master/sub-units', ['controller' => 'Master\SubUnitController']);
$routes->resource('master/gedung', ['controller' => 'Master\GedungController']);
$routes->resource('master/lantai', ['controller' => 'Master\LantaiController']);
$routes->resource('master/ruangan', ['controller' => 'Master\RuanganController']);
$routes->resource('master/kategori', ['controller' => 'Master\KategoriController']);
$routes->resource('master/sub-kategori', ['controller' => 'Master\SubKategoriController']);
$routes->resource('master/golongan', ['controller' => 'Master\GolonganController']);
$routes->resource('master/merk', ['controller' => 'Master\MerkController']);
$routes->resource('master/type', ['controller' => 'Master\TypeController']);
$routes->resource('master/kondisi-barang', ['controller' => 'Master\KondisiBarangController']);
$routes->resource('master/sumber-dana', ['controller' => 'Master\SumberDanaController']);
$routes->resource('master/aset');

// Custom AJAX routes (AsetController)
$routes->get('master/aset/get-sub-kategori-by-kategori/(:num)', 'Master\AsetController::getSubKategoriByKategori/$1');
$routes->get('master/aset/get-type-by-merk/(:num)', 'Master\AsetController::getTypeByMerk/$1');
$routes->get('master/aset/get-sub-unit-by-unit-kerja/(:num)', 'Master\AsetController::getSubUnitByUnitKerja/$1');
$routes->get('master/aset/get-ruangan-by-sub-unit/(:num)', 'Master\AsetController::getRuanganBySubUnit/$1');
$routes->get('master/aset/lookup-sub-kategori', 'Master\AsetController::lookupSubKategori');

// Referensi (read-only, no resource())
$routes->get('master/unit-kerja', 'Master\UnitKerjaController::index', ['filter' => 'auth']);
$routes->get('master/unit-kerja/show/(:num)', 'Master\UnitKerjaController::show/$1', ['filter' => 'auth']);
$routes->get('master/penanggung-jawab', 'Master\PenanggungJawabController::index', ['filter' => 'auth']);
$routes->get('master/penanggung-jawab/show/(:segment)', 'Master\PenanggungJawabController::show/$1', ['filter' => 'auth']);

// Lantai custom
$routes->get('master/lantai/by-gedung/(:num)', 'Master\LantaiController::byGedung/$1');

// AJAX Search routes (Select2)
$routes->get('search/penanggung-jawab', 'SearchController::searchPenanggungJawab');
```

**Catatan:**
- ⚠️ Filter permission (`['filter' => 'permission:...']`) belum diimplementasikan di route — AuthFilter saja yang aktif
- ⚠️ `gedung` singular, `lantai` singular, `aset` singular dalam route — konsisten dengan route CI4 resource

### 7.6 Filter / Middleware

Filter wajib:

| Filter | Fungsi | Status |
|---|---|---|
| `AuthFilter` | Memastikan user login | ✅ Diimplementasikan |
| `GuestFilter` | Mencegah user login mengakses halaman login | ✅ Diimplementasikan |
| `ForcePasswordChangeFilter` | Memaksa ganti password awal | ✅ Diimplementasikan |
| `RolePermissionFilter` | Memastikan user memiliki permission | ❌ Belum diimplementasikan (stub) |
| `ScopeFilter` | Membatasi data berdasarkan unit scope | ❌ Belum diimplementasikan (stub) |

### 7.7 Library/Service

| Service | Fungsi | Status |
|---|---|---|
| `AuthService` | — (logic inline di LoginController) | ❌ Stub |
| `AuthorizationService` | — | ❌ Stub |
| `AuditService` | — | ❌ Stub |
| `ReferenceDataService` | — | ❌ Stub |
| `MasterDataService` | — | ❌ Stub |
| `AssetNumberService` | Generate `all_id` dan `nomor_aset_baru` | ✅ Ada di `app/Services/AssetNumberService.php` |
| `SearchController` | AJAX search untuk Select2 penanggung jawab | ✅ Ada di `app/Controllers/SearchController.php` |

### 7.8 Standar Coding CodeIgniter 4

1. Gunakan Query Builder/Model, bukan raw SQL concatenation.
2. Aktifkan CSRF protection untuk form.
3. Gunakan validation rules di controller/service.
4. Gunakan `esc()` pada output view.
5. Gunakan soft delete untuk data master yang sudah direferensikan.
6. Gunakan database migration dan seeder.
7. Jangan simpan business rule kompleks di view.
8. Jangan izinkan mass assignment tanpa `$allowedFields`.
9. Audit setiap create, update, delete, activate, deactivate.
10. Gunakan pagination bawaan CI4 untuk listing.

### 7.9 Fitur Preload Edit Ruangan

Implementasi preload data saat edit ruangan:

**Masalah**: Saat form edit ruangan dibuka, field dropdown (Gedung, Lantai, Jenis Ruangan) dan Select2 Penanggung Jawab tampil kosong meskipun data sudah tersimpan.

**Solusi**:

1. **Controller** (`RuanganController::edit()`) melempar data tambahan ke view:
   - `$pjData` - data penanggung jawab dari `PenanggungJawabReadOnlyModel::getById()`
   - `$jenisRuanganOptions` - opsi ENUM dari `RuanganModel::getJenisRuanganOptions()`

2. **Model** (`RuanganModel::getJenisRuanganOptions()`) membaca ENUM secara dinamis:
   ```php
   public function getJenisRuanganOptions(): array
   {
       $db = db_connect();
       $query = $db->query("SHOW COLUMNS FROM aset_ruangan LIKE 'jenis_ruangan'");
       $row = $query->getRow();
       preg_match("/^enum\(\'(.*)\'\)$/i", $row->Type, $matches);
       return explode("','", $matches[1]);
   }
   ```

3. **View** (`form.php`) menggunakan data preload:
   - Dropdown jenis ruangan di-generate dari `$jenisRuanganOptions`
   - Select2 PJ menggunakan `initSelection` callback untuk inisialisasi nilai default

4. **SearchController** menangani AJAX search untuk Select2 PJ dengan filter `flag_karyawan='1'`

**Database change**: Kolom `jenis_ruangan` diubah dari `VARCHAR(50)` ke `ENUM('Kantor', 'Ruang Kuliah', 'Laboratorium', 'Perpustakaan', 'Gudang', 'Lainnya')` via migration `2026-05-02-000001_UpdateJenisRuanganToEnum.php`

## 8. Analisa Permission dan Security

### 8.1 Rancangan Permission

Permission minimum:

| Modul | View | Create | Update | Delete/Nonaktif | Approve | Export/Cetak |
|---|---|---|---|---|---|---|
| Unit Kerja | Ya | Tidak | Tidak | Tidak | Tidak | Ya |
| Penanggung Jawab | Ya | Tidak | Tidak | Tidak | Tidak | Ya |
| Sub Unit | Ya | Ya | Ya | Ya | Tidak | Ya |
| Gedung | Ya | Ya | Ya | Ya | Tidak | Ya |
| Lantai | Ya | Ya | Ya | Ya | Tidak | Ya |
| Ruangan | Ya | Ya | Ya | Ya | Tidak | Ya |
| Kategori | Ya | Ya | Ya | Ya | Tidak | Ya |
| Sub Kategori | Ya | Ya | Ya | Ya | Tidak | Ya |
| Golongan | Ya | Ya | Ya | Ya | Tidak | Ya |
| Merk | Ya | Ya | Ya | Ya | Tidak | Ya |
| Type | Ya | Ya | Ya | Ya | Tidak | Ya |
| Kondisi Barang | Ya | Ya | Ya | Ya | Tidak | Ya |
| Sumber Dana | Ya | Ya | Ya | Ya | Tidak | Ya |
| User Management | Ya | Ya | Ya | Nonaktif | Tidak | Tidak |
| Role Permission | Ya | Ya | Ya | Nonaktif | Tidak | Tidak |

### 8.2 Matrix Role

| Role | Lihat | Tambah | Edit | Nonaktif | Approve | Cetak/Export |
|---|---|---|---|---|---|---|
| `super_admin` | Semua | Semua | Semua | Semua | Semua | Semua |
| `admin_aset_pusat` | Semua master | Semua master | Semua master | Semua master | Transaksi aset nanti | Semua |
| `admin_unit` | Scope unit | Sub Unit/Ruangan scope | Data scope | Data scope | Tidak | Scope unit |
| `operator_unit` | Scope unit | Input tertentu | Edit terbatas | Tidak | Tidak | Scope unit |
| `viewer_pimpinan` | Dashboard/list/laporan | Tidak | Tidak | Tidak | Tidak | Ya |
| `auditor` | Data dan audit | Tidak | Tidak | Tidak | Tidak | Ya |

### 8.3 Proteksi Tabel Read-Only

Proteksi dilakukan berlapis:

1. UI tidak menampilkan tombol tambah/edit/hapus untuk `tbl_unit_kerja` dan `ylpi_karyawan`.
2. Controller tidak memiliki endpoint write.
3. Model read-only override method write dan melempar exception jika dipanggil.
4. User database production untuk aplikasi sebaiknya hanya diberi privilege `SELECT` pada database/server referensi.
5. Audit log mencatat setiap akses detail data sensitif penanggung jawab jika diperlukan.

### 8.4 Keamanan Login

1. Password hash menggunakan `password_hash()` dan `password_verify()`.
2. Session regeneration setelah login.
3. Rate limit login berdasarkan username dan IP.
4. Lock sementara setelah beberapa kali gagal login.
5. CSRF aktif untuk form login dan seluruh form state-changing.
6. Password default `myUIR2026` wajib diganti setelah login pertama.
7. Cookie session menggunakan `httponly`, `secure` pada HTTPS, dan `samesite=Lax` atau `Strict`.

### 8.5 Data Privacy

Data penanggung jawab memuat nomor HP, WhatsApp, dan email. Tampilan data tersebut harus dibatasi:

1. `viewer_pimpinan` dapat melihat nama dan unit, tetapi nomor HP/WA dapat disembunyikan.
2. `operator_unit` hanya melihat kontak dalam scope operasional.
3. QR publik tidak boleh menampilkan nomor HP, WhatsApp, email, nilai aset, atau informasi internal sensitif.

## 9. Alur Proses / Flow

### 9.1 Flow Login

```text
User buka halaman login
  -> input username dan password
  -> sistem validasi CSRF
  -> sistem cari user aktif
  -> sistem verifikasi password hash
  -> jika gagal, catat login attempt
  -> jika berhasil, regenerate session
  -> load role, permission, scope
  -> jika force_password_change = 1, arahkan ke ganti password
  -> jika normal, arahkan ke dashboard
```

### 9.2 Flow Unit/Fakultas

| Aspek | Detail |
|---|---|
| Sumber data | `tbl_unit_kerja` |
| Siapa input | Bukan aplikasi aset |
| Aksi aplikasi | Lihat, cari, pilih sebagai dropdown |
| Relasi | Ke Sub Unit, Gedung, Aset, User Scope |
| Validasi | Harus ada di referensi saat dipilih |
| Output | Listing Unit/Fakultas, detail unit, dropdown |
| Tambah/edit/nonaktif | Tidak boleh dari aplikasi |

### 9.3 Flow Sub Unit

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin, Admin Aset Pusat, Admin Unit sesuai scope |
| Data input | Unit/Fakultas, kode, nama, jenis, keterangan, status |
| Relasi | `unit_kerja_id` ke `tbl_unit_kerja.id_unit_kerja` secara logis; Ruangan dan Aset |
| Validasi wajib | Unit/Fakultas valid, kode unik per unit, nama wajib |
| Output | List Sub Unit per Unit, detail Sub Unit |
| Tambah/edit/nonaktif | Boleh sesuai permission; hapus fisik tidak disarankan |

Flow:

```text
Admin pilih Unit/Fakultas
  -> input kode dan nama Sub Unit
  -> sistem validasi unit referensi
  -> sistem cek duplikasi kode per unit
  -> simpan Sub Unit
  -> tulis audit log
  -> tampil pada listing dan dropdown Ruangan
```

### 9.4 Flow Gedung

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat |
| Data input | Unit/Fakultas pemilik, kode gedung, nama gedung, jumlah lantai, alamat ringkas, status |
| Relasi | `unit_kerja_id` ke `tbl_unit_kerja`; Lantai (auto-create) |
| Validasi wajib | Unit valid, kode gedung unik, nama wajib, jumlah lantai minimal 1 |
| Output | List Gedung, detail Gedung, dropdown Lantai |
| Tambah/edit/nonaktif | Boleh sesuai permission |

Flow:

```text
Admin input Gedung
  -> pilih Unit/Fakultas pemilik
  -> input kode gedung, nama gedung
  -> input jumlah lantai (minimal 1, wajib)
  -> sistem validasi Unit/Fakultas
  -> sistem cek kode gedung unik
  -> simpan Gedung, dapat gd_id
  -> sistem otomatis membuat N record aset_lantai:
     - untuk i = 1 s/d jumlah_lantai:
       - kode_lantai = LT_{gd_id}_{i}
       - nama_lantai = Lantai {i}
       - nomor_lantai = i
       - gedung_id = gd_id
       - is_active = 1
  -> tampilkan pesan sukses beserta jumlah lantai yang dibuat
  -> tulis audit log
```

Catatan: kolom `jumlah_lantai` bersifat readonly setelah gedung dibuat. Pengelolaan lantai tambahan atau perubahan dilakukan melalui menu Master Lantai.

### 9.5 Flow Lantai

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat/Admin Unit sesuai scope gedung |
| Data input | Gedung, kode lantai, nama lantai, nomor urut |
| Relasi | Gedung ke Lantai |
| Validasi wajib | Gedung aktif, kode lantai unik per gedung, nomor lantai valid |
| Output | List Lantai per Gedung, dropdown Ruangan |
| Tambah/edit/nonaktif | Boleh sesuai permission |

Catatan: Lantai otomatis dibuat saat input Gedung baru berdasarkan `jumlah_lantai`. Lantai juga dapat ditambahkan, diedit, atau dinonaktifkan secara manual melalui menu Master Lantai. Penambahan manual Lantai tidak mengubah nilai `jumlah_lantai` pada tabel `aset_gedung` (field tersebut hanya mencatat jumlah awal).

### 9.6 Flow Ruangan

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat/Admin Unit sesuai scope |
| Data input | Lantai, kode ruangan, nama ruangan, jenis ruangan, Sub Unit, Penanggung Jawab |
| Relasi | Lantai, Sub Unit, Penanggung Jawab |
| Validasi wajib | Lantai aktif, Sub Unit aktif, PJ valid (`flag_karyawan='1'`), kode ruangan unik per lantai |
| Output | List Ruangan dengan pagination 20/halaman, 5 kolom: Kode, Nama Ruangan, Gedung/Lantai (gabung), Sub Unit, Aksi (edit/delete icon buttons) |
| Tambah/edit/nonaktif | Boleh sesuai permission |

**Fitur Preload Edit**: Saat form edit dibuka, sistem otomatis:
1. Mengambil data ruangan dan data relasi (lantai → gedung)
2. Mengambil `$pjData` dari `ylpi_karyawan` via `PenanggungJawabReadOnlyModel::getById()`
3. Membaca opsi ENUM `jenis_ruangan` dari database via `RuanganModel::getJenisRuanganOptions()`
4. Melempar `$pjData` dan `$jenisRuanganOptions` ke view
5. View merender Select2 dengan `initSelection` callback untuk preload nilai PJ
6. View merender dropdown jenis ruangan dari `$jenisRuanganOptions`

Flow:

```text
Admin pilih Gedung
  -> pilih Lantai
  -> pilih Sub Unit pemilik
  -> pilih Penanggung Jawab dari ylpi_karyawan
  -> input kode/nama/jenis Ruangan
  -> sistem validasi relasi
  -> simpan Ruangan
  -> tampilkan jalur lokasi lengkap
```

### 9.7 Flow Penanggung Jawab

| Aspek | Detail |
|---|---|
| Sumber data | `ylpi_karyawan` atau `vw_penanggung_jawab` |
| Siapa input | Bukan aplikasi aset |
| Data dipakai | `id_kpe`, `npk`, `nama_gelar`, `jenkel`, `kategori`, `no_hp1`, `no_wa`, `email`, `unit_kerja`, `flag_karyawan` |
| Relasi | Ke Ruangan dan Aset |
| Validasi wajib | ID KPE valid dan `flag_karyawan = 1` saat dipilih |
| Output | List PJ aktif, detail PJ aktif, dropdown/select2 hanya PJ aktif |
| Tambah/edit/nonaktif | Tidak boleh dari aplikasi |

### 9.8 Flow Kategori

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat |
| Data input | Kode, nama, jenis aset, keterangan, status |
| Relasi | Sub Kategori, Aset |
| Validasi wajib | Kode unik, nama unik |
| Output | List kategori, filter aset |
| Tambah/edit/nonaktif | Boleh; nonaktif jika sudah dipakai |

### 9.9 Flow Sub Kategori

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat |
| Data input | Kategori, kode, nama, flag wajib merk/type/ruangan |
| Relasi | Kategori, Aset |
| Validasi wajib | Kategori aktif, kode unik per kategori |
| Output | List sub kategori, dropdown berdasarkan kategori |
| Tambah/edit/nonaktif | Boleh; nonaktif jika sudah dipakai |

### 9.10 Flow Golongan

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat |
| Data input | Kode, nama, kelompok, keterangan |
| Relasi | Aset, **terikat pada Kategori melalui kelompok/jenis_aset** |
| Validasi wajib | Kode unik, nama unik |
| Validasi relasi | Golongan wajib sesuai dengan Kategori yang dipilih (`golongan.kelompok = kategori.jenis_aset`) |
| Aturan cascading | Saat user memilih Kategori, dropdown Golongan hanya menampilkan golongan dengan kelompok yang sesuai. Jika Kategori berubah, Golongan direset. |
| Aturan backend | Jika golongan_id di-submit, server memvalidasi bahwa kombinasi kategori_id + golongan_id konsisten (golongan.kelompok = kategori.jenis_aset). Jika tidak konsisten, submit ditolak dengan pesan error. |
| Output | List golongan, dropdown golongan terfilter per kategori |
| Tambah/edit/nonaktif | Boleh |

**Aturan Relasi Kategori–Golongan (Dependent Dropdown):**

Pemilihan Golongan mengikuti Kategori yang dipilih. Kolom `kelompok` pada `aset_golongan` dicocokkan dengan kolom `jenis_aset` pada `aset_kategori`:

| kategori.jenis_aset | golongan.kelompok | Golongan yang tersedia |
|---|---|---|
| bangunan | bangunan | Harta Tetap / Aset Tetap, Bangunan, Tanah |
| non_bangunan | non_bangunan | Kendaraan, Peralatan Elektronik, Furniture, Inventaris |
| lainnya | lainnya | Perangkat Lunak |

Alur UI: User memilih Kategori → AJAX memuat Golongan yang sesuai → dropdown Golongan direset dan diisi ulang. Jika Kategori belum dipilih, dropdown Golongan menampilkan placeholder "Pilih Kategori terlebih dahulu".

### 9.11 Flow Merk

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat/Operator sesuai permission |
| Data input | Kode merk, nama merk, keterangan |
| Relasi | Type, Aset |
| Validasi wajib | Nama merk unik |
| Output | List merk, dropdown type |
| Tambah/edit/nonaktif | Boleh |

### 9.12 Flow Type

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat/Operator sesuai permission |
| Data input | Merk, kode type, nama type, keterangan |
| Relasi | Merk, Aset |
| Validasi wajib | Nama type unik per merk |
| Output | List type per merk |
| Tambah/edit/nonaktif | Boleh |

### 9.13 Flow Kondisi Barang

| Aspek | Detail |
|---|---|
| Siapa input | Super Admin/Admin Aset Pusat |
| Data input | Kode kondisi, nama kondisi, level, bisa digunakan/tidak |
| Relasi | Aset |
| Validasi wajib | Kode unik, nama unik |
| Output | List kondisi, filter aset |
| Tambah/edit/nonaktif | Boleh, tetapi kondisi standar sebaiknya dikunci |

### 9.14 Flow Output/List Data

Semua listing master wajib memiliki:

1. Search keyword.
2. Filter status aktif/nonaktif.
3. Filter relasi, misalnya Gedung untuk Lantai, Lantai/Sub Unit untuk Ruangan.
4. Pagination.
5. Sorting.
6. Tombol detail.
7. Tombol tambah/edit/nonaktif sesuai permission.
8. Export sederhana sesuai permission.
9. Empty state yang jelas jika data belum tersedia.

## 10. Urutan Implementasi

Urutan implementasi paling aman:

### 10.1 Tahap 0: Persiapan Proyek

1. Buat project CodeIgniter 4.
2. Konfigurasi database DEV `uir_aset`.
3. Aktifkan `.env`.
4. Siapkan base layout Bootstrap.
5. Siapkan migration dan seeder.
6. Buat helper standar.

### 10.2 Tahap 1: Auth/Login

1. Buat tabel `sys_users`, `sys_roles`, `sys_permissions`, pivot role.
2. Buat seed role dan admin awal.
3. Buat login/logout.
4. Buat password hash.
5. Buat force password change.
6. Buat AuthFilter.

### 10.3 Tahap 2: Permission dan Menu

1. Buat permission per modul.
2. Buat RolePermissionFilter.
3. Buat sidebar dinamis berdasarkan permission.
4. Buat user management sederhana.

### 10.4 Tahap 3: Integrasi Referensi Read-Only

1. Buat `UnitKerjaReadOnlyModel`.
2. Buat `PenanggungJawabReadOnlyModel`.
3. Buat listing dan lookup endpoint.
4. Pastikan tidak ada endpoint write.
5. Buat service validasi referensi.

### 10.5 Tahap 4: Master Data Organisasi-Lokasi

1. Master Sub Unit.
2. Master Gedung.
3. Master Lantai.
4. Master Ruangan.
5. View/list Ruangan lengkap.
6. Validasi relasi berjenjang.

### 10.6 Tahap 5: Master Lookup Aset

1. Kategori.
2. Sub Kategori.
3. Golongan.
4. Merk.
5. Type.
6. Kondisi Barang.
7. Sumber Dana.

### 10.7 Tahap 6: Output/List dan UX Polishing

1. Search/filter/pagination.
2. Breadcrumb lokasi.
3. Badge status.
4. Modal konfirmasi.
5. Alert sukses/error.
6. Export sederhana.

### 10.8 Tahap 7: Persiapan Aset Non-Bangunan ✅ SELESAI

**Status: SELESAI** (30 April 2026)

Implementasi yang telah selesai:

1. **Database** `aset_all_uir` — tabel sudah ada dengan struktur lengkap sesuai Section 6.4.16. Primary key `all_id` VARCHAR(15). Kolom `status_aset` ENUM('draft','aktif','nonaktif','hilang','dihapus'). Semua FK fisik ke tabel master internal (kategori, sub_kategori, golongan, merk, type, kondisi, sumber_dana, sub_unit, ruangan).
2. **Model** `AsetModel` (`App\Models\Master\AsetModel`) — mengarah ke `aset_all_uir`. PK `all_id`, `useAutoIncrement=false`, `useSoftDeletes=true`, `useTimestamps=true`. `allowedFields` mencakup 23 kolom termasuk `deleted_at`. Method `withDetails()` untuk JOIN semua relasi.
3. **Service** `AssetNumberService` (`App\Services\AssetNumberService`) — generator `all_id` format `all_xxx-yyyyyyy` (xxx = sub_kategori_id 3 digit, yyyyyyy = 7 karakter acak). Generator `nomor_aset_baru` format `{KODE_KATEGORI}-{TAHUN}-{SEQ}` contoh `IT-2026-00001`. Helper `getSubKategoriInfo()`, `isMerkRequired()`, `isTypeRequired()`, `isRuanganRequired()`.
4. **Controller** `AsetController` (`App\Controllers\Master\AsetController`) — CRUD lengkap: `index()`, `new()`, `create()`, `edit()`, `update()`, `delete()`, `show()` (JSON). AJAX lookups: `getSubKategoriByKategori()`, `getTypeByMerk()`, `getSubUnitByUnitKerja()`, `getRuanganBySubUnit()`, `lookupSubKategori()`. Validasi dinamis berdasarkan `wajib_merk`, `wajib_type`, `wajib_ruangan` dari sub_kategori.
5. **Route** `/master/aset` — resource route + 5 custom AJAX endpoints.
6. **View** `master/aset/index.php` — listing dengan search, filter status/kategori, smart pagination sliding window. `master/aset/form.php` — form registrasi/edit dengan dropdown berjenjang dan validasi dinamis.
7. **Menu sidebar** — "Registrasi Aset" ditambahkan di grup "REGISTRASI ASET" (setelah Sumber Dana) di `app/Views/layouts/main.php`.

**Catatan penting:**
- `nomor_aset_baru` tidak di-generate manual saat edit (field tidak muncul di form edit — sesuai alur registrasi, nomor baru di-generate saat create)
- `all_id` tidak bisa diubah setelah dibuat
- `status_aset` ENUM di database pernah typo `hissing`, sudah diperbaiki ke `hilang`

### 10.9 Tahap 8: Persiapan Integrasi Production

1. Pisahkan konfigurasi database referensi.
2. Siapkan connection `default` untuk aplikasi dan `referensi` untuk sistem kampus.
3. Buat adapter repository agar pemindahan server tidak mengubah controller.
4. Siapkan fallback jika server referensi tidak tersedia.

## 11. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data `tbl_unit_kerja` tidak sesuai kebutuhan aset | Sub Unit dan Gedung salah mapping | Buat tabel Sub Unit internal dan validasi awal dengan UIR |
| Relasi `ylpi_karyawan.unit_kerja` tidak cocok | Unit PJ kosong atau salah tampil | Gunakan LEFT JOIN dan tampilkan kosong jika tidak cocok |
| Tabel referensi pindah server production | FK fisik rusak | Jangan gunakan FK fisik ke tabel referensi; gunakan service adapter |
| Data ganda Sub Unit/Gedung/Ruangan | Listing tidak akurat | Unique key dan validasi server-side |
| Ruangan tanpa PJ | Sulit tanggung jawab kehilangan | Wajibkan `penanggung_jawab_id_kpe` saat input ruangan |
| Sub Unit tanpa ruangan | Struktur organisasi tidak lengkap | Dashboard exception dan validasi minimal satu ruangan per sub unit jika sudah operasional |
| Unit tanpa Sub Unit | Input ruangan/aset terganggu | Seeder sub unit default per unit |
| Hak akses terlalu luas | Risiko perubahan data tidak sah | RBAC, scope, audit log |
| Read-only dilanggar oleh developer | Data referensi rusak | Model read-only, privilege DB SELECT-only, code review |
| Password default tidak diganti | Risiko keamanan | Force password change dan audit |
| Query listing lambat | UX buruk | Index, pagination, view, filter |
| Mutasi manual di luar sistem nanti | Data aset tidak valid | SOP, audit exception, approval workflow |

### 11.1 Catatan Production Berbeda Server

Saat production, `tbl_unit_kerja` dan `ylpi_karyawan` akan dipindah ke server berbeda. Rekomendasi:

1. Gunakan konfigurasi multiple database connection di CodeIgniter 4.
2. Buat `ReferenceDataService` sebagai satu pintu akses referensi.
3. Jangan join langsung lintas server pada query operasional berat.
4. Untuk dropdown, gunakan cache terkontrol dengan TTL.
5. Untuk validasi kritis, cek ke sumber referensi saat simpan.
6. Jika jaringan ke server referensi gagal, aplikasi tetap bisa membuka data lokal tetapi tidak boleh menyimpan relasi baru yang belum tervalidasi.

### 11.2 Best Practice MySQL/InnoDB

1. Gunakan charset `utf8mb4` dan collation konsisten.
2. Gunakan InnoDB untuk semua tabel aplikasi.
3. Gunakan index pada semua kolom pencarian dan relasi.
4. Hindari `ENUM` jika daftar sering berubah; gunakan lookup table. `ENUM` masih boleh untuk status sistem yang sangat stabil.
5. Gunakan transaction untuk operasi lintas tabel.
6. Hindari delete fisik pada master yang sudah dipakai.
7. Backup database harian untuk production.
8. Siapkan migration rollback dengan hati-hati.

## 12. Analisa UI/UX dan Desain Visual

### 12.1 Arah Desain

Antarmuka harus terasa sebagai sistem resmi universitas, bukan template admin generik. Karakter visual yang disarankan adalah islami, elegan, bersih, profesional, modern, dan nyaman dilihat. Identitas UIR dapat dibangun melalui hijau cerah yang seimbang, aksen emas lembut, permukaan putih kehijauan, border halus, ikon outline, dan layout yang rapi.

Gunakan Bootstrap versi kekinian, disarankan Bootstrap 5.3 atau versi stabil terbaru saat implementasi. Untuk interaksi tabel dan form, boleh menambahkan plugin seperti DataTables Bootstrap 5, Select2 Bootstrap 5 theme, SweetAlert2, Flatpickr, dan Bootstrap Icons atau Lucide Icons.

### 12.2 Skema Warna

Palet warna yang diimplementasikan (soft green theme):

| Token | Warna | Hex | Penggunaan |
|---|---|---:|---|
| Primary Green | Hijau soft | `#a1d99b` | Aksen utama, icon edit, badge success |
| Primary Dark | Hijau gelap elegan | `#1f5f4a` | Active menu, gradient button, hover accent |
| Primary Medium | Hijau medium | `#2f7a5f` | Gradient secondary, card title |
| Primary Soft | Hijau lembut | `#dcefe7` | Background badge, alert success, table hover |
| Surface | Putih bersih | `#FFFFFF` | Card, form, table |
| Sidebar | Gradient pastel | `#e4f0e7→#dbeadf` | Sidebar background |
| Navbar | Glass morph | `rgba(255,255,255,0.82)` | Topbar dengan backdrop-filter blur |
| Border | Abu hijau | `#e3e8df` | Border card/input/table |
| Text Primary | Slate gelap | `#1f2933` | Teks utama |
| Text Muted | Abu netral | `#6b7280` | Caption, hint |
| Danger | Merah soft | `#d9534f` | Delete button, alert danger |
| Info | Teal soft | `#5ca8b8` | Detail button, badge info |
| Warning | Emas | `#b89b5e` | Badge warning |
| Background | Gradient hijau | `#f8faf7→#f3f6f2` | Body background |

Fitur visual:
- **Navbar**: glassmorphism dengan `backdrop-filter: blur(10px)`
- **Card**: `rgba(255,255,255,0.92)` dengan radius 18px dan shadow lembut
- **Button primary**: gradient `#1f5f4a→#2f7a5f` dengan shadow dan hover lift
- **Icon action buttons**: 34px, rounded 10px, SVG putih — edit (hijau gradient), delete (merah), detail (teal)
- **Table hover**: `#d4ede0` dengan transisi 0.22s ease
- **Pagination**: 40px, rounded 12px, active gradient + shadow, hover `#edf7f1`
- **Font**: Inter (Google Fonts), weight 400-800 |

### 12.3 Tipografi

Rekomendasi font web:

1. `Inter` atau `Source Sans 3` untuk UI yang bersih dan mudah dibaca.
2. `Nunito Sans` dapat dipakai jika ingin kesan lebih ramah, tetapi tetap profesional.
3. Heading gunakan weight 600-700.
4. Body gunakan 14-16px.
5. Label form 13-14px dengan font-weight 500.
6. Angka KPI gunakan font dengan `font-variant-numeric: tabular-nums`.

### 12.4 Layout Admin Dashboard

Struktur layout:

```text
┌─────────────────────────────────────────────────────┐
│ Topbar: glass morph white, blur, brand + nav + user │
├───────────────┬─────────────────────────────────────┤
│ Sidebar       │ Breadcrumb                          │
│ pastel green  │ Page title + Tambah button (header) │
│ gradient      │ Cards / filter / table + pagination │
│               │                                     │
│ - Dashboard   │                                     │
│ - Master Data │                                     │
│ - Referensi   │                                     │
│ - Sistem      │                                     │
└───────────────┴─────────────────────────────────────┘
```

Sidebar sebaiknya:

1. Lebar desktop col-md-2 (Bootstrap grid).
2. Collapsible collapse pada mobile.
3. Background gradient pastel `#e4f0e7→#dbeadf`.
4. Active menu menggunakan gradient hijau gelap `#1f5f4a→#2f7a5f` dengan teks putih dan shadow soft.
5. Section label menggunakan uppercase 10px, letter-spacing 1.2px, color `rgba(31,95,74,0.55)`.
6. Ikon menu 16px dengan opacity 0.7 (1.0 saat active).
7. Hover: background `rgba(255,255,255,0.55)` + translateX(2px).

Topbar sebaiknya:

1. Tinggi 64px.
2. Glass morph: background `rgba(255,255,255,0.82)` + `backdrop-filter: blur(10px)`.
3. Brand: teks hijau gelap `#1f5f4a` dengan gradient badge via CSS `::before` pseudo-element.
4. Nav-link: 13.5px, weight 600, hover background `rgba(31,95,74,0.06)`.
5. User dropdown ringkas.
6. Tidak terlalu banyak tombol.

### 12.5 Komponen UI

| Komponen | Rekomendasi |
|---|---|
| Card | Rounded `1rem`, border halus, shadow sangat ringan |
| Button | Primary gradient hijau gelap, hover lift, radius 10px. Icon action buttons 34px SVG untuk edit/delete/detail |
| Badge | Status aktif hijau lembut, nonaktif abu, warning amber |
| Dropdown | Tidak dipakai untuk aksi row — diganti icon buttons (edit, delete, detail) |
| Modal | Untuk input cepat dan konfirmasi nonaktif |
| Alert | Jelas, ringkas, bisa ditutup |
| Breadcrumb | Tampilkan jalur Dashboard > Master Lokasi > Ruangan |
| Table | Header uppercase, zebra `#fcfdfc`, hover `#d4ede0` dengan transisi 0.22s, action icon buttons di kanan |
| Form | Label jelas, required marker, help text, validasi inline |
| Empty state | Ilustrasi minimal atau icon outline, CTA tambah data |

### 12.6 Halaman Login

Desain login:

1. Background gradient lembut hijau-putih.
2. Card login di tengah dengan logo UIR.
3. Judul: Sistem Informasi Aset UIR.
4. Subjudul: Manajemen aset kampus berbasis lokasi dan tanggung jawab.
5. Input username dan password dengan icon.
6. Tombol login hijau UIR.
7. Alert error tidak membocorkan apakah username atau password yang salah.
8. Footer kecil: Universitas Islam Riau.

Visual login harus resmi, tidak ramai. Aksen islami bisa berupa pattern geometris sangat halus di background.

### 12.7 Halaman Dashboard

Dashboard tahap awal cukup menampilkan:

1. Total Unit/Fakultas referensi.
2. Total Sub Unit.
3. Total Gedung.
4. Total Ruangan.
5. Total Ruangan tanpa PJ jika ada exception.
6. Total master Kategori/Sub Kategori.
7. Quick action: tambah Sub Unit, tambah Gedung, tambah Ruangan.
8. Tabel ringkas Ruangan terbaru.

KPI card gunakan icon outline, angka besar, label kecil, dan background putih. Warna hanya dipakai untuk menandai kategori informasi.

### 12.8 Halaman Master Data

Pola halaman master data:

```text
Page Header
  Judul + deskripsi singkat
  Tombol Tambah

Filter Card
  Search keyword
  Filter status
  Filter relasi

Data Table
  Kolom utama
  Badge status
  Aksi detail/edit/nonaktif
```

Contoh Ruangan (5 kolom, ringkas):

| Kolom | Isi |
|---|---|
| Kode | Kode ruangan (dengan `<code>` styling) |
| Nama Ruangan | Nama ruangan (text-truncate, max 200px) |
| Gedung / Lantai | Nama gedung (baris atas) + info lantai (baris bawah kecil) |
| Sub Unit | Nama sub unit |
| Aksi | Icon button edit (hijau) + delete (merah) |

### 12.9 Halaman Detail Aset

Walaupun registrasi aset penuh belum tahap awal, desain detail aset harus disiapkan:

1. Header: nomor aset baru, nomor aset lama, status, QR.
2. Card identitas: nama, kategori, sub kategori, merk, type.
3. Card kepemilikan: Unit/Fakultas, Sub Unit, Penanggung Jawab.
4. Card lokasi: Gedung, Lantai, Ruangan jika ada.
5. Card kondisi: kondisi barang, sumber dana, tahun perolehan.
6. Timeline histori lokasi untuk fase berikutnya.
7. Tab dokumen/foto untuk fase berikutnya.

### 12.10 Halaman Laporan

Halaman laporan tahap awal:

1. Filter Unit/Fakultas.
2. Filter Gedung/Lantai/Ruangan.
3. Filter status aktif.
4. Preview tabel.
5. Tombol export.

Laporan awal yang realistis:

1. Daftar Sub Unit per Unit/Fakultas.
2. Daftar Gedung per Unit/Fakultas.
3. Daftar Ruangan per Gedung/Lantai.
4. Daftar Penanggung Jawab Ruangan.
5. Daftar Master Kategori/Sub Kategori.

### 12.11 Halaman Input Form

Best practice form:

1. Gunakan layout dua kolom pada desktop dan satu kolom pada mobile.
2. Field wajib diberi tanda jelas.
3. Dropdown relasi menggunakan Select2 dengan search.
4. Validasi muncul dekat field, bukan hanya alert atas.
5. Tombol Simpan dan Batal selalu terlihat di bawah form.
6. Untuk form panjang, gunakan section card.
7. Untuk relasi berjenjang, gunakan dependent dropdown: Gedung -> Lantai -> Ruangan.

### 12.12 Halaman Tabel Listing

Best practice tabel:

1. Gunakan pagination server-side.
2. Gunakan kolom aksi berupa dropdown agar tabel tidak penuh.
3. Gunakan badge aktif/nonaktif.
4. Gunakan empty state jika tidak ada data.
5. Pada mobile, tampilkan table responsive atau card list.
6. Jangan menampilkan terlalu banyak kolom sensitif di list.

### 12.13 Template Bootstrap yang Disarankan

Template modern yang cocok:

1. AdminLTE 4 jika ingin cepat dan familiar untuk tim PHP.
2. Tabler jika ingin tampilan modern, bersih, dan ringan.
3. Sneat Bootstrap 5 jika membutuhkan desain admin yang sangat rapi.
4. CoreUI Bootstrap jika ingin komponen enterprise.

Rekomendasi utama: Tabler atau AdminLTE 4. Tabler lebih modern dan bersih, sedangkan AdminLTE lebih umum di ekosistem PHP dan mudah dipelajari.

### 12.14 Best Practice UX Sistem Aset Kampus

1. Pencarian harus selalu tersedia di dashboard dan halaman listing.
2. Struktur lokasi harus selalu ditampilkan sebagai breadcrumb: Unit/Fakultas -> Gedung -> Lantai -> Ruangan.
3. Pilihan penanggung jawab harus searchable karena data karyawan bisa banyak.
4. Jangan memaksa user mengingat kode; selalu tampilkan kode dan nama.
5. Berikan warning jika menonaktifkan master yang masih dipakai.
6. Gunakan audit trail untuk memberi rasa aman pada operator.
7. Gunakan status dan badge yang konsisten.
8. Buat halaman exception, misalnya Ruangan tanpa PJ, Sub Unit tanpa Ruangan, Gedung tanpa Lantai.

## 13. Rekomendasi Dokumen Lanjutan untuk AI Agent Programmer

### 13.1 Daftar Dokumen Pendukung Ideal

| Dokumen | Fungsi | Penyusun | Kapan Dibutuhkan | Kaitannya dengan Pengembangan |
|---|---|---|---|---|
| PRD | Menjelaskan tujuan produk, scope, user needs, prioritas | Product Owner/System Analyst | Sebelum coding | Menjadi dasar kebutuhan bisnis |
| SRS | Menjelaskan kebutuhan fungsional dan nonfungsional rinci | System Analyst | Sebelum desain teknis | Menjadi kontrak requirement |
| SDD/Technical Spec | Menjelaskan arsitektur, modul, service, struktur CI4 | Solution Architect/Senior Developer | Sebelum coding | Menjadi panduan implementasi |
| ERD | Menggambarkan relasi tabel | Database Designer | Sebelum migration | Mencegah salah desain relasi |
| Data Dictionary | Menjelaskan kolom, tipe, validasi, index | Database Designer | Sebelum migration dan coding model | Membantu AI Agent membuat migration/model |
| Use Case | Menjelaskan interaksi aktor dengan sistem | System Analyst | Sebelum coding controller | Membantu desain flow |
| User Flow | Menggambarkan alur layar dan proses | UI/UX Analyst | Sebelum UI coding | Membantu navigasi dan form |
| Wireframe | Sketsa halaman login, dashboard, master, detail | UI/UX Analyst | Sebelum frontend | Mencegah UI tidak konsisten |
| API Spec | Daftar endpoint, request, response | Backend Developer/Architect | Jika memakai AJAX/API | Membantu integrasi frontend |
| Coding Guide | Standar folder, naming, service, validation, auth | Senior Developer | Sebelum coding | Menjaga konsistensi AI Agent |
| Migration Plan | Urutan migration dan seeder | Database Designer/Developer | Awal coding | Membuat database reproducible |
| Testing Plan | Unit, integration, security, UAT | QA Engineer | Sebelum testing | Menjamin fitur sesuai requirement |
| SOP Master Data | Tata cara input/ubah/nonaktif master | Admin Aset/System Analyst | Sebelum go-live | Mengurangi data ganda |
| Deployment Guide | Cara deploy DEV/UAT/Production | DevOps/IT | Sebelum production | Mengurangi risiko go-live |
| Backup & Recovery Plan | Strategi backup dan restore | IT/DBA | Sebelum production | Melindungi data aset |
| Security Checklist | Checklist CSRF, XSS, SQL injection, password | Security/Developer | Sebelum UAT | Mengurangi celah keamanan |

### 13.2 Urutan Dokumen Kerja dari Awal Sampai Siap Coding

Urutan kerja yang direkomendasikan:

```text
PRD
  -> SRS
  -> ERD
  -> Data Dictionary
  -> UI User Flow
  -> Wireframe
  -> Technical Spec / SDD CodeIgniter 4
  -> API Spec jika diperlukan
  -> Migration Plan
  -> Coding Guide
  -> Test Plan
  -> Deployment Guide
  -> SOP Operasional
```

Untuk proyek Sistem Aset UIR tahap awal, dokumen yang paling wajib sebelum coding adalah:

1. ERD final.
2. Data Dictionary final.
3. Permission Matrix final.
4. Wireframe halaman master data.
5. Coding Guide CodeIgniter 4.
6. Migration dan Seeder Plan.

### 13.3 Paket Instruksi untuk AI Agent Programmer

AI Agent Programmer sebaiknya diberi instruksi ringkas berikut:

```text
Bangun aplikasi CodeIgniter 4 untuk Sistem Informasi Aset UIR.
Gunakan database MySQL `uir_aset`.
Jangan insert/update/delete tabel `tbl_unit_kerja` dan `ylpi_karyawan`.
Buat auth multiuser dengan role-permission.
Buat seed admin awal username admin password myUIR2026, password di-hash.
Buat master Sub Unit, Gedung, Lantai, Ruangan, Kategori, Sub Kategori, Golongan, Merk, Type, Kondisi Barang, Sumber Dana.
Pastikan relasi:
- Gedung -> Unit/Fakultas read-only
- Gedung -> Lantai -> Ruangan
- Unit/Fakultas -> Sub Unit -> Ruangan
- Ruangan -> Penanggung Jawab dari ylpi_karyawan
Gunakan Bootstrap 5 dengan tema hijau UIR modern.
Gunakan migration, seeder, validation, CSRF, audit log, dan soft delete.
```

## 14. Open Questions

Beberapa hal masih perlu dikonfirmasi sebelum coding penuh:

1. Tipe data asli `tbl_unit_kerja.id_unit_kerja` dan `ylpi_karyawan.id_kpe`.
2. Apakah Sub Unit akan diinput manual seluruhnya atau dibuat otomatis default dari setiap Unit/Fakultas.
3. Format kode Gedung, Lantai, Ruangan, Kategori, dan nomor aset baru.
4. Apakah Admin Unit boleh membuat Gedung atau hanya boleh membuat Ruangan/Sub Unit.
5. Apakah nomor HP/WA penanggung jawab boleh dilihat semua user internal atau hanya role tertentu.
6. Apakah production menggunakan satu aplikasi dengan dua koneksi database atau nanti menggunakan API dari sistem kampus.
7. Apakah master Sumber Dana wajib masuk coding tahap awal atau hanya disiapkan untuk fase aset non-bangunan.
8. Apakah aset kendaraan wajib memiliki lokasi deskriptif khusus selain Unit/Sub Unit/PJ.
9. Apakah template Bootstrap yang dipilih adalah Tabler, AdminLTE 4, atau custom Bootstrap 5.

## 15. Kesimpulan

Desain paling aman untuk Sistem Informasi Aset UIR adalah memulai dari fondasi data yang benar. Tahap awal tidak perlu langsung membangun seluruh siklus hidup aset, tetapi harus memastikan auth, permission, master organisasi-lokasi, master klasifikasi, dan referensi read-only berjalan benar.

Dengan struktur `tbl_unit_kerja` dan `ylpi_karyawan` sebagai referensi read-only, serta tabel baru untuk Sub Unit, Gedung, Lantai, Ruangan, dan lookup aset, aplikasi akan siap dikembangkan secara bertahap ke registrasi aset, mutasi, opname, QR publik, histori lokasi, dan pelaporan. Pendekatan ini mengurangi risiko salah desain sejak awal, menjaga kesiapan integrasi production lintas server, dan memberikan fondasi kuat bagi AI Agent Programmer untuk mulai coding CodeIgniter 4 secara terarah.
