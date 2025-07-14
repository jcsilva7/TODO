<?php
    /** @var mysqli $conn */
    require_once 'database.php';
    session_start();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM user WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if(!$user || !password_verify($password, $user['password'])){
            echo "<script> alert('Wrong Username or Password'); window.location.href='login.php'; </script>";
            exit;
        }
        else{
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
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

    <title>Login</title>
</head>

<body class="bg-dark text-light">
    <h1>Welcome! Please Log in or Register</h1>

    <form action="login.php" method="post">
        <div class="d-flex justify-content-center align-items-center flex-column gap-2 form-group">
            <label for="username" class="align-self-start">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
            <label for="password" class="align-self-start">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>

            <div class="form-check align-self-start">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check Password</label>
            </div>

            <button type="submit" name="login" class="btn btn-success">Submit</button>

            <a href="register.php">Dont have an account? Sign Up</a>
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