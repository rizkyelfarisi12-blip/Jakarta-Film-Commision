<?php

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION
|--------------------------------------------------------------------------
|
| File ini digunakan untuk melindungi halaman admin.
|
| Jangan include file ini pada:
| - login.php
| - login-process.php
| - logout.php
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {

    session_start();

}


/*
|--------------------------------------------------------------------------
| PATH CONFIG
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../../config/path.php";


/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["admin_id"]) ||
    empty($_SESSION["admin_id"])
) {

    /*
    |--------------------------------------------------------------------------
    | CLEAR INVALID SESSION
    |--------------------------------------------------------------------------
    */

    unset(
        $_SESSION["admin_id"],
        $_SESSION["admin_username"],
        $_SESSION["admin_name"],
        $_SESSION["admin_role"]
    );


    /*
    |--------------------------------------------------------------------------
    | REDIRECT TO LOGIN
    |--------------------------------------------------------------------------
    */

    header(
        "Location: " .
        ADMIN_URL .
        "/login.php"
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| NORMALIZE SESSION DATA
|--------------------------------------------------------------------------
|
| Data ini nantinya akan digunakan oleh permission system.
|
*/

$adminId =
    (int) $_SESSION["admin_id"];


$adminUsername =
    $_SESSION["admin_username"] ?? "";


$adminName =
    $_SESSION["admin_name"] ?? "";


$adminRole =
    $_SESSION["admin_role"] ?? "";


/*
|--------------------------------------------------------------------------
| OPTIONAL GLOBAL VARIABLES
|--------------------------------------------------------------------------
|
| Bisa langsung digunakan oleh halaman admin setelah auth.php dipanggil.
|
*/

$currentAdmin = [

    "id" =>
        $adminId,

    "username" =>
        $adminUsername,

    "name" =>
        $adminName,

    "role" =>
        $adminRole

];


/*
|--------------------------------------------------------------------------
| REQUIRE ROLE
|--------------------------------------------------------------------------
|
| Panggil di halaman yang mau dibatasi ke role tertentu.
| Taruh SEBELUM $pageTitle / include header.php, supaya
| dicek sebelum ada output apa pun.
|
| Contoh (di admin/users/index.php dan admin/users/form.php):
|
|   require_once __DIR__ . "/../includes/auth.php";
|   requireRole(["super_admin"]);
|
|--------------------------------------------------------------------------
*/

function requireRole(array $allowedRoles) {

    global $adminRole;

    if (
        !in_array(
            $adminRole,
            $allowedRoles,
            true
        )
    ) {

        http_response_code(403);

        echo "<!DOCTYPE html><html><head><title>403 Forbidden</title></head>";
        echo "<body style=\"font-family:sans-serif;text-align:center;padding:80px 20px;\">";
        echo "<h1>403 - Access Denied</h1>";
        echo "<p>You don't have permission to access this page.</p>";
        echo "<p><a href=\"" . ADMIN_URL . "/dashboard.php\">Back to Dashboard</a></p>";
        echo "</body></html>";

        exit;

    }

}