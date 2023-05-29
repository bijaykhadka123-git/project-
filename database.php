<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "project";

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

return $mysqli;
?>