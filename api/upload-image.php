<?php

if(!isset($_FILES['image'])){
    die(json_encode([
        "success"=>false,
        "message"=>"No file"
    ]));
}

$targetDir = "../uploads/events/";

if(!file_exists($targetDir)){
    mkdir($targetDir,0777,true);
}

$filename =
time() . "-" .
basename($_FILES["image"]["name"]);

$targetFile =
$targetDir . $filename;

if(move_uploaded_file(
    $_FILES["image"]["tmp_name"],
    $targetFile
)){

    echo json_encode([
        "success"=>true,
        "path"=>"uploads/events/".$filename
    ]);

}else{

    echo json_encode([
        "success"=>false
    ]);

}