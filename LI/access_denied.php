<?php
require_once "auth.php";
startSessionIfNeeded();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доступ запрещён</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container auth-container">

        <div class="card">
            <h1>⛔ Доступ запрещён</h1>

            <p class="public-text">
                У вас нет прав для просмотра этой страницы.
                Эта функция доступна только администратору.
            </p>

            <div class="form-actions center-actions">
                <a href="index.php" class="primary-link">На главную</a>
                <a href="rooms.php" class="secondary-link">К комнатам</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>