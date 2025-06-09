<?php
$koneksi = new mysqli("localhost", "root", "", "ppdb");

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Validasi input
$nis = isset($_POST['nis']) ? trim($_POST['nis']) : '';
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telepon = isset($_POST['telepon']) ? trim($_POST['telepon']) : '';
$rata = isset($_POST['rata']) ? trim($_POST['rata']) : '';
$jk = isset($_POST['jk']) ? trim($_POST['jk']) : '';
$kode_jur = isset($_POST['kode_jur']) ? trim($_POST['kode_jur']) : '';
$tahun_ajaran = isset($_POST['Tahun_ajaran']) ? trim($_POST['Tahun_ajaran']) : '';
$foto_name = isset($_FILES["surat_kelulusan"]["name"]) ? $_FILES["surat_kelulusan"]["name"] : '';
$kd_petugas = isset($_POST['Kd_petugas']) ? intval($_POST['Kd_petugas']) : 0;

// Validasi jika ada field kosong
if (empty($nis) || empty($nama) || empty($alamat) || empty($email) || empty($telepon) || empty($rata) || empty($jk) || empty($kode_jur) || empty($tahun_ajaran) || empty($foto_name) || $kd_petugas === 0) {
    echo "<script>alert('Semua field wajib diisi, termasuk Petugas!'); window.history.back();</script>";
    exit;
}

// Direktori upload
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
}

$foto_tmp = $_FILES["surat_kelulusan"]["tmp_name"];
$foto_path = $target_dir . basename($foto_name);

// Validasi file upload
if (!move_uploaded_file($foto_tmp, $foto_path)) {
    echo "<script>alert('Gagal upload file surat kelulusan.'); window.history.back();</script>";
    exit;
}

// Check for duplicate NIS
if (!empty($nis)) {
    $check_stmt = $koneksi->prepare("SELECT COUNT(*) FROM pendaftaran WHERE NIS = ?");
    $check_stmt->bind_param("s", $nis);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "<script>alert('NIS sudah terdaftar! Gunakan NIS yang berbeda.'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('NIS tidak boleh kosong!'); window.history.back();</script>";
    exit;
}

// Gunakan prepared statement untuk mencegah SQL Injection
$stmt = $koneksi->prepare("INSERT INTO pendaftaran (NIS, Nama, Alamat, Email, Telepon, Rata, JK, kode_jur, surat_kelulusan, Tahun_ajaran, Kd_petugas) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sssssdssssi", $nis, $nama, $alamat, $email, $telepon, $rata, $jk, $kode_jur, $foto_name, $tahun_ajaran, $kd_petugas);

    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error saat menyimpan data: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Error pada statement SQL: " . $koneksi->error . "'); window.history.back();</script>";
}

// Tutup koneksi
$koneksi->close();
?>