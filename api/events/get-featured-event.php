<?php

require_once __DIR__ . "/../db.php";

header('Content-Type: application/json');


/*
|--------------------------------------------------------------------------
| STEP 1
| Cari event yang sedang di-feature
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

ORDER BY
    featured_until DESC,
    start_date ASC

LIMIT 1

";


$result = mysqli_query($conn, $sql);


if($result && mysqli_num_rows($result) > 0){

    echo json_encode([

        "status" => "success",

        "type" => "featured",

        "event" => mysqli_fetch_assoc($result)

    ]);

    exit;

}


/*
|--------------------------------------------------------------------------
| STEP 2
| Jika tidak ada featured,
| cari event terdekat
|--------------------------------------------------------------------------
|
| end_date boleh NULL.
|
| Jika end_date NULL,
| maka start_date dianggap sebagai tanggal selesai.
|
|--------------------------------------------------------------------------
*/

$sql = "

SELECT *

FROM events

WHERE

    status = 'published'

AND (

    COALESCE(end_date, start_date) >= CURDATE()

)

ORDER BY

    start_date ASC

LIMIT 1

";


$result = mysqli_query($conn, $sql);


if($result && mysqli_num_rows($result) > 0){

    echo json_encode([

        "status" => "success",

        "type" => "upcoming",

        "event" => mysqli_fetch_assoc($result)

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

    "status" => "empty"

]);
