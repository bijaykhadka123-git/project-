<?php
session_start();
include 'database2.php';



$is_invalid = false;



if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $categoryFilter = $_GET['category'];
    $sql = "SELECT * FROM pdf WHERE 1";
    if (!empty($searchQuery)) {
        $sql .= " AND (keywords LIKE '%$searchQuery%' OR title LIKE '%$searchQuery%')";
    }
   if (!empty($categoryFilter)) {
        $sql .= " AND category = '$categoryFilter'";
    }
   $result = $conn->query($sql);

    if ($result->num_rows > 0) {
       
    } else {
        echo "<div class='no-files'>No files found for the specified keyword or category.</div>";
    }

    // Store the search keyword in the database
    if (!empty($searchQuery)) {
        $insertKeywordSql = "INSERT INTO search_keywords (keyword) VALUES ('$searchQuery')";
        $conn->query($insertKeywordSql);
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'database.php';

    $email = $mysqli->real_escape_string($_POST["email"]);
    $sql = sprintf("SELECT * FROM users WHERE email='%s'", $email);
    $result = $conn->query($sql);

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

// Step 2: Retrieve the user information from the database
$userId = $_SESSION["user_id"]; // Assuming you have the user ID stored in a session variable

// Prepare the statement
$userSql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userSql);

if (!$stmt) {
    die("Failed to prepare user query: " . $mysqli->error);
}

// Bind the user ID parameter
$stmt->bind_param("s", $userId);

// Execute the query
$stmt->execute();

// Get the result
$userResult = $stmt->get_result();

if (!$userResult || $userResult->num_rows == 0) {
    die("User not found");
}

$user = $userResult->fetch_assoc();

// Step 3: Define the action and timestamp
$loginAction = "Logged in";
$loginTime = date("Y-m-d H:i:s");

// Step 4: Insert the login history into the database
$insertSql = "INSERT INTO login_history (user_id, action, timestamp) VALUES ('{$user['id']}', '$loginAction', '$loginTime')";
$insertResult = $conn->query($insertSql);

if (!$insertResult) {
    die("Failed to insert login history: " . $mysqli->error);
}


$conn->close();

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
    <style>
    .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .file-container {
        width: 20%;
        box-sizing: border-box;
        padding: 10px;
        border: 1px solid #ccc;
        display: inline-block;
        margin: 20px;
        height: 250px;
        overflow: hidden;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    .file-count {
        font-weight: bold;
        margin-bottom: 5px;
    }

    h3 {
        margin: 10px 0;
        font-size: 18px;

    }

    .default-photo {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 5px 5px 0 0;
    }

    .file-options {
        display: flex;
        justify-content: center;
    }

    .view-button,
    .download-button {
        display: inline-block;
        padding: 5px 10px;
        color: white;
        text-decoration: none;
        margin-right: 5px;
        margin-top: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .view-button {
        background-color: red;
    }

    .download-button {
        background-color: #ff9800;
    }

    .view-button:hover,
    .download-button:hover {
        background-color: #0056b3;
    }

    #sidebarContainer {
        flex: 0 0 200px;
        transition: transform 0.3s ease-in-out;
        transform: translateX(-100%);
        /* Start with the sidebar hidden */
    }

    #sidebarContainer.active {
        transform: translateX(0%);
        /* Show the sidebar by moving it back to 0% */
    }

    .keyword-buttons {
        margin-top: 50px;
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .keyword-button {
        padding: 5px 10px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .keyword-button:hover {
        background-color: #ff9800;

    }

    .keyword-container {
        text-align: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .centered-heading {
        color: black;
        margin-bottom: 10px;
        align-items: center;
        margin-top: 100px;

    }
    </style>
</head>

<body>
    <div id="container">
        <div id="sidebarContainer" class="inactive">
            <!-- Add the sidebar code here -->
            <section id="sidebar" class="sidebar">
                <a href="#" class="brand">
                    <i class='bx bxs-smile'></i>
                    <span class="text">userhub</span>
                </a>
                <ul class="side-menu top">
                    <li class="active">
                        <a href="user_dashboard.php">
                            <i class='bx bxs-dashboard'></i>
                            <span class="text">elibrary</span>
                        </a>
                    </li>
                    <li>
                        <a href="profile.php">
                            <i class='bx bxs-user profile-icon'></i>
                            <span class="text">profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="userfeedback.php">
                            <i class='bx bxs-message-dots'></i>
                            <span class="text">feedback</span>
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
        </div>


        <!-- Add the sidebar code here -->

        <nav id="navbar" class="navbar">
            <div class="switch-mode">
                <input type="checkbox" id="switch-mode-checkbox" hidden>
                <label for="switch-mode-checkbox" class="slider"></label>
            </div>
            <form id="search-form" action="#" method="GET" class="search-form">
                <div class="search-wrapper">
                    <input type="text" id="search-input" name="search" placeholder="Search..." />
                    <select id="category-select" name="category">
                        <option value="">All Categories</option>
                        <option value="Course">Course</option>
                        <option value="Note">Note</option>

                    </select>
                    <button type="button" id="search-clear-button" class="clear-button"><i
                            class="fa fa-times"></i></button>
                </div>
                <button type="submit" id="search-button"><i class="fa fa-search"></i></button>
            </form>

            <a href="#" class="profile">
                <div class="profile">
                    <img src="auth/img/library.jpg" alt="Profile Picture" class="profile-picture">

                </div>
            </a>

        </nav>
        <?php

include 'database2.php';
// Retrieve recent search keywords
$recentKeywordsSql = "SELECT DISTINCT keyword FROM search_keywords ORDER BY search_timestamp DESC LIMIT 5";
$recentKeywordsResult = $conn->query($recentKeywordsSql);
if ($recentKeywordsResult->num_rows > 0) {
    echo "<div class='keyword-container'>";
    echo "<h2 class='centered-heading'>Recent Search Keywords:</h2>";
    echo "<div class='keyword-buttons'>";
    while ($row = $recentKeywordsResult->fetch_assoc()) {
        echo "<form action='display.php' method='post' style='display: inline-block; margin: 5px;'>";
        echo "<input type='hidden' name='search' value='" . htmlspecialchars($row['keyword']) . "'>";
        echo "<button type='submit' class='keyword-button'>" . htmlspecialchars($row['keyword']) . "</button>";
        echo "</form>";
    }
    echo "</div>";
    echo "</div>";

}
echo "</form>";

?>




        <!-- NAVBAR -->
        <div class="dashboard-container">


            <?php
  
    

             if (isset($_GET['search'])) {
                $keyword = $_GET['search'];
                if (trim($keyword) !== '') { // Check if the keyword is not empty after trimming
                    echo "<h1>Search results for: " . htmlspecialchars($keyword) . "</h1>";
                } else {
                    echo "<h1>Welcome to e-library</h1>";
                }
            } else {
                echo "<h1 >Welcome to e-library</h1>";
            }
        

        
            // Display the list of available PDF files based on search keyword
include 'database2.php';

if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $categoryFilter = $_GET['category'];

    // Initialize the base query
    $sql = "SELECT * FROM pdf WHERE 1";

    if (!empty($searchQuery)) {
        $sql .= " AND keywords LIKE '%$searchQuery%' OR title LIKE '%$searchQuery%' OR filename LIKE '%$searchQuery%'";
    }

    if (!empty($categoryFilter)) {
        $sql .= " AND category = '$categoryFilter'";
    }

    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<div class='file-container'>";
            echo "<span class='file-count'>" . $count . "</span>";

            echo "<h3>" . $row['title'] . "</h3>";
            echo "<img src='uploads/xyz.jpg' alt='Default Photo' class='default-photo'>";
            echo "<div class='file-options'>";
            echo "<a class='view-button' href='view.php?id=" . $row['id'] . "'>View</a>";
            echo "<a class='download-button' href='user_dashboard.php?download=" . $row['filename'] . "'>Download</a>";
            echo "</div>";
            echo "</div>";
            $count++;
        }
    } else {
        echo "<div class='no-files'>No files found for the specified keyword or category.</div>";
    }
} else {
    $sql = "SELECT * FROM pdf ORDER BY time_stamp DESC LIMIT 12";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $count = 1; // Initialize the count variable
        while ($row = $result->fetch_assoc()) {
            echo "<div class='file-container'>";
            echo "<span class='file-count'>" . $count . "</span>";

            echo "<h3>" . $row['title'] . "</h3>";
            echo "<img src='uploads/xyz.jpg' alt='Default Photo' class='default-photo'>";
            echo "<div class='file-options'>";
            echo "<a class='view-button' href='view.php?id=" . $row['id'] . "'>View</a>";
            echo "<a class='download-button' href='user_dashboard.php?download=" . $row['filename'] . "'>Download</a>";
            echo "</div>";
            echo "</div>";
            $count++;
        }
    } else {
        echo "<div class='no-files'>No files available.</div>";
    }
}



$conn->close();
?>


        </div>
        <script src="user.js
        ">

        </script>
    </div>
</body>

</html>