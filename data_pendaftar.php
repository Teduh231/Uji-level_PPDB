<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'petugas' && $_SESSION['role'] !== 'kepsek') {
    header("Location: dashboard.php");
    exit;
}


$conn = new mysqli("localhost", "root", "", "ppdb");
if ($conn->connect_error)
    die("Koneksi gagal: " . $conn->connect_error);

// Ambil data pendaftar (tampilkan semua kolom yang ada)
$pendaftar = [];
$result = $conn->query("SELECT pendaftaran.*, jurusan.Nama_jurusan, petugas.nama_petugas 
                        FROM pendaftaran
                        LEFT JOIN jurusan ON pendaftaran.kode_jur = jurusan.Kode_jur
                        LEFT JOIN petugas ON pendaftaran.Kd_petugas = petugas.Kd_petugas
                        ORDER BY pendaftaran.id_pendaftar DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftar[] = $row;
    }
}

// Proses penghapusan data
if (isset($_GET['delete'])) {
    $id_pendaftar = intval($_GET['delete']);
    $delete_query = "DELETE FROM pendaftaran WHERE id_pendaftar = $id_pendaftar";
    if ($conn->query($delete_query)) {
        echo "<script>alert('Data berhasil dihapus!');window.location='data_pendaftar.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');window.location='data_pendaftar.php';</script>";
    }
}

if (isset($_POST['edit_pendaftar'])) {
    $id_pendaftar = intval($_POST['id_pendaftar']);
    $nis = $conn->real_escape_string($_POST['nis']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $telepon = $conn->real_escape_string($_POST['telepon']);
    $rata = floatval($_POST['rata']);
    $jk = $conn->real_escape_string($_POST['jk']);
    $kode_jur = $conn->real_escape_string($_POST['kode_jur']);
    $tahun_ajaran = $conn->real_escape_string($_POST['tahun_ajaran']);

    $update_query = "UPDATE pendaftaran SET NIS = '$nis', Nama = '$nama', Email = '$email', Telepon = '$telepon', Rata = '$rata', JK = '$jk', kode_jur = '$kode_jur', Tahun_ajaran = '$tahun_ajaran' WHERE id_pendaftar = $id_pendaftar";
    if ($conn->query($update_query)) {
        echo "<script>alert('Data berhasil diperbarui!');window.location='data_pendaftar.php';</script>";
    } else {
        die("Error: " . $conn->error);
    }
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Lengkap Pendaftar PPDB</title>
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
            max-width: 1500px;
            margin: 40px auto;
            margin-top: 100px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background: #fff;
        }

        th,
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
        }

        th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
        }

        tr:hover {
            background: #f8fafc;
        }

        .btn-hapus {
            background-color: rgb(193, 48, 60);
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
            background-color: rgb(160, 42, 42);
            /* Darker red */
        }

        .btn-k {
            background-color: rgb(116, 56, 6);
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

        .btn-edit {
            background-color: rgb(116, 56, 6);
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

        .btn-edit:hover {
            background-color: rgb(82, 39, 4);
            /* Darker red */
        }

        @media (max-width: 700px) {
            .container {
                padding: 1rem;
            }

            table,
            th,
            td {
                font-size: 13px;
            }

            th,
            td {
                padding: 6px 4px;
            }
        }

        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fff;
            padding: 2rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            width: 500px;
            max-width: 90%;
        }

        .close-btn {
            color: #334155;
            float: right;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover {
            color: #1e293b;
        }

        .popup-content h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }

        .popup-content form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .popup-content form label {
            font-weight: bold;
            color: #1e293b;
        }

        .popup-content form input,
        .popup-content form select,
        .popup-content form button {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
        }

        .popup-content form button {
            background-color: #4B3621;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .popup-content form button:hover {
            background-color: #3A2A18;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Data Lengkap Pendaftar PPDB</h2>
        <table>
            <thead>
                <tr>
                    <th>No Pendaftar</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Rata-Rata</th>
                    <th>Jenis Kelamin</th>
                    <th>Jurusan</th>
                    <th>Tahun ajaran</th>
                    <th>Petugas</th>
                    <?php if ($_SESSION['role'] === 'petugas'): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendaftar)): ?>
                    <tr>
                        <td colspan="10" style="color:#888;">Belum ada data pendaftar.</td>
                    </tr>
                <?php else:
                    $no = 1;
                    foreach ($pendaftar as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['no_pendaftar']) ?></td>
                            <td><?= htmlspecialchars($row['NIS']) ?></td>
                            <td><?= htmlspecialchars($row['Nama']) ?></td>
                            <td><?= htmlspecialchars($row['Email']) ?></td>
                            <td><?= htmlspecialchars($row['Telepon']) ?></td>
                            <td><?= htmlspecialchars($row['Rata']) ?></td>
                            <td><?= htmlspecialchars($row['JK'] === 'L' ? 'Laki-Laki' : 'Perempuan') ?></td>
                            <td><?= htmlspecialchars($row['Nama_jurusan']) ?></td>
                            <td><?= htmlspecialchars($row['Tahun_ajaran']) ?></td>
                            <td><?= htmlspecialchars($row['nama_petugas']) ?></td>
                            <td>
                                <?php if ($_SESSION['role'] === 'petugas'): ?>
                                    <a href="edit_pendaftar.php?id=<?= $row['id_pendaftar'] ?>" class="btn-edit">
                                        <i class='fas fa-edit'></i> Edit
                                    </a>
                                    <a href="data_pendaftar.php?delete=<?= $row['id_pendaftar'] ?>" class="btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class='fas fa-trash'></i> Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Popup -->
    <center>
        <div id="editPopup" class="popup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">&times;</span>
                <h2>Edit Data Pendaftar</h2>
                <form method="POST" id="editForm">
                    <input type="hidden" name="id_pendaftar" id="id_pendaftar">
                    <label for="nis">NIS</label>
                    <input type="text" name="nis" id="nis" required>
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" id="nama" required>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                    <label for="telepon">Telepon</label>
                    <input type="text" name="telepon" id="telepon" required>
                    <label for="rata">Rata-Rata</label>
                    <input type="number" name="rata" id="rata" required>
                    <label for="jk">Jenis Kelamin</label>
                    <select name="jk" id="jk" required>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <label for="kode_jur">Jurusan</label>
                    <select name="kode_jur" id="kode_jur" required>
                        <?php
                        $jurusan = $conn->query("SELECT * FROM jurusan");
                        while ($row = $jurusan->fetch_assoc()) {
                            echo "<option value='{$row['Kode_jur']}'>{$row['Nama_jurusan']}</option>";
                        }
                        ?>
                    </select>
                    <label for="tahun_ajaran">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahun_ajaran" required>
                    <button type="submit" name="edit_pendaftar">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </center>

    <script>
        // Function to open the edit popup and populate the form
        function openEditPopup(data) {
            document.getElementById('id_pendaftar').value = data.id_pendaftar;
            document.getElementById('nis').value = data.nis;
            document.getElementById('nama').value = data.nama;
            document.getElementById('email').value = data.email;
            document.getElementById('telepon').value = data.telepon;
            document.getElementById('rata').value = data.rata;
            document.getElementById('jk').value = data.jk;
            document.getElementById('kode_jur').value = data.kode_jur;
            document.getElementById('tahun_ajaran').value = data.tahun_ajaran;
            document.getElementById('editPopup').style.display = 'block';
        }

        // Function to close the popup
        function closePopup() {
            document.getElementById('editPopup').style.display = 'none';
        }

        // Event listener for the edit buttons
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const row = this.closest('tr');
                const data = {
                    id_pendaftar: row.children[0].textContent,
                    nis: row.children[1].textContent,
                    nama: row.children[2].textContent,
                    email: row.children[3].textContent,
                    telepon: row.children[4].textContent,
                    rata: row.children[5].textContent,
                    jk: row.children[6].textContent === 'Laki-Laki' ? 'L' : 'P',
                    kode_jur: row.children[7].textContent,
                    tahun_ajaran: row.children[8].textContent
                };
                openEditPopup(data);
            });
        });

        // Event listener for the close button
        document.querySelector('.close-btn').addEventListener('click', closePopup);
    </script>
</body>

</html>