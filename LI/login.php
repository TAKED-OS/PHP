<?php
session_start();
require_once "db.php";

$errors = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors[] = "Введите логин.";
    }

    if ($password === "") {
        $errors[] = "Введите пароль.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            SELECT id, username, password_hash, role
            FROM users
            WHERE username = ?
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            header("Location: index.php");
            exit;
        }

        $errors[] = "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container auth-container">

        <div class="card">
            <h1>🔐 Вход в систему</h1>

            <p class="public-text">
                Войдите в систему, чтобы получить доступ к управлению детьми, комнатами и распределением.
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
                            placeholder="Введите логин"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Введите пароль"
                        >
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-button">Войти</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>