<?php

require_once __DIR__ . "/../db.php";

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| STEP 1
| Cari event yang sedang dipromosikan
|--------------------------------------------------------------------------
*/

$sql = "

SELECT *

FROM events

WHERE
    status = 'published'
AND featured = 1
AND (
    featured_start IS NULL
    OR featured_start <= CURDATE()
)
AND (
    featured_until IS NULL
    OR featured_until >= CURDATE()
)
ORDER BY featured_until DESC
LIMIT 1

";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)){

    echo json_encode([
        "status"=>"success",
        "type"=>"featured",
        "event"=>mysqli_fetch_assoc($result)
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| STEP 2
| Cari event terdekat
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *
FROM events
WHERE
    status='published'
AND end_date >= CURDATE()
ORDER BY start_date ASC
LIMIT 1
";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)){

    echo json_encode([
        "status"=>"success",
        "type"=>"upcoming",
        "event"=>mysqli_fetch_assoc($result)
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| STEP 3
| Tidak ada event
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status"=>"empty"
]);