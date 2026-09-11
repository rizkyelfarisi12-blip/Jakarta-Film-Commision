<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| HELPER RESPONSE
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


$rawData = file_get_contents("php://input");

$data = json_decode($rawData, true);

if(!is_array($data)){

    response(
        false,
        "Request tidak valid."
    );

}

$id = $data["id"] ?? null;


if(!$id || !is_numeric($id)){

    response(
        false,
        "ID event tidak valid."
    );

}


$id = (int) $id;

$stmt = $conn->prepare("
    SELECT id
    FROM events
    WHERE id = ?
    LIMIT 1
");


if(!$stmt){

    response(
        false,
        "Gagal menyiapkan query."
    );

}


$stmt->bind_param(
    "i",
    $id
);


$stmt->execute();


$result = $stmt->get_result();


if($result->num_rows === 0){

    $stmt->close();

    response(
        false,
        "Event tidak ditemukan."
    );

}


$stmt->close();

$stmt = $conn->prepare("
    DELETE FROM events
    WHERE id = ?
");


if(!$stmt){

    response(
        false,
        "Gagal menyiapkan proses delete."
    );

}

$stmt->bind_param(
    "i",
    $id
);

if(!$stmt->execute()){

    $stmt->close();

    response(
        false,
        "Gagal menghapus event."
    );

}

if($stmt->affected_rows < 1){

    $stmt->close();

    response(
        false,
        "Event gagal dihapus."
    );

}


$stmt->close();

response(
    true,
    "Event berhasil dihapus.",
    [
        "id" => $id
    ]
);