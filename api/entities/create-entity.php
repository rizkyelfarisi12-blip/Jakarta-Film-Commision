<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$companyName = trim($input["company_name"] ?? "");
$description = trim($input["description"] ?? "");
$entityType = trim($input["entity_type"] ?? "");
$entityTypeName = trim($input["entity_type_name"] ?? "");
$productionType = trim($input["production_type"] ?? "");
$nib = trim($input["nib"] ?? "");
$companyProfile = trim($input["company_profile"] ?? "");
$logo = trim($input["logo"] ?? "");
$socialMedia = trim($input["social_media"] ?? "");
$interest = trim($input["interest"] ?? "");

$picName = trim($input["pic_name"] ?? "");
$picPosition = trim($input["pic_position"] ?? "");
$picEmail = trim($input["pic_email"] ?? "");
$picPhone = trim($input["pic_phone"] ?? "");

$status = $input["status"] ?? "pending";
$submittedAt = trim($input["submitted_at"] ?? "");

$allowedStatus = ["pending", "approved", "rejected"];

if ($companyName === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Company name is required.",
    ]);

    exit;

}

if (!in_array($status, $allowedStatus, true)) {
    $status = "pending";
}

if ($entityType !== "Others") {
    $entityTypeName = "";
}

$submittedAt = $submittedAt !== "" ? $submittedAt . " 00:00:00" : date("Y-m-d H:i:s");

try {

    $stmt = $conn->prepare(
        "
        INSERT INTO members_entities
            (submitted_at, company_name, description, entity_type,
             entity_type_name, production_type, nib, company_profile,
             logo, social_media, interest, pic_name, pic_position,
             pic_email, pic_phone, status)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param(
        "ssssssssssssssss",
        $submittedAt,
        $companyName,
        $description,
        $entityType,
        $entityTypeName,
        $productionType,
        $nib,
        $companyProfile,
        $logo,
        $socialMedia,
        $interest,
        $picName,
        $picPosition,
        $picEmail,
        $picPhone,
        $status
    );

    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Entity successfully created.",
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create entity.",
    ]);

}
