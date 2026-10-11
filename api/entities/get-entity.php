<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid ID.",
    ]);

    exit;

}

try {

    $stmt = $conn->prepare(
        "
        SELECT
            id,
            submitted_at,
            company_name,
            description,
            entity_type,
            entity_type_name,
            production_type,
            nib,
            company_profile,
            logo,
            social_media,
            interest,
            pic_name,
            pic_position,
            pic_email,
            pic_phone,
            status,
            created_at,
            updated_at
        FROM members_entities
        WHERE id = ?
        LIMIT 1
        "
    );

    if (!$stmt) {
        throw new Exception($conn->error ?: "Failed to prepare statement.");
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    $stmt->close();

    if (!$item) {

        echo json_encode([
            "success" => false,
            "message" => "Entity not found.",
        ]);

        exit;

    }

    echo json_encode([
        "success" => true,
        "data" => $item,
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to load entity.",
    ]);

}
