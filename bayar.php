<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'tu')) {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Ambil data pendaftar berdasarkan no_pendaftar
$pendaftar = null;
if (isset($_POST['cek_pendaftar'])) {
    $no_pendaftar = $conn->real_escape_string($_POST['no_pendaftar']);
    $result = $conn->query("SELECT id_pendaftar, no_pendaftar, Nama, Tahun_ajaran FROM pendaftaran WHERE no_pendaftar = '$no_pendaftar'");
    if ($result && $result->num_rows > 0) {
        $pendaftar = $result->fetch_assoc();
    } else {
        echo "<script>alert('No Pendaftar tidak ditemukan!');</script>";
    }
}

// Ambil biaya berdasarkan tahun ajaran
$biaya_tahunan = [];
if ($pendaftar) {
    $tahun_ajaran = $pendaftar['Tahun_ajaran'];
    $result_biaya = $conn->query("SELECT * FROM biaya_tahunan WHERE Tahun_ajaran = '$tahun_ajaran'");
    if ($result_biaya && $result_biaya->num_rows > 0) {
        $biaya_tahunan = $result_biaya->fetch_assoc();
    }
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    $no_pendaftar = $conn->real_escape_string($_POST['no_pendaftar']);
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
    $stmt = $conn->prepare("INSERT INTO pembayaran (no_pendaftar, jenis_biaya, jumlah_biaya, uang_dibayar, kembalian, tanggal_pembayaran) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Error pada query: " . $conn->error);
    }
    $stmt->bind_param("ssdii", $no_pendaftar, $jenis_biaya, $jumlah_biaya, $uang_dibayar, $kembalian);

    if ($stmt->execute()) {
        echo "<script>alert('Pembayaran berhasil!'); window.location.href='bayar.php';</script>";
    } else {
        echo "<script>alert('Gagal melakukan pembayaran.'); window.history.back();</script>";
    }
    $stmt->close();
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pembayaran</title>
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

            // Set nilai input jumlah biaya berdasarkan pilihan jenis biaya
            if (biaya[jenisBiaya] !== undefined) {
                jumlahBiayaInput.value = biaya[jenisBiaya];
            } else {
                jumlahBiayaInput.value = 0;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Sistem Pembayaran</h2>
        <?php if (!$pendaftar): ?>
        <!-- Form untuk memasukkan No Pendaftar -->
        <form method="POST">
            <label for="no_pendaftar">Masukkan No Pendaftar</label>
            <input type="text" id="no_pendaftar" name="no_pendaftar" placeholder="No Pendaftar" required>
            <button type="submit" name="cek_pendaftar">Cek Pendaftar</button>
        </form>
        <?php endif; ?>

        <?php if ($pendaftar): ?>
        <!-- Tampilkan nama pendaftar -->
        <h3>Nama Pendaftar: <?= htmlspecialchars($pendaftar['Nama']) ?></h3>

        <!-- Form pembayaran jika pendaftar ditemukan -->
        <form method="POST">
            <input type="hidden" name="no_pendaftar" value="<?= htmlspecialchars($pendaftar['no_pendaftar']) ?>">
            
            <label for="jenis_biaya">Jenis Biaya</label>
            <select id="jenis_biaya" name="jenis_biaya" required onchange="updateJumlahBiaya()">
                <option value="" disabled selected>Pilih Jenis Biaya</option>
                <?php
                $jenis_biaya = ['B_pendaftaran', 'B_SPP', 'B_Awaltahun', 'B_seragam'];
                foreach ($jenis_biaya as $biaya) {
                    echo "<option value='$biaya'>" . str_replace('_', ' ', ucfirst($biaya)) . "</option>";
                }
                ?>
            </select>

            <label for="jumlah_biaya">Jumlah Biaya</label>
            <input type="number" id="jumlah_biaya" name="jumlah_biaya" placeholder="Jumlah Biaya" step="0.01" required readonly>

            <label for="uang_dibayar">Uang Dibayar</label>
            <input type="number" id="uang_dibayar" name="uang_dibayar" placeholder="Uang Dibayar" step="0.01" required>

            <button type="submit" name="bayar">Bayar</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>