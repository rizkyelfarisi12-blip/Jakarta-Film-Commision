<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| HELPER RESPONSE
|--------------------------------------------------------------------------
*/

function response($success, $message, $extra = [])
{
    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| READ JSON
|--------------------------------------------------------------------------
*/

$rawInput = file_get_contents("php://input");

$data = json_decode(
    $rawInput,
    true
);

if (!is_array($data)) {

    response(
        false,
        "Invalid JSON request."
    );

}


/*
|--------------------------------------------------------------------------
| HELPER VALUE
|--------------------------------------------------------------------------
*/

function value($data, $key, $default = "")
{
    return isset($data[$key])
        ? $data[$key]
        : $default;
}


/*
|--------------------------------------------------------------------------
| BASIC DATA
|--------------------------------------------------------------------------
*/

$title = trim(
    value($data, "title")
);

$slug = trim(
    value($data, "slug")
);

$category = trim(
    value($data, "category")
);


/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
|
| Others:
|
| category      = Others
| category_name = custom name
|
| Category lain:
|
| category_name = NULL
|
|--------------------------------------------------------------------------
*/

$category_name = null;

if ($category === "Others") {

    $category_name = trim(
        value($data, "category_name")
    );

}


/*
|--------------------------------------------------------------------------
| TITLE VALIDATION
|--------------------------------------------------------------------------
*/

if ($title === "") {

    response(
        false,
        "Title is required."
    );

}

if (mb_strlen($title) > 255) {

    response(
        false,
        "Title must not exceed 255 characters."
    );

}


/*
|--------------------------------------------------------------------------
| SLUG VALIDATION
|--------------------------------------------------------------------------
*/

if ($slug === "") {

    response(
        false,
        "Slug is required."
    );

}

if (mb_strlen($slug) > 255) {

    response(
        false,
        "Slug must not exceed 255 characters."
    );

}


/*
|--------------------------------------------------------------------------
| CATEGORY VALIDATION
|--------------------------------------------------------------------------
*/

$allowedCategories = [
    "Nonton Di",
    "Nonton Bareng",
    "Jakarta Film Lab",
    "Others"
];

if ($category === "") {

    response(
        false,
        "Category is required."
    );

}

if (!in_array(
    $category,
    $allowedCategories,
    true
)) {

    response(
        false,
        "Invalid category."
    );

}


/*
|--------------------------------------------------------------------------
| CUSTOM CATEGORY
|--------------------------------------------------------------------------
*/

if ($category === "Others") {

    if ($category_name === "") {

        response(
            false,
            "Custom category name is required for Others."
        );

    }

    if (mb_strlen($category_name) > 100) {

        response(
            false,
            "Category name must not exceed 100 characters."
        );

    }

} else {

    /*
    | Jangan menyimpan category_name
    | untuk category utama.
    */

    $category_name = null;

}


/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

$start_date = value(
    $data,
    "start_date"
);

$end_date = value(
    $data,
    "end_date"
);

if ($start_date === "") {
    $start_date = null;
}

if ($end_date === "") {
    $end_date = null;
}


/*
|--------------------------------------------------------------------------
| DATE VALIDATION
|--------------------------------------------------------------------------
*/

function isValidDate($date)
{
    if (!$date) {
        return false;
    }

    $d = DateTime::createFromFormat(
        "Y-m-d",
        $date
    );

    return
        $d &&
        $d->format("Y-m-d") === $date;
}


if (
    $start_date !== null &&
    !isValidDate($start_date)
) {

    response(
        false,
        "Invalid start date."
    );

}

if (
    $end_date !== null &&
    !isValidDate($end_date)
) {

    response(
        false,
        "Invalid end date."
    );

}

if (
    $start_date !== null &&
    $end_date !== null &&
    $end_date < $start_date
) {

    response(
        false,
        "End date cannot be earlier than start date."
    );

}


/*
|--------------------------------------------------------------------------
| TIME
|--------------------------------------------------------------------------
*/

$start_time = value(
    $data,
    "start_time"
);

$end_time = value(
    $data,
    "end_time"
);

if ($start_time === "") {
    $start_time = null;
}

if ($end_time === "") {
    $end_time = null;
}


/*
|--------------------------------------------------------------------------
| TIME VALIDATION
|--------------------------------------------------------------------------
*/

function isValidTime($time)
{
    if (!$time) {
        return false;
    }

    $formats = [
        "H:i",
        "H:i:s"
    ];

    foreach ($formats as $format) {

        $t = DateTime::createFromFormat(
            $format,
            $time
        );

        if (
            $t &&
            $t->format($format) === $time
        ) {

            return true;

        }

    }

    return false;
}


if (
    $start_time !== null &&
    !isValidTime($start_time)
) {

    response(
        false,
        "Invalid start time."
    );

}

if (
    $end_time !== null &&
    !isValidTime($end_time)
) {

    response(
        false,
        "Invalid end time."
    );

}


/*
|--------------------------------------------------------------------------
| LOCATION
|--------------------------------------------------------------------------
*/

$location = trim(
    value($data, "location")
);

$address = trim(
    value($data, "address")
);

if (mb_strlen($location) > 255) {

    response(
        false,
        "Location must not exceed 255 characters."
    );

}


/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/

$cover_image = trim(
    value($data, "cover_image")
);

if (mb_strlen($cover_image) > 255) {

    response(
        false,
        "Cover image path is too long."
    );

}


/*
|--------------------------------------------------------------------------
| DESCRIPTION
|--------------------------------------------------------------------------
*/

$description = trim(
    value($data, "description")
);


/*
|--------------------------------------------------------------------------
| ARTICLE CONTENT
|--------------------------------------------------------------------------
*/

$content = value(
    $data,
    "content",
    "[]"
);

$schedule = value(
    $data,
    "schedule",
    "[]"
);


/*
|--------------------------------------------------------------------------
| JSON VALIDATION
|--------------------------------------------------------------------------
*/

$contentDecoded = json_decode(
    $content,
    true
);

if (
    json_last_error() !== JSON_ERROR_NONE
) {

    response(
        false,
        "Invalid article content JSON."
    );

}

if (!is_array($contentDecoded)) {

    response(
        false,
        "Article content must be a JSON array."
    );

}


$scheduleDecoded = json_decode(
    $schedule,
    true
);

if (
    json_last_error() !== JSON_ERROR_NONE
) {

    response(
        false,
        "Invalid schedule JSON."
    );

}

if (!is_array($scheduleDecoded)) {

    response(
        false,
        "Schedule must be a JSON array."
    );

}


/*
|--------------------------------------------------------------------------
| GOOGLE MAP
|--------------------------------------------------------------------------
*/

$map_url = trim(
    value($data, "map_url")
);


/*
|--------------------------------------------------------------------------
| TIMEZONE
|--------------------------------------------------------------------------
*/

$timezone = trim(
    value(
        $data,
        "timezone",
        "Asia/Jakarta"
    )
);

$timezoneList = DateTimeZone::listIdentifiers();

if (!in_array(
    $timezone,
    $timezoneList,
    true
)) {

    response(
        false,
        "Invalid timezone."
    );

}


/*
|--------------------------------------------------------------------------
| FEATURED
|--------------------------------------------------------------------------
*/

$featured = isset($data["featured"])
    ? (int)$data["featured"]
    : 0;

if (
    $featured !== 0 &&
    $featured !== 1
) {

    response(
        false,
        "Featured must be 0 or 1."
    );

}


/*
|--------------------------------------------------------------------------
| FEATURED DATES
|--------------------------------------------------------------------------
*/

$featured_start = value(
    $data,
    "featured_start"
);

$featured_until = value(
    $data,
    "featured_until"
);

if ($featured_start === "") {
    $featured_start = null;
}

if ($featured_until === "") {
    $featured_until = null;
}


/*
|--------------------------------------------------------------------------
| FEATURED VALIDATION
|--------------------------------------------------------------------------
*/

if ($featured === 1) {

    if ($featured_start === null) {

        response(
            false,
            "Featured start date is required when event is featured."
        );

    }

    if ($featured_until === null) {

        response(
            false,
            "Featured until date is required when event is featured."
        );

    }

}

if (
    $featured_start !== null &&
    !isValidDate($featured_start)
) {

    response(
        false,
        "Invalid featured start date."
    );

}

if (
    $featured_until !== null &&
    !isValidDate($featured_until)
) {

    response(
        false,
        "Invalid featured until date."
    );

}

if (
    $featured_start !== null &&
    $featured_until !== null &&
    $featured_until < $featured_start
) {

    response(
        false,
        "Featured until date cannot be earlier than featured start date."
    );

}


/*
|--------------------------------------------------------------------------
| IF NOT FEATURED
|--------------------------------------------------------------------------
*/

if ($featured === 0) {

    $featured_start = null;
    $featured_until = null;

}


/*
|--------------------------------------------------------------------------
| SEO
|--------------------------------------------------------------------------
*/

$meta_title = trim(
    value($data, "meta_title")
);

$meta_description = trim(
    value($data, "meta_description")
);

if (mb_strlen($meta_title) > 255) {

    response(
        false,
        "Meta title must not exceed 255 characters."
    );

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

$status = trim(
    value(
        $data,
        "status",
        "draft"
    )
);

$allowedStatuses = [
    "draft",
    "published"
];

if (!in_array(
    $status,
    $allowedStatuses,
    true
)) {

    response(
        false,
        "Invalid status. Allowed values are draft or published."
    );

}


/*
|--------------------------------------------------------------------------
| CHECK SLUG DUPLICATE
|--------------------------------------------------------------------------
*/

$checkSlug = $conn->prepare("
    SELECT id
    FROM events
    WHERE slug = ?
    LIMIT 1
");

if (!$checkSlug) {

    response(
        false,
        "Failed to prepare slug validation.",
        [
            "error" => $conn->error
        ]
    );

}

$checkSlug->bind_param(
    "s",
    $slug
);

$checkSlug->execute();

$checkResult = $checkSlug->get_result();

if ($checkResult->num_rows > 0) {

    $checkSlug->close();

    response(
        false,
        "Slug already exists. Please use another slug."
    );

}

$checkSlug->close();


/*
|--------------------------------------------------------------------------
| TRANSACTION
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();


try {


    /*
    |--------------------------------------------------------------------------
    | ONLY ONE ACTIVE FEATURED EVENT
    |--------------------------------------------------------------------------
    */

    if ($featured === 1) {

        $reset = $conn->prepare("
            UPDATE events
            SET
                featured = 0,
                featured_start = NULL,
                featured_until = NULL
            WHERE featured = 1
        ");

        if (!$reset) {

            throw new Exception(
                "Failed to prepare featured reset."
            );

        }

        if (!$reset->execute()) {

            throw new Exception(
                "Failed to reset existing featured event."
            );

        }

        $reset->close();

    }


    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    */

    $sql = "

        INSERT INTO events (

            title,
            slug,

            category,
            category_name,

            start_date,
            end_date,

            start_time,
            end_time,

            timezone,

            location,
            address,

            cover_image,

            description,

            content,
            schedule,

            map_url,

            featured,
            featured_start,
            featured_until,

            meta_title,
            meta_description,

            status

        )

        VALUES (

            ?, ?,
            ?, ?,
            ?, ?,
            ?, ?,
            ?,
            ?, ?,
            ?,
            ?,
            ?, ?,
            ?,
            ?, ?, ?,
            ?, ?,
            ?

        )

    ";


    $stmt = $conn->prepare(
        $sql
    );

    if (!$stmt) {

        throw new Exception(
            "Failed to prepare insert statement: " .
            $conn->error
        );

    }


    $stmt->bind_param(

        "ssssssssssssssssisssss",

        $title,
        $slug,

        $category,
        $category_name,

        $start_date,
        $end_date,

        $start_time,
        $end_time,

        $timezone,

        $location,
        $address,

        $cover_image,

        $description,

        $content,
        $schedule,

        $map_url,

        $featured,
        $featured_start,
        $featured_until,

        $meta_title,
        $meta_description,

        $status

    );


    if (!$stmt->execute()) {

        throw new Exception(
            "Failed to create event: " .
            $stmt->error
        );

    }


    $newId = $conn->insert_id;

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    response(
        true,
        "Event successfully created.",
        [
            "id" => $newId,
            "status" => $status,
            "featured" => $featured,
            "timezone" => $timezone,
            "category" => $category,
            "category_name" => $category_name
        ]
    );


} catch (Throwable $error) {

    $conn->rollback();

    response(
        false,
        "Failed to create event.",
        [
            "error" => $error->getMessage()
        ]
    );

}


$conn->close();