<?php

header("Content-Type: application/json; charset=UTF-8");


/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| MYSQLI ERROR MODE
|--------------------------------------------------------------------------
*/

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);


/*
|--------------------------------------------------------------------------
| RESPONSE HELPER
|--------------------------------------------------------------------------
*/

function response(
    bool $success,
    string $message = "",
    array $data = []
) {

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data"    => $data
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| STATUS FILTER
|--------------------------------------------------------------------------
*/

$status =
    isset($_GET["status"])
        ? trim($_GET["status"])
        : "";


$allowedStatuses = [
    "",
    "draft",
    "published"
];


if (
    !in_array(
        $status,
        $allowedStatuses,
        true
    )
) {

    http_response_code(422);

    response(
        false,
        "Invalid status filter."
    );

}


/*
|--------------------------------------------------------------------------
| LOAD PRESS RELEASES
|--------------------------------------------------------------------------
*/

try {


    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */

    $sql = "

        SELECT

            id,

            title,

            slug,

            description,

            content,

            cover_image,

            category,

            category_name,

            published_date,

            status,

            meta_title,

            meta_description,

            created_at,

            updated_at

        FROM press_releases

    ";


    /*
    |--------------------------------------------------------------------------
    | STATUS FILTER
    |--------------------------------------------------------------------------
    */

    if (
        $status !== ""
    ) {

        $sql .= "

            WHERE status = ?

        ";

    }


    /*
    |--------------------------------------------------------------------------
    | ORDER
    |--------------------------------------------------------------------------
    */

    $sql .= "

        ORDER BY

            published_date DESC,

            created_at DESC,

            id DESC

    ";


    /*
    |--------------------------------------------------------------------------
    | PREPARE
    |--------------------------------------------------------------------------
    */

    $stmt =
        $conn->prepare(
            $sql
        );


    /*
    |--------------------------------------------------------------------------
    | BIND STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $status !== ""
    ) {

        $stmt->bind_param(
            "s",
            $status
        );

    }


    /*
    |--------------------------------------------------------------------------
    | EXECUTE
    |--------------------------------------------------------------------------
    */

    $stmt->execute();


    $result =
        $stmt->get_result();


    /*
    |--------------------------------------------------------------------------
    | PRESS RELEASE ARRAY
    |--------------------------------------------------------------------------
    */

    $pressReleases = [];


    while (
        $row =
        $result->fetch_assoc()
    ) {


        /*
        |--------------------------------------------------------------------------
        | ID
        |--------------------------------------------------------------------------
        */

        $row["id"] =
            (int)$row["id"];


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        $row["title"] =
            trim(
                (string)(
                    $row["title"] ?? ""
                )
            );


        /*
        |--------------------------------------------------------------------------
        | SLUG
        |--------------------------------------------------------------------------
        */

        $row["slug"] =
            trim(
                (string)(
                    $row["slug"] ?? ""
                )
            );


        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION
        |--------------------------------------------------------------------------
        |
        | Short description digunakan untuk card/list.
        |
        */

        $row["description"] =
            trim(
                (string)(
                    $row["description"] ?? ""
                )
            );


        /*
        |--------------------------------------------------------------------------
        | COVER IMAGE
        |--------------------------------------------------------------------------
        */

        $row["cover_image"] =
            trim(
                (string)(
                    $row["cover_image"] ?? ""
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $category =
            trim(
                (string)(
                    $row["category"] ?? ""
                )
            );


        $categoryName =
            trim(
                (string)(
                    $row["category_name"] ?? ""
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CATEGORY DISPLAY
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | category      = Others
        | category_name = Film Festival
        |
        | category_display = Film Festival
        |
        */

        if (
            $category === "Others"
        ) {

            $row["category_display"] =
                $categoryName !== ""
                    ? $categoryName
                    : "Others";


            /*
            |------------------------------------------------------------------
            | Untuk filter tetap menggunakan "Others"
            |------------------------------------------------------------------
            */

            $row["category_filter"] =
                "Others";

        } else {

            $row["category_display"] =
                $category !== ""
                    ? $category
                    : "Others";


            $row["category_filter"] =
                $category !== ""
                    ? $category
                    : "Others";

        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $row["published_date"]
            )
        ) {

            $row["published_date"] =
                date(
                    "Y-m-d",
                    strtotime(
                        $row["published_date"]
                    )
                );

        } else {

            $row["published_date"] =
                null;

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $row["status"] =
            trim(
                (string)(
                    $row["status"] ?? "draft"
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CONTENT
        |--------------------------------------------------------------------------
        |
        | Pastikan selalu string.
        |
        */

        $row["content"] =
            (string)(
                $row["content"] ?? ""
            );


        /*
        |--------------------------------------------------------------------------
        | ADD ITEM
        |--------------------------------------------------------------------------
        */

        $pressReleases[] =
            $row;

    }


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | TOTAL ALL PRESS RELEASE
    |--------------------------------------------------------------------------
    */

    $totalQuery =
        $conn->query(
            "
            SELECT COUNT(*) AS total
            FROM press_releases
            "
        );


    $totalRow =
        $totalQuery->fetch_assoc();


    $total =
        (int)(
            $totalRow["total"] ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | PUBLISHED COUNT
    |--------------------------------------------------------------------------
    */

    $publishedQuery =
        $conn->query(
            "
            SELECT COUNT(*) AS total
            FROM press_releases
            WHERE status = 'published'
            "
        );


    $publishedRow =
        $publishedQuery->fetch_assoc();


    $published =
        (int)(
            $publishedRow["total"] ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | DRAFT COUNT
    |--------------------------------------------------------------------------
    */

    $draftQuery =
        $conn->query(
            "
            SELECT COUNT(*) AS total
            FROM press_releases
            WHERE status = 'draft'
            "
        );


    $draftRow =
        $draftQuery->fetch_assoc();


    $draft =
        (int)(
            $draftRow["total"] ?? 0
        );


    /*
    |--------------------------------------------------------------------------
    | CATEGORY LIST
    |--------------------------------------------------------------------------
    */

    $categoryQuery =
        $conn->query(
            "
            SELECT DISTINCT category
            FROM press_releases
            WHERE category IS NOT NULL
            AND category <> ''
            ORDER BY category ASC
            "
        );


    $categories = [];


    while (
        $categoryRow =
        $categoryQuery->fetch_assoc()
    ) {

        $categoryValue =
            trim(
                (string)(
                    $categoryRow["category"] ?? ""
                )
            );


        if (
            $categoryValue === ""
        ) {

            continue;

        }


        if (
            !in_array(
                $categoryValue,
                $categories,
                true
            )
        ) {

            $categories[] =
                $categoryValue;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

    response(

        true,

        "Press releases successfully retrieved.",

        [

            "total" =>
                $total,

            "published" =>
                $published,

            "draft" =>
                $draft,

            "categories" =>
                $categories,

            "items" =>
                $pressReleases

        ]

    );


} catch (
    Throwable $e
) {


    /*
    |--------------------------------------------------------------------------
    | SERVER ERROR
    |--------------------------------------------------------------------------
    */

    http_response_code(500);


    response(

        false,

        "Failed to retrieve press releases.",

        [

            "error" =>
                $e->getMessage()

        ]

    );

}


/*
|--------------------------------------------------------------------------
| CLOSE DATABASE
|--------------------------------------------------------------------------
*/

$conn->close();