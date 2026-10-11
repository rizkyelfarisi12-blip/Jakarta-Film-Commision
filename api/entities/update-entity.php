<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$input = json_decode(file_get_contents("php://input"), true);

$id = isset($input["id"]) ? (int) $input["id"] : 0;

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

if ($id <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid ID.",
    ]);

    exit;

}

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

$submittedAt = $submittedAt !== "" ? $submittedAt . " 00:00:00" : null;

try {

    $stmt = $conn->prepare(
        "
        UPDATE members_entities
        SET
            submitted_at = COALESCE(?, submitted_at),
            company_name = ?,
            description = ?,
            entity_type = ?,
            entity_type_name = ?,
            production_type = ?,
            nib = ?,
            company_profile = ?,
            logo = ?,
            social_media = ?,
            interest = ?,
            pic_name = ?,
            pic_position = ?,
            pic_email = ?,
            pic_phone = ?,
            status = ?
        WHERE id = ?
        LIMIT 1
        "
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param(
        "ssssssssssssssssi",
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
        $status,
        $id
    );

    $stmt->execute();
    $stmt->close();

    echo json_encode([
        "success" => true,
        "message" => "Entity successfully updated.",
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update entity.",
    ]);

}
