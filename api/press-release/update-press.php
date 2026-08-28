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
    string $message,
    array $data = []
) {

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| REQUEST METHOD
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    http_response_code(405);

    response(
        false,
        "Invalid request method."
    );

}


/*
|--------------------------------------------------------------------------
| READ JSON BODY
|--------------------------------------------------------------------------
*/

$rawInput =
    file_get_contents("php://input");


if (
    $rawInput === false ||
    trim($rawInput) === ""
) {

    http_response_code(400);

    response(
        false,
        "Request body is empty."
    );

}


/*
|--------------------------------------------------------------------------
| DECODE JSON
|--------------------------------------------------------------------------
*/

$data =
    json_decode(
        $rawInput,
        true
    );


if (
    !is_array($data)
) {

    http_response_code(400);

    response(
        false,
        "Invalid JSON request."
    );

}


/*
|--------------------------------------------------------------------------
| INPUT HELPER
|--------------------------------------------------------------------------
*/

function getInput(
    array $data,
    string $key,
    string $default = ""
): string {

    if (
        !array_key_exists(
            $key,
            $data
        )
    ) {

        return $default;

    }


    if (
        is_array(
            $data[$key]
        )
    ) {

        return $default;

    }


    return trim(
        (string) $data[$key]
    );

}


/*
|--------------------------------------------------------------------------
| COLLECT ID
|--------------------------------------------------------------------------
*/

$idValue =
    $data["id"] ?? null;


/*
|--------------------------------------------------------------------------
| VALIDATE ID
|--------------------------------------------------------------------------
*/

if (
    $idValue === null ||
    $idValue === "" ||
    !is_numeric($idValue)
) {

    http_response_code(422);

    response(
        false,
        "Press Release ID is required."
    );

}


$id =
    (int) $idValue;


if (
    $id <= 0
) {

    http_response_code(422);

    response(
        false,
        "Invalid Press Release ID."
    );

}


/*
|--------------------------------------------------------------------------
| COLLECT INPUT
|--------------------------------------------------------------------------
*/

$title =
    getInput(
        $data,
        "title"
    );


$slug =
    getInput(
        $data,
        "slug"
    );


$description =
    getInput(
        $data,
        "description"
    );


$content =
    getInput(
        $data,
        "content"
    );


$coverImage =
    getInput(
        $data,
        "cover_image"
    );


$category =
    getInput(
        $data,
        "category"
    );


$categoryName =
    getInput(
        $data,
        "category_name"
    );


$publishedDate =
    getInput(
        $data,
        "published_date"
    );


$status =
    getInput(
        $data,
        "status",
        "draft"
    );


$metaTitle =
    getInput(
        $data,
        "meta_title"
    );


$metaDescription =
    getInput(
        $data,
        "meta_description"
    );


/*
|--------------------------------------------------------------------------
| CHECK EXISTING PRESS RELEASE
|--------------------------------------------------------------------------
*/

$check =
    $conn->prepare(
        "
        SELECT
            id,
            cover_image
        FROM press_releases
        WHERE id = ?
        LIMIT 1
        "
    );


$check->bind_param(
    "i",
    $id
);


$check->execute();


$checkResult =
    $check->get_result();


if (
    $checkResult->num_rows === 0
) {

    $check->close();

    http_response_code(404);

    response(
        false,
        "Press Release not found."
    );

}


$existing =
    $checkResult->fetch_assoc();


$existingCoverImage =
    trim(
        (string) (
            $existing["cover_image"] ?? ""
        )
    );


$check->close();


/*
|--------------------------------------------------------------------------
| VALIDATE TITLE
|--------------------------------------------------------------------------
*/

if (
    $title === ""
) {

    http_response_code(422);

    response(
        false,
        "Title is required."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE SLUG
|--------------------------------------------------------------------------
*/

if (
    $slug === ""
) {

    http_response_code(422);

    response(
        false,
        "Slug is required."
    );

}


/*
|--------------------------------------------------------------------------
| SLUG FORMAT
|--------------------------------------------------------------------------
|
| Only:
| a-z
| 0-9
| hyphen
|
|--------------------------------------------------------------------------
*/

if (
    !preg_match(
        '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        $slug
    )
) {

    http_response_code(422);

    response(
        false,
        "Invalid slug format."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE DESCRIPTION
|--------------------------------------------------------------------------
*/

if (
    $description === ""
) {

    http_response_code(422);

    response(
        false,
        "Short description is required."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE CATEGORY
|--------------------------------------------------------------------------
*/

$allowedCategories = [

    "News",
    "Event",
    "Announcement",
    "Others"

];


if (
    !in_array(
        $category,
        $allowedCategories,
        true
    )
) {

    http_response_code(422);

    response(
        false,
        "Invalid category."
    );

}


/*
|--------------------------------------------------------------------------
| CATEGORY OTHERS
|--------------------------------------------------------------------------
*/

if (
    $category === "Others"
) {

    if (
        $categoryName === ""
    ) {

        http_response_code(422);

        response(
            false,
            "Please enter the custom category name."
        );

    }

} else {

    /*
    |----------------------------------------------------------------------
    | RESET CUSTOM CATEGORY
    |----------------------------------------------------------------------
    */

    $categoryName = "";

}


/*
|--------------------------------------------------------------------------
| VALIDATE COVER IMAGE
|--------------------------------------------------------------------------
*/

if (
    $coverImage === ""
) {

    /*
    |----------------------------------------------------------------------
    | Jika user tidak mengirim cover baru,
    | gunakan cover lama.
    |----------------------------------------------------------------------
    */

    $coverImage =
        $existingCoverImage;

}


/*
|--------------------------------------------------------------------------
| COVER IMAGE PATH NORMALIZATION
|--------------------------------------------------------------------------
*/

$coverImage =
    str_replace(
        "\\",
        "/",
        $coverImage
    );


/*
|--------------------------------------------------------------------------
| VALIDATE COVER IMAGE PATH
|--------------------------------------------------------------------------
*/

if (
    $coverImage === ""
) {

    http_response_code(422);

    response(
        false,
        "Cover image is required."
    );

}


if (
    strpos(
        $coverImage,
        "uploads/press-release/"
    ) !== 0
) {

    http_response_code(422);

    response(
        false,
        "Invalid cover image path."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE DATE
|--------------------------------------------------------------------------
*/

if (
    $publishedDate !== ""
) {

    $dateObject =
        DateTime::createFromFormat(
            "Y-m-d",
            $publishedDate
        );


    $dateErrors =
        DateTime::getLastErrors();


    /*
    |----------------------------------------------------------------------
    | PHP 8.2+
    | getLastErrors() dapat mengembalikan false jika tidak ada error.
    |----------------------------------------------------------------------
    */

    if (
        $dateObject === false ||
        (
            is_array($dateErrors) &&
            (
                $dateErrors["warning_count"] > 0 ||
                $dateErrors["error_count"] > 0
            )
        ) ||
        $dateObject->format("Y-m-d") !== $publishedDate
    ) {

        http_response_code(422);

        response(
            false,
            "Invalid published date."
        );

    }

}


/*
|--------------------------------------------------------------------------
| VALIDATE STATUS
|--------------------------------------------------------------------------
*/

$allowedStatuses = [

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
        "Invalid status."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE CONTENT
|--------------------------------------------------------------------------
*/

if (
    $content === ""
) {

    http_response_code(422);

    response(
        false,
        "Article content is required."
    );

}


/*
|--------------------------------------------------------------------------
| DECODE CONTENT
|--------------------------------------------------------------------------
*/

$contentData =
    json_decode(
        $content,
        true
    );


if (
    !is_array(
        $contentData
    )
) {

    http_response_code(422);

    response(
        false,
        "Invalid article content."
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE ARTICLE BLOCKS
|--------------------------------------------------------------------------
*/

$hasParagraph = false;


foreach (
    $contentData
    as $index => $block
) {

    if (
        !is_array($block)
    ) {

        http_response_code(422);

        response(
            false,
            "Invalid article block."
        );

    }


    $blockType =
        isset(
        $block["type"]
    )
        ? trim(
            (string) $block["type"]
        )
        : "";


    /*
    |----------------------------------------------------------------------
    | PARAGRAPH
    |----------------------------------------------------------------------
    */

    if (
        $blockType === "paragraph"
    ) {

        $paragraphContent =
            isset(
            $block["content"]
        )
            ? trim(
                (string) $block["content"]
            )
            : "";


        if (
            $paragraphContent === ""
        ) {

            http_response_code(422);

            response(
                false,
                "Paragraph " .
                ($index + 1) .
                " is empty."
            );

        }


        $hasParagraph = true;

        continue;

    }


    /*
    |----------------------------------------------------------------------
    | IMAGE
    |----------------------------------------------------------------------
    */

    if (
        $blockType === "image"
    ) {

        $imageSrc =
            isset(
            $block["src"]
        )
            ? trim(
                (string) $block["src"]
            )
            : "";


        if (
            $imageSrc === ""
        ) {

            http_response_code(422);

            response(
                false,
                "Article image " .
                ($index + 1) .
                " does not have an image."
            );

        }


        /*
        |------------------------------------------------------------------
        | Normalize image path
        |------------------------------------------------------------------
        */

        $contentData[$index]["src"] =
            str_replace(
                "\\",
                "/",
                $imageSrc
            );


        continue;

    }


    /*
    |----------------------------------------------------------------------
    | UNKNOWN BLOCK
    |----------------------------------------------------------------------
    */

    http_response_code(422);

    response(
        false,
        "Invalid article block type."
    );

}


/*
|--------------------------------------------------------------------------
| REQUIRE AT LEAST ONE PARAGRAPH
|--------------------------------------------------------------------------
*/

if (
    !$hasParagraph
) {

    http_response_code(422);

    response(
        false,
        "Article must contain at least one paragraph."
    );

}


/*
|--------------------------------------------------------------------------
| RE-ENCODE CONTENT
|--------------------------------------------------------------------------
|
| Kita encode ulang agar normalisasi path image ikut tersimpan.
|
|--------------------------------------------------------------------------
*/

$content =
    json_encode(
        $contentData,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );


if (
    $content === false
) {

    http_response_code(422);

    response(
        false,
        "Failed to encode article content."
    );

}


/*
|--------------------------------------------------------------------------
| CHECK SLUG DUPLICATE
|--------------------------------------------------------------------------
|
| Slug boleh sama dengan dirinya sendiri.
|
|--------------------------------------------------------------------------
*/

$slugCheck =
    $conn->prepare(
        "
        SELECT id
        FROM press_releases
        WHERE slug = ?
        AND id != ?
        LIMIT 1
        "
    );


$slugCheck->bind_param(
    "si",
    $slug,
    $id
);


$slugCheck->execute();


$slugResult =
    $slugCheck->get_result();


if (
    $slugResult->num_rows > 0
) {

    $slugCheck->close();

    http_response_code(409);

    response(
        false,
        "A Press Release with this slug already exists."
    );

}


$slugCheck->close();


/*
|--------------------------------------------------------------------------
| UPDATE PRESS RELEASE
|--------------------------------------------------------------------------
*/

$sql = "

    UPDATE press_releases

    SET

        title = ?,

        slug = ?,

        description = ?,

        content = ?,

        cover_image = ?,

        category = ?,

        category_name = ?,

        published_date = ?,

        status = ?,

        meta_title = ?,

        meta_description = ?

    WHERE id = ?

    LIMIT 1

";


$stmt =
    $conn->prepare(
        $sql
    );


$stmt->bind_param(
    "sssssssssssi",
    $title,
    $slug,
    $description,
    $content,
    $coverImage,
    $category,
    $categoryName,
    $publishedDate,
    $status,
    $metaTitle,
    $metaDescription,
    $id
);


/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

$stmt->execute();


/*
|--------------------------------------------------------------------------
| CLOSE STATEMENT
|--------------------------------------------------------------------------
*/

$stmt->close();


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

response(

    true,

    "Press Release successfully updated.",

    [

        "id" =>
            $id,

        "press_release" =>
            [

                "id" =>
                    $id,

                "title" =>
                    $title,

                "slug" =>
                    $slug,

                "description" =>
                    $description,

                "content" =>
                    $content,

                "cover_image" =>
                    $coverImage,

                "category" =>
                    $category,

                "category_name" =>
                    $categoryName,

                "published_date" =>
                    $publishedDate !== ""
                    ? $publishedDate
                    : null,

                "status" =>
                    $status,

                "meta_title" =>
                    $metaTitle,

                "meta_description" =>
                    $metaDescription

            ]

    ]

);


/*
|--------------------------------------------------------------------------
| CLOSE DATABASE
|--------------------------------------------------------------------------
*/

$conn->close();