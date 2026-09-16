<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| READ INPUT
|--------------------------------------------------------------------------
*/

$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    response(false, "Invalid JSON request.");
}

function value($data, $key, $default = "") {
    return isset($data[$key]) ? $data[$key] : $default;
}


/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$username = trim(value($data, "username"));
$name     = trim(value($data, "name"));
$email    = trim(value($data, "email"));
$password = (string) value($data, "password");
$role     = trim(value($data, "role"));
$status   = trim(value($data, "status", "active"));


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if ($username === "") {
    response(false, "Username is required.");
}

if (!preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $username)) {
    response(false, "Username must be 3-50 characters and may only contain letters, numbers, dot, and underscore.");
}

if ($name === "") {
    response(false, "Name is required.");
}

if (mb_strlen($name) > 100) {
    response(false, "Name must not exceed 100 characters.");
}

/*
| Email bersifat opsional (nullable di database).
*/
if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response(false, "Invalid email address.");
}

$allowedRoles = ["super_admin", "content_admin", "communication_admin", "membership_admin"];

if (!in_array($role, $allowedRoles, true)) {
    response(false, "Invalid role.");
}

$allowedStatuses = ["active", "inactive"];

if (!in_array($status, $allowedStatuses, true)) {
    response(false, "Invalid status.");
}

if ($password === "" || mb_strlen($password) < 8) {
    response(false, "Password must be at least 8 characters.");
}


/*
|--------------------------------------------------------------------------
| CHECK USERNAME DUPLICATE
|--------------------------------------------------------------------------
*/

$checkUsername = $conn->prepare("SELECT id FROM admin_users WHERE username = ? LIMIT 1");
$checkUsername->bind_param("s", $username);
$checkUsername->execute();

if ($checkUsername->get_result()->num_rows > 0) {
    $checkUsername->close();
    response(false, "This username is already taken.");
}

$checkUsername->close();


/*
|--------------------------------------------------------------------------
| ONLY ONE SUPER ADMIN ALLOWED
|--------------------------------------------------------------------------
|
| Sesuai kebijakan: hanya boleh ada 1 akun Super Admin.
| Hapus/ubah blok ini kalau kebijakan ini berubah nanti.
|--------------------------------------------------------------------------
*/

if ($role === "super_admin") {

    $superAdminCheck = $conn->query("
        SELECT COUNT(*) AS total FROM admin_users WHERE role = 'super_admin'
    ");

    $superAdminCount = (int) ($superAdminCheck->fetch_assoc()["total"] ?? 0);

    if ($superAdminCount >= 1) {
        response(false, "Only one Super Admin account is allowed. Edit the existing Super Admin instead.");
    }

}


/*
|--------------------------------------------------------------------------
| HASH PASSWORD
|--------------------------------------------------------------------------
*/

$passwordHash = password_hash($password, PASSWORD_DEFAULT);


/*
|--------------------------------------------------------------------------
| INSERT
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare("
        INSERT INTO admin_users (username, password_hash, name, email, role, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $emailValue = $email !== "" ? $email : null;

    $stmt->bind_param(
        "ssssss",
        $username,
        $passwordHash,
        $name,
        $emailValue,
        $role,
        $status
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to create user: " . $stmt->error);
    }

    $newId = $conn->insert_id;

    $stmt->close();

    response(true, "User successfully created.", [
        "id" => $newId
    ]);

} catch (Throwable $error) {

    response(false, "Failed to create user.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();