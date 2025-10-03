# Audit Log Export Feature

## Overview
Fitur export audit log memungkinkan admin untuk mengekspor data audit log ke format Excel (.xlsx) dengan filter yang sama seperti yang diterapkan di halaman index.

## Features
- Export ke format Excel (.xlsx)
- Mendukung semua filter yang tersedia (tanggal, action, user, search)
- Export hanya data yang sesuai dengan filter yang diterapkan
- File download otomatis dengan nama file yang unik berdasarkan timestamp
- Loading state pada tombol export
- Error handling dengan pesan yang informatif

## How to Use
1. Buka halaman Admin → Audit Log
2. (Opsional) Terapkan filter yang diinginkan menggunakan tombol "Filter"
3. Klik tombol "Export Excel" (hijau)
4. File akan otomatis terdownload

## File Structure
- **Controller**: `app/Http/Controllers/AuditLogController.php` - method `export()`
- **Export Class**: `app/Exports/AuditLogExport.php`
- **Route**: `admin/audit/export` (POST)
- **View**: `resources/views/admin/audit/index.blade.php`

## Excel Output Columns
1. Tanggal - Format: dd/mm/yyyy  
2. Waktu - Format: HH:mm:ss
3. Nama User
4. Email User
5. Role
6. Action
7. Controller
8. Route
9. Method
10. URL
11. IP Address
12. User Agent
13. Status Code
14. Request Data (JSON format)

## Technical Details
- Menggunakan package `maatwebsite/excel` v3.1
- Export menggunakan query yang sama dengan tampilan index untuk konsistensi
- File Excel memiliki styling untuk header (bold, font size 12)
- Auto-sizing kolom untuk readability
- Sheet title: "Audit Log"

## Security
- Hanya admin yang bisa mengakses fitur export
- Menggunakan CSRF protection
- Filter yang sama dengan tampilan web diterapkan pada export

## Error Handling
- Try-catch pada controller untuk menangani error export
- Flash message error jika export gagal
- Loading state pada UI untuk feedback user
