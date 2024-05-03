<?php
session_start();
require_once 'config.php';
require_once 'applyWatermark.php';

function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website Title</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <div class="headerleft">
<h1>AquaMark</h1>
    <?php if (isUserLoggedIn()): ?>
        <h3>Привет, <?php echo $_SESSION['username']; ?></h3>
    <?php endif;?>
</div>
    <div class="headerrihgt">
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="insrtuct.php">Инструкция</a></li>
                <?php if (isUserLoggedIn()): ?>
                    <li><a href="profile.php">Личный кабинет</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Войти</a></li>
                    <li><a href="register.php">Зарегистрироваться</a></li>
                <?php endif;?>
            </ul>
        </nav>
    </div>
</header>
