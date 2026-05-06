<?php
require_once "auth.php";
requireAdmin();

require_once "db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    die("Некорректный ID ребёнка.");
}

// Проверяем, существует ли ребёнок
$stmt = $conn->prepare("SELECT id FROM children WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Ребёнок не найден.");
}

// Удаляем ребёнка
$stmt = $conn->prepare("DELETE FROM children WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list_children.php?deleted=1");
    exit;
}

die("Ошибка при удалении ребёнка.");