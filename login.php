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
        $filepath = "/path/to/your/pdf/files/" . $row['filename']; // Replace with the actual path to your PDF files

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

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
    /* CSS for login page */
    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    h1 {
        text-align: center;
    }

    label {
        margin-bottom: 5px;
    }

    input[type="email"],
    input[type="password"] {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    button {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    button:active {
        background-color: #3e8e41;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php if ($is_invalid) : ?>
        <em>Invalid login</em>
        <?php endif; ?>
        <form method="POST">
            <h1>login</h1>
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <button>Login</button>
        </form>
    </div>
</body>

</html>