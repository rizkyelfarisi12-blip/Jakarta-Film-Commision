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

$id = isset($data["id"]) ? (int) $data["id"] : 0;

if ($id <= 0) {
    response(false, "Invalid user ID.");
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

    if ($user["role"] === "super_admin" && $user["status"] === "active") {
        response(false, "Cannot delete the only Super Admin account.");
    }

    $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error, $stmt->errno);
    }

    $stmt->close();

    response(true, "User successfully deleted.");

} catch (Throwable $error) {

    /*
    |--------------------------------------------------------------------------
    | FOREIGN KEY CONSTRAINT
    |--------------------------------------------------------------------------
    |
    | admin_login_history punya FK ke admin_users tanpa ON DELETE
    | CASCADE, jadi user yang sudah punya riwayat login TIDAK BISA
    | dihapus langsung (akan error 1451). Kasih pesan yang jelas,
    | arahkan ke nonaktifkan saja.
    |--------------------------------------------------------------------------
    */

    if ($error->getCode() == 1451) {

        response(false, "This user has login history and cannot be deleted. Deactivate the account instead.");

    }

    response(false, "Failed to delete user.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();