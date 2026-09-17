<?php

/*
|--------------------------------------------------------------------------
| UPLOAD PROFILE PHOTO - INDIVIDUAL MEMBERSHIP
|--------------------------------------------------------------------------
|
| Pola sama seperti events/upload-image.php atau
| press-release/upload-press-image.php - sesuaikan folder
| tujuan upload ($uploadDir) kalau struktur uploads di project
| kamu beda.
|
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

if (!isset($_FILES["photo"])) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "No photo file received.",
    ]);

    exit;

}

$file = $_FILES["photo"];

$allowedTypes = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp",
];

if ($file["error"] !== UPLOAD_ERR_OK) {

    echo json_encode([
        "success" => false,
        "message" => "Upload failed.",
    ]);

    exit;

}

if (!isset($allowedTypes[$file["type"]])) {

    echo json_encode([
        "success" => false,
        "message" => "Only JPG, PNG and WEBP images are allowed.",
    ]);

    exit;

}

if ($file["size"] > 5 * 1024 * 1024) {

    echo json_encode([
        "success" => false,
        "message" => "Image size must not exceed 5 MB.",
    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| DESTINATION FOLDER
|--------------------------------------------------------------------------
|
| Asumsi struktur:  project-root/uploads/membership/individual/
|                    project-root/api/individual/upload-photo.php
|--------------------------------------------------------------------------
*/

$uploadDir = __DIR__ . "/../../uploads/membership/individual/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = $allowedTypes[$file["type"]];

$filename = "individual-" . uniqid() . "-" . time() . "." . $extension;

$destination = $uploadDir . $filename;

if (!move_uploaded_file($file["tmp_name"], $destination)) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to save uploaded file.",
    ]);

    exit;

}

$relativePath = "uploads/membership/individual/" . $filename;

echo json_encode([
    "success" => true,
    "path" => $relativePath,
]);
