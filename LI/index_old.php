<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>🏕 Система лагеря</h1>

            <p style="text-align:center;">
                Управление детьми, комнатами и распределением
            </p>

            <div class="nav">
                <a href="list_children.php">👶 Дети</a>
                <a href="add_child.php">➕ Добавить ребёнка</a>
                <a href="rooms.php">🏠 Комнаты</a>
                <a href="assign_rooms.php">⚡ Авто распределение</a>
                <a href="reset.php">♻ Reset</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>