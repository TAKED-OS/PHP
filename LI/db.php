<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "camp_manager";
$port = 3307;

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");