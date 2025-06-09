<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tu') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error)
    die("Koneksi gagal: " . $conn->connect_error);

// Tambahkan data ke tabel biaya_tahunan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_biaya'])) {
    $tahun_ajaran = intval($_POST['tahun_ajaran']);
    $b_pendaftaran = floatval($_POST['b_pendaftaran']);
    $b_awaltahun = floatval($_POST['b_awaltahun']);
    $b_seragam = floatval($_POST['b_seragam']);
    $b_spp = floatval($_POST['b_spp']);

    // Validasi input tahun ajaran
    if ($tahun_ajaran >= 2000 && $tahun_ajaran <= date("Y") + 10) {
        $stmt = $conn->prepare("INSERT INTO biaya_tahunan (Tahun_ajaran, B_pendaftaran, B_Awaltahun, B_seragam, B_SPP) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idddd", $tahun_ajaran, $b_pendaftaran, $b_awaltahun, $b_seragam, $b_spp);

        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='keuangan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Tahun ajaran tidak valid. Harap masukkan tahun antara 2000 dan " . (date("Y") + 10) . ".'); window.history.back();</script>";
    }
}

// Ambil data biaya tahunan untuk ditampilkan di tabel
$biaya_tahunan = [];
$result = $conn->query("SELECT * FROM biaya_tahunan ORDER BY Tahun_ajaran DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $biaya_tahunan[] = $row;
    }
}
$result->close();

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keuangan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 2rem 2.5rem;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }

        form {
            margin-bottom: 2rem;
        }

        input,
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #4B3621;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(112, 82, 51);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        th,
        td {
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

        .btn-hapus {
            background-color: #e63946;
            /* Red color */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-hapus:hover {
            background-color: #c53030;
            /* Darker red */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Keuangan</h2>
        <form method="POST">
            <input type="number" name="b_pendaftaran" placeholder="Biaya Pendaftaran" step="0.01" required>
            <input type="number" name="b_awaltahun" placeholder="Biaya Awal Tahun" step="0.01" required>
            <input type="number" name="b_seragam" placeholder="Biaya Seragam" step="0.01" required>
            <input type="number" name="b_spp" placeholder="Biaya SPP" step="0.01" required>
            <label for="tahun_ajaran">Tahun Ajaran</label>
            <select id="tahun_ajaran" name="tahun_ajaran" required>
                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                <?php for ($year = 2000; $year <= 2100; $year++): ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" name="add_biaya">Tambah Data</button>
        </form>

        <!-- Tabel Biaya Tahunan -->
        <h2>Data Biaya Tahunan</h2>
        <table>
            <thead>
                <tr>
                    <th>Tahun Ajaran</th>
                    <th>Biaya Pendaftaran</th>
                    <th>Biaya Awal Tahun</th>
                    <th>Biaya Seragam</th>
                    <th>Biaya SPP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($biaya_tahunan)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888;">Belum ada data keuangan.</td>
                    </tr>
                <?php else:
                    foreach ($biaya_tahunan as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Tahun_ajaran']) ?></td>
                            <td>Rp <?= htmlspecialchars(number_format($row['B_pendaftaran'], 2)) ?></td>
                            <td>Rp <?= htmlspecialchars(number_format($row['B_Awaltahun'], 2)) ?></td>
                            <td>Rp <?= htmlspecialchars(number_format($row['B_seragam'], 2)) ?></td>
                            <td>Rp <?= htmlspecialchars(number_format($row['B_SPP'], 2)) ?></td>
                            <td> <a href="data-keuangan.php?delete=<?= $row['Tahun_ajaran'] ?>" class="btn-hapus"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"><i class='fas fa-trash'></i>
                                    Hapus</a></td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>