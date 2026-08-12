<?php

require_once __DIR__ . "/../db.php";

$result = mysqli_query($conn,"
SELECT *
FROM events
WHERE status = 'published'
ORDER BY
start_date DESC,
created_at DESC");

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);