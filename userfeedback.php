<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    form {
        display: grid;
        gap: 10px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button[type="submit"] {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    .cancel-button {

        position: absolute;
        top: 10px;
        right: 10px;
        visibility: visible;
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
        <h1>Feedback Form</h1>
        <button type="button" id="cancel-button" class="cancel-button">
            <i class="material-icons">close</i>
        </button>
        <form id="feedback-form" method="post" action="#">
            <input type="text" id="feedback-subject" name="subject" placeholder="Feedback Subject" required>
            <textarea id="feedback-message" name="message" placeholder="Your Feedback" required></textarea>
            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
    <?php
        include 'database2.php';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $subject = $_POST["subject"];
            $message = $_POST["message"];

            // Insert the feedback data into the database (replace with your actual database handling code)
            $sql = "INSERT INTO feedback (subject, message) VALUES ('$subject', '$message')";
            if ($conn->query($sql) === TRUE) {
                echo "Feedback submitted successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    ?>
</body>
<script>
document.getElementById("cancel-button").addEventListener("click", function() {
    window.location.href = "user_dashboard.php";
});
</script>

</html>