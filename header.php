<?php if (session_status() === PHP_SESSION_NONE)
    session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB IGASAR PINDAD BANDUNG</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            margin: 0;
            padding-top: 40px;
            text-align: center;
        }

        .header {
            background:rgb(243, 201, 147);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
        }

        .logo-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .logo-title:hover {
            transform: scale(1.02);
        }

        .logo-container {
            position: relative;
            width: 50px;
            height: 50px;
            background:rgb(96, 61, 15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .logo-container img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .title-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
            animation-delay: 0.2s;
        }

        .sekolah {
            font-weight: 700;
            color:rgb(114, 74, 21);
            transition: all 0.3s ease;
        }

        .sekolah:first-child {
            font-size: 1.25rem;
            background: #D2B48C;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sekolah:last-child {
            font-size: 0.9rem;
            color:rgb(255, 255, 255);
            font-weight: 500;
        }

        .navigation {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: black;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            background: none;
            border: 1px solid transparent;
        }

        .nav-link:hover {
            color: white;
            background:rgb(133, 95, 55);
            border-color: rgb(152, 109, 64);
        }

        .nav-link.active {
            background: #4B3621;
            color: white;
            border-color:rgb(65, 47, 29);
        }

        .nav-link i {
            margin-right: 5px;
        }

        .nav-link.logout {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .nav-link.logout:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }


        .nav-link.login:hover {
            color: white;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #475569;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .logo-title {
                gap: 0.75rem;
            }

            .logo-container {
                width: 45px;
                height: 45px;
            }

            .logo-container img {
                width: 28px;
                height: 28px;
            }

            .sekolah:first-child {
                font-size: 1.1rem;
            }

            .sekolah:last-child {
                font-size: 0.8rem;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .navigation {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                border-top: 1px solid rgba(226, 232, 240, 0.8);
                flex-direction: column;
                gap: 0;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .navigation.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .nav-link {
                width: 100%;
                padding: 1rem 2rem;
                border-radius: 0;
                justify-content: flex-start;
            }

            .nav-link:hover {
                background: rgba(59, 130, 246, 0.05);
            }
        }

        @media (max-width: 480px) {
            .header-container {
                padding: 0.75rem;
            }

            .logo-title {
                gap: 0.5rem;
            }

            .sekolah:first-child {
                font-size: 1rem;
            }

            .sekolah:last-child {
                font-size: 0.75rem;
            }
        }

        /* Animation - Removed slideDown for header */

        .nav-link {
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .nav-link:nth-child(1) {
            animation-delay: 0.1s;
        }

        .nav-link:nth-child(2) {
            animation-delay: 0.2s;
        }

        .nav-link:nth-child(3) {
            animation-delay: 0.3s;
        }

        .nav-link:nth-child(4) {
            animation-delay: 0.4s;
        }

        .nav-link:nth-child(5) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
    </style>
</head>

<body>
    <div class="header">
        <div class="header-container">
            <div class="logo-title">
                <div class="logo-container">
                    <img src="igasar.png" alt="Logo SMK Igasar Pindad">
                </div>
                <div class="title-text">
                    <div class="sekolah">PPDB</div>
                    <div class="sekolah">SMK IGASAR PINDAD</div>
                </div>
            </div>

            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="navigation" id="navigation">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    Beranda
                </a>
                <!-- <a href="info-jurusan.php" class="nav-link">
                    <i class="fas fa-graduation-cap"></i>
                    Jurusan
                </a> -->

                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_panel.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            Admin Panel
                        </a>
                        <!-- <a href="data_peserta.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            Data Peserta
                        </a> -->
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'kepsek'): ?>
                        <a href="laporan-keuangan.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            Laporan Keuangan
                        </a>
                        <a href="data_pendaftar.php" class="nav-link">
                            <i class="fas fa-user-check"></i>
                            Data Pendaftar
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'tu'): ?>
                        <a href="bayar.php" class="nav-link">
                            <i class="fas fa-credit-card"></i>
                            Pembayaran
                        </a>
                        <a href="keuangan.php" class="nav-link">
                            <i class="fas fa-wallet"></i>
                            Biaya tahunan
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'petugas'): ?>
                        <a href="input_pendaftaran.php" class="nav-link">
                            <i class="fas fa-pen"></i>
                            Input Pendaftar
                        </a>
                        <a href="data_pendaftar.php" class="nav-link">
                            <i class="fas fa-user-check"></i>
                            Data Pendaftar
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-link logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-link login">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                <?php endif; ?>
            </nav>

        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const navigation = document.getElementById('navigation');
            const toggle = document.querySelector('.mobile-menu-toggle i');

            navigation.classList.toggle('active');

            if (navigation.classList.contains('active')) {
                toggle.classList.remove('fa-bars');
                toggle.classList.add('fa-times');
            } else {
                toggle.classList.remove('fa-times');
                toggle.classList.add('fa-bars');
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function (event) {
            const navigation = document.getElementById('navigation');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (!navigation.contains(event.target) && !toggle.contains(event.target)) {
                navigation.classList.remove('active');
                document.querySelector('.mobile-menu-toggle i').classList.remove('fa-times');
                document.querySelector('.mobile-menu-toggle i').classList.add('fa-bars');
            }
        });

        // Highlight active page
        document.addEventListener('DOMContentLoaded', function () {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });

        // Scroll effect for header
        let lastScrollTop = 0;
        window.addEventListener('scroll', function () {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const header = document.querySelector('.header');

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                header.style.transform = 'translateY(0)';
            }

            lastScrollTop = scrollTop;
        });
    </script>
</body>

</html>