<?php

require_once __DIR__ . "/../db.php";

header("Content-Type: application/json; charset=UTF-8");


/* =========================================================
   ERROR HANDLER
========================================================= */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


try {


    /* =====================================================
       LATEST EVENTS
    ===================================================== */

    $latestQuery = mysqli_query(
        $conn,
        "
        SELECT
            id,
            title,
            slug,
            category,
            category_name,
            start_date,
            end_date,
            location,
            description,
            cover_image,
            status,
            featured,
            featured_start,
            featured_until,
            created_at

        FROM events

        ORDER BY
            created_at DESC

        LIMIT 3
        "
    );


    $latestEvents = [];


    while($row = mysqli_fetch_assoc($latestQuery)){

        $latestEvents[] = $row;

    }


    /* =====================================================
       TOTAL PUBLISHED EVENTS
    ===================================================== */

    $totalQuery = mysqli_query(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM events
        WHERE status = 'published'
        "
    );


    $totalRow =
        mysqli_fetch_assoc($totalQuery);


    $totalEvents =
        (int)($totalRow["total"] ?? 0);


    /* =====================================================
       FEATURED EVENT
    ===================================================== */

    $today =
        date("Y-m-d");


    $featuredQuery = mysqli_query(
        $conn,
        "
        SELECT
            id,
            title,
            slug,
            category,
            category_name,
            start_date,
            end_date,
            location,
            description,
            cover_image,
            status,
            featured,
            featured_start,
            featured_until

        FROM events

        WHERE
            status = 'published'

            AND featured = 1

            AND (
                featured_start IS NULL
                OR featured_start = ''
                OR featured_start <= '$today'
            )

            AND (
                featured_until IS NULL
                OR featured_until = ''
                OR featured_until >= '$today'
            )

        ORDER BY
            featured_start DESC,
            created_at DESC

        LIMIT 1
        "
    );


    $featuredEvent = null;


    if($row = mysqli_fetch_assoc($featuredQuery)){

        $featuredEvent = $row;

    }


    /* =====================================================
       RESPONSE
    ===================================================== */

    echo json_encode([

        "success" => true,

        "totalEvents" => $totalEvents,

        "latestEvents" => $latestEvents,

        "featuredEvent" => $featuredEvent

    ], JSON_UNESCAPED_UNICODE);


}catch(Throwable $e){


    http_response_code(500);


    echo json_encode([

        "success" => false,

        "message" =>
            "Dashboard event API error",

        "error" =>
            $e->getMessage()

    ], JSON_UNESCAPED_UNICODE);

}