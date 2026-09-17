<?php

/*
|--------------------------------------------------------------------------
| DELETE INDIVIDUAL MEMBERSHIP RECORD
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = isset($input["id"]) ? (int) $input["id"] : 0;

if ($id <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid ID.",
    ]);

    exit;

}

try {

    $stmt = $conn->prepare(
        "DELETE FROM membership_individual WHERE id = ? LIMIT 1"
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Record not found.",
        ]);

        $stmt->close();
        exit;

    }

    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Record successfully deleted.",
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete record.",
    ]);

}
