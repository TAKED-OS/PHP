<?php
require_once "auth.php";
requireAdmin();

require_once "db.php";

$errors = [];
$successMessage = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($username === "") {
        $errors[] = "Введите логин администратора.";
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
            $role = "admin";

            $stmt = $conn->prepare("
                INSERT INTO users (username, password_hash, role)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $username, $passwordHash, $role);

            if ($stmt->execute()) {
                $successMessage = "Новый администратор успешно создан.";
                $username = "";
            } else {
                $errors[] = "Ошибка при создании администратора.";
            }
        }
    }
}

$users = [];

$result = $conn->query("
    SELECT id, username, role, created_at
    FROM users
    ORDER BY id DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

/**
 * Возвращает красивое название роли.
 *
 * @param string|null $role Роль пользователя.
 * @return string
 */
function formatUserRole(?string $role): string
{
    if ($role === "admin") {
        return "Администратор";
    }

    if ($role === "user") {
        return "Пользователь";
    }

    return "Не указано";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление администраторами</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>🛠 Управление администраторами</h1>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="card success-box">
                <p><?= htmlspecialchars($successMessage) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="card error-box">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>➕ Создать нового администратора</h2>

            <div class="form-shell">
                <form method="POST" class="child-form">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Логин администратора</label>
                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="<?= htmlspecialchars($username) ?>"
                                required
                                minlength="3"
                                maxlength="100"
                                placeholder="Например: admin2"
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
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-button">Создать администратора</button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card">
            <h2>👥 Список пользователей</h2>

            <?php if (empty($users)): ?>
                <p>Пользователи не найдены.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Логин</th>
                                <th>Роль</th>
                                <th>Дата создания</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$user["id"]) ?></td>
                                    <td class="name-cell"><?= htmlspecialchars($user["username"]) ?></td>
                                    <td><?= htmlspecialchars(formatUserRole($user["role"] ?? null)) ?></td>
                                    <td><?= htmlspecialchars($user["created_at"] ?? "") ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>