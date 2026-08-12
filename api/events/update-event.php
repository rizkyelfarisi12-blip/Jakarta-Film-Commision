<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";


$data = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$data) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON"
    ]);

    exit;
}


/* =========================================================
   ID
========================================================= */

$id = isset($data["id"])
    ? (int)$data["id"]
    : 0;


if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid event ID"
    ]);

    exit;
}


/* =========================================================
   BASIC DATA
========================================================= */

$title = trim($data["title"] ?? "");

$slug = trim($data["slug"] ?? "");
$category =
    trim($data["category"] ?? "");


$category_name =
    $category === "Others"
        ? trim($data["category_name"] ?? "")
        : null;


/*
|--------------------------------------------------------------------------
| CATEGORY VALIDATION
|--------------------------------------------------------------------------
*/

if ($category === "") {

    echo json_encode([
        "success" => false,
        "message" => "Category is required."
    ]);

    exit;
}


if (
    $category === "Others" &&
    $category_name === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Custom category name is required for Others."
    ]);

    exit;
}


$start_date =
    !empty($data["start_date"])
    ? $data["start_date"]
    : null;


$end_date =
    !empty($data["end_date"])
    ? $data["end_date"]
    : null;


$start_time =
    !empty($data["start_time"])
    ? $data["start_time"]
    : null;


$end_time =
    !empty($data["end_time"])
    ? $data["end_time"]
    : null;


$location =
    trim($data["location"] ?? "");


$address =
    trim($data["address"] ?? "");


/* =========================================================
   CONTENT
========================================================= */

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


/* =========================================================
   FEATURED
========================================================= */

$featured =
    isset($data["featured"])
    ? (int)$data["featured"]
    : 0;


$featured_start =
    !empty($data["featured_start"])
    ? $data["featured_start"]
    : null;


$featured_until =
    !empty($data["featured_until"])
    ? $data["featured_until"]
    : null;


/* =========================================================
   SEO
========================================================= */

$meta_title =
    trim($data["meta_title"] ?? "");


$meta_description =
    trim($data["meta_description"] ?? "");


/* =========================================================
   STATUS
========================================================= */

$status =
    !empty($data["status"])
    ? $data["status"]
    : "published";


/* =========================================================
   SQL
========================================================= */

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

/* =========================================================
   ONLY ONE ACTIVE FEATURED EVENT
========================================================= */

if ($featured == 1) {

    $reset = $conn->prepare("

        UPDATE events

        SET
            featured = 0,
            featured_start = NULL,
            featured_until = NULL

        WHERE id != ?

    ");

    $reset->bind_param(
        "i",
        $id
    );

    $reset->execute();

    $reset->close();
}

$stmt = $conn->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);

    exit;
}


/* =========================================================
   BIND PARAMETER

   14 STRING
   1 INTEGER
   5 STRING
   1 INTEGER
========================================================= */
$stmt->bind_param(

    "sssssssssssssssisssssi",

    $title,
    $slug,
    $category,
    $category_name,

    $start_date,
    $end_date,

    $start_time,
    $end_time,

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


/* =========================================================
   EXECUTE
========================================================= */

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Event updated successfully",
        "id" => $id
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Update failed: " . $stmt->error
    ]);

}


$stmt->close();

$conn->close();

?>