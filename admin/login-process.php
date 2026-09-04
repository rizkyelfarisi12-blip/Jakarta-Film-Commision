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
| RATE LIMIT
|--------------------------------------------------------------------------
|
| Proteksi dasar dari brute-force: kunci sementara setelah
| beberapa kali gagal berturut-turut. Disimpan di session
| supaya tidak perlu tabel tambahan.
|
|--------------------------------------------------------------------------
*/

$maxAttempts = 5;

$lockoutSeconds = 900; // 15 menit

if (!isset($_SESSION["login_attempts"])) {
    $_SESSION["login_attempts"] = 0;
}

if (!isset($_SESSION["login_locked_until"])) {
    $_SESSION["login_locked_until"] = 0;
}

if ($_SESSION["login_locked_until"] > time()) {

    $remainingMinutes =
        (int) ceil(
            ($_SESSION["login_locked_until"] - time()) / 60
        );

    header(
        "Location: login.php?error=locked&minutes=" . $remainingMinutes
    );

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
| PENTING: kolom password di tabel admin_users bernama
| "password_hash", bukan "password".
|
|--------------------------------------------------------------------------
*/
$stmt =
    $conn->prepare(
        "
        SELECT
            id,
            username,
            password_hash,
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

$stmt->close();

/*
|--------------------------------------------------------------------------
| HELPER: REGISTER FAILED ATTEMPT
|--------------------------------------------------------------------------
*/
function registerFailedAttempt($maxAttempts, $lockoutSeconds) {

    $_SESSION["login_attempts"]++;

    if ($_SESSION["login_attempts"] >= $maxAttempts) {

        $_SESSION["login_locked_until"] =
            time() + $lockoutSeconds;

        $_SESSION["login_attempts"] = 0;

    }

}

/*
|--------------------------------------------------------------------------
| CHECK USER
|--------------------------------------------------------------------------
*/
if (!$admin) {

    registerFailedAttempt($maxAttempts, $lockoutSeconds);

    header(
        "Location: login.php?error=invalid"
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| VERIFY PASSWORD
|--------------------------------------------------------------------------
|
| Dicek SEBELUM status, supaya pesan error untuk username
| valid tapi password salah tetap generik ("invalid") dan
| tidak membocorkan apakah akun tersebut aktif/nonaktif ke
| orang yang tidak tahu passwordnya.
|
|--------------------------------------------------------------------------
*/
if (
    !password_verify(
        $password,
        $admin["password_hash"]
    )
) {

    registerFailedAttempt($maxAttempts, $lockoutSeconds);

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
| RESET RATE LIMIT ON SUCCESS
|--------------------------------------------------------------------------
*/
$_SESSION["login_attempts"] = 0;
$_SESSION["login_locked_until"] = 0;

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
| RECORD LOGIN HISTORY
|--------------------------------------------------------------------------
*/
$ipAddress =
    $_SERVER["REMOTE_ADDR"] ?? null;

$userAgent =
    $_SERVER["HTTP_USER_AGENT"] ?? null;

$history =
    $conn->prepare(
        "
        INSERT INTO admin_login_history
            (admin_id, ip_address, user_agent)
        VALUES
            (?, ?, ?)
        "
    );

if ($history) {

    $history->bind_param(
        "iss",
        $admin["id"],
        $ipAddress,
        $userAgent
    );
    $history->execute();
    $history->close();
}

// redirect
header(
    "Location: dashboard.php"
);

exit;