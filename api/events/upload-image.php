<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

function response(
    bool $success,
    string $message,
    array $extra = []
) {

    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| CONFIGURATION
|--------------------------------------------------------------------------
*/

/*
 * upload-press-image.php berada di:
 *
 * /api/press-release/
 *
 * Maka root website adalah:
 *
 * /../../
 */

$rootPath =
    realpath(
        __DIR__ . "/../../"
    );


if (!$rootPath) {

    response(
        false,
        "Unable to determine website root directory."
    );

}


/*
|--------------------------------------------------------------------------
| UPLOAD DIRECTORY
|--------------------------------------------------------------------------
*/

$uploadRoot =
    $rootPath .
    DIRECTORY_SEPARATOR .
    "uploads" .
    DIRECTORY_SEPARATOR .
    "press-release";


$coverDirectory =
    $uploadRoot .
    DIRECTORY_SEPARATOR .
    "cover";


$articleDirectory =
    $uploadRoot .
    DIRECTORY_SEPARATOR .
    "article";


/*
|--------------------------------------------------------------------------
| REQUEST TYPE
|--------------------------------------------------------------------------
*/

$type =
    isset($_POST["type"])
        ? trim($_POST["type"])
        : "";


$allowedTypes = [

    "cover",
    "article"

];


if (
    !in_array(
        $type,
        $allowedTypes,
        true
    )
) {

    response(
        false,
        "Invalid upload type. Allowed types are cover or article."
    );

}


/*
|--------------------------------------------------------------------------
| IMAGE FILE
|--------------------------------------------------------------------------
*/

if (
    !isset($_FILES["image"])
) {

    response(
        false,
        "No image file was uploaded."
    );

}


$file =
    $_FILES["image"];


/*
|--------------------------------------------------------------------------
| UPLOAD ERROR
|--------------------------------------------------------------------------
*/

if (
    !isset($file["error"])
) {

    response(
        false,
        "Invalid upload request."
    );

}


if (
    $file["error"] !== UPLOAD_ERR_OK
) {

    $uploadErrors = [

        UPLOAD_ERR_INI_SIZE =>
            "The uploaded file exceeds the server upload limit.",

        UPLOAD_ERR_FORM_SIZE =>
            "The uploaded file exceeds the allowed size.",

        UPLOAD_ERR_PARTIAL =>
            "The image was only partially uploaded.",

        UPLOAD_ERR_NO_FILE =>
            "No image file was uploaded.",

        UPLOAD_ERR_NO_TMP_DIR =>
            "Temporary upload directory is missing.",

        UPLOAD_ERR_CANT_WRITE =>
            "Failed to write the uploaded file.",

        UPLOAD_ERR_EXTENSION =>
            "The upload was stopped by a PHP extension."

    ];


    $message =
        $uploadErrors[
            $file["error"]
        ]
        ??
        "Unknown upload error.";


    response(
        false,
        $message
    );

}


/*
|--------------------------------------------------------------------------
| BASIC FILE VALIDATION
|--------------------------------------------------------------------------
*/

if (
    !isset($file["tmp_name"]) ||
    !is_uploaded_file($file["tmp_name"])
) {

    response(
        false,
        "Invalid uploaded file."
    );

}


/*
|--------------------------------------------------------------------------
| FILE SIZE
|--------------------------------------------------------------------------
*/

$maxFileSize =
    5 * 1024 * 1024;


$fileSize =
    (int)$file["size"];


if (
    $fileSize <= 0
) {

    response(
        false,
        "The uploaded image is empty."
    );

}


if (
    $fileSize > $maxFileSize
) {

    response(
        false,
        "Image size must not exceed 5 MB."
    );

}


/*
|--------------------------------------------------------------------------
| MIME TYPE
|--------------------------------------------------------------------------
|
| Do not trust $_FILES["type"].
| Detect the actual MIME type from the file.
|--------------------------------------------------------------------------
*/

$finfo =
    new finfo(
        FILEINFO_MIME_TYPE
    );


$mimeType =
    $finfo->file(
        $file["tmp_name"]
    );


$allowedMimeTypes = [

    "image/jpeg" =>
        "jpg",

    "image/png" =>
        "png",

    "image/webp" =>
        "webp"

];


if (
    !isset(
        $allowedMimeTypes[
            $mimeType
        ]
    )
) {

    response(
        false,
        "Invalid image format. Only JPG, PNG and WEBP are allowed."
    );

}


$extension =
    $allowedMimeTypes[
        $mimeType
    ];


/*
|--------------------------------------------------------------------------
| IMAGE VALIDATION
|--------------------------------------------------------------------------
*/

$imageInfo =
    @getimagesize(
        $file["tmp_name"]
    );


if (
    $imageInfo === false
) {

    response(
        false,
        "The uploaded file is not a valid image."
    );

}


/*
|--------------------------------------------------------------------------
| IMAGE DIMENSIONS
|--------------------------------------------------------------------------
*/

$width =
    isset($imageInfo[0])
        ? (int)$imageInfo[0]
        : 0;


$height =
    isset($imageInfo[1])
        ? (int)$imageInfo[1]
        : 0;


if (
    $width <= 0 ||
    $height <= 0
) {

    response(
        false,
        "Invalid image dimensions."
    );

}


/*
|--------------------------------------------------------------------------
| OPTIONAL DIMENSION LIMIT
|--------------------------------------------------------------------------
|
| Prevent extremely large images from being uploaded.
|--------------------------------------------------------------------------
*/

$maxWidth =
    10000;


$maxHeight =
    10000;


if (
    $width > $maxWidth ||
    $height > $maxHeight
) {

    response(
        false,
        "Image dimensions are too large."
    );

}


/*
|--------------------------------------------------------------------------
| SELECT DIRECTORY
|--------------------------------------------------------------------------
*/

if (
    $type === "cover"
) {

    $targetDirectory =
        $coverDirectory;

} else {

    $targetDirectory =
        $articleDirectory;

}


/*
|--------------------------------------------------------------------------
| CREATE DIRECTORY
|--------------------------------------------------------------------------
*/

if (
    !is_dir(
        $targetDirectory
    )
) {

    if (
        !mkdir(
            $targetDirectory,
            0755,
            true
        )
    ) {

        response(
            false,
            "Failed to create upload directory."
        );

    }

}


/*
|--------------------------------------------------------------------------
| DIRECTORY WRITABLE CHECK
|--------------------------------------------------------------------------
*/

if (
    !is_writable(
        $targetDirectory
    )
) {

    response(
        false,
        "Upload directory is not writable."
    );

}


/*
|--------------------------------------------------------------------------
| GENERATE UNIQUE FILE NAME
|--------------------------------------------------------------------------
*/

$prefix =
    $type === "cover"
        ? "press-cover"
        : "press-article";


try {

    $randomPart =
        bin2hex(
            random_bytes(
                12
            )
        );

} catch (
    Throwable $error
) {

    $randomPart =
        uniqid(
            "",
            true
        );

}


$fileName =
    $prefix .
    "-" .
    date("YmdHis") .
    "-" .
    $randomPart .
    "." .
    $extension;


/*
|--------------------------------------------------------------------------
| FINAL PATH
|--------------------------------------------------------------------------
*/

$targetPath =
    $targetDirectory .
    DIRECTORY_SEPARATOR .
    $fileName;


/*
|--------------------------------------------------------------------------
| MOVE UPLOADED FILE
|--------------------------------------------------------------------------
*/

if (
    !move_uploaded_file(
        $file["tmp_name"],
        $targetPath
    )
) {

    response(
        false,
        "Failed to save uploaded image."
    );

}


/*
|--------------------------------------------------------------------------
| SET FILE PERMISSION
|--------------------------------------------------------------------------
*/

@chmod(
    $targetPath,
    0644
);


/*
|--------------------------------------------------------------------------
| DATABASE-SAFE / URL-SAFE PATH
|--------------------------------------------------------------------------
*/

if (
    $type === "cover"
) {

    $relativePath =
        "uploads/press-release/cover/" .
        $fileName;

} else {

    $relativePath =
        "uploads/press-release/article/" .
        $fileName;

}


/*
|--------------------------------------------------------------------------
| IMAGE URL
|--------------------------------------------------------------------------
|
| This is optional information for the frontend.
|--------------------------------------------------------------------------
*/

$protocol =
    (
        isset($_SERVER["HTTPS"]) &&
        $_SERVER["HTTPS"] !== "off"
    )
        ? "https"
        : "http";


$host =
    $_SERVER["HTTP_HOST"]
    ?? "";


$basePath =
    "";


/*
 * Detect the website base path.
 *
 * Example:
 *
 * http://localhost/jfc/
 *
 * becomes:
 *
 * /jfc/
 */

$scriptName =
    $_SERVER["SCRIPT_NAME"]
    ?? "";


$apiPosition =
    strpos(
        $scriptName,
        "/api/"
    );


if (
    $apiPosition !== false
) {

    $basePath =
        substr(
            $scriptName,
            0,
            $apiPosition
        );

}


/*
|--------------------------------------------------------------------------
| NORMALIZE BASE PATH
|--------------------------------------------------------------------------
*/

if (
    $basePath === "/"
) {

    $basePath =
        "";

}


$basePath =
    rtrim(
        $basePath,
        "/"
    );


$imageUrl =
    $protocol .
    "://" .
    $host .
    $basePath .
    "/" .
    $relativePath;


/*
|--------------------------------------------------------------------------
| SUCCESS RESPONSE
|--------------------------------------------------------------------------
*/

response(

    true,

    "Image uploaded successfully.",

    [

        "data" => [

            "type" =>
                $type,

            "path" =>
                $relativePath,

            "url" =>
                $imageUrl,

            "filename" =>
                $fileName,

            "mime_type" =>
                $mimeType,

            "extension" =>
                $extension,

            "size" =>
                $fileSize,

            "width" =>
                $width,

            "height" =>
                $height

        ]

    ]

);