<?php
$server = "localhost";
$user = "root";
$pass = "";
$dbname = "ewu_cafe";
$conn = new mysqli($server,$user,$pass,$dbname);
if(!$conn){
    echo "Oopps! : {$conn->connect_error}";
}
?>
