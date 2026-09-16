<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);


function response(bool $success, string $message = "", array $extra = []) {

    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;

}


$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    response(false, "Invalid JSON request.");
}

$id     = isset($data["id"]) ? (int) $data["id"] : 0;
$status = isset($data["status"]) ? trim($data["status"]) : "";

if ($id <= 0) {
    response(false, "Invalid user ID.");
}

$allowedStatuses = ["active", "inactive"];

if (!in_array($status, $allowedStatuses, true)) {
    response(false, "Invalid status.");
}


try {

    $checkStmt = $conn->prepare("SELECT role, status FROM admin_users WHERE id = ? LIMIT 1");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $user = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if (!$user) {
        response(false, "User not found.");
    }

    /*
    |--------------------------------------------------------------------------
    | PREVENT DEACTIVATING THE ONLY SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    if ($user["role"] === "super_admin" && $user["status"] === "active" && $status === "inactive") {
        response(false, "Cannot deactivate the only Super Admin account.");
    }

    $stmt = $conn->prepare("UPDATE admin_users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update status: " . $stmt->error);
    }

    $stmt->close();

    response(true, "User status updated.", [
        "status" => $status
    ]);

} catch (Throwable $error) {

    response(false, "Failed to update user status.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();
