<?php

/*
|--------------------------------------------------------------------------
| GET INDIVIDUAL MEMBERSHIP DATA
|--------------------------------------------------------------------------
|
| Disesuaikan dengan skema tabel members_individual:
| full_name, whatsapp, email, role, role_other, portfolio_link,
| interests (JSON array), photo, status, created_at, updated_at.
|
| Catatan: tabel ini TIDAK punya kolom submitted_at terpisah,
| jadi tanggal registrasi memakai created_at.
|
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

try {

    $result = $conn->query(
        "
        SELECT
            id,
            full_name,
            whatsapp,
            email,
            role,
            role_other,
            portfolio_link,
            interests,
            photo,
            status,
            created_at,
            updated_at
        FROM members_individual
        ORDER BY created_at DESC
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
        "message" => "Failed to load individual membership data.",
    ]);

}
