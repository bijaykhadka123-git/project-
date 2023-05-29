<?php
session_start();

// Check if the user clicked the logout link
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_destroy();

    // Redirect the admin to the login page
    header("Location: index.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "project";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_errno) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Check if the file download is requested
if (isset($_GET['download'])) {
    $filename = $_GET['download'];
    $filepath = "uploads/" . $filename;

    if (file_exists($filepath)) {
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($filepath);
        exit;
    } else {
        echo "File not found.";
    }
}

// Check if the form is submitted
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $filename = $_POST['filename'];
    $content = $_POST['content'];

    // Update the record in the database
    $sql = "UPDATE pdf SET filename='$filename', content='$content' WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully.";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Retrieve the file details based on the ID
if (isset($_GET['id']) && isset($_GET['filename'])) {
    $id = $_GET['id'];
    $filename = $_GET['filename'];

    // Retrieve the record from the database
    $sql = "SELECT * FROM pdf WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $filename = $row['filename'];
        $content = file_get_contents("uploads/" . $filename);
    } else {
        echo "File not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update PDF</title>
    <style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
    }

    form {
        margin-top: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
    }

    .form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-group textarea {
        width: 100%;
        height: 200px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-group .btn {
        padding: 8px 12px;
        background-color: #2196f3;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Update PDF</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="filename">Filename:</label>
                <input type="text" id="filename" name="filename" value="<?php echo $filename; ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?php echo $content; ?></textarea>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <input type="submit" name="update" value="Update" class="btn">
            </div>
        </form>
    </div>
</body>

</html>