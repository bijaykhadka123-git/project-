<?php
// Step 1: Start the session
session_start();

include 'database2.php';
// Step 3: Retrieve login history with user names from the table
$historySql = "SELECT lh.*, u.name FROM login_history lh
               INNER JOIN users u ON lh.user_id = u.id";
$historyResult = $conn->query($historySql);

if (!$historyResult) {
    die("Failed to retrieve login history: " . $conn->error);
}

// Step 4: Group the login history by user ID using an associative array
$loginHistoryByUser = array();
while ($row = $historyResult->fetch_assoc()) {
    $userID = $row["user_id"];
    if (!isset($loginHistoryByUser[$userID])) {
        $loginHistoryByUser[$userID] = array(
            "user_name" => $row["name"],
            "history" => array()
        );
    }
    $loginHistoryByUser[$userID]["history"][] = $row;
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
</head>

<body>

    <p>Total number of users: <?php echo count($loginHistoryByUser); ?></p>
    <h2>Login History</h2>
    <?php if (count($loginHistoryByUser) > 0): ?>
    <?php foreach ($loginHistoryByUser as $userID => $userData): ?>
    <h3>User ID: <?php echo $userID; ?> </h3>
    <table>
        <tr>
            <th>Action</th>
            <th>Timestamp</th>
            <th>username</th>
        </tr>
        <?php
                $latestLoginHistory = array_slice($userData["history"], 0, 5);
                foreach ($latestLoginHistory as $row): ?>
        <tr>
            <td><?php echo $row["action"]; ?></td>
            <td><?php echo $row["timestamp"]; ?></td>
            <td><?php echo $userData["user_name"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endforeach; ?>
    <?php else: ?>
    <p>No login history found.</p>
    <?php endif; ?>

    <?php
    // Step 5: Close the database connection
    $conn->close();
    ?>
</body>

</html>