<?php

session_start();

require_once __DIR__ . "/../api/db.php";


/*
|--------------------------------------------------------------------------
| ONLY POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| GET INPUT
|--------------------------------------------------------------------------
*/

$username =
    trim(
        $_POST["username"] ?? ""
    );

$password =
    $_POST["password"] ?? "";


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (
    $username === "" ||
    $password === ""
) {

    header(
        "Location: login.php?error=empty"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| GET ADMIN
|--------------------------------------------------------------------------
|
| Untuk sementara kita menggunakan tabel:
|
| admin_users
|
| Struktur tabel akan kita buat setelah ini.
|
|--------------------------------------------------------------------------
*/

$stmt =
    $conn->prepare(
        "
        SELECT
            id,
            username,
            password,
            name,
            role,
            status
        FROM admin_users
        WHERE username = ?
        LIMIT 1
        "
    );


if (!$stmt) {

    header(
        "Location: login.php?error=system"
    );

    exit;

}


$stmt->bind_param(
    "s",
    $username
);


$stmt->execute();


$result =
    $stmt->get_result();


$admin =
    $result->fetch_assoc();


/*
|--------------------------------------------------------------------------
| CHECK USER
|--------------------------------------------------------------------------
*/

if (!$admin) {

    header(
        "Location: login.php?error=invalid"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| CHECK STATUS
|--------------------------------------------------------------------------
*/

if (
    $admin["status"] !== "active"
) {

    header(
        "Location: login.php?error=inactive"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| VERIFY PASSWORD
|--------------------------------------------------------------------------
*/

if (
    !password_verify(
        $password,
        $admin["password"]
    )
) {

    header(
        "Location: login.php?error=invalid"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| SESSION REGENERATE
|--------------------------------------------------------------------------
|
| Mencegah session fixation.
|
|--------------------------------------------------------------------------
*/

session_regenerate_id(true);


/*
|--------------------------------------------------------------------------
| CREATE ADMIN SESSION
|--------------------------------------------------------------------------
*/

$_SESSION["admin_id"] =
    (int)$admin["id"];


$_SESSION["admin_username"] =
    $admin["username"];


$_SESSION["admin_name"] =
    $admin["name"];


$_SESSION["admin_role"] =
    $admin["role"];


/*
|--------------------------------------------------------------------------
| LOGIN TIME
|--------------------------------------------------------------------------
*/

$_SESSION["admin_login_time"] =
    time();


/*
|--------------------------------------------------------------------------
| UPDATE LAST LOGIN
|--------------------------------------------------------------------------
*/

$update =
    $conn->prepare(
        "
        UPDATE admin_users
        SET
            last_login = NOW()
        WHERE id = ?
        "
    );


if ($update) {

    $update->bind_param(
        "i",
        $admin["id"]
    );

    $update->execute();

    $update->close();

}


/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header(
    "Location: dashboard.php"
);

exit;