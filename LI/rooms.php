<?php

require_once "auth.php";
requireLogin();

require_once "db.php";

/**
 * Вычисляет возраст ребёнка по дате рождения.
 *
 * @param string|null $birthDate Дата рождения ребёнка.
 * @return int
 */
function getAge(?string $birthDate): int
{
    if (empty($birthDate)) {
        return 0;
    }

    return (new DateTime($birthDate))->diff(new DateTime())->y;
}

$errors = [];
$successMessage = "";

// Сообщения после redirect
if (isset($_GET["reset"]) && $_GET["reset"] === "1" && isAdmin()) {
    $successMessage = "Распределение успешно сброшено.";
}

if (isset($_GET["assigned"]) && $_GET["assigned"] === "1" && isAdmin()) {
    $successMessage = "Автоматическое распределение выполнено.";
}

// Обработка действий администратора
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isAdmin()) {
        http_response_code(403);
        die("Доступ запрещён. Изменять комнаты может только администратор.");
    }

    $action = $_POST["action"] ?? "";

    // Добавление комнаты
    if ($action === "add_room") {
        $roomNumber = trim($_POST["room_number"] ?? "");
        $capacity = (int)($_POST["capacity"] ?? 0);

        if ($roomNumber === "") {
            $errors[] = "Введите номер комнаты.";
        }

        if ($capacity <= 0) {
            $errors[] = "Вместимость комнаты должна быть больше 0.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("
                INSERT INTO rooms (room_number, capacity)
                VALUES (?, ?)
            ");
            $stmt->bind_param("si", $roomNumber, $capacity);

            if ($stmt->execute()) {
                $successMessage = "Комната успешно добавлена.";
            } else {
                $errors[] = "Ошибка при добавлении комнаты. Возможно, такая комната уже существует.";
            }
        }
    }

    // Изменение вместимости комнаты
    if ($action === "update_capacity") {
        $roomId = (int)($_POST["room_id"] ?? 0);
        $capacity = (int)($_POST["capacity"] ?? 0);

        if ($roomId <= 0) {
            $errors[] = "Некорректный ID комнаты.";
        }

        if ($capacity <= 0) {
            $errors[] = "Вместимость должна быть больше 0.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("
                UPDATE rooms
                SET capacity = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $capacity, $roomId);

            if ($stmt->execute()) {
                $successMessage = "Вместимость комнаты обновлена.";
            } else {
                $errors[] = "Ошибка при обновлении вместимости.";
            }
        }
    }

    // Удаление комнаты
    if ($action === "delete_room") {
        $roomId = (int)($_POST["room_id"] ?? 0);

        if ($roomId <= 0) {
            $errors[] = "Некорректный ID комнаты.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("
                SELECT COUNT(*) AS total
                FROM children
                WHERE room_id = ?
            ");
            $stmt->bind_param("i", $roomId);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $childrenCount = (int)$row["total"];

            if ($childrenCount > 0) {
                $errors[] = "Нельзя удалить комнату, в которой уже есть дети.";
            } else {
                $stmt = $conn->prepare("
                    DELETE FROM rooms
                    WHERE id = ?
                ");
                $stmt->bind_param("i", $roomId);

                if ($stmt->execute()) {
                    $successMessage = "Комната успешно удалена.";
                } else {
                    $errors[] = "Ошибка при удалении комнаты.";
                }
            }
        }
    }
}

// Получаем список комнат
$roomsStmt = $conn->prepare("
    SELECT id, room_number, capacity
    FROM rooms
    ORDER BY CAST(room_number AS UNSIGNED), room_number
");
$roomsStmt->execute();
$roomsRes = $roomsStmt->get_result();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Комнаты</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>🏠 Комнаты</h1>

            <?php if (isAdmin()): ?>
                <div class="room-actions">
                    <a href="assign_rooms.php" class="edit-btn">⚡ Авто распределение</a>
                    <a href="reset.php" class="button-link">♻️ Reset</a>
                    <a href="export_excel.php" class="edit-btn">📥 Скачать Excel</a>
                </div>
            <?php else: ?>
                <p class="public-text">
                    Режим просмотра. Изменять комнаты и распределение может только администратор.
                </p>
            <?php endif; ?>
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

        <?php if (isAdmin()): ?>
            <div class="card">
                <h2>➕ Добавить комнату</h2>

                <div class="form-shell">
                    <form method="POST" class="child-form">
                        <input type="hidden" name="action" value="add_room">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="room_number">Номер комнаты</label>
                                <input
                                    id="room_number"
                                    type="text"
                                    name="room_number"
                                    required
                                    maxlength="20"
                                    placeholder="Например: 15"
                                >
                            </div>

                            <div class="form-group">
                                <label for="capacity">Вместимость</label>
                                <input
                                    id="capacity"
                                    type="number"
                                    name="capacity"
                                    required
                                    min="1"
                                    max="20"
                                    placeholder="Например: 4"
                                >
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="primary-button">Добавить комнату</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="room-grid">
            <?php while ($room = $roomsRes->fetch_assoc()): ?>
                <?php
                $roomId = (int)$room["id"];

                $childrenStmt = $conn->prepare("
                    SELECT id, name, birth_date, gender
                    FROM children
                    WHERE room_id = ?
                    ORDER BY name
                ");
                $childrenStmt->bind_param("i", $roomId);
                $childrenStmt->execute();

                $childrenResult = $childrenStmt->get_result();

                $children = [];
                while ($childRow = $childrenResult->fetch_assoc()) {
                    $children[] = $childRow;
                }
                ?>

                <div class="room-card">
                    <div class="room-title">
                        <h2>🏠 Комната <?= htmlspecialchars($room["room_number"]) ?></h2>
                        <span class="badge">
                            <?= count($children) ?> / <?= (int)$room["capacity"] ?>
                        </span>
                    </div>

                    <?php if (isAdmin()): ?>
                        <form method="POST" class="room-capacity-form">
                            <input type="hidden" name="action" value="update_capacity">
                            <input type="hidden" name="room_id" value="<?= $roomId ?>">

                            <div class="form-group">
                                <label>Вместимость</label>
                                <input
                                    type="number"
                                    name="capacity"
                                    value="<?= (int)$room["capacity"] ?>"
                                    min="1"
                                    max="20"
                                    required
                                >
                            </div>

                            <button type="submit">Сохранить</button>
                        </form>

                        <?php if (count($children) === 0): ?>
                            <form
                                method="POST"
                                class="room-delete-form"
                                onsubmit="return confirm('Удалить эту комнату?');"
                            >
                                <input type="hidden" name="action" value="delete_room">
                                <input type="hidden" name="room_id" value="<?= $roomId ?>">

                                <button type="submit" class="delete-room-button">
                                    Удалить пустую комнату
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <ul class="dropzone" data-room="<?= $roomId ?>">
                        <?php if (empty($children)): ?>
                            <li style="list-style:none; color:#64748b;">Пусто</li>
                        <?php else: ?>
                            <?php foreach ($children as $child): ?>
                                <li
                                    class="<?= isAdmin() ? 'draggable' : 'viewer-child-item' ?>"
                                    <?= isAdmin() ? 'draggable="true"' : '' ?>
                                    data-id="<?= (int)$child["id"] ?>"
                                >
                                    👶 <?= htmlspecialchars($child["name"]) ?>
                                    (
                                        <?= getAge($child["birth_date"]) ?>,
                                        <?= $child["gender"] === "male" ? "M" : "F" ?>
                                    )
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="card">
            <h2>⚠️ Не распределены</h2>

            <?php
            $unassignedStmt = $conn->prepare("
                SELECT id, name, birth_date, gender
                FROM children
                WHERE room_id IS NULL
                ORDER BY name
            ");
            $unassignedStmt->execute();
            $unassigned = $unassignedStmt->get_result();
            ?>

            <?php if ($unassigned->num_rows === 0): ?>
                <p>✔ Все дети распределены</p>
            <?php else: ?>
                <ul>
                    <?php while ($child = $unassigned->fetch_assoc()): ?>
                        <li>
                            👶 <?= htmlspecialchars($child["name"]) ?>
                            (
                                <?= getAge($child["birth_date"]) ?>,
                                <?= $child["gender"] === "male" ? "M" : "F" ?>
                            )
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php if (isAdmin()): ?>
<script>
let draggedId = null;

document.querySelectorAll(".draggable").forEach(el => {
    el.addEventListener("dragstart", () => {
        draggedId = el.dataset.id;
    });
});

document.querySelectorAll(".dropzone").forEach(zone => {
    zone.addEventListener("dragover", e => {
        e.preventDefault();
    });

    zone.addEventListener("drop", e => {
        e.preventDefault();

        if (!draggedId) return;

        fetch("move_child.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `child_id=${encodeURIComponent(draggedId)}&room_id=${encodeURIComponent(zone.dataset.room)}`
        })
        .then(r => r.text())
        .then(res => {
            if (res.trim() === "OK") {
                location.reload();
            } else {
                alert(res);
            }
        })
        .catch(() => {
            alert("Ошибка при перемещении ребёнка");
        });
    });
});
</script>
<?php endif; ?>

</body>
</html>