<?php
include '../config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION['role'];

if ($role !== 'admin' && $role !== 'kasir') {
    header("Location: ../dashboard.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM penjualan");

$totalPenjualanHariIni = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT IFNULL(SUM(total_harga), 0) as total FROM penjualan WHERE DATE(tanggal) = CURDATE()"
))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Data Penjualan - Toko Elektronik</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            margin: 0;
            display: flex;
            background-color: #f8fafc;
            height: 100vh;
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
            margin-bottom: 12px;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: #ecf0f1;
            padding: 10px;
            display: block;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #1abc9c;
            color: white;
        }
        .sidebar .logout {
            color: red;
        }

        .main {
            flex: 1;
            padding: 30px;
            margin-left: 220px;
            width: calc(100% - 220px);
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topbar h1 {
            font-size: 24px;
            color: #111827;
        }

        .ringkasan {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .ringkasan .card {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            width: 220px;
            text-align: center;
        }

        .ringkasan .card h3 {
            font-size: 18px;
            color: #111827;
        }

        .ringkasan .card p {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
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

        table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        th {
            background-color: #f1f5f9;
            color: #374151;
        }
        td {
            color: #4b5563;
        }

        td a {
            padding: 6px 10px;
            border-radius: 5px;
            color: white;
            font-size: 13px;
            text-decoration: none;
            margin-right: 5px;
        }
        .btn-edit {
            background-color: #3b82f6;
        }
        .btn-edit:hover {
            background-color: #2563eb;
        }
        .btn-hapus {
            background-color: #ef4444;
        }
        .btn-hapus:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>🛒 Toko Elektronik</h2>
    <ul>
        <li><a href="../dashboard.php">Dashboard</a></li>

        <?php if ($role === 'admin'): ?>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="../penjualan/index.php" class="active">Penjualan</a></li>
            <li><a href="../pembelian/index.php">Pembelian</a></li>
            <li><a href="../pemasukan/index.php">Pemasukan</a></li>
            <li><a href="../pengeluaran/index.php">Pengeluaran</a></li>
        <?php elseif ($role === 'kasir'): ?>
            <li><a href="../penjualan/index.php" class="active">Penjualan</a></li>
        <?php endif; ?>

        <li><a href="../laporan.php">Laporan</a></li>
        <li><a href="../logout.php" class="logout">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="topbar">
        <h1>Data Penjualan</h1>
        <div>
            <a href="tambah.php" class="btn">+ Tambah Penjualan</a>
            <a href="../dashboard.php" class="btn btn-kembali">Kembali</a>
        </div>
    </div>

    <div class="ringkasan">
        <div class="card">
            <h3>Total Penjualan Hari Ini</h3>
            <p>Rp <?= number_format($totalPenjualanHariIni, 0, ',', '.') ?></p>
        </div>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>