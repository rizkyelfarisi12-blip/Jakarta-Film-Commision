<?php

/*
|--------------------------------------------------------------------------
| GET ENTITIES MEMBERSHIP DATA
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

try {

    $result = $conn->query(
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
        ORDER BY submitted_at DESC, created_at DESC
        "
    );

    if (!$result) {
        throw new Exception($conn->error ?: "Failed to fetch data.");
    }

    $items = [];

    $total = 0;
    $pending = 0;
    $approved = 0;
    $rejected = 0;

    while ($row = $result->fetch_assoc()) {

        $items[] = $row;

        $total++;

        switch ($row["status"]) {

            case "approved":
                $approved++;
                break;

            case "rejected":
                $rejected++;
                break;

            case "pending":
            default:
                $pending++;
                break;

        }

    }

    echo json_encode([

        "success" => true,

        "data" => [

            "items" => $items,

            "total" => $total,
            "pending" => $pending,
            "approved" => $approved,
            "rejected" => $rejected,

        ],

    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to load entities membership data.",
    ]);

}
