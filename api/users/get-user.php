<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);


function response(bool $success, string $message = "", array $data = null) {

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data"    => $data
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;

}


$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    response(false, "Invalid user ID.");
}


try {

    $stmt = $conn->prepare("
        SELECT id, username, name, email, role, status, last_login, created_at, updated_at
        FROM admin_users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$user) {
        response(false, "User not found.");
    }

    $user["id"] = (int) $user["id"];

    response(true, "User successfully retrieved.", $user);

} catch (Throwable $error) {

    http_response_code(500);

    response(false, "Failed to retrieve user.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();