<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "ppdb");

$jumlah_pendaftar = 0;
$jurusan_paling_diminati = "-";
$jumlah_pendaftar_jurusan = 0;

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql_jumlah = "SELECT COUNT(*) as total FROM pendaftaran";
$result_jumlah = $conn->query($sql_jumlah);

if (!$result_jumlah) {
    die("Error pada query jumlah pendaftar: " . $conn->error);
}

if ($result_jumlah && $row = $result_jumlah->fetch_assoc()) {
    $jumlah_pendaftar = $row['total'];
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @keyframes slide-in-up {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .slide-in-up {
            animation: slide-in-up 0.8s ease-out;
        }

        .content-container {
            text-align: center;
            padding: 2rem;
            background-color: #FAEBD7;
        }

        .content-container h1 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .content-container p {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        .info-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .info-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
            overflow: hidden;
        }

        .info-header {
            background: #D2B48C;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 0.75rem;
        }

        .info-body {
            padding: 1.5rem;
        }

        .info-body i {
            font-size: 2rem;
            color: #808000;
            margin-bottom: 0.5rem;
        }

        .info-body p {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0.5rem 0;
        }

        .info-body h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #808000;
            margin: 0.5rem 0;
        }
    </style>
</head>

<body>


    <div class="content-container">
        <h1 class="slide-in-up">Selamat datang di aplikasi PPDB Igasar Pindad</h1>
        <div class="info-container slide-in-up">
            <div class="info-card">
                <div class="info-header">Pendaftar PPDB</div>
                <div class="info-body">
                    <i class="fas fa-users"></i>
                    <p>Jumlah Pendaftar</p>
                    <h2><?= $jumlah_pendaftar ?></h2>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>