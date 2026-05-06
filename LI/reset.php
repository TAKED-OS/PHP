<?php

require_once "auth.php";
requireAdmin();

require_once "db.php";

$stmt = $conn->prepare("UPDATE children SET room_id = NULL");

if ($stmt->execute()) {
    header("Location: rooms.php?reset=1");
    exit;
}

die("Ошибка при сбросе распределения.");