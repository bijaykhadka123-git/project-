<?php
session_start();
$is_invalid = false; 
// Check if the user clicked the logout link
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
     session_destroy();
     header("Location: auth/login.php");
    exit();
}
// Check if the user is already logged in
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        header("Location: user_dashboard.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   include 'database2.php';

    $email = $conn->real_escape_string($_POST["email"]);
    $sql = sprintf("SELECT * FROM users WHERE email='%s'", $email);
    $result = $conn->query($sql);

    if ($result) {
        $user = $result->fetch_assoc();

        if ($user) {
            if (password_verify($_POST["password"], $user["hash_password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role"] = $user["role"]; 
                // Redirect the user to the appropriate dashboard based on the user role
                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    header("Location: user_dashboard.php");
                    exit();
                }
            } else {
                $is_invalid = true;
            }
        } else {
            $is_invalid = true;
        }
    } else {
        die("Query failed: " . $conn->error);
    }
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
    /* CSS for login page */
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
    }


    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        position: relative;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .cancel-button {
        position: absolute;
        top: 10px;
        right: 10px;
        visibility: visible !important;
        background: none;
        border: none;
        color: #000;
    }



    .cancel-button i {
        font-size: 20px;
    }

    .cancel-button:hover {
        color: red;
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
        width: 98%;
    }

    button {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button[type="submit"] {
        width: auto;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button[type=submit]:hover {
        background-color: #45a049;
    }

    button[type=submit]:active {
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
            <h1>Login</h1>
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <button type="submit">Login</button>
        </form>

        <button type="button" id="cancel-button" class="cancel-button">
            <i class="material-icons">close</i>
        </button>

        <p>Not signed up yet? <a href="index.php">Sign up here</a>.</p>
        <script>
        document.getElementById("cancel-button").addEventListener("click", function() {

            window.location.href = "auth/index.html";
        });
        </script>
    </div>
</body>

</html>