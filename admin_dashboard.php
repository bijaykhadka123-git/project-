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
    <a href="#" class="notification">
        <i class='bx bxs-bell'></i>
        <span class="num">8</span>
    </a>

    <input type="checkbox" id="switch-mode" hidden>
    <label for="switch-mode" class="switch-mode"></label>

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
    const switchMode = document.getElementById('switch-mode');

    switchMode.addEventListener('change', function() {
        if (this.checked) {
            // Checkbox is checked, perform action for checked state
            document.body.classList.add('dark-mode');
        } else {
            // Checkbox is unchecked, perform action for unchecked state
            document.body.classList.remove('dark-mode');
        }
    });
    </script>

</body>

</html>