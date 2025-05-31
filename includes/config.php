<?php 

$host = "localhost:3308";
$user = "root";
$password = "";
$database = "loan_db";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

?>