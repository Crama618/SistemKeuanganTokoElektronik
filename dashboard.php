<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$role = $_SESSION['role'] ?? '';

$totalBarang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$totalPelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan"))['total'];
$totalPenjualanHariIni = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT IFNULL(SUM(total_harga),0) as total FROM penjualan WHERE DATE(tanggal) = CURDATE()"
))['total'];
$totalPengeluaranHariIni = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT IFNULL(SUM(jumlah),0) as total FROM pengeluaran WHERE DATE(tanggal) = CURDATE()"
))['total'];

$riwayatPengeluaran = mysqli_query($conn, "SELECT * FROM pengeluaran WHERE DATE(tanggal) = CURDATE() ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Toko Elektronik</title>
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
            position: fixed;
            padding-top: 20px;
        }

        .main {
            margin-left: 220px;
            width: calc(100% - 220px);
            padding: 30px;
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
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>🛒 Toko Elektronik</h2>
        <ul>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>

            <?php if ($role === 'admin'): ?>
                <li><a href="barang/index.php">Barang</a></li>
                <li><a href="penjualan/index.php">Penjualan</a></li>
                <li><a href="pembelian/index.php">Pembelian</a></li>
                <li><a href="pemasukan/index.php">Pemasukan</a></li>
                <li><a href="pengeluaran/index.php">Pengeluaran</a></li>
            <?php elseif ($role === 'kasir'): ?>
                <li><a href="penjualan/index.php">Penjualan</a></li>
            <?php endif; ?>

            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Dashboard</h1>
            <div>
                <?php
                $role = $_SESSION['role'] ?? '';
                if ($role === 'admin') {
                    echo '<a href="admin.php" style="text-decoration:none; color:#111827;">' . htmlspecialchars($role) . '</a>';
                } elseif ($role === 'kasir') {
                    echo '<a href="kasir.php" style="text-decoration:none; color:#111827;">' . htmlspecialchars($role) . '</a>';
                } else {
                    echo htmlspecialchars($role);
                }
                ?>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="card-container">
            <div class="card">
                <h3>Total Penjualan Hari Ini</h3>
                <p>Rp <?= number_format($totalPenjualanHariIni, 0, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Total Pengeluaran Hari Ini</h3>
                <p>Rp <?= number_format($totalPengeluaranHariIni, 0, ',', '.') ?></p>
            </div>
            <div class="card">
                <h3>Total Barang</h3>
                <p><?= $totalBarang ?></p>
            </div>
            <div class="card">
                <h3>Total Pelanggan</h3>
                <p><?= $totalPelanggan ?></p>
            </div>
        </div>

        <h2>Riwayat Pengeluaran Hari Ini</h2>
        <table>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
            <?php if (mysqli_num_rows($riwayatPengeluaran) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($riwayatPengeluaran)): ?>
                    <tr>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3" class="no-data">Tidak ada pengeluaran hari ini.</td></tr>
            <?php endif; ?>
        </table>

    </div>
</body>
</html>
