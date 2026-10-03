# Lion Group - Enterprise File Management System (FMS)

A modern, robust, and secure **Full Stack File Management System (FMS)** built with **Laravel 11**, **Vue.js 3 (Inertia.js)**, **Tailwind CSS**, and **PostgreSQL** for managing and archiving enterprise corporate documents.

---

## 📑 Daftar Isi
- [1. Fitur Utama & Functional Requirements](#1-fitur-utama--functional-requirements)
- [2. Reusable Components Suite (Frontend Vue 3)](#2-reusable-components-suite-frontend-vue-3)
- [3. Bonus Features Implemented](#3-bonus-features-implemented)
- [4. Technology Stack](#4-technology-stack)
- [5. Arsitektur & Clean Code Structure](#5-arsitektur--clean-code-structure)
- [6. Akun Login Default](#6-akun-login-default)
- [7. Panduan Instalasi & Menjalankan Project](#7-panduan-instalasi--menjalankan-project)
  - [Opsi A: Menggunakan Local PHP & Node.js](#opsi-a-menggunakan-local-php--nodejs)
  - [Opsi B: Menggunakan Docker Compose](#opsi-b-menggunakan-docker-compose)
- [8. Pengujian / Automated Tests (Pest PHP)](#8-pengujian--automated-tests-pest-php)
- [9. RESTful API Reference](#9-restful-api-reference)

---

## 1. Fitur Utama & Functional Requirements

### 🔐 1.1 Authentication & Role-Based Access Control (RBAC)
- **Built-in Authentication** menggunakan Laravel Breeze + Inertia Vue.
- **2 Peran (Roles)**:
  - **Administrator (`administrator`)**: Memiliki izin penuh mengelola folder, upload file, edit metadata, hapus file/folder, kelola department, melihat audit log, dan statistik.
  - **Viewer (`viewer`)**: Memiliki hak akses melihat folder (*read-only*), melihat detail file, mengunduh file, *search & filter*, dan *live preview*. Ditolak (*403 Forbidden*) jika mencoba mengubah atau menghapus data.

### 📁 1.2 Folder Management (Hierarchical / Multi-level)
- Struktur folder bertingkat tanpa batas kedalaman (*unlimited nesting depth*).
- Root folder memiliki `parent_id = NULL`.
- Fitur Administrator:
  - **Create Folder** (bisa memilih parent folder secara dinamis melalui visual selector tree).
  - **Rename & Move Folder** (dilengkapi validasi pencegahan *circular ancestry*).
  - **Delete Folder** (*soft deletes* dengan *cascade logic* untuk subfolder dan file).

### 📄 1.3 File Management
- Administrator dapat mengunggah file ke root atau subfolder tertentu.
- **Field Wajib**:
  - `Title` (Judul dokumen)
  - `Department` (Departemen pemilik/metadata)
  - `File` (Binary dokumen, max 50MB)
- Fitur Administrator:
  - **Edit Informasi Dokumen**: Judul, departemen, lokasi folder, catatan/deskripsi, dan opsi penggantian file (*replace file*).
  - **Hapus Dokumen**: *Soft delete* dengan audit trail.

### 🔍 1.4 File Detail (Requirement 3.4)
Menampilkan seluruh informasi metadata yang dipersyaratkan:
- **Folder** (beserta link navigasi breadcrumbs)
- **Nama File** (nama asli file fisik)
- **Title** (judul dokumen resmi)
- **Department** (badge departemen)
- **Uploaded By** (nama user pengunggah)
- **Upload Date** (tanggal & waktu upload format terstruktur)
- **File Size & Format** (otomatis dikonversi ke B / KB / MB / GB)
- **Download Counter** (jumlah unduhan)
- **Direct Previewer** (Inline renderer PDF, Image, Code/Text)

### 🔎 1.5 Search & Multi-criteria Filter
- **Fulltext Search**: Pencarian real-time berdasarkan *Title*, *File Name*, dan *Description*.
- **Filter Departemen**: Filter dokumen berdasarkan departemen asal.
- **Filter Tipe Dokumen**: PDF, Gambar (PNG/JPG/SVG), Spreadsheet (CSV/XLS), Dokumen Teks.
- **Sorting**: Urutkan berdasarkan tanggal terbaru, abjad judul, ukuran file, atau jumlah unduhan.
- **Tampilan Fleksibel**: Toggle tampilan *Grid View* atau *Table View*.

### 🏢 1.6 Department Management
- Administrator dapat:
  - **Create Department** (Nama, Kode/Singkatan unik seperti `IT`, `HR`, `FIN`, `OPS`, deskripsi, dan warna tema badge).
  - **Update Department**.
  - **Delete Department** (*Soft delete*).
- Digunakan sebagai metadata wajib pada setiap file dokumen.

### 📊 1.7 Dashboard
- **Total Folders** Metric Card
- **Total Files** Metric Card
- **Total Departments** Metric Card
- **Total Storage Used** (Kapasitas penyimpanan cloud/lokal yang terpakai)
- **10 File Terbaru** (Tabel interaktif dokumen terbaru dengan aksi cepat preview & download)
- **Department Breakdown** (Distribusi jumlah dokumen per departemen)
- **Recent Activities** (Widget aktivitas audit terbaru)

---

## 2. Reusable Components Suite (Frontend Vue 3)

Komponen frontend dirancang modular, berorientasi *Atomic Design*, mendukung *Dark Mode*, dan terletak di `resources/js/Components/UI/`:

| Komponen | Path | Deskripsi & Kegunaan |
| :--- | :--- | :--- |
| **Button** | `Components/UI/Button.vue` | Tombol interaktif dengan 7 varian (`primary`, `secondary`, `danger`, `warning`, `success`, `outline`, `ghost`), 4 ukuran (`xs`, `sm`, `md`, `lg`), status `loading` spinner otomatis, dan slot icon. |
| **Badge** | `Components/UI/Badge.vue` | Label status & departemen dengan berbagai tema warna (`blue`, `emerald`, `amber`, `rose`, `purple`, `sky`, `indigo`), dot indicator, dan ukuran. |
| **Card** | `Components/UI/Card.vue` | Wadah container fleksibel dengan slot `header`, default body, dan `footer`, opsi padding, dan hover effects. |
| **StatCard** | `Components/UI/StatCard.vue` | Kartu metrik analitik dashboard dengan ikon dinamis, label, nilai, dan background aksen warna. |
| **Modal** | `Components/UI/Modal.vue` | Modal dialog animasi smooth, backdrop blur, tombol close, escape handler, dan varian lebar (`sm` hingga `5xl`). |
| **ConfirmModal** | `Components/UI/ConfirmModal.vue` | Modal konfirmasi aksi destruktif/penting (seperti delete) dengan loading state pencegah double-click. |
| **Breadcrumb** | `Components/UI/Breadcrumb.vue` | Navigasi hirarki folder bertingkat interaktif dengan ikon Home dan Folder. |
| **SearchBar** | `Components/UI/SearchBar.vue` | Input pencarian instan dilengkapi auto debounce dan tombol clear input. |
| **SelectInput** | `Components/UI/SelectInput.vue` | Dropdown select seragam dengan styling Tailwind dark & light mode. |
| **TextareaInput** | `Components/UI/TextareaInput.vue` | Textarea seragam dengan auto-focus dan styling responsif. |
| **DragDropUpload** | `Components/UI/DragDropUpload.vue` | Zona unggah interaktif *Drag & Drop* dengan deteksi drag-over, preview kartu file, format ukuran file, dan validasi limit ukuran. |
| **FilePreviewModal** | `Components/UI/FilePreviewModal.vue` | Penampil dokumen inline kaya fitur: PDF embedded iframe, penampil gambar resolusi penuh, teks/CSV renderer, dan fallback download. |
| **FolderCard** | `Components/UI/FolderCard.vue` | Kartu visual folder menampilkan nama, jumlah subfolder, jumlah file, dan dropdown aksi edit/delete bagi Admin. |
| **FileCard** | `Components/UI/FileCard.vue` | Kartu visual dokumen untuk Grid View dengan metadata, badge departemen, aksi cepat preview/download/edit/delete. |
| **FileRow** | `Components/UI/FileRow.vue` | Baris tabel dokumen untuk Table View dengan ikon format file, ukuran, departemen, dan menu dropdown aksi. |
| **Pagination** | `Components/UI/Pagination.vue` | Komponen navigasi pagination Inertia dengan info total records. |
| **EmptyState** | `Components/UI/EmptyState.vue` | Tampilan placeholder ramah pengguna ketika data kosong disertai tombol aksi cepat (*call to action*). |
| **ToastNotification** | `Components/UI/ToastNotification.vue` | Notifikasi popup mengambang (*toast alert*) otomatis membaca session flash Laravel (`success`, `error`, `warning`, `info`). |

---

## 3. Bonus Features Implemented

- [x] **Hierarchical Folder Breadcrumbs**: Navigasi hirarki dinamis dari root hingga subfolder terdalam.
- [x] **Live PDF & Image Preview**: Preview dokumen langsung di browser tanpa perlu mengunduh terlebih dahulu.
- [x] **Drag & Drop Upload**: Fitur drag-and-drop file upload yang interaktif dan mudah digunakan.
- [x] **System Activity Audit Log**: Pencatatan riwayat aksi (unggah, unduh, ubah, hapus) beserta user, waktu, dan IP address.
- [x] **Soft Deletes**: Fitur keamanan penghapusan dokumen, folder, dan departemen tanpa kehilangan data permanen.
- [x] **Comprehensive Automated Tests (Pest PHP)**: 44 unit & feature tests mencakup autentikasi, RBAC authorization, CRUD folder, upload file, unduhan, dan departemen (100% PASS).
- [x] **Docker Container Support**: Dilengkapi `Dockerfile` dan `docker-compose.yml` untuk instant deployment multi-container (PHP 8.2 + PostgreSQL 16).
- [x] **Dark Mode Theme**: Dukungan class-based dark mode dengan tombol toggle dan persistensi di local storage.
- [x] **Clean Architecture (Service Layer Pattern)**: Pemisahan logika bisnis dari Controller ke `FolderService`, `FileService`, `DepartmentService`, `DashboardService`, serta validasi terisolasi di `FormRequest` dan `Policies`.
- [x] **Postman API Collection**: Ekspor koleksi JSON endpoint REST API siap uji (`Lion_FMS_API.postman_collection.json`).
- [x] **1-Click Quick Demo Login**: Tombol auto-fill akun Admin & Viewer pada halaman login untuk memudahkan reviewer.

---

## 4. Technology Stack

| Layer | Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x |
| **Frontend Framework** | Vue.js 3 (Composition API, `<script setup>`) |
| **Fullstack Bridge** | Inertia.js (Server-driven Single Page Application) |
| **Styling & Theme** | Tailwind CSS (dengan Dark Mode support) |
| **Database** | PostgreSQL 16 |
| **Icon Library** | Lucide Icons (`lucide-vue-next`) |
| **Testing Suite** | Pest PHP & PHPUnit |
| **Containerization** | Docker & Docker Compose |

---

## 5. Arsitektur & Clean Code Structure

Struktur folder mengikuti praktik standar *Clean Architecture*:

```
app/
├── Http/
│   ├── Controllers/           # RESTful Controllers tipis (Inertia & JSON API)
│   │   ├── DashboardController.php
│   │   ├── FolderController.php
│   │   ├── FileController.php
│   │   ├── DepartmentController.php
│   │   └── ActivityLogController.php
│   ├── Middleware/
│   │   ├── EnsureUserHasRole.php    # Middleware pengecekan peran (RBAC)
│   │   └── HandleInertiaRequests.php # Shared flash & auth data
│   └── Requests/              # Validasi terisolasi (FormRequest)
│       ├── Folder/
│       ├── File/
│       └── Department/
├── Models/                    # Eloquent Models & Relasi Database
│   ├── User.php
│   ├── Department.php
│   ├── Folder.php
│   ├── File.php
│   └── ActivityLog.php
├── Policies/                  # Otorisasi hak akses (Gates / Policies)
│   ├── FolderPolicy.php
│   ├── FilePolicy.php
│   └── DepartmentPolicy.php
└── Services/                  # Business Logic & Data Transformation Layer
    ├── FolderService.php
    ├── FileService.php
    ├── DepartmentService.php
    └── DashboardService.php

resources/js/
├── Components/
│   ├── UI/                    # 18 Reusable Vue Components
│   ├── File/                  # Modals upload & edit file
│   ├── Folder/                # Modals folder management
│   └── Department/            # Modals department management
├── Layouts/
│   └── AuthenticatedLayout.vue # Layout utama dengan navbar, search & dark mode
└── Pages/
    ├── Dashboard.vue          # Halaman Dashboard
    ├── Folders/Index.vue      # Halaman Folder Browser bertingkat
    ├── Files/Index.vue        # Halaman Search & Explorer Dokumen
    ├── Files/Show.vue         # Halaman Detail Dokumen (Req 3.4)
    ├── Departments/Index.vue  # Halaman Kelola Departemen
    ├── ActivityLogs/Index.vue # Halaman Audit Trail
    └── Auth/Login.vue         # Halaman Login dengan Demo Fill
```

---

## 6. Akun Login Default

Database Seeder telah menyediakan 2 akun pengguna sesuai dengan spesifikasi peran:

### 👑 Administrator
- **Email**: `admin@example.com`
- **Password**: `password`
- **Hak Akses**: Kelola Folder (Create/Rename/Delete), Upload File, Edit Metadata File, Hapus File, Kelola Departemen, Melihat Dashboard & Audit Trail.

### 👤 Viewer
- **Email**: `viewer@example.com`
- **Password**: `password`
- **Hak Akses**: Melihat Folder (*Read-only*), Melihat Detail File, Unduh File, Filter & Pencarian Dokumen. *Tidak dapat memanipulasi data.*

---

## 7. Panduan Instalasi & Menjalankan Project

### Opsi A: Menggunakan Local PHP & Node.js

#### 1. Prasyarat:
- PHP >= 8.2 (dengan ekstensi `pdo_pgsql`, `pgsql`, `gd`, `zip`)
- PostgreSQL 14 / 16
- Composer
- Node.js >= 18 & NPM

#### 2. Setup Environment:
```bash
# Clone repository & masuk ke direktori project
cd test-lion-group

# Salin konfigurasi environment
cp .env.example .env

# Generate Application Key
php artisan key:generate
```

Pastikan konfigurasi PostgreSQL di `.env` sudah sesuai dengan kredensial mesin Anda:
```ini
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fms_lion_group
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

#### 3. Install Dependencies & Build Frontend:
```bash
# Install PHP dependencies
composer install

# Install NPM dependencies & build Vite assets
npm install
npm run build
```

#### 4. Jalankan Migration, Storage Link & Database Seeder:
```bash
# Hubungkan storage public
php artisan storage:link

# Jalankan migration beserta seeders
php artisan migrate:fresh --seed
```

#### 5. Jalankan Web Server:
```bash
# Jalankan web server Laravel
php artisan serve

# Aplikasi dapat diakses di: http://localhost:8000
```
*(Opsional untuk mode development: jalankan `npm run dev` pada terminal terpisah).*

---

### Opsi B: Menggunakan Docker Compose

Jika ingin menjalankan aplikasi secara instan menggunakan Docker tanpa perlu menginstal PHP atau PostgreSQL di host:

```bash
# Jalankan container backend dan PostgreSQL
docker compose up -d --build

# Jalankan seeder di dalam container
docker compose exec app php artisan db:seed

# Buka browser di http://localhost:8000
```

Untuk mematikan container:
```bash
docker compose down
```

---

## 8. Pengujian / Automated Tests (Pest PHP)

Project ini dilengkapi dengan suite pengujian otomatis menyeluruh (*Unit & Feature Tests*) untuk memverifikasi fungsionalitas dan keamanan otorisasi:

```bash
# Jalankan seluruh test suite
./vendor/bin/pest
```

Hasil pengujian mencakup:
- `RoleAuthorizationTest`: Memastikan Administrator memiliki akses penuh dan Viewer diblokir (*403 Forbidden*) dari operasi mutasi.
- `FolderManagementTest`: Pengujian CRUD folder, struktur parent-child, dan pencegahan *circular nesting*.
- `FileManagementTest`: Pengujian validasi field wajib, upload, edit metadata, increment download count, preview stream, dan soft delete.
- `DepartmentManagementTest`: Pengujian CRUD departemen dan aturan keunikan *code* & *name*.
- `DashboardTest`: Verifikasi rendering props agregasi statistik dan file terbaru di Inertia.

---

## 9. RESTful API Reference

Endpoint API tersedia pada prefix `/api/v1` dan menerima header `Accept: application/json`:

| Method | Endpoint | Deskripsi | Otorisasi |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/dashboard` | Mengambil data agregasi statistik & 10 file terbaru | Auth |
| `GET` | `/api/v1/folders` | Mengambil daftar folder root dan tree struktur | Auth |
| `GET` | `/api/v1/folders/{id}` | Mengambil detail folder dan isi subfolder/file | Auth |
| `POST` | `/api/v1/folders` | Membuat folder baru | Admin Only |
| `PUT` | `/api/v1/folders/{id}` | Mengubah nama / memindahkan folder | Admin Only |
| `DELETE` | `/api/v1/folders/{id}` | Menghapus folder beserta anakannya | Admin Only |
| `GET` | `/api/v1/files` | Pencarian & filter dokumen (`?search=&department_id=&type=`) | Auth |
| `GET` | `/api/v1/files/{id}` | Mengambil detail metadata dokumen | Auth |
| `GET` | `/api/v1/files/{id}/download` | Mengunduh file fisik dan mencatat hitungan unduhan | Auth |
| `GET` | `/api/v1/files/{id}/preview` | Stream file inline untuk preview browser | Auth |
| `POST` | `/api/v1/files` | Mengunggah dokumen baru (*multipart/form-data*) | Admin Only |
| `PUT` | `/api/v1/files/{id}` | Mengubah metadata dokumen / ganti file | Admin Only |
| `DELETE` | `/api/v1/files/{id}` | Menghapus dokumen (*soft delete*) | Admin Only |
| `GET` | `/api/v1/departments` | Mengambil daftar departemen dan jumlah dokumen | Auth |
| `POST` | `/api/v1/departments` | Membuat departemen baru | Admin Only |
| `PUT` | `/api/v1/departments/{id}` | Mengubah data departemen | Admin Only |
| `DELETE` | `/api/v1/departments/{id}` | Menghapus departemen | Admin Only |
| `GET` | `/api/v1/activity-logs` | Mengambil riwayat log audit aktivitas | Admin Only |

File Postman collection siap pakai: `Lion_FMS_API.postman_collection.json`.

---

## 10. Lisensi & Author

Dikembangkan untuk **Technical Test - Full Stack Laravel Developer (Lion Group)**.
Semua hak cipta dilindungi undang-undang.
