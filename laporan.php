<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

$role = $_SESSION['role'] ?? '';

$tanggal_awal  = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';

$where = "";
if ($tanggal_awal && $tanggal_akhir) {
    $where = "WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

$pemasukan_query = mysqli_query($conn, "SELECT * FROM pemasukan $where ORDER BY tanggal ASC");
$pengeluaran_query = mysqli_query($conn, "SELECT * FROM pengeluaran $where ORDER BY tanggal ASC");

$total_pemasukan = 0;
$total_pengeluaran = 0;

$data_pemasukan = [];
$data_pengeluaran = [];

while ($row = mysqli_fetch_assoc($pemasukan_query)) {
    $data_pemasukan[] = $row;
    $total_pemasukan += $row['jumlah'];
}

while ($row = mysqli_fetch_assoc($pengeluaran_query)) {
    $data_pengeluaran[] = $row;
    $total_pengeluaran += $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Toko Elektronik</title>
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

        .filter-section, .data-section { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px; }
        .filter-section h3, .data-section h3 { color: #2c3e50; margin-bottom: 20px; font-size: 18px; }

        .filter-form { display: flex; gap: 20px; align-items: end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-weight: 600; color: #495057; font-size: 14px; }
        .form-group input { padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; min-width: 150px; }
        .form-group input:focus { outline: none; border-color: #1abc9c; box-shadow: 0 0 0 3px rgba(26, 188, 156, 0.1); }
        .filter-btn { padding: 10px 25px; background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 14px; }
        .filter-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(26, 188, 156, 0.3); }

        .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 30px; }
        .summary-card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-left: 5px solid transparent; transition: all 0.3s ease; }
        .summary-card:hover { transform: translateY(-5px); box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15); }
        .summary-card.income { border-left-color: #27ae60; }
        .summary-card.expense { border-left-color: #e74c3c; }
        .summary-card.balance { border-left-color: #3498db; }
        .summary-card h3 { color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .summary-card .amount { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .summary-card.income .amount { color: #27ae60; }
        .summary-card.expense .amount { color: #e74c3c; }
        .summary-card.balance .amount { color: #2c3e50; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; border-radius: 8px; overflow: hidden; background: #fff; }
        thead { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
        th { padding: 15px 20px; text-align: left; font-weight: 600; color: #495057; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr { transition: all 0.3s ease; }
        tbody tr:hover { background-color: #f8f9fa; }
        tbody tr:nth-child(even) { background-color: #fdfdfd; }
        td { padding: 15px 20px; color: #495057; font-size: 14px; border-bottom: 1px solid #e9ecef; }
        .no-data { text-align: center; color: #6c757d; font-style: italic; padding: 40px 20px; }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; width: 100%; padding: 20px; }
            .filter-form { flex-direction: column; align-items: stretch; }
            .summary-cards { grid-template-columns: 1fr; }
            .header { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🛒 Toko Elektronik</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>

            <?php if ($role === 'admin'): ?>
                <li><a href="barang/index.php">Barang</a></li>
                <li><a href="penjualan/index.php">Penjualan</a></li>
                <li><a href="pembelian/index.php">Pembelian</a></li>
                <li><a href="pemasukan/index.php">Pemasukan</a></li>
                <li><a href="pengeluaran/index.php">Pengeluaran</a></li>
            <?php elseif ($role === 'kasir'): ?>
                <li><a href="penjualan/index.php">Penjualan</a></li>
            <?php endif; ?>

            <li><a href="laporan.php" class="active">Laporan</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="header">
            <h1>Laporan Keuangan</h1>
            <div class="user-info">
                <?= strtolower($role) ?>
            </div>
        </div>

        <div class="filter-section">
            <h3>Filter Laporan</h3>
            <form method="get" class="filter-form">
                <div class="form-group">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir ?>">
                </div>
                <button type="submit" class="filter-btn">Filter</button>
            </form>
        </div>

        <div class="summary-cards">
            <div class="summary-card income">
                <h3>Total Pemasukan</h3>
                <div class="amount">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></div>
            </div>
            <div class="summary-card expense">
                <h3>Total Pengeluaran</h3>
                <div class="amount">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
            </div>
            <div class="summary-card balance">
                <h3>Saldo Akhir</h3>
                <div class="amount">Rp <?= number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') ?></div>
            </div>
        </div>

        <div class="data-section">
            <h3>Data Pemasukan</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data_pemasukan)) {
                            foreach ($data_pemasukan as $row) { ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="3" class="no-data">Tidak ada data pemasukan</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="data-section">
            <h3>Data Pengeluaran</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data_pengeluaran)) {
                            foreach ($data_pengeluaran as $row) { ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="3" class="no-data">Tidak ada data pengeluaran</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>