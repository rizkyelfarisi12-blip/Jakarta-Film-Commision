<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

function response($success, $message = "", $data = [])
{
    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data"    => $data
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GET EVENTS
|--------------------------------------------------------------------------
|
| Untuk menjaga kompatibilitas dengan events.js lama,
| endpoint ini tetap mengembalikan array event.
|
|--------------------------------------------------------------------------
*/

$sql = "

    SELECT
        *

    FROM events

    WHERE status = 'published'

    ORDER BY
        start_date DESC,
        created_at DESC

";


$result = mysqli_query(
    $conn,
    $sql
);


if (!$result) {

    /*
    |----------------------------------------------------------------------
    | ERROR
    |----------------------------------------------------------------------
    */

    echo json_encode(
        [
            "success" => false,
            "message" => "Failed to load events."
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

}


$events = [];


while (
    $row =
    mysqli_fetch_assoc($result)
) {


    /*
    |--------------------------------------------------------------------------
    | CATEGORY DISPLAY
    |--------------------------------------------------------------------------
    */

    $category =
        trim(
            $row["category"] ?? ""
        );


    $categoryName =
        trim(
            $row["category_name"] ?? ""
        );


    if (
        $category === "Others" &&
        $categoryName !== ""
    ) {

        $row["category_display"] =
            $categoryName;

    } else {

        $row["category_display"] =
            $category !== ""
                ? $category
                : "Others";

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY FILTER
    |--------------------------------------------------------------------------
    */

    $row["category_filter"] =
        $category !== ""
            ? $category
            : "Others";


    /*
    |--------------------------------------------------------------------------
    | FEATURED NORMALIZATION
    |--------------------------------------------------------------------------
    */

    $row["featured"] =
        (int)($row["featured"] ?? 0);


    /*
    |--------------------------------------------------------------------------
    | ID NORMALIZATION
    |--------------------------------------------------------------------------
    */

    $row["id"] =
        (int)$row["id"];


    /*
    |--------------------------------------------------------------------------
    | ADD TO ARRAY
    |--------------------------------------------------------------------------
    */

    $events[] = $row;

}


/*
|--------------------------------------------------------------------------
| OUTPUT
|--------------------------------------------------------------------------
|
| Tetap array agar events.js lama tidak rusak.
|
|--------------------------------------------------------------------------
*/

echo json_encode(
    $events,
    JSON_UNESCAPED_UNICODE
);


$conn->close();