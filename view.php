<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "project";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_errno) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM pdf WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filename = $row['filename'];
        $filepath = "uploads/" . $filename;

        if (file_exists($filepath)) {
            // Display the PDF file in an iframe
            echo "<iframe src='" . $filepath . "' width='100%' height='600px'></iframe>";
        } else {
            echo "File not found.";
        }
    } else {
        echo "Invalid PDF ID.";
    }
} else {
    echo "PDF ID not provided.";
}

$conn->close();
?>