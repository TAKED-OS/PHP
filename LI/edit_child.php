<?php
require_once "auth.php";
requireAdmin();

require_once "db.php";

$errors = [];
$successMessage = "";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Некорректный ID ребёнка.");
}

$stmt = $conn->prepare("
    SELECT id, name, age, gender, group_name, wishes, notes, birth_date, room_id
    FROM children
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$child = $result->fetch_assoc();

if (!$child) {
    die("Ребёнок не найден.");
}

$name = $child["name"] ?? "";
$birthDate = $child["birth_date"] ?? "";
$gender = $child["gender"] ?? "";
$groupName = $child["group_name"] ?? "";
$wishes = $child["wishes"] ?? "";
$notes = $child["notes"] ?? "";
$age = $child["age"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $birthDate = trim($_POST["birth_date"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $groupName = trim($_POST["group_name"] ?? "");
    $wishes = trim($_POST["wishes"] ?? "");
    $notes = trim($_POST["notes"] ?? "");

    if ($name === "") {
        $errors[] = "Введите имя ребёнка.";
    } elseif (mb_strlen($name) < 2) {
        $errors[] = "Имя должно содержать минимум 2 символа.";
    } elseif (mb_strlen($name) > 150) {
        $errors[] = "Имя слишком длинное.";
    }

    if ($birthDate === "") {
        $errors[] = "Укажите дату рождения.";
    } else {
        $date = DateTime::createFromFormat("Y-m-d", $birthDate);

        if (!$date) {
            $errors[] = "Некорректный формат даты рождения.";
        } else {
            $today = new DateTime();

            if ($date > $today) {
                $errors[] = "Дата рождения не может быть в будущем.";
            } else {
                $age = $date->diff($today)->y;

                if ($age < 6 || $age > 18) {
                    $errors[] = "Возраст ребёнка должен быть от 6 до 18 лет.";
                }
            }
        }
    }

    if ($gender === "") {
        $errors[] = "Выберите пол.";
    } elseif (!in_array($gender, ["male", "female"], true)) {
        $errors[] = "Некорректное значение пола.";
    }

    if ($groupName !== "" && mb_strlen($groupName) > 100) {
        $errors[] = "Название группы слишком длинное.";
    }

    if (mb_strlen($wishes) > 1000) {
        $errors[] = "Поле пожеланий слишком длинное.";
    }

    if (mb_strlen($notes) > 1000) {
        $errors[] = "Поле примечаний слишком длинное.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE children
            SET name = ?,
                age = ?,
                gender = ?,
                group_name = ?,
                wishes = ?,
                notes = ?,
                birth_date = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sisssssi",
            $name,
            $age,
            $gender,
            $groupName,
            $wishes,
            $notes,
            $birthDate,
            $id
        );

        if ($stmt->execute()) {
            $successMessage = "Данные ребёнка успешно обновлены.";
        } else {
            $errors[] = "Ошибка при обновлении данных: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать ребёнка</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>✏️ Редактировать ребёнка</h1>
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
            <div class="form-shell">
                <form method="POST" class="child-form">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Имя ребёнка</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="<?= htmlspecialchars($name) ?>"
                                required
                                minlength="2"
                                maxlength="150"
                            >
                        </div>

                        <div class="form-group">
                            <label for="birth_date">Дата рождения</label>
                            <input
                                id="birth_date"
                                type="date"
                                name="birth_date"
                                value="<?= htmlspecialchars($birthDate) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="gender">Пол</label>
                            <select id="gender" name="gender" required>
                                <option value="">Выберите пол</option>
                                <option value="male" <?= $gender === "male" ? "selected" : "" ?>>Мальчик</option>
                                <option value="female" <?= $gender === "female" ? "selected" : "" ?>>Девочка</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="group_name">Группа / отряд</label>
                            <input
                                id="group_name"
                                type="text"
                                name="group_name"
                                value="<?= htmlspecialchars($groupName) ?>"
                                maxlength="100"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="wishes">Пожелания</label>
                        <textarea
                            id="wishes"
                            name="wishes"
                            maxlength="1000"
                        ><?= htmlspecialchars($wishes) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="notes">Примечания</label>
                        <textarea
                            id="notes"
                            name="notes"
                            maxlength="1000"
                        ><?= htmlspecialchars($notes) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-button">Сохранить изменения</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>