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
            // Delete the file from the server
            unlink($filepath);

            // Delete the record from the database
            $deleteSql = "DELETE FROM pdf WHERE id = '$id'";
            if ($conn->query($deleteSql)) {
                echo '<script>alert("File deleted successfully.");</script>';
            } else {
                echo "Error deleting file: " . $conn->error;
            }
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