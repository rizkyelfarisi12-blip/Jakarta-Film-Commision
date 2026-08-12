<?php

require_once __DIR__ . "/../db.php";

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data['id'];

$stmt = $conn->prepare("
DELETE FROM events
WHERE id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();

if($stmt->affected_rows){

    echo json_encode([
        "success"=>true
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>"Event not found"
    ]);

}

$stmt->close();
$conn->close();
// if(mysqli_query($conn,$sql)){
//     echo "deleted";
// }else{
//     echo mysqli_error($conn);
// }