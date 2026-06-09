<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "jfc"
);

if(!$conn){
    die(mysqli_connect_error());
}