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
    }
}

$sql = "SELECT * FROM pdf";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download PDF</title>
    <style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    .btn {
        display: inline-block;
        padding: 8px 12px;
        margin: 4px;
        border-radius: 4px;
        text-decoration: none;
        color: #fff;
    }

    .btn-info {
        background-color: #4caf50;
    }

    .btn-primary {
        background-color: #2196f3;
    }

    .btn-warning {
        background-color: #ff9800;
    }

    .btn-danger {
        background-color: #f44336;
    }

    p {
        text-align: center;
        margin-top: 20px;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Download PDF</h1>
        <div class="tabe-responsive">
            <table class="table-one">
                <tr>
                    <th>
                        sr.no
                    </th>
                    <th>
                        file name
                    </th>
                    <th>
                        action
                    </th>
                </tr>
                <?php
                $count = 1; // Initialize the count variable
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . $row['filename'] . "</td>";
                        echo '<td>
                                <a href="?download=' . $row['filename'] . '" class="btn btn-info">Download</a>
                                <a href="view.php?id=' . $row['id'] . '" class="btn btn-primary">View</a>
                                <a href="update.php?id=' . $row['id'] . '" class="btn btn-warning">Update</a>
                                <a href="delete.php?id=' . $row['id'] . '" class="btn btn-danger">Delete</a>
                              </td>';
                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='3'>No records found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <button class="logout-button"><a href="?action=logout">Logout</a></button>

</body>

</html>