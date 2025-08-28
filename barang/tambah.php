<?php
include '../config.php';

if (isset($_POST['submit'])) {
    $nama       = $_POST['nama'];
    $deskripsi  = $_POST['deskripsi'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok       = $_POST['stok'];
    $kategori   = $_POST['kategori'];

    $query = "INSERT INTO barang (nama, deskripsi, harga_beli, harga_jual, stok, kategori) 
              VALUES ('$nama', '$deskripsi', '$harga_beli', '$harga_jual', '$stok', '$kategori')";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #111827;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
        }

        form input[type="text"],
        form input[type="number"],
        form textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background-color: #f9fafb;
        }

        form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit {
            background-color: #4f46e5;
            color: white;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .btn-back {
            background-color: #e5e7eb;
            color: #111827;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back:hover {
            background-color: #d1d5db;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Tambah Barang</h1>
    <form method="POST">
        <label for="nama">Nama Barang</label>
        <input type="text" id="nama" name="nama" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi"></textarea>

        <label for="harga_beli">Harga Beli</label>
        <input type="number" id="harga_beli" name="harga_beli" required>

        <label for="harga_jual">Harga Jual</label>
        <input type="number" id="harga_jual" name="harga_jual" required>

        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" required>

        <label for="kategori">Kategori</label>
        <input type="text" id="kategori" name="kategori">

        <div class="btn-group">
            <a href="index.php" class="btn btn-back">← Kembali</a>
            <button type="submit" name="submit" class="btn btn-submit">Simpan Barang</button>
        </div>
    </form>
</div>

</body>
</html>
