<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST)) {
        echo '<script>alert("Name is required");</script>';
        die();
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Valid email is required");</script>';
        die();
    }

    if (strlen($_POST["password"]) < 8) {
        echo '<script>alert("Password should be at least 8 characters");</script>';
        die();
    }

    if (!preg_match("/[a-z]/i", $_POST["password"])) {
        echo '<script>alert("Password must contain at least one letter");</script>';
        die();
    }

    if (!preg_match("/[0-9]/", $_POST["password"])) {
        echo '<script>alert("Password must contain at least one number");</script>';
        die();
    }

    if ($_POST["password"] !== $_POST["repeat_password"]) {
        echo '<script>alert("Passwords must match");</script>';
        die();
    }

    // Check if the email address is allowed for the admin role
    $allowedAdminEmails = ["admin1@example.com", "admin2@example.com"];
    $email = $_POST["email"];
    if ($_POST["role"] === "admin" && !in_array($email, $allowedAdminEmails)) {
        echo '<script>alert("Email address not allowed for the admin role");</script>';
        die();
    }

    $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Debug: Display hashed password
    echo "Hashed Password: " . $hashed_password . "<br>";

    $mysqli = require __DIR__ . "/database.php";

    // Debug: Display $mysqli variable
    echo "MySQLi Object: ";
    var_dump($mysqli);

    $role = $_POST["role"];

    $sql = "INSERT INTO users (name, email, hash_password, role) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("ssss", $_POST["name"], $_POST["email"], $hashed_password, $role);
    if ($stmt->execute()) {
        header("location: login.php"); // Redirect to the login page
        exit();
    } else {
        if ($mysqli->errno === 1062) {
            echo '<script>alert("Email is already taken");</script>';
            die();
        } else {
            die($mysqli->error . " " . $mysqli->errno);
        }
    }
}
?>