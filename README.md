# Absensi RS
Aplikasi Web Absensi RS

## Konfigurasi Database
File config database : "src/settings.php"

File import sql : "absensi.sql"

File update query : "update_query.sql" (sebelum menjalankan update query, pastikan data di database di-backup terlebih dahulu agar tidak hilang, setelah update query, jalankan import data yang barusan di-backup itu)

DATA DEMO : ada di folder "DATA_DEMO", drop/remove semua "table" yg ada di database terlebih dahulu, kemudian import file sql-nya melalui terminal "cmd" dengan perintah ini : mysql -u root -p [nama_db] < [nama_file_import.sql]

## Login Superadmin
username : superadmin

password : rahasia1234

## Akses URL
http://localhost/[ROOT_DIR]/public
