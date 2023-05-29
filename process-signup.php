<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST)) {
        die("Name is required");
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        die("Valid email is required");
    }

    if (strlen($_POST["password"]) < 8) {
        die("Password should be at least 8 characters");
    }

    if (!preg_match("/[a-z]/i", $_POST["password"])) {
        die("Password must contain at least one letter");
    }

    if (!preg_match("/[0-9]/", $_POST["password"])) {
        die("Password must contain at least one number");
    }

    if ($_POST["password"] !== $_POST["repeat_password"]) {
        die("Passwords must match");
    }

    // Check if the email address is allowed for the admin role
    $allowedAdminEmails = ["admin1@example.com", "admin2@example.com"];
    $email = $_POST["email"];
    if ($_POST["role"] === "admin" && !in_array($email, $allowedAdminEmails)) {
        die("Email address not allowed for the admin role");
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
            die("Email is already taken");
        } else {
            die($mysqli->error . " " . $mysqli->errno);
        }
    }
}
?>