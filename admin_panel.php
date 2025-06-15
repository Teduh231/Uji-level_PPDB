<?php
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['add_petugas'])) {
    $kd_petugas = $_POST['kd_petugas'];
    $nama_petugas = $_POST['nama_petugas'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO petugas (Kd_petugas, nama_petugas, role, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $kd_petugas, $nama_petugas, $role, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Petugas berhasil ditambahkan!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan petugas.'); window.history.back();</script>";
    }
}

if (isset($_GET['delete_petugas'])) {
    $kd_petugas = $_GET['delete_petugas'];
    $stmt = $conn->prepare("DELETE FROM petugas WHERE Kd_petugas = ?");
    $stmt->bind_param("s", $kd_petugas);

    if ($stmt->execute()) {
        echo "<script>alert('Petugas berhasil dihapus!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus petugas.'); window.history.back();</script>";
    }
}

if (isset($_POST['edit_petugas'])) {
    $kd_petugas = $_POST['kd_petugas'];
    $nama_petugas = $_POST['nama_petugas'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE petugas SET Nama_petugas = ?, Role = ? WHERE Kd_petugas = ?");
    $stmt->bind_param("sss", $nama_petugas, $role, $kd_petugas);

    if ($stmt->execute()) {
        echo "<script>alert('Petugas berhasil diedit!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal mengedit petugas.'); window.history.back();</script>";
    }
}

if (isset($_POST['add_jurusan'])) {
    $kode_jur = $_POST['kode_jur'];
    $nama_jurusan = $_POST['nama_jurusan'];
    $informasi_jurusan = $_POST['informasi_jurusan'];

    $stmt = $conn->prepare("INSERT INTO jurusan (Kode_jur, Nama_jurusan, Informasi_jurusan) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $kode_jur, $nama_jurusan, $informasi_jurusan);

    if ($stmt->execute()) {
        echo "<script>alert('Jurusan berhasil ditambahkan!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jurusan.'); window.history.back();</script>";
    }
}

if (isset($_GET['delete_jurusan'])) {
    $kode_jur = $_GET['delete_jurusan'];
    $stmt = $conn->prepare("DELETE FROM jurusan WHERE Kode_jur = ?");
    $stmt->bind_param("s", $kode_jur);

    if ($stmt->execute()) {
        echo "<script>alert('Jurusan berhasil dihapus!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus jurusan.'); window.history.back();</script>";
    }
}

if (isset($_POST['edit_jurusan'])) {
    $kode_jur = $_POST['kode_jur'];
    $nama_jurusan = $_POST['nama_jurusan'];
    $informasi_jurusan = $_POST['informasi_jurusan'];

    $stmt = $conn->prepare("UPDATE jurusan SET Nama_jurusan = ?, Informasi_jurusan = ? WHERE Kode_jur = ?");
    $stmt->bind_param("sss", $nama_jurusan, $informasi_jurusan, $kode_jur);

    if ($stmt->execute()) {
        echo "<script>alert('Jurusan berhasil diedit!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal mengedit jurusan.'); window.history.back();</script>";
    }
}

if (isset($_POST['set_kuota'])) {
    $tahun_ajaran = $_POST['tahun_ajaran'];
    $kuota_pendaftar = intval($_POST['kuota_pendaftar']);

    // Periksa apakah tahun ajaran sudah ada di tabel tahun_ajaran
    $check = $conn->prepare("SELECT * FROM tahun_ajaran WHERE Tahun_ajaran = ?");
    $check->bind_param("s", $tahun_ajaran);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update kuota jika tahun ajaran sudah ada
        $stmt = $conn->prepare("UPDATE tahun_ajaran SET kuota_pendaftar = ? WHERE Tahun_ajaran = ?");
        $stmt->bind_param("is", $kuota_pendaftar, $tahun_ajaran);
    } else {
        // Tambahkan tahun ajaran baru jika belum ada
        $stmt = $conn->prepare("INSERT INTO tahun_ajaran (Tahun_ajaran, kuota_pendaftar) VALUES (?, ?)");
        $stmt->bind_param("si", $tahun_ajaran, $kuota_pendaftar);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Kuota berhasil diset!'); window.location.href='admin_panel.php';</script>";
    } else {
        echo "<script>alert('Gagal menyet kuota.'); window.history.back();</script>";
    }
}
include 'header.php';
?>

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
        background-color: #2563eb;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #1d4ed8;
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

    .btn-edit, .btn-hapus {
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
        text-align: center;
    }

    .btn-edit {
        background-color: #10b981;
        color: #fff;
    }

    .btn-edit:hover {
        background-color: #059669;
    }

    .btn-hapus {
        background-color: #e63946;
        color: #fff;
    }

    .btn-hapus:hover {
        background-color: #c53030;
    }
</style>

<div class="container">
    <h2>Tambah Petugas Baru</h2>
    <form method="POST">
        <label for="kd_petugas">Kode Petugas</label>
        <input type="text" id="kd_petugas" name="kd_petugas" required>

        <label for="nama_petugas">Nama Petugas</label>
        <input type="text" id="nama_petugas" name="nama_petugas" required>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="" disabled selected>Pilih Role</option>
            <option value="admin">Admin</option>
            <option value="tu">TU</option>
            <option value="kepsek">Kepsek</option>
        </select>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" name="add_petugas">Tambah Petugas</button>
    </form>
</div>
<div class="container">
    <h2>Daftar Petugas</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Petugas</th>
                <th>Nama Petugas</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM petugas");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['Kd_petugas']}</td>
                    <td>{$row['nama_petugas']}</td>
                    <td>{$row['role']}</td>
                    <td>
                        <a href='admin_panel.php?edit_petugas={$row['Kd_petugas']}' class='btn-edit'>Edit</a>
                        <a href='admin_panel.php?delete_petugas={$row['Kd_petugas']}' class='btn-hapus'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<div class="container">
    <h2>Tambah Jurusan</h2>
    <form method="POST">
        <label for="kode_jur">Kode Jurusan</label>
        <input type="text" id="kode_jur" name="kode_jur" required>

        <label for="nama_jurusan">Nama Jurusan</label>
        <input type="text" id="nama_jurusan" name="nama_jurusan" required>

        <label for="informasi_jurusan">Informasi Jurusan</label>
        <textarea id="informasi_jurusan" name="informasi_jurusan" required></textarea>

        <button type="submit" name="add_jurusan">Tambah Jurusan</button>
    </form>
</div>
<div class="container">
    <h2>Daftar Jurusan</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Jurusan</th>
                <th>Nama Jurusan</th>
                <th>Informasi Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM jurusan");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['Kode_jur']}</td>
                    <td>{$row['Nama_jurusan']}</td>
                    <td>
                        <a href='admin_panel.php?edit_jurusan={$row['Kode_jur']}' class='btn-edit'>Edit</a>
                        <a href='admin_panel.php?delete_jurusan={$row['Kode_jur']}' class='btn-hapus'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<div class="container">
    <h2>Set Kuota Maksimum Pendaftar</h2>
    <form method="POST">
        <label for="tahun_ajaran">Tahun Ajaran</label>
        <select id="tahun_ajaran" name="tahun_ajaran" required>
            <option value="" disabled selected>Pilih Tahun Ajaran</option>
            <?php
            $result = $conn->query("SELECT DISTINCT Tahun_ajaran FROM biaya_tahunan ORDER BY Tahun_ajaran DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['Tahun_ajaran']}'>{$row['Tahun_ajaran']}</option>";
            }
            ?>
        </select>
        <label for="kuota_pendaftar">Kuota Maksimum Pendaftar</label>
        <input type="number" id="kuota_pendaftar" name="kuota_pendaftar" required>
        <button type="submit" name="set_kuota">Set Kuota</button>
    </form>
</div>
<div class="container">
    <h2>Daftar Kuota Pendaftar</h2>
    <table>
        <thead>
            <tr>
                <th>Tahun Ajaran</th>
                <th>Kuota Pendaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM tahun_ajaran ORDER BY Tahun_ajaran DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['Tahun_ajaran']}</td>
                    <td>{$row['kuota_pendaftar']}</td>
                    <td>
                        <a href='admin_panel.php?edit_tahun={$row['Tahun_ajaran']}' class='btn-edit'>Edit</a>
                        <a href='admin_panel.php?delete_tahun={$row['Tahun_ajaran']}' class='btn-hapus'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<div class="container">
    <h2>History Login</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Petugas</th>
                <th>Nama Petugas</th>
                <th>Role</th>
                <th>Waktu Login</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("
                SELECT lh.id, lh.kd_petugas, p.nama_petugas, p.role, lh.login_time, lh.ip_address
                FROM login_history lh
                JOIN petugas p ON lh.kd_petugas = p.Kd_petugas
                ORDER BY lh.login_time DESC
            ");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['kd_petugas']}</td>
                    <td>{$row['nama_petugas']}</td>
                    <td>{$row['role']}</td>
                    <td>{$row['login_time']}</td>
                    <td>{$row['ip_address']}</td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>