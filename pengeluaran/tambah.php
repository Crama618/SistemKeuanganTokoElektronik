<?php
include '../config.php';

if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn, "INSERT INTO pengeluaran (tanggal, keterangan, jumlah) VALUES ('$tanggal', '$keterangan', '$jumlah')");
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah Pengeluaran</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="date"],
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="date"]:focus,
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #1abc9c;
            outline: none;
        }
        button {
            width: 100%;
            background-color: #1abc9c;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #159e86;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Pengeluaran</h2>
        <form method="post">
            <label for="tanggal">Tanggal:</label>
            <input type="date" id="tanggal" name="tanggal" required />
            
            <label for="keterangan">Keterangan:</label>
            <input type="text" id="keterangan" name="keterangan" required />
            
            <label for="jumlah">Jumlah:</label>
            <input type="number" id="jumlah" name="jumlah" required />
            
            <button type="submit" name="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
