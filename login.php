<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PPDB - SMK Igasar Pindad</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="academic-year">TA 2025/2026</div>

        <div class="school-header">
            <div class="logo-container">
                <div class="logo">
                    <img src="igasar.png" alt="Logo SMK Igasar Pindad" onerror="this.style.display='none';">
                </div>
            </div>
            <h1 class="school-name">SMK Igasar Pindad</h1>
        </div>



        <h2 class="form-title">Masuk</h2>

        <form action="login_process.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="user" class="form-label">Username</label>
                <input type="text" id="user" name="user" class="form-input" placeholder="Masukkan email atau username"
                    required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="pass" class="form-label">Password</label>
                <input type="password" id="pass" name="pass" class="form-input" placeholder="Masukkan password" required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="login-button" id="loginBtn">
                Masuk ke Portal PPDB
            </button>
        </form>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #FAEBD7;
            /* Warna latar belakang yang sama dengan content */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1a202c;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 32px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: slide-in-up 0.8s ease-out;
            /* Animasi slide-in-up */
        }

        .school-header {
            margin-bottom: 24px;
        }

        .logo {
            width: 72px;
            height: 72px;
            background: rgb(133, 95, 55);
            /* Warna yang sama dengan elemen di content */
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: rgb(180, 130, 76);
            /* Warna teks yang sama dengan content */
            margin-bottom: 4px;
        }

        .school-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        .form-title {
            font-size: 24px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
            background: #f9fafb;
            transition: border-color 0.2s ease;
        }

        .form-input:focus {
            border-color:rgb(202, 187, 171);
            box-shadow: 0 0 0 3px rgba(0, 217, 217, 0.2);
        }

        .login-button {
            width: 100%;
            padding: 12px;
            background: rgb(133, 95, 55);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .login-button:hover {
            background:rgb(104, 75, 43);
            transform: translateY(-2px);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Animasi slide-in-up */
        @keyframes slide-in-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
                margin: 16px;
                max-width: none;
            }

            .school-name {
                font-size: 22px;
            }

            .form-title {
                font-size: 24px;
            }

            .academic-year {
                position: static;
                display: block;
                text-align: center;
                margin-bottom: 16px;
            }
        }

        /* Loading state */
        .login-button.loading {
            position: relative;
            color: transparent;
        }

        .login-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Focus styles for accessibility */
        .form-input:focus,
        .login-button:focus,
        .forgot-link:focus,
        .register-link:focus {
            outline: 2px solid #3A5C96;
            outline-offset: 2px;
        }
    </style>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });

        // Simple fade-in animation
        window.addEventListener('load', function () {
            const container = document.querySelector('.login-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(10px)';
            container.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

            requestAnimationFrame(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>

</html>