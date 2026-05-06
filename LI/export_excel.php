<?php
include 'db.php';

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=rooms.xls");
echo "\xEF\xBB\xBF";

function getAge($birth) {
    return (new DateTime($birth))->diff(new DateTime())->y;
}

$roomsRes = $conn->query("SELECT * FROM rooms ORDER BY room_number");

echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }
    th {
        background: #dbeafe;
    }
    .room-title {
        font-weight: bold;
        font-size: 16px;
        background: #bfdbfe;
        padding: 8px;
    }
</style>';
echo '</head>';
echo '<body>';

while ($room = $roomsRes->fetch_assoc()) {
    $roomId = (int)$room['id'];

    $childrenRes = $conn->query("
        SELECT name, birth_date, gender
        FROM children
        WHERE room_id = $roomId
        ORDER BY name
    ");

    echo '<table>';
    echo '<tr><td class="room-title" colspan="4">Комната ' . htmlspecialchars($room['room_number']) . ' | Мест: ' . (int)$room['capacity'] . '</td></tr>';
    echo '<tr>
            <th>№</th>
            <th>Имя</th>
            <th>Возраст</th>
            <th>Пол</th>
          </tr>';

    $i = 1;

    while ($child = $childrenRes->fetch_assoc()) {
        $age = getAge($child['birth_date']);
        $gender = $child['gender'] === 'male' ? 'M' : 'F';

        echo '<tr>';
        echo '<td>' . $i . '</td>';
        echo '<td>' . htmlspecialchars($child['name']) . '</td>';
        echo '<td>' . $age . '</td>';
        echo '<td>' . $gender . '</td>';
        echo '</tr>';

        $i++;
    }

    if ($i === 1) {
        echo '<tr><td colspan="4">Пусто</td></tr>';
    }

    echo '</table>';
}

/* Нераспределённые */
$unassigned = $conn->query("
    SELECT name, birth_date, gender
    FROM children
    WHERE room_id IS NULL
    ORDER BY name
");

echo '<table>';
echo '<tr><td class="room-title" colspan="4">Не распределены</td></tr>';
echo '<tr>
        <th>№</th>
        <th>Имя</th>
        <th>Возраст</th>
        <th>Пол</th>
      </tr>';

$i = 1;
while ($child = $unassigned->fetch_assoc()) {
    $age = getAge($child['birth_date']);
    $gender = $child['gender'] === 'male' ? 'M' : 'F';

    echo '<tr>';
    echo '<td>' . $i . '</td>';
    echo '<td>' . htmlspecialchars($child['name']) . '</td>';
    echo '<td>' . $age . '</td>';
    echo '<td>' . $gender . '</td>';
    echo '</tr>';

    $i++;
}

if ($i === 1) {
    echo '<tr><td colspan="4">Все дети распределены</td></tr>';
}

echo '</table>';

echo '</body></html>';
?>