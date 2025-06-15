<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas') {
    header("Location: dashboard.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Proses penyimpanan data pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = $_POST['NIS'];
    $nama = $_POST['Nama'];
    $alamat = $_POST['Alamat'];
    $email = $_POST['Email'];
    $telepon = $_POST['Telepon'];
    $rata = $_POST['Rata'];
    $jk = $_POST['JK'];
    $kode_jur = $_POST['kode_jur'];
    $tahun_ajaran = $_POST['Tahun_ajaran'];
    $kd_petugas = $_POST['Kd_petugas'];

    // Validasi input
    if (empty($nis) || empty($nama) || empty($alamat) || empty($email) || empty($telepon) || empty($rata) || empty($jk) || empty($kode_jur) || empty($tahun_ajaran) || empty($kd_petugas)) {
        die("Semua field harus diisi.");
    }

    // Generate nomor pendaftar
    $query_no_pendaftar = "SELECT COUNT(*) AS total FROM pendaftaran";
    $result_no_pendaftar = $conn->query($query_no_pendaftar);
    $row_no_pendaftar = $result_no_pendaftar->fetch_assoc();
    $no_pendaftar = "P" . str_pad($row_no_pendaftar['total'] + 1, 5, "0", STR_PAD_LEFT);

    // Insert data ke tabel pendaftaran
    $stmt = $conn->prepare("INSERT INTO pendaftaran (no_pendaftar, NIS, Nama, Alamat, Email, Telepon, Rata, JK, kode_jur, Tahun_ajaran, Kd_petugas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Query gagal: " . $conn->error);
    }
    $stmt->bind_param("sssssssssss", $no_pendaftar, $nis, $nama, $alamat, $email, $telepon, $rata, $jk, $kode_jur, $tahun_ajaran, $kd_petugas);

    if ($stmt->execute()) {
        echo "<script>alert('Data pendaftar berhasil disimpan! Nomor Pendaftar: $no_pendaftar'); window.location.href='input_pendaftaran.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data pendaftar.'); window.history.back();</script>";
    }
    $stmt->close();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pendaftaran</title>
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
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: bold;
            color: #1e293b;
        }

        input, select, textarea, button {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
        }

        button {
            background-color: #4B3621; /* Warna tombol */
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3A2A18; /* Warna hover tombol */
        }

        select {
            appearance: none;
            background: #fff;
            cursor: pointer;
        }

        textarea {
            resize: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Input Pendaftaran</h2>
        <form method="POST">
            <label for="nis">NIS</label>
            <input type="text" id="nis" name="NIS" placeholder="NIS Peserta" maxlength="20" required>

            <label for="nama">Nama</label>
            <input type="text" id="nama" name="Nama" placeholder="Nama Peserta" maxlength="100" required>

            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="Alamat" placeholder="Alamat Peserta" rows="4" required></textarea>

            <label for="email">Email</label>
            <input type="email" id="email" name="Email" placeholder="Email Peserta" maxlength="100" required>

            <label for="telepon">Telepon</label>
            <input type="text" id="telepon" name="Telepon" placeholder="Nomor Telepon" maxlength="20" required>

            <label for="rata">Rata-Rata</label>
            <input type="text" id="rata" name="Rata" placeholder="Rata-Rata Nilai" maxlength="10" required>

            <label for="jk">Jenis Kelamin</label>
            <select id="jk" name="JK" required>
                <option value="L">Laki-Laki</option>
                <option value="P">Perempuan</option>
            </select>

            <label for="kode_jur">Pilihan Jurusan</label>
            <select id="kode_jur" name="kode_jur" required>
                <option value="" disabled selected>Pilih Jurusan</option>
                <?php
                $query = "SELECT Kode_jur, Nama_jurusan FROM jurusan";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['Kode_jur']}'>{$row['Nama_jurusan']}</option>";
                }
                ?>
            </select>

            <label for="tahun_ajaran">Tahun Ajaran</label>
            <select id="tahun_ajaran" name="Tahun_ajaran" required>
                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                <?php
                $query = "SELECT DISTINCT Tahun_ajaran FROM biaya_tahunan ORDER BY Tahun_ajaran DESC";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['Tahun_ajaran']}'>{$row['Tahun_ajaran']}</option>";
                }
                ?>
            </select>

            <label for="kd_petugas">Petugas</label>
            <select id="kd_petugas" name="Kd_petugas" required>
                <option value="" disabled selected>Pilih Petugas</option>
                <?php
                $query = "SELECT Kd_petugas, nama_petugas FROM petugas WHERE role = 'petugas'";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['Kd_petugas']}'>{$row['nama_petugas']}</option>";
                }
                ?>
            </select>

            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>