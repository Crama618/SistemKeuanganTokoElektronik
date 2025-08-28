<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../penjualan/index.php");
    exit();
}

include '../config.php';

// Cek apakah ID ada dan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("DELETE FROM penjualan WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" untuk integer

    // Eksekusi query
    if ($stmt->execute()) {
        // Redirect ke index.php setelah berhasil menghapus
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error; // Tampilkan error jika gagal
    }

    // Tutup prepared statement
    $stmt->close();
} else {
    // Jika ID tidak valid, redirect ke halaman index
    header("Location: index.php");
    exit();
}

// Tutup koneksi
$conn->close();
?>
