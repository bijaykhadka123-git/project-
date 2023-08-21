<!DOCTYPE html>
<html>

<head>
    <style>
    .pdf-container {
        width: 15%;
        box-sizing: border-box;
        padding: 10px;
        border: 1px solid #ccc;
        display: inline-block;

        height: 250px;
        overflow: hidden;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
        margin: 20px;
        margin-left: 100px;
    }

    .file-count {
        font-weight: bold;
        margin-bottom: 5px;
    }

    h2 {
        margin: 10px 0;
        font-size: 18px;
        text-align: center;

    }

    .default-photo {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 5px 5px 0 0;
    }

    .file-options {
        display: flex;
        justify-content: center;
    }

    .view-button,
    .download-button {
        display: inline-block;
        padding: 5px 10px;
        color: white;
        text-decoration: none;
        margin-right: 5px;
        margin-top: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .view-button {
        background-color: red;
    }

    .download-button {
        background-color: #ff9800;
    }

    .view-button:hover,
    .download-button:hover {
        background-color: #0056b3;
    }
    </style>
</head>
</body>

</html>
<?php


include 'database2.php';

if (isset($_POST['search'])) {
    $searchQuery = $conn->real_escape_string($_POST['search']);

    // Query to fetch PDFs based on the clicked keyword
    $pdfQuery = "SELECT * FROM pdf WHERE keywords LIKE '%$searchQuery%' OR title LIKE '%$searchQuery%'";

    $pdfResult = $conn->query($pdfQuery);

    if ($pdfResult->num_rows > 0) {
      
        echo "<h2>PDFs matching the keyword: " . htmlspecialchars($searchQuery) . "</h2>";
          $count=1;
        while ($Row = $pdfResult->fetch_assoc()) {
            echo "<div class='pdf-container'>";
            echo "<span class='file-count'>" . $count . "</span>";
                  echo "<h3>" . $Row['title'] . "</h3>"; 
             echo "<img src='uploads/xyz.jpg' alt='Default Photo' class='default-photo'>";
      
            echo "<a class='view-button' href='view.php?id=" . $Row['id'] . "'>View</a>";
            echo "<a class='download-button' href='user_dashboard.php?download=" . $Row['filename'] . "'>Download</a>"; // Complete the download-button link
            echo "</div>";
            $count++;
        }
    } else {
        echo "<p>No PDFs found for the keyword: " . htmlspecialchars($searchQuery) . "</p>";
    }
}
?>

</body>

</html>