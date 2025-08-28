<?php
include '../config.php';
$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM barang WHERE id='$id'");
$data = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $nama       = $_POST['nama'];
    $deskripsi  = $_POST['deskripsi'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok       = $_POST['stok'];
    $kategori   = $_POST['kategori'];

    $query = "UPDATE barang SET nama='$nama', deskripsi='$deskripsi', harga_beli='$harga_beli', 
              harga_jual='$harga_jual', stok='$stok', kategori='$kategori' WHERE id='$id'";
    mysqli_query($conn, $query);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Barang</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f0f4f8;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1.8px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
            resize: vertical;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: #4f46e5;
            outline: none;
        }
        textarea {
            min-height: 80px;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        button, .btn-back {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s;
        }
        button:hover, .btn-back:hover {
            background-color: #4338ca;
        }
        .btn-back {
            background-color: #6b7280;
        }
        .btn-back:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Barang</h2>
        <form method="POST" autocomplete="off">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi"><?= htmlspecialchars($data['deskripsi']) ?></textarea>

            <label for="harga_beli">Harga Beli</label>
            <input type="number" id="harga_beli" name="harga_beli" value="<?= htmlspecialchars($data['harga_beli']) ?>" required>

            <label for="harga_jual">Harga Jual</label>
            <input type="number" id="harga_jual" name="harga_jual" value="<?= htmlspecialchars($data['harga_jual']) ?>" required>

            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($data['stok']) ?>" required>

            <label for="kategori">Kategori</label>
            <input type="text" id="kategori" name="kategori" value="<?= htmlspecialchars($data['kategori']) ?>">

            <div class="btn-group">
                <a href="barang.php" class="btn-back">Kembali</a>
                <button type="submit" name="submit">Update</button>
            </div>
        </form>
    </div>
</body>
</html>
