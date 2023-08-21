<?php
session_start();
include 'database2.php';

// Check if the user is logged in and has the 'user_id' value set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the current user's details from the database
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $userDetails = $result->fetch_assoc();
} else {
    // Handle the case when user details are not found
    $userDetails = null;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">



    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;

    }

    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;

        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
        position: relative;
    }


    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    h1 {
        text-align: center;
    }

    p {
        text-align: center;
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
    </style>

</head>

<body>
    <div class="container">
        <h1>User Profile</h1>
        <?php if ($userDetails) : ?>
        <button type="button" id="cancel-button" class="cancel-button">
            <i class="material-icons">close</i>
        </button>

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
    </div>
</body>
<script>
document.getElementById("cancel-button").addEventListener("click", function() {
    window.location.href = "user_dashboard.php";
});
</script>

</html>