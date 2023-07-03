<?php
// Step 1: Start the session
session_start();

include 'database.php';
// Step 3: Retrieve login history from the table
$historySql = "SELECT * FROM login_history";
$historyResult = $mysqli->query($historySql);

if (!$historyResult) {
    die("Failed to retrieve login history: " . $mysqli->error);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login History</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
    }

    h2 {
        text-align: center;
        margin-top: 30px;
    }

    table {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #333;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    </style>
    </style>
</head>

<body>

    <p>total number of users:
        <?php echo $historyResult->num_rows; ?></p>
    <h2>Login History</h2>
    <?php if ($historyResult->num_rows > 0): ?>
    <table>
        <tr>
            <th>User ID</th>
            <th>Action</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $historyResult->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["user_id"]; ?></td>
            <td><?php echo $row["action"]; ?></td>
            <td><?php echo $row["timestamp"]; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <p>No login history found.</p>
    <?php endif; ?>

    <?php
    // Step 4: Close the database connection
    $mysqli->close();
    ?>
</body>

</html>