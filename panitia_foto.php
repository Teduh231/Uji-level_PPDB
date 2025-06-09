<?php
if (!isset($_GET['nama'])) exit;
$nama = $_GET['nama'];
$conn = new mysqli("localhost", "root", "", "ppdb_igasar");
$stmt = $conn->prepare("SELECT foto_panitia FROM panitia_ppdb WHERE nama_panitia = ?");
$stmt->bind_param("s", $nama);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($foto);
if ($stmt->num_rows > 0) {
    $stmt->fetch();
    header("Content-Type: image/jpeg"); // Ganti jika bukan jpeg
    echo $foto;
}
$stmt->close();
$conn->close();