<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

function response($success, $message, $extra = [])
{
    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| READ JSON REQUEST
|--------------------------------------------------------------------------
*/

$rawInput = file_get_contents("php://input");

$data = json_decode(
    $rawInput,
    true
);


if (!is_array($data)) {

    response(
        false,
        "Invalid JSON request."
    );

}


/*
|--------------------------------------------------------------------------
| GET ID
|--------------------------------------------------------------------------
*/

$id = isset($data["id"])
    ? (int) $data["id"]
    : 0;


if ($id <= 0) {

    response(
        false,
        "Valid press release ID is required."
    );

}


/*
|--------------------------------------------------------------------------
| CHECK PRESS RELEASE
|--------------------------------------------------------------------------
*/

$check = $conn->prepare("
    SELECT
        id,
        title,
        cover_image
    FROM press_releases
    WHERE id = ?
    LIMIT 1
");


if (!$check) {

    response(
        false,
        "Failed to prepare press release validation.",
        [
            "error" => $conn->error
        ]
    );

}


$check->bind_param(
    "i",
    $id
);


if (!$check->execute()) {

    $check->close();

    response(
        false,
        "Failed to check press release."
    );

}


$result = $check->get_result();

$pressRelease = $result->fetch_assoc();

$check->close();


if (!$pressRelease) {

    response(
        false,
        "Press release not found."
    );

}


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM press_releases
    WHERE id = ?
    LIMIT 1
");


if (!$stmt) {

    response(
        false,
        "Failed to prepare delete statement.",
        [
            "error" => $conn->error
        ]
    );

}


$stmt->bind_param(
    "i",
    $id
);


if (!$stmt->execute()) {

    $error = $stmt->error;

    $stmt->close();

    response(
        false,
        "Failed to delete press release.",
        [
            "error" => $error
        ]
    );

}


$affectedRows =
    $stmt->affected_rows;


$stmt->close();


/*
|--------------------------------------------------------------------------
| VERIFY DELETE
|--------------------------------------------------------------------------
*/

if ($affectedRows !== 1) {

    response(
        false,
        "Press release could not be deleted."
    );

}


/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

response(
    true,
    "Press release successfully deleted.",
    [
        "id" => $id
    ]
);


$conn->close();