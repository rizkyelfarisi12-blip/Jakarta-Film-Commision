<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../db.php";


/*
|--------------------------------------------------------------------------
| RESPONSE
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

$data = json_decode(
    file_get_contents("php://input"),
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
| ID
|--------------------------------------------------------------------------
*/

$id = isset($data["id"])
    ? (int)$data["id"]
    : 0;

if ($id <= 0) {

    response(
        false,
        "Invalid event ID."
    );

}


/*
|--------------------------------------------------------------------------
| CHECK EVENT EXISTS
|--------------------------------------------------------------------------
*/

$checkEvent = $conn->prepare("
    SELECT id
    FROM events
    WHERE id = ?
    LIMIT 1
");

$checkEvent->bind_param(
    "i",
    $id
);

$checkEvent->execute();

$eventResult =
    $checkEvent->get_result();

if ($eventResult->num_rows === 0) {

    $checkEvent->close();

    response(
        false,
        "Event not found."
    );

}

$checkEvent->close();


/*
|--------------------------------------------------------------------------
| BASIC DATA
|--------------------------------------------------------------------------
*/

$title = trim(
    $data["title"] ?? ""
);

$slug = trim(
    $data["slug"] ?? ""
);

$category = trim(
    $data["category"] ?? ""
);


/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

$category_name = null;

if ($category === "Others") {

    $category_name =
        trim(
            $data["category_name"] ?? ""
        );

}


/*
|--------------------------------------------------------------------------
| BASIC VALIDATION
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

    $category_name = null;

}


/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

$start_date =
    !empty($data["start_date"])
    ? $data["start_date"]
    : null;

$end_date =
    !empty($data["end_date"])
    ? $data["end_date"]
    : null;


function isValidDate($date)
{
    if (!$date) {
        return false;
    }

    $d =
        DateTime::createFromFormat(
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

$start_time =
    !empty($data["start_time"])
    ? $data["start_time"]
    : null;

$end_time =
    !empty($data["end_time"])
    ? $data["end_time"]
    : null;


function isValidTime($time)
{
    if (!$time) {
        return false;
    }

    foreach (
        ["H:i", "H:i:s"]
        as $format
    ) {

        $t =
            DateTime::createFromFormat(
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

$location =
    trim($data["location"] ?? "");

$address =
    trim($data["address"] ?? "");

if (mb_strlen($location) > 255) {

    response(
        false,
        "Location must not exceed 255 characters."
    );

}


/*
|--------------------------------------------------------------------------
| CONTENT
|--------------------------------------------------------------------------
*/

$cover_image =
    trim($data["cover_image"] ?? "");

$description =
    trim($data["description"] ?? "");

$content =
    $data["content"] ?? "[]";

$schedule =
    $data["schedule"] ?? "[]";

$map_url =
    trim($data["map_url"] ?? "");


/*
|--------------------------------------------------------------------------
| JSON VALIDATION
|--------------------------------------------------------------------------
*/

$contentDecoded =
    json_decode(
        $content,
        true
    );

if (
    json_last_error() !== JSON_ERROR_NONE ||
    !is_array($contentDecoded)
) {

    response(
        false,
        "Invalid article content JSON."
    );

}


$scheduleDecoded =
    json_decode(
        $schedule,
        true
    );

if (
    json_last_error() !== JSON_ERROR_NONE ||
    !is_array($scheduleDecoded)
) {

    response(
        false,
        "Invalid schedule JSON."
    );

}


/*
|--------------------------------------------------------------------------
| TIMEZONE
|--------------------------------------------------------------------------
*/

$timezone =
    trim(
        $data["timezone"] ??
        "Asia/Jakarta"
    );

if (!in_array(
    $timezone,
    DateTimeZone::listIdentifiers(),
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

$featured =
    isset($data["featured"])
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

$featured_start =
    !empty($data["featured_start"])
    ? $data["featured_start"]
    : null;

$featured_until =
    !empty($data["featured_until"])
    ? $data["featured_until"]
    : null;


if ($featured === 1) {

    if (
        $featured_start === null ||
        $featured_until === null
    ) {

        response(
            false,
            "Featured start and until dates are required."
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
| CLEAR FEATURED DATES
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

$meta_title =
    trim($data["meta_title"] ?? "");

$meta_description =
    trim($data["meta_description"] ?? "");

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

$status =
    !empty($data["status"])
    ? trim($data["status"])
    : "published";

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
        "Invalid status."
    );

}


/*
|--------------------------------------------------------------------------
| CHECK SLUG DUPLICATE
|--------------------------------------------------------------------------
*/

$checkSlug =
    $conn->prepare("
        SELECT id
        FROM events
        WHERE slug = ?
        AND id != ?
        LIMIT 1
    ");

$checkSlug->bind_param(
    "si",
    $slug,
    $id
);

$checkSlug->execute();

$slugResult =
    $checkSlug->get_result();

if ($slugResult->num_rows > 0) {

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
    | RESET OTHER FEATURED EVENT
    |--------------------------------------------------------------------------
    */

    if ($featured === 1) {

        $reset =
            $conn->prepare("
                UPDATE events
                SET
                    featured = 0,
                    featured_start = NULL,
                    featured_until = NULL
                WHERE featured = 1
                AND id != ?
            ");

        $reset->bind_param(
            "i",
            $id
        );

        if (!$reset->execute()) {

            throw new Exception(
                "Failed to reset featured event."
            );

        }

        $reset->close();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    $sql = "

        UPDATE events SET

            title = ?,
            slug = ?,

            category = ?,
            category_name = ?,

            start_date = ?,
            end_date = ?,

            start_time = ?,
            end_time = ?,

            timezone = ?,

            location = ?,
            address = ?,

            cover_image = ?,

            description = ?,

            content = ?,
            schedule = ?,

            map_url = ?,

            featured = ?,
            featured_start = ?,
            featured_until = ?,

            meta_title = ?,
            meta_description = ?,

            status = ?,

            updated_at = NOW()

        WHERE id = ?

    ";


    $stmt =
        $conn->prepare($sql);

    if (!$stmt) {

        throw new Exception(
            "Prepare failed: " .
            $conn->error
        );

    }


    $stmt->bind_param(

        "ssssssssssssssssisssssi",

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

        $status,

        $id

    );


    if (!$stmt->execute()) {

        throw new Exception(
            "Update failed: " .
            $stmt->error
        );

    }


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    response(
        true,
        "Event updated successfully.",
        [
            "id" => $id,
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
        "Failed to update event.",
        [
            "error" => $error->getMessage()
        ]
    );

}


$conn->close();