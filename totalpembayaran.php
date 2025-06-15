<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tu') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Proses update status pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_pendaftar = intval($_POST['id_pendaftar']);
    $status = $conn->real_escape_string($_POST['status']);
    $update_query = "UPDATE pembayaran SET status = '$status' WHERE id_pendaftar = $id_pendaftar";
    if ($conn->query($update_query)) {
        echo "<script>alert('Status pembayaran berhasil diperbarui!');window.location='totalpembayaran.php';</script>";
    } else {
        die("Error: " . $conn->error);
    }
}

// Query untuk mendapatkan data pembayaran
$query = "SELECT pendaftaran.id_pendaftar, pendaftaran.Nama, pendaftaran.Tahun_ajaran, 
                 SUM(pembayaran.uang_dibayar) AS total_pembayaran, pembayaran.status
          FROM pembayaran
          JOIN pendaftaran ON pembayaran.id_pendaftar = pendaftaran.id_pendaftar
          GROUP BY pendaftaran.id_pendaftar, pendaftaran.Nama, pendaftaran.Tahun_ajaran, pembayaran.status
          ORDER BY pendaftaran.Tahun_ajaran DESC";

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
        .status-select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .update-btn {
            padding: 5px 10px;
            background-color: #4B3621;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .update-btn:hover {
            background-color: #3A2A18;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Laporan Keuangan</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tahun Ajaran</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporan_keuangan)): ?>
                    <tr><td colspan="5" style="color:#888;">Belum ada data pembayaran.</td></tr>
                <?php else: foreach ($laporan_keuangan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Nama']) ?></td>
                        <td><?= htmlspecialchars($row['Tahun_ajaran']) ?></td>
                        <td>Rp <?= htmlspecialchars(number_format($row['total_pembayaran'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_pendaftar" value="<?= htmlspecialchars($row['id_pendaftar']) ?>">
                                <select name="status" class="status-select">
                                    <option value="Sudah lunas" <?= $row['status'] === 'Sudah lunas' ? 'selected' : '' ?>>Sudah lunas</option>
                                    <option value="Belum lunas" <?= $row['status'] === 'Belum lunas' ? 'selected' : '' ?>>Belum lunas</option>
                                    <option value="Belum membayar" <?= $row['status'] === 'Belum membayar' ? 'selected' : '' ?>>Belum membayar</option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>