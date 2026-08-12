<?php

header("Content-Type: application/json");

if(!isset($_FILES["image"])){

    echo json_encode([
        "success" => false,
        "message" => "No file uploaded"
    ]);

    exit;
}


$file = $_FILES["image"];


/* =========================
   ERROR
========================= */

if($file["error"] !== UPLOAD_ERR_OK){

    echo json_encode([
        "success" => false,
        "message" => "Upload error code: " . $file["error"]
    ]);

    exit;
}


/* =========================
   SIZE
========================= */

if($file["size"] > 5 * 1024 * 1024){

    echo json_encode([
        "success" => false,
        "message" => "Maximum upload is 5MB"
    ]);

    exit;
}


/* =========================
   EXTENSION
========================= */

$allowed = [
    "jpg",
    "jpeg",
    "png",
    "webp",
    "avif"
];

$extension =
    strtolower(
        pathinfo(
            $file["name"],
            PATHINFO_EXTENSION
        )
    );


if(!in_array($extension, $allowed, true)){

    echo json_encode([
        "success" => false,
        "message" => "File type not allowed"
    ]);

    exit;
}


/* =========================
   DIRECTORY
========================= */

$targetDir =
    __DIR__ . "/../../uploads/events/";


if(!is_dir($targetDir)){

    mkdir(
        $targetDir,
        0777,
        true
    );

}


/* =========================
   FILE NAME
========================= */

$name =
    preg_replace(
        "/[^a-zA-Z0-9\-_]/",
        "-",
        pathinfo(
            $file["name"],
            PATHINFO_FILENAME
        )
    );


$filename =
    time() . "-" .
    $name . "." .
    $extension;


$targetFile =
    $targetDir . $filename;


/* =========================
   MOVE
========================= */

if(!move_uploaded_file(
    $file["tmp_name"],
    $targetFile
)){

    echo json_encode([
        "success" => false,
        "message" => "Failed to save file"
    ]);

    exit;
}


/* =========================
   SUCCESS
========================= */

echo json_encode([

    "success" => true,

    "path" =>
        "uploads/events/" . $filename

]);
