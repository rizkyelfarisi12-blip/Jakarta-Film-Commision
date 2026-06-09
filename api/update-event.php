<?php

include "db.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = (int)$data['id'];

$title = $data['title'];
$slug = generateSlug($title);
$category = $data['category'];
$event_date = $data['event_date'];
$event_time = $data['event_time'];
$location = $data['location'];
$address = $data['address'];
$image = $data['image'];
$description = $data['description'];
$content = $data['content'];
$schedule = $data['schedule'];
$map_url = $data['map_url'];

$stmt = $conn->prepare("
UPDATE events SET

title=?,
slug=?,
category=?,
event_date=?,
event_time=?,
location=?,
address=?,
image=?,
description=?,
content=?,
schedule=?,
map_url=?

WHERE id=?
");

$stmt->bind_param(
    "ssssssssssssi",
    $title,
    $slug,
    $category,
    $event_date,
    $event_time,
    $location,
    $address,
    $image,
    $description,
    $content,
    $schedule,
    $map_url,
    $id
);

if($stmt->execute()){

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "error"=>$stmt->error
    ]);

}

function generateSlug($text){

    $text = strtolower($text);

    $text = preg_replace(
        '/[^a-z0-9\s-]/',
        '',
        $text
    );

    $text = preg_replace(
        '/[\s-]+/',
        '-',
        $text
    );

    return trim($text,'-');
}