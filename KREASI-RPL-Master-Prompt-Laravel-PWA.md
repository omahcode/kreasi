# KREASI RPL
## Master Prompt & Technical Specification

**Karya RPL Edukasi dan Aplikasi Siswa Inovatif (PjBL) & Bimbingan PKL Siswa Kelas XII**

Version: 1.0  
Technology: Laravel 12 + Livewire + Tailwind CSS + PWA  
Database: MySQL

---

# INSTRUCTION FOR AI DEVELOPER

Anda bertindak sebagai Senior Laravel Developer, System Architect, UI/UX Designer, dan Database Engineer.

Buat aplikasi web production-ready bernama **KREASI RPL**.

Jangan membuat prototype sederhana. Bangun sistem yang memiliki struktur rapi, scalable, aman, dan mudah dikembangkan sekolah.

Gunakan Laravel sebagai fullstack framework.

**Tidak menggunakan React.js, Vue.js, Angular, atau frontend framework terpisah.**

---

# 1. PRODUCT DESCRIPTION

KREASI RPL adalah platform digital khusus Jurusan Rekayasa Perangkat Lunak (RPL).

Fungsi utama:

1. Platform Project Based Learning (PjBL)
2. Manajemen proyek siswa
3. Dokumentasi hasil karya siswa
4. Monitoring progres pekerjaan
5. Pengumpulan tugas
6. Penilaian guru
7. Bimbingan laporan PKL kelas XII
8. Portofolio digital siswa


Aplikasi harus terasa seperti:

- Google Classroom modern
- Notion education workspace
- Moodle modern
- Dashboard startup teknologi


---

# 2. TECHNOLOGY STACK


## Backend

Laravel 12

PHP 8.3+

Database:
MySQL


## Frontend

Gunakan:

- Blade Template
- Livewire
- Alpine.js
- Tailwind CSS


## Package Recommended

Authentication:
Laravel Breeze

Permission:
Spatie Laravel Permission

Excel Import:
Laravel Excel

File Management:
Laravel Storage


---

# 3. APPLICATION TYPE

Buat sebagai:

Progressive Web Application (PWA)


Requirement:

- Installable pada Android
- Responsive desktop/mobile
- Offline cache
- Web manifest
- Service worker
- Mobile friendly


---

# 4. BRANDING DESIGN


Gunakan identitas logo:

KREASI RPL


Konsep:

- Teknologi
- Kreativitas
- Pendidikan
- Inovasi


## Color Palette


Primary Blue:

#064A8E
#0B5FA5


Secondary Turquoise:

#13B8B0
#22C7C0


Accent:

#FFB703
#FFC857


Background:

#F8FAFC
#FFFFFF


UI harus menggunakan warna tersebut secara konsisten.


---

# 5. DESIGN SYSTEM


Style:

- Modern
- Clean
- Professional
- Friendly untuk siswa


Gunakan:

- Rounded card
- Soft shadow
- Gradient ringan
- Icon illustration
- Progress visualization


Komponen:

- Sidebar
- Navbar
- Dashboard card
- Timeline
- Modal
- Notification dropdown
- Table modern


Mobile:

Gunakan bottom navigation.


---

# 6. USER ROLE


## ADMIN


Permission:

- Kelola user
- Kelola guru
- Kelola siswa
- Kelola kelas
- Import Excel
- Monitoring aktivitas
- Laporan


---

## GURU


Permission:

- Membuat proyek PjBL
- Membuat kelompok
- Menentukan anggota
- Review tugas
- Memberikan nilai
- Membimbing PKL


---

## KETUA KELOMPOK


Permission:

- Melihat anggota
- Mengatur progres kelompok
- Mengisi laporan perkembangan


---

## SISWA


Permission:

- Melihat proyek
- Upload pekerjaan
- Membuat logbook
- Melihat komentar
- Mengirim revisi


---

# 7. MAIN MODULE


# MODULE AUTHENTICATION

Features:

- Login
- Logout
- Forgot password
- Role permission


---

# MODULE DASHBOARD


Admin:

- Total siswa
- Total guru
- Total proyek
- Statistik PKL
- Aktivitas terakhir


Guru:

- Proyek aktif
- Pengumpulan masuk
- Revisi menunggu


Siswa:

- Proyek saya
- Deadline
- Progress
- Status PKL


---

# MODULE PjBL


## Project Creation


Guru dapat membuat:

- Judul proyek
- Deskripsi
- Tujuan
- Instruksi
- File lampiran
- Deadline
- Rubrik penilaian


Mode:

## Group Project

Guru menentukan:

- Ketua
- Anggota


## Individual Project

Siswa mengerjakan sendiri.


---

## Project Activity


Fitur:

- Logbook
- Upload file
- Upload gambar
- Progress
- Comment
- Revision
- Score


---

# MODULE PKL


Khusus:

Kelas XII


Workflow:

Siswa mengajukan judul

↓

Guru melakukan review

↓

Revisi

↓

Persetujuan

↓

Upload Bab I-V

↓

Review

↓

Final Approval


---

# PKL FEATURES


Setiap bab memiliki:

- File upload
- Version history
- Comment
- Status


Status:

Draft

Submitted

Revision Required

Approved


---

# 8. DATABASE DESIGN


## users

id
name
email
password
role
status


## students

id
user_id
nis
class_id


## teachers

id
user_id
teacher_number


## classes

id
name
grade_level
academic_year_id


## academic_years

id
name
is_active


## projects

id
title
description
type
status
deadline
created_by


## project_members

id
project_id
student_id
role


## project_logs

id
project_id
student_id
description
image
created_at


## submissions

id
project_id
student_id
file
version
status


## reviews

id
submission_id
teacher_id
comment
score


## pkl_reports

id
student_id
title
company_name
status


## pkl_chapters

id
report_id
chapter_number
file
status


## notifications

id
user_id
title
message
read_at


## audit_logs

id
user_id
action
description
created_at


---

# 9. IMPORT EXCEL


Admin dapat:

- Upload Excel siswa
- Preview data
- Validasi kesalahan
- Import


Kolom:

- Nama
- NIS
- Email
- Kelas


---

# 10. SECURITY


Implementasikan:

- Authentication
- Middleware
- Permission
- CSRF Protection
- Validation
- File security
- Private storage
- Audit log


Jangan percaya data dari client tanpa validasi server.


---

# 11. LARAVEL STRUCTURE


app/

Models/

Livewire/

Services/

Policies/

Notifications/


resources/

views/

components/

layouts/


database/

migrations/

seeders/


routes/

web.php


---

# 12. LIVEWIRE COMPONENT


Buat component:

Auth

Dashboard

Projects

ProjectDetail

ProjectSubmission

ProjectReview

PKLManagement

UserManagement

ImportStudent

Notification


---

# 13. DEVELOPMENT ORDER


Build secara bertahap:


PHASE 1

Foundation:

- Laravel setup
- Database
- Authentication
- Role


PHASE 2

Academic:

- User
- Teacher
- Student
- Class


PHASE 3

PjBL:

- Project
- Group
- Submission
- Review


PHASE 4

PKL:

- Title submission
- Chapter management
- Revision


PHASE 5

PWA:

- Manifest
- Service worker
- Install


PHASE 6

Production:

- Security test
- Optimization
- Deployment


---

# 14. DEPLOYMENT


Support:

Shared Hosting Laravel

dan VPS.


Requirement:

- PHP-FPM
- Nginx/Apache
- MySQL
- HTTPS


---

# 15. FINAL EXPECTATION


Hasil akhir:

KREASI RPL menjadi platform digital sekolah yang:

- Modern
- Cepat
- Responsive
- Installable
- Aman
- Mudah dikembangkan


Tujuan utama:

"Membangun rumah digital kreativitas siswa RPL untuk belajar, membuat karya, mendokumentasikan proyek, dan mengelola PKL secara profesional."
