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
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap');

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

    /* css for side bar */

    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 220px;
        background-color: #333;
        color: #fff;
        padding-top: 20px;
        box-sizing: border-box;
        overflow-y: auto;
    }

    .brand {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: #fff;
        text-decoration: none;
        font-size: 20px;
    }

    .brand i {
        margin-right: 10px;
    }

    .side-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .side-menu li {
        margin-bottom: 15px;
    }

    .side-menu a {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .side-menu a:hover {
        background-color: #555;
    }

    .side-menu i {
        margin-right: 10px;
    }

    .text {
        flex-grow: 1;
    }

    .active a {
        background-color: #555;
    }

    .top {
        margin-top: 60px;
    }

    .logout-button {
        display: inline-block;
        padding: 5px 10px;
        background-color: #f44336;
        color: #fff;
        text-decoration: none;
        border: none;
        border-radius: 2px;
        font-size: 15px;
        cursor: pointer;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }

    /* Navbar Styles */


    /* Switch mode toggle styles */

    .navbar {
        background-color: #f2f2f2;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .switch-mode {
        position: fixed;
        top: 30px;
        right: 90px;
        transform: translateY(-50%);
        z-index: 9999;
    }

    .switch-mode .slider {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 24px;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 24px;
        cursor: pointer;
    }

    .switch-mode .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: #fff;
        transition: 0.4s;
        border-radius: 50%;
    }

    .switch-mode input:checked+.slider {
        background-color: #2196f3;
    }

    .switch-mode input:checked+.slider:before {
        transform: translateX(16px);
    }

    .switch-mode .slider.round {
        border-radius: 34px;
    }

    .switch-mode .slider.round:before {
        border-radius: 50%;
    }


    /* Dark mode styles */

    body.dark {
        background-color: #222;
        color: #fff;
    }

    body.dark .container {
        background-color: #333;
    }

    body.dark th {
        background-color: #444;
    }

    body.dark .btn-info {
        background-color: #6fbf73;
    }

    body.dark .btn-primary {
        background-color: #4d8ff2;
    }

    body.dark .btn-warning {
        background-color: #ffa94d;
    }

    body.dark .btn-danger {
        background-color: #f26868;
    }

    body.dark .logout-button {
        background-color: #f26868;
    }

    body.dark .logout-button:hover {
        background-color: #cf5959;
    }

    body.dark .message {
        color: #fff;
    }


    /* Notification styles */

    .notification {
        position: fixed;
        top: 30px;
        right: 50px;
        transform: translateY(-50%);
        z-index: 9999;
    }

    .notification.active {
        background-color: #4caf50;
    }

    .notification .notification-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: red;
        color: #fff;
        font-size: 12px;
        padding: 2px 4px;
        border-radius: 50%;
    }


    /* Profile styles */

    .profile {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        text-decoration: none;
        color: #333;
    }


    /* Navbar Styles */
    </style>
</head>

<body>
    !-- Add the sidebar code here --!
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-smile'></i>
            <span class="text">userhub</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="#">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Analyst </span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-message-dots'></i>
                    <span class="text">Message</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li>
                <a href="?action=logout">
                    <i class='bx bxs-log-out-circle'></i>

                    <span class="logout-button">Logout</span>

                </a>
            </li>
        </ul>
    </section>
    <!-- End of sidebar code -->
    <!-- NAVBAR -->
    <nav id="navbar">
        <div class="switch-mode">
            <input type="checkbox" id="switch-mode-checkbox" hidden>
            <label for="switch-mode-checkbox" class="slider"></label>
        </div>

        <a href="#" class="notification">
            <i class="bx bxs-bell"></i>
            <span class="notification-count">8</span>
        </a>
    </nav>



    <!-- NAVBAR -->
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
    <script>
    // Get the switch mode checkbox element
    const switchModeCheckbox = document.getElementById('switch-mode-checkbox');

    // Listen for the checkbox change event
    switchModeCheckbox.addEventListener('change', function() {
        // Toggle dark mode class on the body element
        document.body.classList.toggle('dark');
    });
    </script>

</body>

</html>