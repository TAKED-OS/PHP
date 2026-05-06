<?php
require_once "auth.php";
startSessionIfNeeded();

require_once "db.php";

$totalChildren = 0;
$totalRooms = 0;
$totalBeds = 0;
$assignedChildren = 0;
$latestChildren = [];

$result = $conn->query("SELECT COUNT(*) AS total FROM children");
if ($result) {
    $row = $result->fetch_assoc();
    $totalChildren = (int)$row["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM rooms");
if ($result) {
    $row = $result->fetch_assoc();
    $totalRooms = (int)$row["total"];
}

$result = $conn->query("SELECT SUM(capacity) AS total FROM rooms");
if ($result) {
    $row = $result->fetch_assoc();
    $totalBeds = (int)($row["total"] ?? 0);
}

$result = $conn->query("SELECT COUNT(*) AS total FROM children WHERE room_id IS NOT NULL");
if ($result) {
    $row = $result->fetch_assoc();
    $assignedChildren = (int)$row["total"];
}

$result = $conn->query("
    SELECT name, gender, birth_date, group_name
    FROM children
    ORDER BY id DESC
    LIMIT 5
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $latestChildren[] = $row;
    }
}

/**
 * Вычисляет возраст ребёнка по дате рождения.
 *
 * @param string|null $birthDate Дата рождения.
 * @return int
 */
function calculateAge(?string $birthDate): int
{
    if (empty($birthDate)) {
        return 0;
    }

    return (new DateTime($birthDate))->diff(new DateTime())->y;
}

/**
 * Возвращает удобное название пола.
 *
 * @param string|null $gender Пол из базы данных.
 * @return string
 */
function formatGender(?string $gender): string
{
    if ($gender === "male") {
        return "Мальчик";
    }

    if ($gender === "female") {
        return "Девочка";
    }

    return "Не указано";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Camp Room Allocation System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>🏕 Camp Room Allocation System</h1>

            <p class="public-text">
                Веб-приложение предназначено для автоматического распределения детей по комнатам в лагере.
                Система учитывает пол, возраст, вместимость комнат и пожелания детей.
            </p>
        </div>

        <div class="card">
            <h2>📊 Статистика лагеря</h2>

            <div class="stats-grid">
                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)$totalChildren) ?></h2>
                    <p>Детей в базе</p>
                </div>

                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)$totalRooms) ?></h2>
                    <p>Комнат</p>
                </div>

                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)$totalBeds) ?></h2>
                    <p>Всего мест</p>
                </div>

                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)$assignedChildren) ?></h2>
                    <p>Распределено детей</p>
                </div>

                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)max(0, $totalChildren - $assignedChildren)) ?></h2>
                    <p>Не распределено</p>
                </div>

                <div class="stat-card">
                    <h2><?= htmlspecialchars((string)max(0, $totalBeds - $assignedChildren)) ?></h2>
                    <p>Свободных мест</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>👶 Последние добавленные дети</h2>

            <?php if (empty($latestChildren)): ?>
                <p>Пока нет добавленных детей.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Пол</th>
                                <th>Возраст</th>
                                <th>Группа</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestChildren as $child): ?>
                                <tr>
                                    <td class="name-cell"><?= htmlspecialchars($child["name"]) ?></td>
                                    <td><?= htmlspecialchars(formatGender($child["gender"] ?? null)) ?></td>
                                    <td><?= htmlspecialchars((string)calculateAge($child["birth_date"] ?? null)) ?></td>
                                    <td><?= htmlspecialchars($child["group_name"] ?? "Без группы") ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>ℹ️ Возможности системы</h2>

            <div class="feature-grid">
                <div class="feature-card">
                    <h3>⚡ Автоматическое распределение</h3>
                    <p>Система распределяет детей по комнатам с учётом ограничений.</p>
                </div>

                <div class="feature-card">
                    <h3>🧒 Учёт пола и возраста</h3>
                    <p>Мальчики и девочки не смешиваются, а разница возраста контролируется.</p>
                </div>

                <div class="feature-card">
                    <h3>✋ Ручная корректировка</h3>
                    <p>Администратор может вручную перемещать детей между комнатами.</p>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>