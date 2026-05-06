<?php

require_once "auth.php";
requireAdmin();

require_once "db.php";

/**
 * Возвращает возраст по дате рождения.
 *
 * @param string|null $birthDate
 * @param int|null $storedAge
 * @return int
 */
function getChildAge(?string $birthDate, ?int $storedAge): int
{
    if (!empty($birthDate)) {
        return (new DateTime($birthDate))->diff(new DateTime())->y;
    }

    return $storedAge ?? 0;
}

$childId = isset($_POST["child_id"]) ? (int)$_POST["child_id"] : 0;
$roomId = isset($_POST["room_id"]) ? (int)$_POST["room_id"] : 0;

if ($childId <= 0 || $roomId <= 0) {
    echo "Некорректные данные.";
    exit;
}

// Получаем ребёнка
$stmt = $conn->prepare("
    SELECT id, name, age, gender, birth_date
    FROM children
    WHERE id = ?
");
$stmt->bind_param("i", $childId);
$stmt->execute();

$childResult = $stmt->get_result();
$child = $childResult->fetch_assoc();

if (!$child) {
    echo "Ребёнок не найден.";
    exit;
}

$childAge = getChildAge($child["birth_date"], isset($child["age"]) ? (int)$child["age"] : null);
$childGender = $child["gender"];

// Получаем комнату
$stmt = $conn->prepare("
    SELECT id, capacity
    FROM rooms
    WHERE id = ?
");
$stmt->bind_param("i", $roomId);
$stmt->execute();

$roomResult = $stmt->get_result();
$room = $roomResult->fetch_assoc();

if (!$room) {
    echo "Комната не найдена.";
    exit;
}

// Получаем детей, которые уже находятся в комнате
$stmt = $conn->prepare("
    SELECT id, name, age, gender, birth_date
    FROM children
    WHERE room_id = ?
");
$stmt->bind_param("i", $roomId);
$stmt->execute();

$roomChildrenResult = $stmt->get_result();

$roomChildren = [];

while ($row = $roomChildrenResult->fetch_assoc()) {
    // Если ребёнок уже в этой комнате, не сравниваем его с самим собой
    if ((int)$row["id"] === $childId) {
        continue;
    }

    $row["calculated_age"] = getChildAge(
        $row["birth_date"],
        isset($row["age"]) ? (int)$row["age"] : null
    );

    $roomChildren[] = $row;
}

// Проверяем вместимость
$currentCount = count($roomChildren);
$capacity = (int)$room["capacity"];

if ($currentCount >= $capacity) {
    echo "В комнате нет свободных мест.";
    exit;
}

// Проверяем пол
foreach ($roomChildren as $existingChild) {
    if ($existingChild["gender"] !== $childGender) {
        echo "Нельзя поселить мальчиков и девочек в одну комнату.";
        exit;
    }
}

// Проверяем возрастную разницу
foreach ($roomChildren as $existingChild) {
    if (abs($existingChild["calculated_age"] - $childAge) > 2) {
        echo "Нельзя поселить детей с разницей возраста больше 2 лет.";
        exit;
    }
}

// Перемещаем ребёнка
$stmt = $conn->prepare("
    UPDATE children
    SET room_id = ?
    WHERE id = ?
");
$stmt->bind_param("ii", $roomId, $childId);

if ($stmt->execute()) {
    echo "OK";
    exit;
}

echo "Ошибка при перемещении ребёнка.";