<?php
session_start();

include_once 'database2.php';

// Check if the form is submitted for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['title'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];

        // Update the title in the database
        $updateQuery = "UPDATE pdf SET title='$title' WHERE id='$id'";
        if ($conn->query($updateQuery) === TRUE) {
            echo "Title updated successfully!";
        } else {
            echo "Error updating title: " . $conn->error;
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

        // Check if the file exists
        if (isset($file['title'])) {
            // Serve the file for download
            if (isset($_GET['download'])) {
                $filepath = "uploads/" . $file['title'];
                if (file_exists($filepath)) {
                    header("Content-Type: application/pdf");
                    header("Content-Disposition: attachment; filename=" . $file['title']);
                    readfile($filepath);
                    exit;
                } else {
                    echo "File not found.";
                }
            }

            // Display the file content for viewing
            if (isset($_GET['view'])) {
                $filepath = "uploads/" . $file['title'];
                if (file_exists($filepath)) {
                    header("Content-Type: application/pdf");
                    readfile($filepath);
                    exit;
                } else {
                    echo "File not found.";
                }
            }

            // Delete the file
            if (isset($_GET['delete'])) {
                $filepath = "uploads/" . $file['title'];
                if (file_exists($filepath)) {
                    // Remove the file from the server
                    unlink($filepath);

                    // Delete the file record from the database
                    $deleteQuery = "DELETE FROM pdf WHERE id='$id'";
                    if ($conn->query($deleteQuery) === TRUE) {
                        echo "File deleted successfully!";
                    } else {
                        echo "Error deleting file: " . $conn->error;
                    }
                } else {
                    echo "File not found.";
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
            <input type="text" name="title" value="<?php echo $file['title']; ?>">
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
            <a href="admin_dashboard.php">Back to File List</a>
        </p>
    </div>
</body>

</html>