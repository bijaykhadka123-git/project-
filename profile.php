<?php
session_start();
include 'database.php';

// Check if the user is logged in and has the 'user_id' value set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit();
}

// Retrieve the current user's details from the database
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$userId'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
} else {
    // Handle the case when user details are not found
    $userDetails = null;
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Add your CSS stylesheets here -->
    <style>
    table {

        width: 50%;
        justify-content: center;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    </style>
</head>

<body>
    <!-- Add your HTML markup for the profile page -->
    <h1>User Profile</h1>
    <?php if ($userDetails) : ?>
    <table>
        <tr>
            <th>Name</th>
            <td><?php echo $userDetails['name']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $userDetails['email']; ?></td>
        </tr>
        <!-- Add more user details as needed -->
    </table>
    <?php else : ?>
    <p>User details not found.</p>
    <?php endif; ?>
    <!-- Add your JavaScript code here -->
</body>

</html>