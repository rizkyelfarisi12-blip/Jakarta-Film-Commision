<?php

/*
|--------------------------------------------------------------------------
| UPLOAD COMPANY PROFILE DOCUMENT - ENTITIES MEMBERSHIP
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

if (!isset($_FILES["company_profile"])) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "No file received.",
    ]);

    exit;

}

$file = $_FILES["company_profile"];

$allowedTypes = [
    "application/pdf" => "pdf",
    "application/msword" => "doc",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => "docx",
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
        "message" => "Only PDF, DOC or DOCX files are allowed.",
    ]);

    exit;

}

if ($file["size"] > 10 * 1024 * 1024) {

    echo json_encode([
        "success" => false,
        "message" => "File size must not exceed 10 MB.",
    ]);

    exit;

}

/*
|--------------------------------------------------------------------------
| DESTINATION FOLDER
|--------------------------------------------------------------------------
|
| Asumsi struktur:  project-root/uploads/membership/entities/profile/
|                    project-root/api/company/upload-profile.php
|--------------------------------------------------------------------------
*/

$uploadDir = __DIR__ . "/../../uploads/membership/entities/profile/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = $allowedTypes[$file["type"]];

$filename = "profile-" . uniqid() . "-" . time() . "." . $extension;

$destination = $uploadDir . $filename;

if (!move_uploaded_file($file["tmp_name"], $destination)) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to save uploaded file.",
    ]);

    exit;

}

$relativePath = "uploads/membership/entities/profile/" . $filename;

echo json_encode([
    "success" => true,
    "path" => $relativePath,
    "filename" => $file["name"],
]);
