<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
    .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }


    .file-container {
        width: 25%;
        box-sizing: border-box;
        padding: 10px;
        border: 1px solid #ccc;
        display: inline-block;
        margin: 20px;
        height: 200px;
        overflow: hidden;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .file-count {
        font-weight: bold;
        margin-bottom: 5px;
    }

    h1 {
        margin: 0;
        font-size: 30px;
        text-align: center;
    }

    .file-options {
        margin-top: 10px;
    }

    .view-button {
        display: inline-block;
        padding: 5px 10px;
        background-color: red;
        color: white;
        text-decoration: none;
        margin-right: 5px;

    }

    .download-button {
        display: inline-block;
        padding: 5px 10px;
        background-color: #ff9800;
        color: white;
        text-decoration: none;
        margin-right: 5px;

    }

    .view-button:hover {
        background-color: #0056b3;
    }

    .download-button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="dashboard_container">
        <h1>here you can see recent update book </h1>


        <?php
        include "database2.php";

        // Retrieve the PDF files from the database
       $sql = "SELECT * FROM pdf ORDER BY time_stamp DESC LIMIT 8";
        $result = $conn->query($sql);

      
    if ($result->num_rows > 0) {
        $count = 1; // Initialize the count variable
        while ($row = $result->fetch_assoc()) {
            echo "<div class='file-container'>";
            echo "<span class='file-count'>" . $count . "</span>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<div class='file-options'>";
            echo "<a class='view-button' href='view.php?id=" . $row['id'] . "'>View</a>";
            echo "<a class='download-button' href='user_dashboard.php?download=" . $row['filename'] . "'>Download</a>";
            echo "</div>";
            echo "</div>";
            $count++;
        }
    } else {
        echo "<div class='no-files'>No files available.</div>";
    }


$conn->close();
?>
    </div>
</body>

</html>