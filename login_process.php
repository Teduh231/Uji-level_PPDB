<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "ppdb");
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass'];

    // Cek login untuk petugas (Admin, Kepsek, TU, Petugas)
    $sql_petugas = "SELECT Kd_petugas, nama_petugas, password, role FROM petugas WHERE nama_petugas='$user' LIMIT 1";
    $result_petugas = $conn->query($sql_petugas);
    if ($result_petugas && $result_petugas->num_rows === 1) {
        $petugas = $result_petugas->fetch_assoc();
        if ($pass === $petugas['password']) { // Perbandingan langsung tanpa hashing
            $_SESSION['user_id'] = $petugas['Kd_petugas'];
            $_SESSION['user_nama'] = $petugas['nama_petugas'];
            $_SESSION['role'] = $petugas['role'];

            // Catat login ke tabel login_history
            $kd_petugas = $petugas['Kd_petugas'];
            $login_time = date('Y-m-d H:i:s');
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $stmt = $conn->prepare("INSERT INTO login_history (kd_petugas, login_time, ip_address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $kd_petugas, $login_time, $ip_address);
            $stmt->execute();

            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Password salah!');window.location='login.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');window.location='login.php';</script>";
        exit;
    }

    $conn->close();
} else {
    header("Location: login.php");
    exit;
}
?>