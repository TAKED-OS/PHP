<?php
include 'db.php';

$from = (int)$_POST['from_room'];
$to = (int)$_POST['to_room'];

if ($from === $to) {
    die("Нельзя перемещать в ту же комнату");
}

$fromRoom = $conn->query("SELECT * FROM rooms WHERE id=$from")->fetch_assoc();
$toRoom = $conn->query("SELECT * FROM rooms WHERE id=$to")->fetch_assoc();

/* нельзя забрать последнюю кровать */
if ($fromRoom['capacity'] <= 1) {
    die("В комнате должна остаться минимум 1 кровать");
}

/* просто защита от отрицательных значений */
if ($toRoom['capacity'] < 0) {
    die("Ошибка данных");
}

/* перенос */
$conn->query("UPDATE rooms SET capacity = capacity - 1 WHERE id=$from");
$conn->query("UPDATE rooms SET capacity = capacity + 1 WHERE id=$to");

header("Location: rooms.php");
exit();
?>