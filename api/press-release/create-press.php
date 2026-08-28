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
    array $data = [],
    int $httpCode = 200
): void {

    http_response_code($httpCode);

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
    ($_SERVER["REQUEST_METHOD"] ?? "") !== "POST"
) {

    response(
        false,
        "Invalid request method.",
        [],
        405
    );

}


/*
|--------------------------------------------------------------------------
| READ JSON BODY
|--------------------------------------------------------------------------
*/

$rawInput =
    file_get_contents(
        "php://input"
    );


if (
    $rawInput === false ||
    trim($rawInput) === ""
) {

    response(
        false,
        "Request body is empty.",
        [],
        400
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

    response(
        false,
        "Invalid JSON request.",
        [],
        400
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


$location =
    getInput(
        $data,
        "location",
        "Jakarta"
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
| LOCATION DEFAULT
|--------------------------------------------------------------------------
*/

if (
    $location === ""
) {

    $location = "Jakarta";

}


/*
|--------------------------------------------------------------------------
| VALIDATE TITLE
|--------------------------------------------------------------------------
*/

if (
    $title === ""
) {

    response(
        false,
        "Title is required.",
        [],
        422
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

    response(
        false,
        "Slug is required.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| NORMALIZE SLUG
|--------------------------------------------------------------------------
*/

$slug =
    strtolower(
        $slug
    );


/*
|--------------------------------------------------------------------------
| SLUG FORMAT
|--------------------------------------------------------------------------
|
| Allowed:
|
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

    response(
        false,
        "Invalid slug format.",
        [],
        422
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

    response(
        false,
        "Short description is required.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE COVER IMAGE
|--------------------------------------------------------------------------
*/

if (
    $coverImage === ""
) {

    response(
        false,
        "Cover image is required.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| NORMALIZE COVER IMAGE PATH
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
| REMOVE ROOT URL IF SENT
|--------------------------------------------------------------------------
|
| Accept:
|
| /jfc/uploads/press-release/file.webp
|
| or:
|
| uploads/press-release/file.webp
|
|--------------------------------------------------------------------------
*/

$rootPrefix =
    "/jfc/";


if (
    strpos(
        $coverImage,
        $rootPrefix
    ) === 0
) {

    $coverImage =
        substr(
            $coverImage,
            strlen($rootPrefix)
        );

}


/*
|--------------------------------------------------------------------------
| REMOVE LEADING SLASH
|--------------------------------------------------------------------------
*/

$coverImage =
    ltrim(
        $coverImage,
        "/"
    );


/*
|--------------------------------------------------------------------------
| VALIDATE COVER IMAGE PATH
|--------------------------------------------------------------------------
*/

if (
    strpos(
        $coverImage,
        "uploads/press-release/"
    ) !== 0
) {

    response(
        false,
        "Invalid cover image path.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| PREVENT PATH TRAVERSAL
|--------------------------------------------------------------------------
*/

if (
    strpos(
        $coverImage,
        ".."
    ) !== false
) {

    response(
        false,
        "Invalid cover image path.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE CATEGORY
|--------------------------------------------------------------------------
|
| Harus sama persis dengan value <option> di form HTML
| (press-release-form.php / create.php / edit.php).
|
|--------------------------------------------------------------------------
*/

$allowedCategories = [

    "Official Release",

    "Program Update",

    "Industry News",

    "Others"

];


if (
    !in_array(
        $category,
        $allowedCategories,
        true
    )
) {

    response(
        false,
        "Invalid category.",
        [],
        422
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

        response(
            false,
            "Please enter the custom category name.",
            [],
            422
        );

    }

} else {

    /*
    |----------------------------------------------------------------------
    | NORMAL CATEGORY
    |----------------------------------------------------------------------
    */

    $categoryName = "";

}


/*
|--------------------------------------------------------------------------
| VALIDATE LOCATION
|--------------------------------------------------------------------------
*/

if (
    mb_strlen(
        $location
    ) > 255
) {

    response(
        false,
        "Location must not exceed 255 characters.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| VALIDATE PUBLISHED DATE
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
    | PHP 8.2+ COMPATIBILITY
    |----------------------------------------------------------------------
    */

    $hasDateErrors =
        is_array(
            $dateErrors
        ) &&
        (
            ($dateErrors["warning_count"] ?? 0) > 0 ||
            ($dateErrors["error_count"] ?? 0) > 0
        );


    if (
        $dateObject === false ||
        $hasDateErrors ||
        $dateObject->format("Y-m-d") !== $publishedDate
    ) {

        response(
            false,
            "Invalid published date.",
            [],
            422
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

    response(
        false,
        "Invalid status.",
        [],
        422
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

    response(
        false,
        "Article content is required.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| DECODE ARTICLE CONTENT
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

    response(
        false,
        "Invalid article content.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| ARTICLE CONTENT MUST NOT BE EMPTY
|--------------------------------------------------------------------------
*/

if (
    count(
        $contentData
    ) === 0
) {

    response(
        false,
        "Article must contain at least one block.",
        [],
        422
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

    /*
    |--------------------------------------------------------------------------
    | BLOCK MUST BE ARRAY
    |--------------------------------------------------------------------------
    */

    if (
        !is_array(
            $block
        )
    ) {

        response(
            false,
            "Invalid article block at position " .
            ($index + 1) .
            ".",
            [],
            422
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BLOCK TYPE
    |--------------------------------------------------------------------------
    */

    $blockType =
        isset(
        $block["type"]
    )
        ? trim(
            (string) $block["type"]
        )
        : "";


    /*
    |--------------------------------------------------------------------------
    | PARAGRAPH
    |--------------------------------------------------------------------------
    */

    if (
        $blockType === "paragraph"
    ) {

        $paragraphContent =
            isset(
            $block["content"]
        )
            ? (string) $block["content"]
            : "";


        /*
        |----------------------------------------------------------------------
        | STRIP HTML ONLY FOR EMPTY CHECK
        |----------------------------------------------------------------------
        |
        | Rich text boleh mengandung:
        |
        | <strong>
        | <em>
        | <u>
        | <a>
        |
        | Jadi kita tidak boleh menghapus HTML dari content
        | sebelum disimpan.
        |
        |----------------------------------------------------------------------
        */

        $plainText =
            trim(
                html_entity_decode(
                    strip_tags(
                        $paragraphContent
                    ),
                    ENT_QUOTES |
                    ENT_HTML5,
                    "UTF-8"
                )
            );


        if (
            $plainText === ""
        ) {

            response(
                false,
                "Paragraph " .
                ($index + 1) .
                " is empty.",
                [],
                422
            );

        }


        $hasParagraph = true;


        continue;

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
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

            response(
                false,
                "Article image " .
                ($index + 1) .
                " does not have an image.",
                [],
                422
            );

        }


        /*
        |----------------------------------------------------------------------
        | NORMALIZE ARTICLE IMAGE PATH
        |----------------------------------------------------------------------
        */

        $imageSrc =
            str_replace(
                "\\",
                "/",
                $imageSrc
            );


        /*
        |----------------------------------------------------------------------
        | REMOVE ROOT URL
        |----------------------------------------------------------------------
        */

        if (
            strpos(
                $imageSrc,
                $rootPrefix
            ) === 0
        ) {

            $imageSrc =
                substr(
                    $imageSrc,
                    strlen($rootPrefix)
                );

        }


        $imageSrc =
            ltrim(
                $imageSrc,
                "/"
            );


        /*
        |----------------------------------------------------------------------
        | VALIDATE ARTICLE IMAGE PATH
        |----------------------------------------------------------------------
        */

        if (
            strpos(
                $imageSrc,
                "uploads/press-release/"
            ) !== 0
        ) {

            response(
                false,
                "Invalid article image path.",
                [],
                422
            );

        }


        /*
        |----------------------------------------------------------------------
        | PATH TRAVERSAL PROTECTION
        |----------------------------------------------------------------------
        */

        if (
            strpos(
                $imageSrc,
                ".."
            ) !== false
        ) {

            response(
                false,
                "Invalid article image path.",
                [],
                422
            );

        }


        /*
        |----------------------------------------------------------------------
        | SAVE NORMALIZED PATH
        |----------------------------------------------------------------------
        */

        $contentData[$index]["src"] =
            $imageSrc;


        continue;

    }


    /*
    |--------------------------------------------------------------------------
    | UNKNOWN BLOCK TYPE
    |--------------------------------------------------------------------------
    */

    response(
        false,
        "Invalid article block type at position " .
        ($index + 1) .
        ".",
        [],
        422
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

    response(
        false,
        "Article must contain at least one paragraph.",
        [],
        422
    );

}


/*
|--------------------------------------------------------------------------
| RE-ENCODE CONTENT
|--------------------------------------------------------------------------
|
| Ini penting karena path image mungkin sudah dinormalisasi.
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

    response(
        false,
        "Failed to encode article content.",
        [],
        500
    );

}


/*
|--------------------------------------------------------------------------
| CHECK SLUG DUPLICATE
|--------------------------------------------------------------------------
*/

$slugCheck =
    $conn->prepare(
        "
        SELECT id
        FROM press_releases
        WHERE slug = ?
        LIMIT 1
        "
    );


$slugCheck->bind_param(
    "s",
    $slug
);


$slugCheck->execute();


$slugResult =
    $slugCheck->get_result();


if (
    $slugResult->num_rows > 0
) {

    $slugCheck->close();


    response(
        false,
        "A Press Release with this slug already exists.",
        [],
        409
    );

}


$slugCheck->close();


/*
|--------------------------------------------------------------------------
| INSERT PRESS RELEASE
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| id tidak dimasukkan.
|
| AUTO_INCREMENT database yang membuat ID.
|
|--------------------------------------------------------------------------
*/

$sql = "

    INSERT INTO press_releases (

        title,

        slug,

        description,

        content,

        cover_image,

        category,

        category_name,

        location,

        published_date,

        status,

        meta_title,

        meta_description

    )

    VALUES (

        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?

    )

";


$stmt =
    $conn->prepare(
        $sql
    );


$stmt->bind_param(
    "ssssssssssss",

    $title,

    $slug,

    $description,

    $content,

    $coverImage,

    $category,

    $categoryName,

    $location,

    $publishedDate,

    $status,

    $metaTitle,

    $metaDescription

);


/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

$stmt->execute();


/*
|--------------------------------------------------------------------------
| GET AUTO GENERATED ID
|--------------------------------------------------------------------------
*/

$newId =
    (int) $conn->insert_id;


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

    "Press Release successfully created.",

    [

        "id" =>
            $newId,

        "press_release" => [

            "id" =>
                $newId,

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

            "location" =>
                $location,

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

    ],

    201

);


/*
|--------------------------------------------------------------------------
| CLOSE DATABASE
|--------------------------------------------------------------------------
*/

$conn->close();