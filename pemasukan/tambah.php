<?php
include '../config.php';
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn, "INSERT INTO pemasukan (tanggal, keterangan, jumlah) VALUES ('$tanggal','$keterangan','$jumlah')");
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemasukan</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
        }

        label {
            font-size: 14px;
            color: #495057;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #16a085;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #1abc9c;
            font-size: 14px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Pemasukan</h2>
    <form method="POST">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" required>
        
        <label for="keterangan">Keterangan:</label>
        <input type="text" name="keterangan" required>
        
        <label for="jumlah">Jumlah:</label>
        <input type="number" name="jumlah" step="0.01" required>
        
        <button type="submit">Simpan</button>
    </form>
    
    <a href="index.php" class="back-btn">← Kembali</a>
</div>

</body>
</html>
