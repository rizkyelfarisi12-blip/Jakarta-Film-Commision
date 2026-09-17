<?php

/*
|--------------------------------------------------------------------------
| UPDATE INDIVIDUAL MEMBERSHIP STATUS
|--------------------------------------------------------------------------
|
| Dipakai admin untuk approve / reject / kembalikan ke pending
| pendaftar Individual Membership.
|
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = isset($input["id"]) ? (int) $input["id"] : 0;

$status = $input["status"] ?? "";

$allowedStatus = ["pending", "approved", "rejected"];

if ($id <= 0 || !in_array($status, $allowedStatus, true)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request.",
    ]);

    exit;

}

try {

    $stmt = $conn->prepare(
        "UPDATE membership_individual SET status = ? WHERE id = ? LIMIT 1"
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Status successfully updated.",
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update status.",
    ]);

}
