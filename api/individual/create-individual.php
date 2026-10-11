<?php

/*
|--------------------------------------------------------------------------
| CREATE INDIVIDUAL MEMBERSHIP RECORD
|--------------------------------------------------------------------------
|
| Sesuai skema members_individual, kolom-kolom berikut NOT NULL
| di database: full_name, whatsapp, email, role, portfolio_link,
| photo. Jadi divalidasi wajib diisi di sini juga.
|
| "interests" disimpan sebagai JSON array text, contoh:
| ["One-Stop Film Service","Promosi & Diplomasi Budaya"]
|
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$fullName = trim($input["full_name"] ?? "");
$whatsapp = trim($input["whatsapp"] ?? "");
$email = trim($input["email"] ?? "");
$role = trim($input["role"] ?? "");
$roleOther = trim($input["role_other"] ?? "");
$portfolioLink = trim($input["portfolio_link"] ?? "");
$photo = trim($input["photo"] ?? "");
$status = $input["status"] ?? "pending";

$interestsInput = $input["interests"] ?? [];
$interests = is_array($interestsInput) ? array_values(array_filter($interestsInput, "strlen")) : [];

$allowedStatus = ["pending", "approved", "rejected"];

/*
|--------------------------------------------------------------------------
| VALIDATION (mengikuti NOT NULL di tabel members_individual)
|--------------------------------------------------------------------------
*/

$errors = [];

if ($fullName === "") $errors[] = "Full name is required.";
if ($whatsapp === "") $errors[] = "WhatsApp number is required.";
if ($email === "") $errors[] = "Email is required.";
if ($role === "") $errors[] = "Role is required.";
if ($role === "Others" && $roleOther === "") $errors[] = "Please specify the custom role.";
if ($portfolioLink === "") $errors[] = "Portfolio link is required.";
if ($photo === "") $errors[] = "Profile photo is required.";

if ($errors) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => implode(" ", $errors),
    ]);

    exit;

}

if (!in_array($status, $allowedStatus, true)) {
    $status = "pending";
}

if ($role !== "Others") {
    $roleOther = "";
}

$interestsJson = json_encode($interests, JSON_UNESCAPED_UNICODE);

try {

    $stmt = $conn->prepare(
        "
        INSERT INTO members_individual
            (full_name, whatsapp, email, role, role_other,
             portfolio_link, interests, photo, status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param(
        "sssssssss",
        $fullName,
        $whatsapp,
        $email,
        $role,
        $roleOther,
        $portfolioLink,
        $interestsJson,
        $photo,
        $status
    );

    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Registrant successfully created.",
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create registrant.",
    ]);

}
