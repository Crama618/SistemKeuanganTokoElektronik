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

$result = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama ASC");

$totalBarang = mysqli_num_rows($result);
$stokRendah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM barang WHERE stok < 10"))['count'];
$kategoriTerbanyak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kategori, COUNT(*) as count FROM barang GROUP BY kategori ORDER BY count DESC LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Toko Elektronik</title>
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

        .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .summary-card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-left: 5px solid transparent; transition: all 0.3s ease; }
        .summary-card:hover { transform: translateY(-5px); box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15); }
        .summary-card.total { border-left-color: #3498db; }
        .summary-card.low-stock { border-left-color: #e74c3c; }
        .summary-card.category { border-left-color: #27ae60; }
        .summary-card h3 { color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .summary-card .amount { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .summary-card.total .amount { color: #3498db; }
        .summary-card.low-stock .amount { color: #e74c3c; }
        .summary-card.category .amount { color: #27ae60; }
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
        .stock-warning { color: #e74c3c; font-weight: 600; }
        .stock-normal { color: #27ae60; font-weight: 600; }
        .no-data { text-align: center; color: #6c757d; font-style: italic; padding: 40px 20px; }

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
                <li><a href="../barang/index.php" class="active">Barang</a></li>
                <li><a href="../penjualan/index.php">Penjualan</a></li>
                <li><a href="../pembelian/index.php">Pembelian</a></li>
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
            <h1>Data Barang</h1>
            <div class="user-info">
                <?= strtolower($role) ?>
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card total">
                <h3>Total Barang</h3>
                <div class="amount"><?= $totalBarang ?></div>
                <div class="subtitle">Item terdaftar</div>
            </div>
            <div class="summary-card low-stock">
                <h3>Stok Rendah</h3>
                <div class="amount"><?= $stokRendah ?></div>
                <div class="subtitle">Barang < 10 stok</div>
            </div>
            <div class="summary-card category">
                <h3>Kategori Terbanyak</h3>
                <div class="amount"><?= $kategoriTerbanyak['kategori'] ?? '-' ?></div>
                <div class="subtitle"><?= isset($kategoriTerbanyak['count']) ? $kategoriTerbanyak['count'] . ' barang' : 'Tidak ada data' ?></div>
            </div>
        </div>

        <div class="actions">
            <h3>Kelola Barang</h3>
            <a href="tambah.php" class="btn">
                <span>+</span> Tambah Barang
            </a>
        </div>

        <div class="table-section">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Deskripsi</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama ASC");
                        if (mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                            <td>
                                <span class="<?= $row['stok'] < 10 ? 'stock-warning' : 'stock-normal' ?>">
                                    <?= $row['stok'] ?>
                                    <?= $row['stok'] < 10 ? ' ⚠️' : ' ✅' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td class="action-btns">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus barang <?= htmlspecialchars($row['nama']) ?>?')">Hapus</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                        <tr><td colspan="7" class="no-data">Tidak ada data barang</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>