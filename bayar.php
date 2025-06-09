<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'tu')) {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Ambil data pendaftar
$pendaftar = [];
$result = $conn->query("SELECT id_pendaftar, Nama, Tahun_ajaran FROM pendaftaran ORDER BY id_pendaftar ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftar[] = $row;
    }
}

// Ambil biaya berdasarkan tahun ajaran
$biaya_tahunan = [];
if (isset($_GET['id_pendaftar'])) {
    $id_pendaftar = intval($_GET['id_pendaftar']);
    $result = $conn->query("SELECT Tahun_ajaran FROM pendaftaran WHERE id_pendaftar = $id_pendaftar");
    if ($result && $result->num_rows > 0) {
        $tahun_ajaran = $result->fetch_assoc()['Tahun_ajaran'];
        $result_biaya = $conn->query("SELECT * FROM biaya_tahunan WHERE Tahun_ajaran = '$tahun_ajaran'");
        if ($result_biaya && $result_biaya->num_rows > 0) {
            $biaya_tahunan = $result_biaya->fetch_assoc();
        }
    }
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    $id_pendaftar = intval($_POST['id_pendaftar']);
    $jenis_biaya = $_POST['jenis_biaya'];
    $jumlah_biaya = floatval($_POST['jumlah_biaya']);
    $uang_dibayar = floatval($_POST['uang_dibayar']);

    // Validasi pembayaran
    if ($uang_dibayar < $jumlah_biaya) {
        echo "<script>alert('Uang yang dibayar kurang.'); window.history.back();</script>";
        exit;
    }

    $kembalian = $uang_dibayar - $jumlah_biaya;

    // Simpan pembayaran ke database
    $stmt = $conn->prepare("INSERT INTO pembayaran (id_pendaftar, jenis_biaya, jumlah_biaya, uang_dibayar, kembalian, tanggal_pembayaran) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Error pada query: " . $conn->error);
    }
    $stmt->bind_param("isdid", $id_pendaftar, $jenis_biaya, $jumlah_biaya, $uang_dibayar, $kembalian);

    if ($stmt->execute()) {
        echo "<script>alert('Pembayaran berhasil!'); window.location.href='bayar.php';</script>";
    } else {
        echo "<script>alert('Gagal melakukan pembayaran.'); window.history.back();</script>";
    }
    $stmt->close();
}

// Ambil data pembayaran untuk ditampilkan
$pembayaran = [];
$result = $conn->query("SELECT pembayaran.*, pendaftaran.Nama FROM pembayaran JOIN pendaftaran ON pembayaran.id_pendaftar = pendaftaran.id_pendaftar ORDER BY pembayaran.tanggal_pembayaran DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pembayaran[] = $row;
    }
}

// Cek jenis biaya yang sudah dibayar
$jenis_biaya_terbayar = [];
if (isset($_GET['id_pendaftar'])) {
    $id_pendaftar = intval($_GET['id_pendaftar']);
    $result = $conn->query("SELECT jenis_biaya FROM pembayaran WHERE id_pendaftar = $id_pendaftar");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $jenis_biaya_terbayar[] = $row['jenis_biaya'];
        }
    }
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pembayaran</title>
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
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
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
        input, select {
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
            background-color:rgb(112, 82, 51);
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
    <script>
        function updateJumlahBiaya() {
            const jenisBiaya = document.getElementById('jenis_biaya').value;
            const jumlahBiayaInput = document.getElementById('jumlah_biaya');

            // Data biaya berdasarkan tahun ajaran
            const biaya = {
                B_pendaftaran: <?= isset($biaya_tahunan['B_pendaftaran']) ? $biaya_tahunan['B_pendaftaran'] : 0 ?>,
                B_SPP: <?= isset($biaya_tahunan['B_SPP']) ? $biaya_tahunan['B_SPP'] : 0 ?>,
                B_Awaltahun: <?= isset($biaya_tahunan['B_Awaltahun']) ? $biaya_tahunan['B_Awaltahun'] : 0 ?>,
                B_seragam: <?= isset($biaya_tahunan['B_seragam']) ? $biaya_tahunan['B_seragam'] : 0 ?>
            };

            // Format Rupiah
            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
            };

            // Set nilai input jumlah biaya berdasarkan pilihan jenis biaya
            if (biaya[jenisBiaya] !== undefined) {
                jumlahBiayaInput.value = biaya[jenisBiaya]; // Set nilai asli (angka)
                jumlahBiayaInput.setAttribute('data-formatted', formatRupiah(biaya[jenisBiaya])); // Simpan format Rupiah
            } else {
                jumlahBiayaInput.value = 0;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Sistem Pembayaran</h2>
        <form method="GET">
            <select name="id_pendaftar" onchange="this.form.submit()" required>
                <option value="" disabled selected>Pilih Pendaftar</option>
                <?php foreach ($pendaftar as $row): ?>
                    <option value="<?= $row['id_pendaftar'] ?>" <?= isset($_GET['id_pendaftar']) && $_GET['id_pendaftar'] == $row['id_pendaftar'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['Nama']) ?> (<?= htmlspecialchars($row['Tahun_ajaran']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <form method="POST">
            <input type="hidden" name="id_pendaftar" value="<?= isset($_GET['id_pendaftar']) ? intval($_GET['id_pendaftar']) : '' ?>">
            
            <label for="jenis_biaya">Jenis Biaya</label>
            <select id="jenis_biaya" name="jenis_biaya" required onchange="updateJumlahBiaya()">
                <option value="" disabled selected>Pilih Jenis Biaya</option>
                <?php
                $jenis_biaya = ['B_pendaftaran', 'B_SPP', 'B_Awaltahun', 'B_seragam'];
                foreach ($jenis_biaya as $biaya) {
                    if (!in_array($biaya, $jenis_biaya_terbayar)) {
                        echo "<option value='$biaya'>" . str_replace('_', ' ', ucfirst($biaya)) . "</option>";
                    }
                }
                ?>
            </select>

            <label for="jumlah_biaya">Jumlah Biaya</label>
            <input type="number" id="jumlah_biaya" name="jumlah_biaya" placeholder="Jumlah Biaya" step="0.01" required readonly>

            <label for="uang_dibayar">Uang Dibayar</label>
            <input type="number" id="uang_dibayar" name="uang_dibayar" placeholder="Uang Dibayar" step="0.01" required>

            <button type="submit" name="bayar">Bayar</button>
        </form>

        <h2>Riwayat Pembayaran</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Pendaftar</th>
                    <th>Jenis Biaya</th>
                    <th>Jumlah Biaya</th>
                    <th>Uang Dibayar</th>
                    <th>Kembalian</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaran)): ?>
                    <tr><td colspan="6" style="color:#888;">Belum ada pembayaran.</td></tr>
                <?php else: foreach ($pembayaran as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Nama']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_biaya']) ?></td>
                        <td><?= htmlspecialchars(number_format($row['jumlah_biaya'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars(number_format($row['uang_dibayar'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars(number_format($row['kembalian'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pembayaran']) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>