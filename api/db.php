<?php

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
| Perubahan: tambah set_charset utf8mb4 (supaya karakter non-ASCII aman)
| dan error koneksi tidak lagi dibocorkan ke browser.
|
| PENTING: kredensial di bawah ini untuk LOCAL (XAMPP). Di server
| produksi ganti host/user/password/database sesuai hosting.
|--------------------------------------------------------------------------
*/

$conn = @mysqli_connect(
    "localhost",
    "root",
    "",
    "jfc"
);

if (!$conn) {

    error_log("DB connection failed: " . mysqli_connect_error());

    http_response_code(500);

    header("Content-Type: application/json; charset=UTF-8");

    die(json_encode([
        "success" => false,
        "message" => "Database connection failed.",
    ]));

}

mysqli_set_charset($conn, "utf8mb4");