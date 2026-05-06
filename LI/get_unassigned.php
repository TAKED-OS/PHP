<?php
include 'db.php';
include 'redis.php';

$redis = getRedis();

$key = "children:unassigned";

/* если есть в кеше */
if ($redis->exists($key)) {
    $children = json_decode($redis->get($key), true);
} else {

    $res = $conn->query("
        SELECT * FROM children WHERE room_id IS NULL
    ");

    $children = [];
    while ($row = $res->fetch_assoc()) {
        $children[] = $row;
    }

    /* кладём в кеш на 60 сек */
    $redis->setex($key, 60, json_encode($children));
}

header('Content-Type: application/json');
echo json_encode($children);
?>