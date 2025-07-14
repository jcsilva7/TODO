<?php
    /** @var mysqli $conn */
    require_once 'database.php';
    session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Just for confirmation, it should never execute this
    if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["password_confirm"])){
        echo "<script> alert('All fields are required'); </script>";
        exit;
    }
    elseif($_POST['password'] != $_POST['password_confirm']){
        echo "<script> alert('Passwords did not match'); window.location.href='register.php';</script>";
        exit;
    }
    else{
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

        $stmt = $conn->prepare("SELECT id FROM user WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script> alert('Username already exists'); window.location.href='register.php';</script>";
        }
        else{
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            try{
                $stmt = $conn->prepare("INSERT INTO user (name, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $password);
                $stmt->execute();

                echo "<script> alert('Registration Successful'); window.location.href='login.php';</script>";
                exit;
            }
            catch(Exception $e){
                echo "<script> alert('Something went wrong'); window.location.href='register.php';</script>";
                exit;
            }
        }

    }
}

$conn->close();
?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="todo.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <title>Register</title>
    </head>

    <body class="bg-dark text-light">
    <h1>Register On Our Page!</h1>

    <form action="register.php" method="post">
        <div class="d-flex justify-content-center align-items-center flex-column gap-2 form-group">
            <label for="username" class="align-self-start">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter a Username" required>
            <label for="password" class="align-self-start">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter a Password" required>

            <div class="form-check align-self-start">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check Password</label>
            </div>

            <label for="password_confirm" class="align-self-start">Enter Your Password Again</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm your Password" required>

            <button type="submit" name="register" class="btn btn-success">Register</button>

            <a href="login.php">Return to Log in</a>

        </div>

    </form>

    <script>
        document.getElementById("exampleCheck1").addEventListener("change", function() {
            const passwordInput = document.getElementById("password");
            if (this.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        });
    </script>

    </body>
    </html>

<?php
    include("footer.html");
?>