<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);


function response(bool $success, string $message = "", array $data = []) {

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


try {

    /*
    |--------------------------------------------------------------------------
    | USERS LIST
    |--------------------------------------------------------------------------
    |
    | password_hash TIDAK di-select, jangan pernah dikirim ke frontend.
    |
    */

    $result = $conn->query("
        SELECT
            id,
            username,
            name,
            email,
            role,
            status,
            last_login,
            created_at,
            updated_at
        FROM admin_users
        ORDER BY created_at DESC
    ");

    $users = [];

    while ($row = $result->fetch_assoc()) {

        $row["id"] = (int) $row["id"];

        $users[] = $row;

    }


    /*
    |--------------------------------------------------------------------------
    | STATS
    |--------------------------------------------------------------------------
    */

    $total = count($users);

    $active = 0;
    $inactive = 0;

    foreach ($users as $user) {

        if ($user["status"] === "active") {
            $active++;
        } else {
            $inactive++;
        }

    }


    response(true, "Users successfully retrieved.", [
        "total"    => $total,
        "active"   => $active,
        "inactive" => $inactive,
        "items"    => $users
    ]);

} catch (Throwable $error) {

    http_response_code(500);

    response(false, "Failed to retrieve users.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();