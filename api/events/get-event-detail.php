<?php

require_once __DIR__ . "/../db.php";

header("Content-Type: application/json");


/*
|--------------------------------------------------------------------------
| GET BY ID
|--------------------------------------------------------------------------
*/

if(isset($_GET["id"])){

    $id = (int)$_GET["id"];

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $id);

}


/*
|--------------------------------------------------------------------------
| GET BY SLUG
|--------------------------------------------------------------------------
*/

elseif(isset($_GET["slug"])){

    $slug = trim($_GET["slug"]);

    $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE slug = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $slug);

}


/*
|--------------------------------------------------------------------------
| NO PARAMETER
|--------------------------------------------------------------------------
*/

else{

    echo json_encode([

        "success" => false,
        "message" => "ID or slug is required"

    ]);

    exit;

}


$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();


if(!$data){

    echo json_encode([

        "success" => false,
        "message" => "Event not found"

    ]);

    exit;

}


echo json_encode($data);