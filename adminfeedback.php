<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Messages</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin: 10px 0;
        padding: 15px;
    }

    li h3 {
        margin: 0;
    }

    li p {
        margin: 10px 0;
    }

    h1 {
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
        <h1>Feedback</h1>
        <button type="button" id="cancel-button" class="cancel-button">
            <i class="material-icons">close</i>
        </button>
        <?php
        include 'database2.php';
        $sql = "SELECT * FROM feedback ORDER BY id DESC LIMIT 10";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<h3>Subject: " . $row["subject"] . "</h3>";
                echo "<p>Message: " . $row["message"] . "</p>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No feedback messages found.</p>";
        }
        ?>

    </div>
</body>
<script>
document.getElementById("cancel-button").addEventListener("click", function() {
    window.location.href = "auth/index.html";
});
</script>

</html>