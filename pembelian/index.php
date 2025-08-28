<?php
session_start();
include '../config.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';

if ($role !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM pembelian ORDER BY tanggal DESC");

$totalPembelianHariIni = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT IFNULL(SUM(total_harga), 0) as total FROM pembelian WHERE DATE(tanggal) = CURDATE()"
))['total'];

$totalPembelianBulanIni = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT IFNULL(SUM(total_harga), 0) as total FROM pembelian WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE())"
))['total'];

$totalTransaksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pembelian"))['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembelian - Toko Elektronik</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; display: flex; }

        .sidebar {width: 220px;background-color: #2c3e50;min-height: 100vh;position: fixed;padding-top: 20px;}
        .main {margin-left: 220px;width: calc(100% - 220px);padding: 30px; }
        .sidebar h2 { color: #fff; padding: 25px 20px; font-size: 20px; font-weight: 600; border-bottom: 1px solid #34495e;display: flex; align-items: center;gap: 10px; }
        .sidebar {width: 220px;background-color: #2c3e50;min-height: 100vh;display: flex;flex-direction: column;padding-top: 20px;position: fixed;}
        .sidebar h2 {color: white;font-size: 18px;margin-bottom: 30px;padding-left: 20px;}
        .sidebar ul {list-style: none;padding: 0;}
        .sidebar ul li {margin-bottom: 10px;}
        .sidebar ul li a {color: #ecf0f1;text-decoration: none;padding: 12px 20px;display: block;border-left: 5px solid transparent;transition: 0.3s;}
        .sidebar ul li a:hover {background-color: #34495e;border-left: 5px solid #1abc9c;}
        .sidebar ul li a.active {background-color: #1abc9c;font-weight: bold;}
        .sidebar .logout {color: red;}

        .main {margin-left: 220px; padding: 30px; width: calc(100% - 220px);}
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef; }
        .header h1 { color: #2c3e50; font-size: 28px; font-weight: 700; }
        .user-info { color: #6c757d; font-size: 14px; }

        .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .summary-card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-left: 5px solid transparent; transition: all 0.3s ease; }
        .summary-card:hover { transform: translateY(-5px); box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15); }
        .summary-card.today { border-left-color: #e74c3c; }
        .summary-card.month { border-left-color: #9b59b6; }
        .summary-card.total { border-left-color: #3498db; }
        .summary-card h3 { color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .summary-card .amount { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .summary-card.today .amount { color: #e74c3c; }
        .summary-card.month .amount { color: #9b59b6; }
        .summary-card.total .amount { color: #3498db; }
        .summary-card .subtitle { font-size: 12px; color: #6c757d; }

        .actions { background: #fff; padding: 20px 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .actions h3 { color: #2c3e50; font-size: 18px; }
        .btn { padding: 12px 25px; background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(26, 188, 156, 0.3); }
        .btn-edit { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 8px 15px; font-size: 12px; }
        .btn-edit:hover { box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3); }
        .btn-danger { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); padding: 8px 15px; font-size: 12px; }
        .btn-danger:hover { box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3); }

        .table-section { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; border-radius: 8px; overflow: hidden; }
        thead { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
        th { padding: 15px 20px; text-align: left; font-weight: 600; color: #495057; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr { transition: all 0.3s ease; }
        tbody tr:hover { background-color: #f8f9fa; }
        tbody tr:nth-child(even) { background-color: #fdfdfd; }
        td { padding: 15px 20px; color: #495057; font-size: 14px; border-bottom: 1px solid #e9ecef; }
        .action-btns { display: flex; gap: 8px; }
        .no-data { text-align: center; color: #6c757d; font-style: italic; padding: 40px 20px; }
        .purchase-amount { color: #e74c3c; font-weight: 600; }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; padding: 20px; }
            .summary-cards { grid-template-columns: 1fr; }
            .header, .actions { flex-direction: column; align-items: flex-start; gap: 15px; }
            .action-btns { flex-direction: column; gap: 5px; }
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
                <li><a href="../penjualan/index.php">Penjualan</a></li>
                <li><a href="../pembelian/index.php" class="active">Pembelian</a></li>
                <li><a href="../pemasukan/index.php">Pemasukan</a></li>
                <li><a href="../pengeluaran/index.php">Pengeluaran</a></li>
            <?php elseif ($role === 'kasir'): ?>
                <li><a href="../penjualan/index.php">Penjualan</a></li>
            <?php endif; ?>

            <li><a href="../laporan.php">Laporan</a></li>
            <li><a href="logout.php" class="logout" style="color: red;">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Data Pembelian</h1>
            <div class="user-info">
                <?= strtolower($role) ?>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card today">
                <h3>Pembelian Hari Ini</h3>
                <div class="amount">Rp <?= number_format($totalPembelianHariIni, 0, ',', '.') ?></div>
                <div class="subtitle">Total belanja hari ini</div>
            </div>
            <div class="summary-card month">
                <h3>Pembelian Bulan Ini</h3>
                <div class="amount">Rp <?= number_format($totalPembelianBulanIni, 0, ',', '.') ?></div>
                <div class="subtitle">Total belanja bulan <?= date('M Y') ?></div>
            </div>
            <div class="summary-card total">
                <h3>Total Transaksi</h3>
                <div class="amount"><?= $totalTransaksi ?></div>
                <div class="subtitle">Jumlah pembelian</div>
            </div>
        </div>

        <div class="actions">
            <h3>Kelola Pembelian</h3>
            <a href="tambah.php" class="btn">
                <span>+</span> Tambah Pembelian
            </a>
        </div>

        <div class="table-section">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><strong>#<?= $row['id'] ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><strong><?= $row['jumlah'] ?></strong> unit</td>
                            <td class="purchase-amount">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td class="action-btns">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus pembelian <?= htmlspecialchars($row['nama_barang']) ?>?')">Hapus</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                        <tr><td colspan="6" class="no-data">Tidak ada data pembelian</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>