<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kepsek') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$tahun_ajaran_filter = isset($_GET['tahun_ajaran']) ? intval($_GET['tahun_ajaran']) : null;

$query = "SELECT pendaftaran.Tahun_ajaran, SUM(pembayaran.uang_dibayar) AS total_pembayaran
          FROM pembayaran
          JOIN pendaftaran ON pembayaran.id_pendaftar = pendaftaran.id_pendaftar";

if ($tahun_ajaran_filter) {
    $query .= " WHERE pendaftaran.Tahun_ajaran = $tahun_ajaran_filter";
}

$query .= " GROUP BY pendaftaran.Tahun_ajaran ORDER BY pendaftaran.Tahun_ajaran DESC";

$result = $conn->query($query);
$laporan_keuangan = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $laporan_keuangan[] = $row;
    }
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #FAEBD7;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            margin-top: 6rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 2rem 2.5rem;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        th, td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Laporan Keuangan</h2>
        <form method="GET">
            <label for="tahun_ajaran">Filter Tahun Ajaran</label>
            <select id="tahun_ajaran" name="tahun_ajaran" onchange="this.form.submit()">
                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                <?php
                $result = $conn->query("SELECT DISTINCT Tahun_ajaran FROM pendaftaran ORDER BY Tahun_ajaran DESC");
                while ($row = $result->fetch_assoc()) {
                    $selected = isset($_GET['tahun_ajaran']) && $_GET['tahun_ajaran'] == $row['Tahun_ajaran'] ? 'selected' : '';
                    echo "<option value='{$row['Tahun_ajaran']}' $selected>{$row['Tahun_ajaran']}</option>";
                }
                ?>
            </select>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Tahun Ajaran</th>
                    <th>Total Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporan_keuangan)): ?>
                    <tr><td colspan="2" style="color:#888;">Belum ada data pembayaran.</td></tr>
                <?php else: foreach ($laporan_keuangan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Tahun_ajaran']) ?></td>
                        <td>Rp <?= htmlspecialchars(number_format($row['total_pembayaran'], 2, ',', '.')) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>