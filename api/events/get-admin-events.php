<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

/*
|--------------------------------------------------------------------------
| GET ALL EVENTS FOR ADMIN
|--------------------------------------------------------------------------
|
| Admin dapat melihat:
| - draft
| - published
|
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT *
    FROM events
    ORDER BY
        CASE status
            WHEN 'draft' THEN 1
            WHEN 'published' THEN 2
            ELSE 3
        END,
        start_date DESC,
        created_at DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to load events.",
        "error" => mysqli_error($conn)
    ]);

    exit;
}

$data = [];

while ($row = mysqli_fetch_assoc($result)) {

    $data[] = $row;

}

echo json_encode([
    "success" => true,
    "data" => $data
]);

$conn->close();