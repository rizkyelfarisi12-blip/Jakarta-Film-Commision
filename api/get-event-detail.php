<?php

include "db.php";

$slug = $_GET['slug'];

$sql = "
SELECT *
FROM events
WHERE slug = '$slug'
LIMIT 1
";

$result =
mysqli_query($conn,$sql);

$data =
mysqli_fetch_assoc($result);

header('Content-Type: application/json');

echo json_encode($data);