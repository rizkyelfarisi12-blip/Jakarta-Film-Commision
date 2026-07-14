<?php

include "db.php";

$sql = "
SELECT *
FROM events
WHERE event_date >= CURDATE()
ORDER BY event_date ASC
LIMIT 1
";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result) > 0){

    echo json_encode([
        "status" => "success",
        "event" => mysqli_fetch_assoc($result)
    ]);

}else{

    echo json_encode([
        "status" => "empty"
    ]);

}