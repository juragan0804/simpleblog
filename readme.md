Tentu! Berikut adalah file README dalam bahasa Indonesia yang mencakup instruksi penggunaan aplikasi dan struktur direktori.

### README.md

```markdown
# Aplikasi Blog Sederhana

Ini adalah aplikasi blog sederhana yang dibangun menggunakan PHP dan MySQL. Aplikasi ini memungkinkan pengguna untuk membuat, mengedit, dan menghapus posting, serta mengomentari posting. Aplikasi ini juga mendukung peran pengguna, di mana admin memiliki lebih banyak izin daripada pengguna biasa.

## Fitur
- Registrasi dan login pengguna
- Membuat, mengedit, dan menghapus posting
- Menambahkan dan menghapus komentar pada posting
- Peran pengguna (admin dan pengguna biasa)
- Desain responsif

## Persyaratan
- Server Nginx
- PHP 7.0 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi

## Instalasi
1. Clone repositori:
   ```bash
   git clone https://github.com/username/simple-blog.git
   ```

2. Navigasi ke direktori proyek:
   ```bash
   cd simple-blog
   ```

3. Impor basis data:
   - Buat basis data MySQL baru.
   - Impor file `simpleblog.sql` ke dalam basis data.

4. Konfigurasi koneksi basis data:
   - Buka `includes/config.php` dan perbarui pengaturan koneksi basis data:
     ```php
     <?php
     if (session_status() == PHP_SESSION_NONE) {
         session_start();
     }

     $host = 'localhost';
     $db = 'simpleblog';
     $user = 'root';
     $pass = '';

     $conn = new mysqli($host, $user, $pass, $db);
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
     ?>
     ```

5. Konfigurasi Nginx:
   - Pastikan file konfigurasi Nginx di `/etc/nginx/sites-available/simple-blog` sudah benar:
     ```nginx
     server {
         listen 80;
         server_name your_domain.com; # Ganti dengan nama domain atau alamat IP Anda

         root /path/to/your/project; # Ganti dengan path ke root direktori proyek Anda
         index index.php index.html index.htm;

         location / {
             try_files $uri $uri/ /index.php?$args;
         }

         location /post {
             rewrite ^/post/([0-9]+)$ /post.php?id=$1 last;
         }

         location /edit_post {
             rewrite ^/edit_post/([0-9]+)$ /edit_post.php?id=$1 last;
         }

         location /delete_post {
             rewrite ^/delete_post/([0-9]+)$ /delete_post.php?id=$1 last;
         }

         location /profile {
             rewrite ^/profile/([0-9]+)$ /profile.php?id=$1 last;
         }

         location /add_comment {
             rewrite ^/add_comment$ /add_comment.php last;
         }

         location /delete_comment {
             rewrite ^/delete_comment/([0-9]+)$ /delete_comment.php?id=$1 last;
         }

         location /login {
             rewrite ^/login$ /login.php last;
         }

         location /logout {
             rewrite ^/logout$ /logout.php last;
         }

         location /register {
             rewrite ^/register$ /register.php last;
         }

         location /about {
             rewrite ^/about$ /about.php last;
         }

         location /home {
             rewrite ^/home$ /index.php last;
         }

         location ~ \.php$ {
             include snippets/fastcgi-php.conf;
             fastcgi_pass unix:/var/run/php/php7.4-fpm.sock; # Ganti dengan versi PHP yang sesuai
             fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
             include fastcgi_params;
         }

         location ~* ^/(edit_post\.php|delete_post\.php|profile\.php|add_comment\.php|delete_comment\.php)$ {
             allow all;
             deny all;
         }

         location /includes {
             deny all;
             return 403;
         }

         location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
             expires max;
             log_not_found off;
         }
     }
     ```

6. Aktifkan konfigurasi Nginx:
   Jika Anda menggunakan konfigurasi di `sites-available`, buat symlink ke `sites-enabled`:
   ```bash
   sudo ln -s /etc/nginx/sites-available/simple-blog /etc/nginx/sites-enabled/
   ```

7. Uji konfigurasi Nginx:
   ```bash
   sudo nginx -t
   ```

8. Muat ulang Nginx:
   ```bash
   sudo systemctl reload nginx
   ```

9. Buka aplikasi di browser Anda:
   ```bash
   http://localhost/simple-blog
   ```

## Struktur Direktori
```
simple-blog/
│
├── includes/
│   ├── config.php         # Konfigurasi koneksi basis data
│   ├── header.php         # Template header
│   ├── footer.php         # Template footer
│
├── css/
│   ├── styles.css         # Stylesheet untuk aplikasi
│
├── .htaccess              # File konfigurasi Apache (tidak digunakan untuk Nginx)
├── index.php              # Halaman utama
├── login.php              # Halaman login
├── logout.php             # Skrip logout
├── register.php           # Halaman registrasi
├── new_post.php           # Halaman untuk membuat posting baru
├── edit_post.php          # Halaman untuk mengedit posting yang ada
├── delete_post.php        # Skrip untuk menghapus posting
├── post.php               # Halaman posting tunggal
├── add_comment.php        # Skrip untuk menambahkan komentar
├── delete_comment.php     # Skrip untuk menghapus komentar
├── manage_users.php       # Halaman admin untuk mengelola pengguna
├── profile.php            # Halaman profil pengguna
├── about.php              # Halaman tentang
```

## Penggunaan

### Registrasi dan Login
- Pengguna dapat mendaftar dengan mengisi formulir registrasi di halaman login.
- Setelah registrasi, pengguna dapat login menggunakan kredensial mereka.

### Membuat Postingan
- Pengguna yang sudah login dapat membuat posting baru dengan mengakses halaman "New Post".

### Mengedit Postingan
- Pengguna dapat mengedit posting mereka sendiri dengan mengakses bagian "Manage Posts" di profil mereka.

### Menghapus Postingan
- Pengguna dapat menghapus posting mereka sendiri.
- Admin dapat menghapus posting siapa saja.

### Menambahkan dan Menghapus Komentar
- Pengguna yang sudah login dapat menambahkan komentar pada postingan.
- Pengguna dapat menghapus komentar mereka sendiri serta komentar dari pengunjung.
- Admin dapat menghapus komentar siapa saja.

### Peran Pengguna
- Pengguna biasa dapat membuat, mengedit, dan menghapus posting dan komentar mereka sendiri.
- Admin memiliki izin tambahan untuk mengelola semua posting dan komentar, serta mengelola pengguna.

## Lisensi
Proyek ini dibuat untuk tubes VAPT 



```

