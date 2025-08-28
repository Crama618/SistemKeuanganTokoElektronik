<?php
include '../config.php';
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}
$data = mysqli_query($conn, "SELECT * FROM pemasukan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Pemasukan - Toko Elektronik</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #f8fafc;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            color: white;
            font-size: 18px;
            margin-bottom: 30px;
            padding-left: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-left: 5px solid transparent;
            transition: 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
            border-left: 5px solid #1abc9c;
        }

        .sidebar ul li a.active {
            background-color: #1abc9c;
            font-weight: bold;
        }

        .sidebar .logout {
            color: red;
        }

        .main {
            margin-left: 220px;
            padding: 30px;
            width: calc(100% - 220px);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            color: #2c3e50;
        }

        .card-container {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .card {
            flex: 1 1 220px;
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
        }

        .card h3 {
            font-size: 16px;
            color: #6b7280;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }

        h2 {
            margin-top: 50px;
            color: #2c3e50;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            background-color: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #ecf0f1;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #888;
        }

        .btn {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            color: white;
            background-color: #4f46e5;
        }

        .btn:hover {
            background-color: #4338ca;
        }

        .btn-kembali {
            background-color: #6b7280;
        }

        .btn-kembali:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>🛒 Toko Elektronik</h2>
        <ul>
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="../penjualan/index.php">Penjualan</a></li>
            <li><a href="../pembelian/index.php">Pembelian</a></li>
            <li><a href="#" class="active">Pemasukan</a></li>
            <li><a href="../pengeluaran/index.php">Pengeluaran</a></li>
            <li><a href="../laporan.php">Laporan</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Data Pemasukan</h1>
            <div>
                <a href="tambah.php" class="btn">+ Tambah</a>
                <a href="../dashboard.php" class="btn btn-kembali">Kembali</a>
            </div>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Total Pemasukan</h3>
                <p>Rp <?= number_format(mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(jumlah),0) as total FROM pemasukan"))['total'], 0, ',', '.') ?></p>
            </div>
        </div>

        <h2>Riwayat Pemasukan</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                    <a href="hapus.php?id=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus data?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
