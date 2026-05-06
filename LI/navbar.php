<?php
require_once "auth.php";
startSessionIfNeeded();
?>

<header class="top-navbar">
    <div class="top-navbar-inner">
        <a href="index.php" class="brand">🏕 Camp System</a>

        <nav class="top-nav-links">
            <a href="index.php">Главная</a>

            <?php if (isLoggedIn()): ?>
                <a href="list_children.php">Дети</a>
                <a href="rooms.php">Комнаты</a>

                <?php if (isAdmin()): ?>
                    <a href="add_child.php">Добавить ребёнка</a>
                    <a href="admin_users.php">Администраторы</a>
                <?php endif; ?>

                <a href="logout.php" class="logout-link">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>