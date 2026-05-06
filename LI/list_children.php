<?php
require_once "auth.php";
requireLogin();

require_once "db.php";

$name = trim($_GET["name"] ?? "");
$gender = trim($_GET["gender"] ?? "");
$groupName = trim($_GET["group_name"] ?? "");
$ageFrom = trim($_GET["age_from"] ?? "");
$ageTo = trim($_GET["age_to"] ?? "");

$where = [];
$params = [];
$types = "";

if ($name !== "") {
    $where[] = "name LIKE ?";
    $params[] = "%" . $name . "%";
    $types .= "s";
}

if ($gender !== "") {
    $where[] = "gender = ?";
    $params[] = $gender;
    $types .= "s";
}

if ($groupName !== "") {
    $where[] = "group_name LIKE ?";
    $params[] = "%" . $groupName . "%";
    $types .= "s";
}

if ($ageFrom !== "" && is_numeric($ageFrom)) {
    $where[] = "age >= ?";
    $params[] = (int)$ageFrom;
    $types .= "i";
}

if ($ageTo !== "" && is_numeric($ageTo)) {
    $where[] = "age <= ?";
    $params[] = (int)$ageTo;
    $types .= "i";
}

$sql = "
    SELECT id, name, age, gender, group_name, wishes, notes, birth_date, room_id, created_at
    FROM children
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$children = [];

while ($row = $result->fetch_assoc()) {
    $children[] = $row;
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

/**
 * Возвращает текст для комнаты.
 *
 * @param mixed $roomId ID комнаты.
 * @return string
 */
function formatRoom($roomId): string
{
    if ($roomId === null || $roomId === "") {
        return "Не распределён";
    }

    return "Комната " . $roomId;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список детей</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="page-center">
    <div class="container">

        <div class="card">
            <h1>👶 Список детей</h1>
        </div>

        <?php if (isset($_GET["deleted"]) && $_GET["deleted"] === "1"): ?>
            <div class="card success-box">
                <p>Ребёнок успешно удалён.</p>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>🔎 Поиск детей</h2>

            <form method="GET" class="search-form">
                <div>
                    <label for="name">Имя</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($name) ?>"
                        placeholder="Например: Александр"
                    >
                </div>

                <div>
                    <label for="gender">Пол</label>
                    <select id="gender" name="gender">
                        <option value="">Любой</option>
                        <option value="male" <?= $gender === "male" ? "selected" : "" ?>>Мальчик</option>
                        <option value="female" <?= $gender === "female" ? "selected" : "" ?>>Девочка</option>
                    </select>
                </div>

                <div>
                    <label for="group_name">Группа</label>
                    <input
                        id="group_name"
                        type="text"
                        name="group_name"
                        value="<?= htmlspecialchars($groupName) ?>"
                        placeholder="Например: Отряд 1"
                    >
                </div>

                <div>
                    <label for="age_from">Возраст от</label>
                    <input
                        id="age_from"
                        type="number"
                        name="age_from"
                        value="<?= htmlspecialchars($ageFrom) ?>"
                        min="6"
                        max="18"
                    >
                </div>

                <div>
                    <label for="age_to">Возраст до</label>
                    <input
                        id="age_to"
                        type="number"
                        name="age_to"
                        value="<?= htmlspecialchars($ageTo) ?>"
                        min="6"
                        max="18"
                    >
                </div>

                <button type="submit">Найти</button>
                <a href="list_children.php" class="button-link">Сбросить</a>
            </form>
        </div>

        <div class="card">
            <h2>Найдено: <?= count($children) ?></h2>

            <?php if (empty($children)): ?>
                <p>Дети не найдены.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Возраст</th>
                                <th>Пол</th>
                                <th>Группа</th>
                                <th>Пожелания</th>
                                <th>Примечания</th>
                                <th>Дата рождения</th>
                                <th>Комната</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($children as $child): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$child["id"]) ?></td>
                                    <td class="name-cell"><?= htmlspecialchars($child["name"]) ?></td>
                                    <td><?= htmlspecialchars((string)($child["age"] ?? "")) ?></td>
                                    <td><?= htmlspecialchars(formatGender($child["gender"] ?? null)) ?></td>
                                    <td><?= htmlspecialchars($child["group_name"] ?? "") ?></td>
                                    <td><?= htmlspecialchars($child["wishes"] ?? "") ?></td>
                                    <td><?= htmlspecialchars($child["notes"] ?? "") ?></td>
                                    <td><?= htmlspecialchars($child["birth_date"] ?? "") ?></td>
                                    <td><?= htmlspecialchars(formatRoom($child["room_id"])) ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <?php if (isAdmin()): ?>
                                                <a class="edit-btn" href="edit_child.php?id=<?= urlencode((string)$child["id"]) ?>">
                                                    ✏️ Редактировать
                                                </a>

                                                <a
                                                    class="delete-btn"
                                                    href="delete_child.php?id=<?= urlencode((string)$child["id"]) ?>"
                                                    onclick="return confirm('Удалить этого ребёнка?');"
                                                >
                                                    🗑 Удалить
                                                </a>
                                            <?php else: ?>
                                                <span class="muted-text">Только просмотр</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
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