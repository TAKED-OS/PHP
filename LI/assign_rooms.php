<?php

require_once "auth.php";
requireAdmin();

require_once "db.php";

/**
 * Вычисляет возраст ребёнка.
 *
 * @param string|null $birthDate Дата рождения.
 * @param int|null $storedAge Возраст из базы данных.
 * @return int
 */
function getChildAge(?string $birthDate, ?int $storedAge): int
{
    if (!empty($birthDate)) {
        return (new DateTime($birthDate))->diff(new DateTime())->y;
    }

    return $storedAge ?? 0;
}

/**
 * Проверяет, можно ли добавить ребёнка в комнату по полу и возрасту.
 *
 * @param array $child Данные ребёнка.
 * @param array $room Данные комнаты.
 * @return bool
 */
function canPlaceChildInRoom(array $child, array $room): bool
{
    if (count($room["children"]) >= $room["capacity"]) {
        return false;
    }

    if ($room["gender"] !== null && $room["gender"] !== $child["gender"]) {
        return false;
    }

    foreach ($room["children"] as $existingChild) {
        if (abs($existingChild["age"] - $child["age"]) > 2) {
            return false;
        }
    }

    return true;
}

/**
 * Проверяет, можно ли добавить группу детей в комнату.
 *
 * @param array $group Группа детей.
 * @param array $room Данные комнаты.
 * @return bool
 */
function canPlaceGroupInRoom(array $group, array $room): bool
{
    if (count($room["children"]) + count($group) > $room["capacity"]) {
        return false;
    }

    foreach ($group as $child) {
        if ($room["gender"] !== null && $room["gender"] !== $child["gender"]) {
            return false;
        }

        foreach ($room["children"] as $existingChild) {
            if (abs($existingChild["age"] - $child["age"]) > 2) {
                return false;
            }
        }
    }

    for ($i = 0; $i < count($group); $i++) {
        for ($j = $i + 1; $j < count($group); $j++) {
            if (abs($group[$i]["age"] - $group[$j]["age"]) > 2) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Размещает группу детей в комнату.
 *
 * @param array $group Группа детей.
 * @param array $room Комната.
 * @return array
 */
function placeGroupIntoRoom(array $group, array $room): array
{
    foreach ($group as $child) {
        $room["children"][] = $child;
    }

    if ($room["gender"] === null && !empty($group)) {
        $room["gender"] = $group[0]["gender"];
    }

    return $room;
}

/**
 * Проверяет, упоминает ли один ребёнок другого в пожеланиях.
 *
 * @param array $child Первый ребёнок.
 * @param array $otherChild Второй ребёнок.
 * @return bool
 */
function hasWishFor(array $child, array $otherChild): bool
{
    $wishes = mb_strtolower($child["wishes"] ?? "");
    $otherName = mb_strtolower($otherChild["name"] ?? "");

    if ($wishes === "" || $otherName === "") {
        return false;
    }

    return mb_strpos($wishes, $otherName) !== false;
}

/**
 * Формирует группы детей с учётом пожеланий.
 *
 * @param array $children Список детей.
 * @return array
 */
function buildWishGroups(array $children): array
{
    $groups = [];
    $usedIds = [];

    foreach ($children as $child) {
        if (isset($usedIds[$child["id"]])) {
            continue;
        }

        $group = [$child];
        $usedIds[$child["id"]] = true;

        foreach ($children as $otherChild) {
            if (isset($usedIds[$otherChild["id"]])) {
                continue;
            }

            if ($child["gender"] !== $otherChild["gender"]) {
                continue;
            }

            if (abs($child["age"] - $otherChild["age"]) > 2) {
                continue;
            }

            $childWantsOther = hasWishFor($child, $otherChild);
            $otherWantsChild = hasWishFor($otherChild, $child);

            if ($childWantsOther || $otherWantsChild) {
                $group[] = $otherChild;
                $usedIds[$otherChild["id"]] = true;
            }
        }

        $groups[] = $group;
    }

    usort($groups, function ($a, $b) {
        return count($b) <=> count($a);
    });

    return $groups;
}

/**
 * Сохраняет распределение детей по комнатам в базе данных.
 *
 * @param mysqli $conn Подключение к базе данных.
 * @param array $rooms Список комнат.
 * @return void
 */
function saveAssignments(mysqli $conn, array $rooms): void
{
    $stmt = $conn->prepare("UPDATE children SET room_id = ? WHERE id = ?");

    foreach ($rooms as $room) {
        $roomId = (int)$room["id"];

        foreach ($room["children"] as $child) {
            $childId = (int)$child["id"];

            $stmt->bind_param("ii", $roomId, $childId);
            $stmt->execute();
        }
    }
}

// 1. Сбрасываем старое распределение
$conn->query("UPDATE children SET room_id = NULL");

// 2. Получаем комнаты
$rooms = [];

$roomsResult = $conn->query("
    SELECT id, room_number, capacity
    FROM rooms
    ORDER BY CAST(room_number AS UNSIGNED), room_number
");

while ($room = $roomsResult->fetch_assoc()) {
    $rooms[] = [
        "id" => (int)$room["id"],
        "room_number" => $room["room_number"],
        "capacity" => (int)$room["capacity"],
        "gender" => null,
        "children" => []
    ];
}

// 3. Получаем детей
$children = [];

$childrenResult = $conn->query("
    SELECT id, name, age, gender, wishes, birth_date
    FROM children
    ORDER BY gender, age, name
");

while ($child = $childrenResult->fetch_assoc()) {
    $children[] = [
        "id" => (int)$child["id"],
        "name" => $child["name"],
        "age" => getChildAge($child["birth_date"], isset($child["age"]) ? (int)$child["age"] : null),
        "gender" => $child["gender"],
        "wishes" => $child["wishes"] ?? "",
        "birth_date" => $child["birth_date"]
    ];
}

// 4. Формируем группы по пожеланиям
$groups = buildWishGroups($children);

// 5. Распределяем группы
$notPlacedChildren = [];

foreach ($groups as $group) {
    $placed = false;

    foreach ($rooms as $index => $room) {
        if (canPlaceGroupInRoom($group, $room)) {
            $rooms[$index] = placeGroupIntoRoom($group, $room);
            $placed = true;
            break;
        }
    }

    // Если группу целиком не получилось разместить — пробуем по одному
    if (!$placed) {
        foreach ($group as $child) {
            $singlePlaced = false;

            foreach ($rooms as $index => $room) {
                if (canPlaceChildInRoom($child, $room)) {
                    $rooms[$index] = placeGroupIntoRoom([$child], $room);
                    $singlePlaced = true;
                    break;
                }
            }

            if (!$singlePlaced) {
                $notPlacedChildren[] = $child;
            }
        }
    }
}

// 6. Сохраняем результат в MySQL
saveAssignments($conn, $rooms);

// 7. Возвращаемся на страницу комнат
header("Location: rooms.php?assigned=1");
exit;