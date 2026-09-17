<?php

/*
|--------------------------------------------------------------------------
| GET INDIVIDUAL MEMBERSHIP DATA
|--------------------------------------------------------------------------
|
| Mengembalikan seluruh data pendaftar Individual Membership,
| sudah termasuk statistik total/pending/approved/rejected,
| supaya query cukup 1x saja dari sisi admin panel (filter,
| search, sort dilakukan di JS - sama seperti pola
| get-admin-events.php / get-admin-users.php).
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
            submitted_at,
            full_name,
            phone,
            email,
            role_in_industry,
            portfolio_link,
            interest,
            photo,
            status,
            created_at,
            updated_at
        FROM membership_individual
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
        "message" => "Failed to load individual membership data.",
    ]);

}
