<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$fullName = trim($input["full_name"] ?? "");
$phone = trim($input["phone"] ?? "");
$email = trim($input["email"] ?? "");
$roleInIndustry = trim($input["role_in_industry"] ?? "");
$portfolioLink = trim($input["portfolio_link"] ?? "");
$interest = trim($input["interest"] ?? "");
$photo = trim($input["photo"] ?? "");
$status = $input["status"] ?? "pending";
$submittedAt = trim($input["submitted_at"] ?? "");

$allowedStatus = ["pending", "approved", "rejected"];

if ($fullName === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Full name is required.",
    ]);

    exit;

}

if (!in_array($status, $allowedStatus, true)) {
    $status = "pending";
}

$submittedAt = $submittedAt !== "" ? $submittedAt . " 00:00:00" : date("Y-m-d H:i:s");

try {

    $stmt = $conn->prepare(
        "
        INSERT INTO membership_individual
            (submitted_at, full_name, phone, email, role_in_industry,
             portfolio_link, interest, photo, status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param(
        "sssssssss",
        $submittedAt,
        $fullName,
        $phone,
        $email,
        $roleInIndustry,
        $portfolioLink,
        $interest,
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
