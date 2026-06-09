<?php

include "db.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data['id'];

$sql = "DELETE FROM events WHERE id = $id";

if(mysqli_query($conn,$sql)){
    echo "deleted";
}else{
    echo mysqli_error($conn);
}