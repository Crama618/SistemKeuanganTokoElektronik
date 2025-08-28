<?php
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM pemasukan WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn, "UPDATE pemasukan SET tanggal='$tanggal', keterangan='$keterangan', jumlah='$jumlah' WHERE id=$id");
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pemasukan</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h2>Edit Pemasukan</h2>
<form method="POST">
    <label>Tanggal</label><br>
    <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required><br>
    <label>Keterangan</label><br>
    <input type="text" name="keterangan" value="<?= $data['keterangan'] ?>" required><br>
    <label>Jumlah</label><br>
    <input type="number" name="jumlah" step="0.01" value="<?= $data['jumlah'] ?>" required><br>
    <button type="submit">Update</button>
</form>
</body>
</html>
