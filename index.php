<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styleone.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">


</head>


<body>
    <div class="container">
        <form action="process-signup.php" method="POST">
            <div class="form-group">
                <h1>Signup</h1>
                <label for="name"> Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="repeat-password">Repeat Password</label>
                <input type="password" id="repeat-password" name="repeat_password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>



            <div class="form-group">
                <button type="submit">Submit</button>
                <button type="button" id="cancel-button" class="cancel-button">
                    <i class="material-icons">close</i></button>
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>


    </form>
    </div>

    <script>
    document.getElementById("cancel-button").addEventListener("click", function() {

        window.location.href = "auth/index.html";
    });
    </script>
    </form>
    </div>
</body>

</html>