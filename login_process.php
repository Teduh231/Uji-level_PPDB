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
        if ($pass === $petugas['password']) {
            $_SESSION['user_id'] = $petugas['Kd_petugas'];
            $_SESSION['user_nama'] = $petugas['nama_petugas'];
            $_SESSION['role'] = $petugas['role'];
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Password salah!');window.location='login.php';</script>";
            exit;
        }
    }

    $conn->close();
} else {
    header("Location: login.php");
    exit;
}
?>