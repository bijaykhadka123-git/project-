<?php
session_start();
include 'database.php';

$is_invalid = false; // Initialize the variable as false

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'database.php';

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
    include 'database2.php';

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
    include 'database.php';
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

include 'database2.php';

// Handle search functionality
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $searchQuery = $conn->real_escape_string($searchQuery); // Sanitize the search query

    // Search by matching keywords
    $sql = "SELECT * FROM pdf WHERE keywords LIKE '%$searchQuery%'";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        $message = "No records found.";
    }
} else {
    // Retrieve all records if no search query is present
    $sql = "SELECT * FROM pdf";
    $result = $conn->query($sql);
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
    <link rel="stylesheet" href="userdash.css">
</head>

<body>
    <!-- Add the sidebar code here -->
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

    <body>
        <!-- Add the sidebar code here -->
        <section id="sidebar">
            <a href="#" class="brand">
                <i class='bx bxs-smile'></i>
                <span class="text">AdminHub</span>
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
                        <span class="text">history </span>
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
        <nav id="navbar" class="navbar">
            <div class="switch-mode">
                <input type="checkbox" id="switch-mode-checkbox" hidden>
                <label for="switch-mode-checkbox" class="slider"></label>
            </div>
            <form id="search-form" action="#" method="GET" class="search-form">
                <div class="search-wrapper">
                    <input type="text" id="search-input" name="search" placeholder="Search..." />
                    <button type="button" id="search-clear-button" class="clear-button"><i
                            class="fa fa-times"></i></button>
                </div>
                <button type="submit" id="search-button"><i class="fa fa-search"></i></button>
            </form>
            <a href="#" class="notification">
                <i class="bx bxs-bell"></i>
                <span class="notification-count">8</span>
            </a>
        </nav>

        <!-- NAVBAR -->

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
        
            
            // Display the list of available PDF files based on search keyword
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "project";

            $conn = new mysqli($host, $username, $password, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if (isset($_GET['search'])) {
                $keyword = $conn->real_escape_string($_GET['search']);

                $sql = "SELECT * FROM pdf WHERE keywords LIKE '%$keyword%'";
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
                    echo "<tr><td colspan='3'>No files found for the specified keyword.</td></tr>";
                }
            } else {
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
            }

            $conn->close();
            ?>
        </div>
        <script>
        // Get the switch mode checkbox element
        const switchModeCheckbox = document.getElementById('switch-mode-checkbox');

        // Listen for the checkbox change event
        switchModeCheckbox.addEventListener('change', function() {
            // Toggle dark mode class on the body element
            document.body.classList.toggle('dark');
        });
        const searchButton = document.getElementById('search-button');
        const searchInput = document.getElementById('search-input');
        const searchClearButton = document.getElementById('search-clear-button');

        searchButton.addEventListener('click', function() {
            if (searchInput.value.trim() !== '') {
                document.getElementById('search-form').submit();
            }
        });

        searchClearButton.addEventListener('click', function() {
            searchInput.value = '';
        });
        </script>
    </body>

</html>