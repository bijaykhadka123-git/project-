<?php
session_start();

// Check if the user clicked the logout link
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_destroy();

    // Redirect the admin to the login page
    header("Location:auth/index.html");
    exit();
}

// Check if the user is not logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    // Redirect the user to the login page
    header("Location:login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        color: #d32f2f;
    }

    form {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    input[type="file"] {
        display: block;
        margin-bottom: 10px;
    }

    input[type="submit"],
    button {
        padding: 10px 20px;
        background-color: #4caf50;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button {
        background-color: #f44336;
        margin-left: 10px;
    }

    button.btn {
        background-color: #f44336;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button.btn1 {
        background-color: #f44336;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button.btn:hover {
        background-color: #d32f2f;
    }
    </style>
</head>

<body>
    <!-- Dashboard content -->
    <?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "project";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_errno) {
        die("Failed to connect to MySQL: " . $conn->connect_error);
    }

    // Handle file upload
    if (isset($_FILES['pdfFile'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["pdfFile"]["name"]);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check file size is less than 1GB
        if ($fileType != "pdf" || $_FILES["pdfFile"]["size"] > 1000000000) {
            echo "Error: Only files less than 1GB can be uploaded.";
        } else {
            // Generate a unique file name using a timestamp
            $timestamp = time();
            $filename = $timestamp . '_' . $_FILES["pdfFile"]["name"];
            $targetFilePath = $targetDir . $filename;

            // Move uploaded file into upload folder
            if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $targetFilePath)) {
                $folder_path = $targetDir;
                $time_stamp = date('Y-m-d H:i:s');

                // Retrieve keywords from the form data
                $keywords = $_POST["keywords"];
                $title = $_POST["title"];

                // Insert the filename, folder path, timestamp, and keywords into the database
                $sql = "INSERT INTO pdf (filename, folder_path, time_stamp, keywords, title) VALUES ('$filename', '$folder_path', '$time_stamp', '$keywords', '$title')";
                if ($conn->query($sql) === TRUE) {
                    echo '<script>alert("File uploaded successfully.");</script>';
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error uploading file.";
            }
        }
    }
    // Close database connection
    $conn->close();
    ?>

    <div class="container">
        <h1>Upload PDF file here</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="pdfFile">Choose a PDF file:</label>
            <input type="file" id="pdfFile" name="pdfFile" required>

            <!-- Add input field or textarea for keywords -->
            <label for="keywords">Keywords:</label>
            <input type="text" id="keywords" name="keywords" placeholder="Enter keywords separated by commas" required>
            <br>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <br><br>

            <input type="submit" name="submit" value="Upload">
            <button class="btn" name="btn">Reset</button>
            <button class="btn1"><a href="admin_dashboard.php">More</a></button>
        </form>
    </div>

    <p><button><a href="?action=logout">Logout</a></button></p>
</body>

</html>