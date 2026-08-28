<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

function response($success, $message = "", $data = null)
{
    $output = [
        "success" => $success,
        "message" => $message
    ];

    if ($data !== null) {
        $output["data"] = $data;
    }

    echo json_encode(
        $output,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GET BY ID
|--------------------------------------------------------------------------
*/

if (isset($_GET["id"])) {

    $id = (int)$_GET["id"];


    if ($id <= 0) {

        response(
            false,
            "Invalid press release ID."
        );

    }


    $stmt = $conn->prepare("
        SELECT *
        FROM press_releases
        WHERE id = ?
        LIMIT 1
    ");


    if (!$stmt) {

        response(
            false,
            "Failed to prepare query.",
            [
                "error" => $conn->error
            ]
        );

    }


    $stmt->bind_param(
        "i",
        $id
    );

}


/*
|--------------------------------------------------------------------------
| GET BY SLUG
|--------------------------------------------------------------------------
*/

elseif (isset($_GET["slug"])) {

    $slug = trim(
        $_GET["slug"]
    );


    if ($slug === "") {

        response(
            false,
            "Slug is required."
        );

    }


    $stmt = $conn->prepare("
        SELECT *
        FROM press_releases
        WHERE slug = ?
        LIMIT 1
    ");


    if (!$stmt) {

        response(
            false,
            "Failed to prepare query.",
            [
                "error" => $conn->error
            ]
        );

    }


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

    response(
        false,
        "ID or slug is required."
    );

}


/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {

    $error =
        $stmt->error;

    $stmt->close();

    response(
        false,
        "Failed to retrieve press release.",
        [
            "error" => $error
        ]
    );

}


/*
|--------------------------------------------------------------------------
| RESULT
|--------------------------------------------------------------------------
*/

$result =
    $stmt->get_result();


$pressRelease =
    $result->fetch_assoc();


$stmt->close();


/*
|--------------------------------------------------------------------------
| NOT FOUND
|--------------------------------------------------------------------------
*/

if (!$pressRelease) {

    response(
        false,
        "Press release not found."
    );

}


/*
|--------------------------------------------------------------------------
| NORMALIZATION
|--------------------------------------------------------------------------
*/

$pressRelease["id"] =
    (int)$pressRelease["id"];


if (isset($pressRelease["featured"])) {

    $pressRelease["featured"] =
        (int)$pressRelease["featured"];

}


/*
|--------------------------------------------------------------------------
| JSON CONTENT
|--------------------------------------------------------------------------
|
| Jika content disimpan sebagai JSON,
| kita kembalikan juga versi decoded.
|
|--------------------------------------------------------------------------
*/

if (
    isset($pressRelease["content"]) &&
    $pressRelease["content"] !== ""
) {

    $decodedContent =
        json_decode(
            $pressRelease["content"],
            true
        );


    if (
        json_last_error() === JSON_ERROR_NONE &&
        is_array($decodedContent)
    ) {

        $pressRelease["content_data"] =
            $decodedContent;

    } else {

        $pressRelease["content_data"] =
            [];

    }

}


/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

response(
    true,
    "Press release successfully retrieved.",
    $pressRelease
);


$conn->close();