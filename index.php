<?php if (session_status() === PHP_SESSION_NONE)
    session_start(); ?>

<style>
    .container {
        height: 100%;
        width: 100%;
        background-color: #FAEBD7;
    }
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="container"><?php include 'header.php'; ?>
        <?php include 'dashboard.php'; ?>
    </div>
</body>

</html>