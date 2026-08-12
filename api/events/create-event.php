<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../db.php";

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

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON request."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| HELPER
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
$title = value($data, "title");

$slug = value($data, "slug");


$category =
    value($data, "category");


$category_name =
    $category === "Others"
        ? trim(value($data, "category_name"))
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
    value($data, "start_date");

$end_date =
    value($data, "end_date");


$start_time =
    value($data, "start_time");

$end_time =
    value($data, "end_time");


$location =
    value($data, "location");

$address =
    value($data, "address");


$cover_image =
    value($data, "cover_image");


$description =
    value($data, "description");

$content =
    value($data, "content");

$schedule =
    value($data, "schedule");


$map_url =
    value($data, "map_url");


/*
|--------------------------------------------------------------------------
| FEATURED
|--------------------------------------------------------------------------
*/

$featured =
    isset($data["featured"])
        ? (int)$data["featured"]
        : 0;


$featured_start =
    value($data, "featured_start");

$featured_until =
    value($data, "featured_until");


if ($featured_start === "") {
    $featured_start = null;
}

if ($featured_until === "") {
    $featured_until = null;
}


/*
|--------------------------------------------------------------------------
| META
|--------------------------------------------------------------------------
*/

$meta_title =
    value($data, "meta_title");

$meta_description =
    value($data, "meta_description");


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

$status = "published";


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
    ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?,
    ?
)

";


$stmt = $conn->prepare($sql);


/*
|--------------------------------------------------------------------------
| PREPARE ERROR
|--------------------------------------------------------------------------
*/

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare SQL statement.",
        "error" => $conn->error
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| BIND
|--------------------------------------------------------------------------
|
| 14 STRING
| 1 INTEGER
| 5 STRING
|
| TOTAL = 20
|
|--------------------------------------------------------------------------
*/
$bindResult = $stmt->bind_param(

    "sssssssssssssssisssss",

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

    $status

);


/*
|--------------------------------------------------------------------------
| BIND ERROR
|--------------------------------------------------------------------------
*/

if (!$bindResult) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to bind parameters.",
        "error" => $stmt->error
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| EXECUTE
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to create event.",
        "error" => $stmt->error
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| SUCCESS
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "message" => "Event successfully created.",

    "id" => $conn->insert_id

]);
