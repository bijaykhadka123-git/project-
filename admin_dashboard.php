<?php
session_start();

// Check if the user clicked the logout link
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_destroy();

    // Redirect the admin to the login page
    header("Location: auth/index.html");
    exit();
}

// Check if the user is not logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    // Redirect the user to the login page
    header("Location: login.php");
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="admin.css" />

</head>

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
                <button type="button" id="search-clear-button" class="clear-button"><i class="fa fa-times"></i></button>
            </div>
            <button type="submit" id="search-button"><i class="fa fa-search"></i></button>
        </form>
        <a href="#" class="notification">
            <i class="bx bxs-bell"></i>
            <span class="notification-count">8</span>
        </a>
    </nav>
    s




    <!-- NAVBAR -->

    <div class="container">
        <h1>Admin Dashboard</h1>
        <table>
            <a href="upload.php" class="btn-upload">
                <i class='bx bxs-cloud-upload'></i>
                <span class="text">upload PDF</span>
            </a>
            <tr>
                <th>Sr. No.</th>
                <th>File Name</th>
                <th>Action</th>
            </tr>
            <?php
            $count = 1;
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
        <div class="message">
            <?php
            if (isset($_GET['message'])) {
                echo $_GET['message'];
            }
            ?>
        </div>
    </div>




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

    searchButton.addEventListener('click', function() {
        const searchQuery = searchInput.value.trim();

        if (searchQuery !== '') {
            window.location.href = 'search.php?q=' + encodeURIComponent(searchQuery);
        }
    });

    const searchinput = document.getElementById('search-input');
    const searchClearButton = document.getElementById('search-clear-button');

    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            searchClearButton.classList.add('active');
        } else {
            searchClearButton.classList.remove('active');
        }
    });

    searchClearButton.addEventListener('click', function() {
        searchinput.value = '';
        searchClearButton.classList.remove('active');
    });
    </script>

</body>

</html>