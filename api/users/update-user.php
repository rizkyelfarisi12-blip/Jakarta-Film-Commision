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

function value($data, $key, $default = "") {
    return isset($data[$key]) ? $data[$key] : $default;
}


/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/

$id       = (int) value($data, "id", 0);
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

if ($id <= 0) {
    response(false, "Invalid user ID.");
}

if ($username === "") {
    response(false, "Username is required.");
}

if (!preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $username)) {
    response(false, "Username must be 3-50 characters and may only contain letters, numbers, dot, and underscore.");
}

if ($name === "") {
    response(false, "Name is required.");
}

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

/*
| Password opsional saat update. Kosong = tidak diubah.
*/
if ($password !== "" && mb_strlen($password) < 8) {
    response(false, "Password must be at least 8 characters.");
}


/*
|--------------------------------------------------------------------------
| CHECK USER EXISTS
|--------------------------------------------------------------------------
*/

$checkUser = $conn->prepare("SELECT id, role, status FROM admin_users WHERE id = ? LIMIT 1");
$checkUser->bind_param("i", $id);
$checkUser->execute();
$existing = $checkUser->get_result()->fetch_assoc();
$checkUser->close();

if (!$existing) {
    response(false, "User not found.");
}


/*
|--------------------------------------------------------------------------
| CHECK USERNAME DUPLICATE (EXCLUDING SELF)
|--------------------------------------------------------------------------
*/

$checkUsername = $conn->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ? LIMIT 1");
$checkUsername->bind_param("si", $username, $id);
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
| Hanya berlaku kalau user ini SEDANG DIPROMOSIKAN jadi
| super_admin (sebelumnya bukan). Mengedit super_admin yang
| memang sudah super_admin (tetap super_admin) tidak kena
| aturan ini.
|--------------------------------------------------------------------------
*/

if ($role === "super_admin" && $existing["role"] !== "super_admin") {

    $superAdminCheck = $conn->query("
        SELECT COUNT(*) AS total FROM admin_users WHERE role = 'super_admin'
    ");

    $superAdminCount = (int) ($superAdminCheck->fetch_assoc()["total"] ?? 0);

    if ($superAdminCount >= 1) {
        response(false, "Only one Super Admin account is allowed.");
    }

}


/*
|--------------------------------------------------------------------------
| PREVENT LOCKING OUT THE LAST ACTIVE SUPER ADMIN
|--------------------------------------------------------------------------
|
| Kalau user ini SAAT INI super_admin aktif, dan perubahan
| akan membuatnya bukan super_admin lagi atau nonaktif,
| pastikan masih ada super_admin aktif lain (mustahil karena
| aturan di atas membatasi hanya 1, tapi kita tetap jaga-jaga
| supaya tidak pernah ada 0 super_admin aktif).
|--------------------------------------------------------------------------
*/

if (
    $existing["role"] === "super_admin" &&
    $existing["status"] === "active" &&
    ($role !== "super_admin" || $status !== "active")
) {

    response(false, "Cannot remove the only Super Admin account. Create/promote another Super Admin first if needed.");

}


/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/

try {

    $emailValue = $email !== "" ? $email : null;

    if ($password !== "") {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE admin_users
            SET username = ?, password_hash = ?, name = ?, email = ?, role = ?, status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "ssssssi",
            $username,
            $passwordHash,
            $name,
            $emailValue,
            $role,
            $status,
            $id
        );

    } else {

        $stmt = $conn->prepare("
            UPDATE admin_users
            SET username = ?, name = ?, email = ?, role = ?, status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssi",
            $username,
            $name,
            $emailValue,
            $role,
            $status,
            $id
        );

    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to update user: " . $stmt->error);
    }

    $stmt->close();

    response(true, "User successfully updated.");

} catch (Throwable $error) {

    response(false, "Failed to update user.", [
        "error" => $error->getMessage()
    ]);

}

$conn->close();