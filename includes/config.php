<?php
$host = 'localhost';  // Ganti dengan host database Anda jika berbeda
$db = 'simpleblog';   // Nama database
$user = 'root';       // Username database
$pass = '';           // Password database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
