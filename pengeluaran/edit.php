<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengeluaran WHERE id=$id"));

if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn, "UPDATE pengeluaran SET tanggal='$tanggal', keterangan='$keterangan', jumlah='$jumlah' WHERE id=$id");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pengeluaran</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h2>Edit Pengeluaran</h2>
<form method="post">
    <label>Tanggal:</label><br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br>
    <label>Keterangan:</label><br>
    <input type="text" name="keterangan" value="<?= $data['keterangan'] ?>" required><br>
    <label>Jumlah:</label><br>
    <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>" required><br><br>
    <button type="submit" name="submit">Update</button>
</form>
</body>
</html>
