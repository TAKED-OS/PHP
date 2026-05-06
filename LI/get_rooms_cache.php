<?php
include 'db.php';
include 'redis.php';

$redis = getRedis();

$key = "rooms:data";

if ($redis->exists($key)) {
    $rooms = json_decode($redis->get($key), true);
} else {

    $res = $conn->query("SELECT * FROM rooms");

    $rooms = [];
    while ($r = $res->fetch_assoc()) {
        $rooms[] = $r;
    }

    $redis->setex($key, 60, json_encode($rooms));
}

header('Content-Type: application/json');
echo json_encode($rooms);
?>