<?php

require_once __DIR__ . "/../db.php";

header("Content-Type: application/json; charset=UTF-8");


/*
|--------------------------------------------------------------------------
| GET BY ID
|--------------------------------------------------------------------------
|
| Digunakan oleh ADMIN.
|
| Admin tetap boleh membuka:
| - draft
| - published
|
*/

if (isset($_GET["id"])) {

    $id = (int) $_GET["id"];

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "i",
        $id
    );

}


/*
|--------------------------------------------------------------------------
| GET BY SLUG
|--------------------------------------------------------------------------
|
| Digunakan oleh PUBLIC WEBSITE.
|
| Hanya event published yang boleh ditampilkan.
|
*/

elseif (isset($_GET["slug"])) {

    $slug =
        trim(
            $_GET["slug"]
        );

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE
            slug = ?
            AND status = 'published'
        LIMIT 1
    ");

    $stmt->bind_param(
        "s",
        $slug
    );

}


/*
|--------------------------------------------------------------------------
| NO PARAMETER
|--------------------------------------------------------------------------
*/

else {

    echo json_encode(
        [
            "success" => false,
            "message" => "ID or slug is required"
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {

    echo json_encode(
        [
            "success" => false,
            "message" => "Failed to load event"
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

}


$result =
    $stmt->get_result();

$data =
    $result->fetch_assoc();


/*
|--------------------------------------------------------------------------
| EVENT NOT FOUND
|--------------------------------------------------------------------------
*/

if (!$data) {

    echo json_encode(
        [
            "success" => false,
            "message" => "Event not found"
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| NORMALIZATION
|--------------------------------------------------------------------------
*/

$data["id"] =
    (int) $data["id"];

$data["featured"] =
    (int) ($data["featured"] ?? 0);


/*
|--------------------------------------------------------------------------
| CATEGORY DISPLAY
|--------------------------------------------------------------------------
*/

$category =
    trim(
        $data["category"] ?? ""
    );

$categoryName =
    trim(
        $data["category_name"] ?? ""
    );


if (
    $category === "Others" &&
    $categoryName !== ""
) {

    $data["category_display"] =
        $categoryName;

} else {

    $data["category_display"] =
        $category !== ""
            ? $category
            : "Others";

}


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode(
    $data,
    JSON_UNESCAPED_UNICODE
);


$stmt->close();

$conn->close();