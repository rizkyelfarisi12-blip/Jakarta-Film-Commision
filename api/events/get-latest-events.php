<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

function response(
    $success,
    $message,
    $data = []
) {

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| GET LATEST EVENTS
|--------------------------------------------------------------------------
|
| Digunakan oleh Dashboard Admin.
|
| Berbeda dengan get-events.php:
|
| get-events.php
|   -> hanya published
|   -> untuk website publik
|
| get-latest-events.php
|   -> semua status
|   -> Dashboard Admin
|   -> hanya 3 event terbaru
|
|--------------------------------------------------------------------------
*/


$sql = "

    SELECT

        id,

        title,
        slug,

        category,
        category_name,

        start_date,
        end_date,

        start_time,
        end_time,

        timezone,

        location,
        address,

        cover_image,

        description,

        featured,
        featured_start,
        featured_until,

        status,

        created_at,
        updated_at

    FROM events

    ORDER BY

        CASE
            WHEN start_date IS NULL THEN 1
            ELSE 0
        END,

        start_date DESC,

        created_at DESC

    LIMIT 3

";


$result =
    $conn->query($sql);


/*
|--------------------------------------------------------------------------
| QUERY ERROR
|--------------------------------------------------------------------------
*/

if (!$result) {

    response(
        false,
        "Failed to load latest events.",
        []
    );

}


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$data = [];


while (
    $row =
    $result->fetch_assoc()
) {


    /*
    |--------------------------------------------------------------------------
    | CATEGORY DISPLAY
    |--------------------------------------------------------------------------
    */

    if (
        $row["category"] === "Others" &&
        !empty(
            trim(
                $row["category_name"] ?? ""
            )
        )
    ) {

        $row["category_display"] =
            trim(
                $row["category_name"]
            );

    } else {

        $row["category_display"] =
            $row["category"] ??
            "Others";

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY FILTER
    |--------------------------------------------------------------------------
    */

    $row["category_filter"] =
        $row["category"] ??
        "Others";


    /*
    |--------------------------------------------------------------------------
    | FEATURED
    |--------------------------------------------------------------------------
    */

    $row["featured"] =
        (int)(
            $row["featured"] ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | RETURN DATA
    |--------------------------------------------------------------------------
    */

    $data[] =
        $row;

}


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

response(
    true,
    "Latest events loaded successfully.",
    $data
);


$conn->close();