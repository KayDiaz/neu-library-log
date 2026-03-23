<?php

$host = "sql113.infinityfree.com";
$user = "if0_41424642";
$password = "EastyBeasty0406";
$database = "if0_41424642_if0_41424642_neu_library";

$conn = mysqli_connect("localhost", "USER_ID", "DATABASE_PASSWORD", "DATABASE_NAME");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>