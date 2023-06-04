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

// Check if the form is submitted for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['filename'])) {
        $id = $_POST['id'];
        $filename = $_POST['filename'];

        // Retrieve the old filename from the database
        $filenameQuery = "SELECT filename FROM pdf WHERE id='$id'";
        $filenameResult = $conn->query($filenameQuery);
        $oldFilename = $filenameResult->fetch_assoc()['filename'];

        // Update the file name in the database
        $updateQuery = "UPDATE pdf SET filename='$filename' WHERE id='$id'";
        if ($conn->query($updateQuery) === TRUE) {
            // Rename the file on the server
            $oldFilePath = "uploads/" . $oldFilename;
            $newFilePath = "uploads/" . $filename;
            if (rename($oldFilePath, $newFilePath)) {
                echo "File name updated successfully!";
            } else {
                echo "Error updating file name on the server.";
            }
        } else {
            echo "Error updating file name: " . $conn->error;
        }
    } else {
        echo "Invalid request.";
    }
}

// Fetch the file details from the database based on the provided id
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $fileQuery = "SELECT * FROM pdf WHERE id='$id'";
    $fileResult = $conn->query($fileQuery);

    if ($fileResult->num_rows > 0) {
        $file = $fileResult->fetch_assoc();

        // Retrieve the file path
        $filepath = "uploads/" . $file['filename'];

        // Check if the file exists
        if (file_exists($filepath)) {
            // Serve the file for download
            if (isset($_GET['download'])) {
                header("Content-Type: application/pdf");
                header("Content-Disposition: attachment; filename=" . $file['filename']);
                readfile($filepath);
                exit;
            }

            // Display the file content for viewing
            if (isset($_GET['view'])) {
                header("Content-Type: application/pdf");
                readfile($filepath);
                exit;
            }

            // Delete the file
            if (isset($_GET['delete'])) {
                // Remove the file from the server
                unlink($filepath);

                // Delete the file record from the database
                $deleteQuery = "DELETE FROM pdf WHERE id='$id'";
                if ($conn->query($deleteQuery) === TRUE) {
                    echo "File deleted successfully!";
                } else {
                    echo "Error deleting file: " . $conn->error;
                }
            }
        } else {
            echo "File not found.";
        }
    } else {
        echo "File not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update PDF File</title>
    <style>
    .container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f2f2f2;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h1 {
        margin-top: 0;

        color: #45a049;
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    input[type="text"],
    input[type="submit"] {
        padding: 10px;
        margin-bottom: 10px;
    }

    input[type="submit"] {
        background-color: #1e90ff;
        color: white;
        cursor: pointer;
        border: none;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }

    p {
        margin-top: 20px;
        text-align: center;
    }

    a {
        color: #1e90ff;
        text-decoration: none;

    }

    a:hover {
        text-decoration: underline;
        color: #45a049;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Update PDF File</h1>

        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $fileQuery = "SELECT * FROM pdf WHERE id='$id'";
            $fileResult = $conn->query($fileQuery);

            if ($fileResult->num_rows > 0) {
                $file = $fileResult->fetch_assoc();
                ?>


        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
            <input type="text" name="filename" value="<?php echo $file['filename']; ?>">
            <input type="submit" value="Update">
        </form>


        <?php
            } else {
                echo "File not found.";
            }
        } else {
            echo "Invalid request.";
        }
        ?>

        <p>
            <a href="download.php">Back to File List</a>
        </p>
    </div>
</body>

</html>