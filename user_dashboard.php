<?php
session_start();

$is_invalid = false; // Initialize the variable as false

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "project";

    $mysqli = new mysqli($host, $username, $password, $database);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $email = $mysqli->real_escape_string($_POST["email"]);
    $sql = sprintf("SELECT * FROM users WHERE email='%s'", $email);
    $result = $mysqli->query($sql);

    if ($result) {
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($_POST["password"], $user["hash_password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role"] = $user["role"]; // Set the "role" value in the session

                // Redirect the user to the appropriate dashboard based on the user role
                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $is_invalid = true;
            }
        } else {
            $is_invalid = true;
        }
    } else {
        die("Query failed: " . $mysqli->error);
    }
}

// Check if the user is logged in and has the 'role' value set in the session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit();
}

// Check if the file download is requested
if (isset($_GET['download'])) {
    $filename = $_GET['download'];

    // Perform necessary checks and retrieve the file path from the database
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "project";

    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $filename = $conn->real_escape_string($filename);
    $sql = "SELECT * FROM pdf WHERE filename = '$filename'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filepath = "uploads/" . $row['filename'];

        if (file_exists($filepath)) {
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename=" . $row['filename']);
            readfile($filepath);
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "File not found.";
    }
}

// Check if the file view is requested
if (isset($_GET['view'])) {
    $fileId = $_GET['view'];

    // Perform necessary checks and retrieve the file path from the database
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "project";

    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $fileId = $conn->real_escape_string($fileId);
    $sql = "SELECT * FROM pdf WHERE id = '$fileId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filepath = "uploads/" . $row['filename'];

        if (file_exists($filepath)) {
            echo "<embed src='$filepath' width='100%' height='500px' />";
            exit;
        } else {
            echo "File not found.";
        }
    } else {
        echo "File not found.";
    }
}

// Check if the logout action is requested
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location:auth/index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .dashboard-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .view-button,
    .download-button {
        display: inline-block;
        padding: 6px 10px;
        margin-right: 5px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .view-button {
        background-color: #2196f3;
        color: #fff;
    }

    .download-button {
        background-color: #4caf50;
        color: #fff;
    }

    .view-button:hover,
    .download-button:hover {
        background-color: #45a049;
    }

    .logout-button {
        display: inline-block;
        padding: 8px 12px;
        background-color: #f44336;
        color: #fff;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }

    .message {
        text-align: center;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <h1>Welcome to e-library </h1>
        <table>
            <tr>
                <th>No.</th>
                <th>File Name</th>
                <th>Actions</th>
            </tr>
            <?php
            // Display the list of available PDF files
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "project";

            $conn = new mysqli($host, $username, $password, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM pdf";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $count = 1; // Initialize the count variable
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . $row['filename'] . "</td>";
                    echo "<td>";
                  echo "<a class='view-button' href='view.php?id=" . $row['id'] . "'>View</a>";

                    echo "<a class='view-button' href='user_dashboard.php?download=" . $row['filename'] . "'>Download</a>";
                    echo "</td>";
                    echo "</tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='3'>No files available.</td></tr>";
            }
            $conn->close();
            ?>
        </table>

    </div>
    <a class="logout-button" href="?action=logout">Logout</a>
</body>

</html>