<?php

header("Content-Type: application/json; charset=UTF-8");


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

function response(
    bool $success,
    string $message = "",
    array $data = [],
    int $httpCode = 200
) {

    http_response_code($httpCode);

    /*
    |--------------------------------------------------------------------------
    | FILE INFORMATION
    |--------------------------------------------------------------------------
    |
    | Untuk kompatibilitas dengan beberapa versi JavaScript:
    |
    | result.file
    | result.data.file
    |
    */

    $response = [
        "success" => $success,
        "message" => $message,
        "data" => $data
    ];


    if (
        isset($data["file"]) &&
        is_array($data["file"])
    ) {

        $response["file"] =
            $data["file"];

    }


    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| REQUEST METHOD
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    response(
        false,
        "Invalid request method.",
        [],
        405
    );

}


/*
|--------------------------------------------------------------------------
| UPLOAD CONFIGURATION
|--------------------------------------------------------------------------
*/

$maxFileSize =
    5 * 1024 * 1024;


/*
|--------------------------------------------------------------------------
| ALLOWED MIME TYPES
|--------------------------------------------------------------------------
*/

$allowedMimeTypes = [

    "image/jpeg" => "jpg",

    "image/png" => "png",

    "image/webp" => "webp"

];


/*
|--------------------------------------------------------------------------
| CHECK FILE
|--------------------------------------------------------------------------
*/

if (
    !isset($_FILES["image"])
) {

    response(
        false,
        "No image file was uploaded.",
        [],
        422
    );

}


$file =
    $_FILES["image"];


/*
|--------------------------------------------------------------------------
| UPLOAD ERROR
|--------------------------------------------------------------------------
*/

$uploadError =
    isset($file["error"])
    ? (int) $file["error"]
    : -1;


if (
    $uploadError !== UPLOAD_ERR_OK
) {

    $errorMessage =
        "Image upload failed.";


    switch ($uploadError) {

        case UPLOAD_ERR_INI_SIZE:

        case UPLOAD_ERR_FORM_SIZE:

            $errorMessage =
                "The uploaded image is too large.";

            break;


        case UPLOAD_ERR_PARTIAL:

            $errorMessage =
                "The image was only partially uploaded.";

            break;


        case UPLOAD_ERR_NO_FILE:

            $errorMessage =
                "No image file was uploaded.";

            break;


        case UPLOAD_ERR_NO_TMP_DIR:

            $errorMessage =
                "Temporary upload directory is missing.";

            break;


        case UPLOAD_ERR_CANT_WRITE:

            $errorMessage =
                "Failed to write uploaded image.";

            break;


        case UPLOAD_ERR_EXTENSION:

            $errorMessage =
                "Image upload was stopped by a server extension.";

            break;

    }


    response(
        false,
        $errorMessage,
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| FILE SIZE
|--------------------------------------------------------------------------
*/

$fileSize =
    isset($file["size"])
    ? (int) $file["size"]
    : 0;


if (
    $fileSize <= 0
) {

    response(
        false,
        "The uploaded image is empty.",
        [],
        422
    );

}


if (
    $fileSize > $maxFileSize
) {

    response(
        false,
        "Image size must not exceed 5 MB.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| TEMP FILE
|--------------------------------------------------------------------------
*/

$tmpFile =
    $file["tmp_name"] ?? "";


if (
    $tmpFile === "" ||
    !is_uploaded_file($tmpFile)
) {

    response(
        false,
        "Invalid uploaded file.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE REAL IMAGE
|--------------------------------------------------------------------------
*/

$imageInfo =
    @getimagesize(
        $tmpFile
    );


if (
    $imageInfo === false
) {

    response(
        false,
        "The uploaded file is not a valid image.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| DETECT MIME TYPE
|--------------------------------------------------------------------------
*/

$finfo =
    new finfo(
        FILEINFO_MIME_TYPE
    );


$mimeType =
    $finfo->file(
        $tmpFile
    );


if (
    !isset(
    $allowedMimeTypes[$mimeType]
)
) {

    response(
        false,
        "Invalid image format. Only JPG, PNG and WEBP are allowed.",
        [],
        422
    );

}


$extension =
    $allowedMimeTypes[$mimeType];


/*
|--------------------------------------------------------------------------
| VERIFY IMAGE TYPE
|--------------------------------------------------------------------------
*/

$allowedImageTypes = [

    IMAGETYPE_JPEG,

    IMAGETYPE_PNG,

    IMAGETYPE_WEBP

];


if (
    !in_array(
        $imageInfo[2],
        $allowedImageTypes,
        true
    )
) {

    response(
        false,
        "Unsupported image type.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| UPLOAD TYPE
|--------------------------------------------------------------------------
|
| cover   = Cover image
| article = Article image
|
*/

$uploadType =
    isset($_POST["type"])
    ? trim(
        (string) $_POST["type"]
    )
    : "article";


$allowedUploadTypes = [

    "cover",

    "article"

];


if (
    !in_array(
        $uploadType,
        $allowedUploadTypes,
        true
    )
) {

    response(
        false,
        "Invalid upload type.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| ROOT DIRECTORY
|--------------------------------------------------------------------------
|
| Struktur project:
|
| /jfc
| ├── api
| │   └── press-release
| │       └── upload-press-image.php
| │
| ├── uploads
| │   └── press-release
| │
| └── ...
|
| Dari:
|
| /jfc/api/press-release/
|
| naik dua level:
|
| ../../
|
| menjadi:
|
| /jfc/
|
|--------------------------------------------------------------------------
*/

$uploadDirectory =
    dirname(
        __DIR__,
        2
    ) .
    "/uploads/press-release";


/*
|--------------------------------------------------------------------------
| NORMALIZE DIRECTORY
|--------------------------------------------------------------------------
*/

$uploadDirectory =
    rtrim(
        str_replace(
            "\\",
            "/",
            $uploadDirectory
        ),
        "/"
    );


/*
|--------------------------------------------------------------------------
| CREATE DIRECTORY
|--------------------------------------------------------------------------
*/

if (
    !is_dir(
        $uploadDirectory
    )
) {

    if (
        !mkdir(
            $uploadDirectory,
            0755,
            true
        )
    ) {

        response(
            false,
            "Failed to create upload directory.",
            [],
            500
        );

    }

}


/*
|--------------------------------------------------------------------------
| CHECK DIRECTORY
|--------------------------------------------------------------------------
*/

if (
    !is_writable(
        $uploadDirectory
    )
) {

    response(
        false,
        "Upload directory is not writable.",
        [],
        500
    );

}


/*
|--------------------------------------------------------------------------
| GENERATE UNIQUE FILE NAME
|--------------------------------------------------------------------------
*/

$uniqueName =
    "press-" .
    $uploadType .
    "-" .
    date("YmdHis") .
    "-" .
    bin2hex(
        random_bytes(6)
    ) .
    "." .
    $extension;


/*
|--------------------------------------------------------------------------
| TARGET PATH
|--------------------------------------------------------------------------
*/

$targetPath =
    $uploadDirectory .
    "/" .
    $uniqueName;


/*
|--------------------------------------------------------------------------
| MOVE FILE
|--------------------------------------------------------------------------
*/

if (
    !move_uploaded_file(
        $tmpFile,
        $targetPath
    )
) {

    response(
        false,
        "Failed to save uploaded image.",
        [],
        500
    );

}


/*
|--------------------------------------------------------------------------
| FILE PERMISSION
|--------------------------------------------------------------------------
*/

@chmod(
    $targetPath,
    0644
);


/*
|--------------------------------------------------------------------------
| PUBLIC PATH
|--------------------------------------------------------------------------
|
| Path yang disimpan ke database:
|
| uploads/press-release/filename.webp
|
|--------------------------------------------------------------------------
*/

$publicPath =
    "uploads/press-release/" .
    $uniqueName;


/*
|--------------------------------------------------------------------------
| PUBLIC URL
|--------------------------------------------------------------------------
|
| Karena project berada di /jfc:
|
| /jfc/uploads/press-release/filename.webp
|
|--------------------------------------------------------------------------
*/

$publicUrl =
    "/jfc/" .
    $publicPath;


/*
|--------------------------------------------------------------------------
| FILE INFORMATION
|--------------------------------------------------------------------------
*/

$fileInformation = [

    "name" =>
        $uniqueName,

    "path" =>
        $publicPath,

    "url" =>
        $publicUrl,

    "type" =>
        $uploadType,

    "mime" =>
        $mimeType,

    "size" =>
        $fileSize,

    "width" =>
        (int) $imageInfo[0],

    "height" =>
        (int) $imageInfo[1]

];


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
|
| Kita mengembalikan file di dua tempat:
|
| result.file
|
| dan:
|
| result.data.file
|
| sehingga kompatibel dengan JS lama maupun JS baru.
|
|--------------------------------------------------------------------------
*/

response(

    true,

    "Image successfully uploaded.",

    [

        "file" =>
            $fileInformation

    ],

    200

);