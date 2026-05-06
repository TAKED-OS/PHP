<?php
session_start();
require_once "db.php";

$errors = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($username === "") {
        $errors[] = "Введите логин.";
    } elseif (mb_strlen($username) < 3) {
        $errors[] = "Логин должен содержать минимум 3 символа.";
    } elseif (mb_strlen($username) > 100) {
        $errors[] = "Логин слишком длинный.";
    }

    if ($password === "") {
        $errors[] = "Введите пароль.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать минимум 6 символов.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Пароли не совпадают.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Пользователь с таким логином уже существует.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt = $conn->prepare("
                INSERT INTO users (username, password_hash, role)
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param("sss", $username, $passwordHash, $role);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            }

            $errors[] = "Ошибка при регистрации.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container auth-container">

        <div class="card">
            <h1>📝 Регистрация</h1>

            <p class="public-text">
                Создайте аккаунт для входа в систему. После регистрации пользователь получает доступ к защищённым разделам.
            </p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="card error-box">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="form-shell auth-shell">
                <form method="POST" class="child-form">

                    <div class="form-group">
                        <label for="username">Логин</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="<?= htmlspecialchars($username) ?>"
                            required
                            minlength="3"
                            maxlength="100"
                            placeholder="Например: user1"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="6"
                            placeholder="Минимум 6 символов"
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Повторите пароль</label>
                        <input
                            id="confirm_password"
                            type="password"
                            name="confirm_password"
                            required
                            minlength="6"
                            placeholder="Повторите пароль"
                        >
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-button">Зарегистрироваться</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>