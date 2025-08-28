<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM penjualan WHERE id=$id"));

if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $total_harga = $_POST['total_harga'];

    mysqli_query($conn, "UPDATE penjualan 
                         SET tanggal='$tanggal', nama_barang='$nama_barang', jumlah='$jumlah', total_harga='$total_harga' 
                         WHERE id=$id");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Penjualan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h2>Edit Penjualan</h2>
<form method="post">
    <label>Tanggal:</label><br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br>
    <label>Nama Barang:</label><br>
    <input type="text" name="nama_barang" value="<?= $data['nama_barang'] ?>" required><br>
    <label>Jumlah:</label><br>
    <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>" required><br>
    <label>Total Harga:</label><br>
    <input type="number" name="total_harga" value="<?= $data['total_harga'] ?>" required><br><br>
    <button type="submit" name="submit">Update</button>
</form>
</body>
</html>
