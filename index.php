<?php
    /** @var mysqli $conn */

    session_start();
    if(!isset($_SESSION['user_id'])){
        header('Location: index.php');
        exit;
    }

    require_once 'database.php';

    include("footer.html");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['logout'])){
            $_SESSION['user_id'] = null;

            session_destroy();
            header("Location: login.php");
            exit;
        }
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="todo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <title>TODO</title>

</head>

<body class="bg-dark text-light m-0">

<div class="content-wrapper p-4">
    <h1 id="title-text">TODO</h1>

    <div class="container">
        <div class="mb-3 d-flex gap-2 buttons">
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addModal" name="add">+</button>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#removeModal" name="remove">-</button>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#statusModal" name="remove">✓</button>
        </div>

        <!-- Add Popup -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="index.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Task</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="name">Task Name</label> <br>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Task Name" maxlength="64" required> <br>
                        <label for="description">Description</label> <br>
                        <input type="text" id="description" name="description" maxlength="512" class="form-control" placeholder="Leave Empty If No Description Is Required"> <br>
                        <label for="expiry">Expiry Date</label> <br>
                        <input type="datetime-local" id="expiry" name="expiry-date" class="form-control" placeholder="YYYY-MM-DD HH:MM:SS Or empty if no expiry date"> <br>
                        <label for="tag">Tag</label> <br>
                        <input type="text" id="tag" name="tag" maxlength="32" class="form-control" placeholder="Enter Tag Name Or Nothing If There's No Tag"> <br>
                        <input class="btn btn-dark" id="add-button" type="submit" value="Add" name="add-task">

                    </div>
                </form>
            </div>
        </div>

        <!-- Remove popup -->
        <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="index.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="removeModalLabel">Remove Task</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="id">Task ID</label> <br>
                        <input type="number" id="id" name="id" placeholder="Task ID" class="form-control" required> <br>
                        <input class="btn btn-dark" id="remove-button" type="submit" value="Remove" name="remove-task">

                    </div>
                </form>
            </div>
        </div>

        <!-- Change Status Popup -->
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="index.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel">Change Status</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="task-id">Task ID</label> <br>
                        <input type="number" id="task-id" name="task-id" class="form-control" placeholder="Task ID" required> <br>

                        <label for="task-status">Task status</label> <br>
                        <select class="form-select" id="task-status" name="task-status" required>
                            <option hidden selected value="-1">(select a status)</option>
                            <option value="0">To Start</option>
                            <option value="1">In Progress</option>
                            <option value="2">Completed</option>
                        </select>

                        <input class="btn btn-dark" id="status-button" type="submit" value="Change" name="status-change">
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-dark-subtle text-light-emphasis content">
            <ul id='column-names-ul'><li id='column-names'><span>Task ID</span> <span>Name</span> <span>Description</span> <span>Status</span> <span>Register Date</span> <span>Expiry Date</span> <span>Tag</span></li></ul>
            <?php
                try{
                    $smtm = $conn->prepare("SELECT * FROM task WHERE user_id = ?");
                    $smtm->bind_param("i", $_SESSION['user_id']);
                    $smtm->execute();
                    $result = $smtm->get_result();
                }
                catch(Exception $e){
                    header("Location: index.php");
                    exit;
                }

               if($result->num_rows > 0){
                   echo "<ul>";
                    while($row = $result->fetch_assoc()){
                        // Default values for attributes that can be null
                        if(empty($row['description'])) $description = "No Description";
                        else $description = htmlspecialchars($row['description']);

                        if(empty($row['expiry_date'])) $date = "No Expiry Date";
                        else $date = htmlspecialchars($row['expiry_date']);

                        if(empty($row['tag'])) $tag = "No Tag";
                        else $tag = htmlspecialchars($row['tag']);

                        echo "<li>" .
                            "<span>" . htmlspecialchars($row['id']) . "</span> " .
                            "<span>" . htmlspecialchars($row['name']) . "</span> " .
                            "<span>" . $description . "</span> " .
                            "<span>" . htmlspecialchars($row['status']) . "</span> " .
                            "<span>" . htmlspecialchars($row['registration_date']) . "</span> " .
                            "<span style='color:" . (
                                    strtotime($date) && strtotime($date) < time() ? 'red' : (
                                            strtotime($date) > time() && $row['status'] == "Completed" ? 'green' : 'gray-100'
                                    )) . ";'>" . $date . "</span> " .
                            "<span>" . $tag . "</span>" .
                            "</li>";
                    }

                    echo "</ul>";
                }

                else echo "<h2>You have no tasks</h2>";

            ?>

        </div>

    </div>
</div>

<form action="index.php" method="post" class="logout">
    <input type="submit" name="logout" value="Log Out">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
    function isValidMySQLDate(string $date): bool{
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        return $d && $d->format("Y-m-d H:i:s") === $date;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['add-task'])){
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            if(!empty($_POST['expiry-date'])) {
                $expiry = str_replace('PM', '', $_POST["expiry-date"]);
                $expiry = str_replace('T', ' ', $expiry) . ':00';

                if(!isValidMySQLDate($expiry)){
                    echo "<script>alert('Invalid Expiry Date'); window.location.href='index.php';</script>";
                    exit;
                }
            }
            else{
                $expiry = null;
            }

            $tag = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_SPECIAL_CHARS);
            $user_id = (int)$_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO task (name, description, expiry_date, tag, status, user_id) VALUES (?, ?, ?, ?, 'To Start', ?)");
            $stmt->bind_param("ssssi", $name, $description, $expiry, $tag, $user_id);

            try{
                $stmt->execute();
                echo "<script>alert('Task Inserted Successfully'); window.location.href='index.php';</script>";
                exit;
            }
            catch(Exception $e){
                echo "<script>alert('Error Inserting Task'); window.location.href='index.php';</script>";
                exit;
            }

        }

        if(isset($_POST['remove-task'])){
            $task_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user_id = (int)$_SESSION['user_id'];

            try{
                $stmt = $conn->prepare("DELETE FROM task WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $task_id, $user_id);
                $stmt->execute();

                if($stmt->affected_rows <= 0){
                    echo "<script>alert('Task Not Found'); window.location.href='index.php';</script>";
                    exit;
                }

                echo "<script>alert('Task Removed Successfully'); window.location.href='index.php';</script>";
                exit;
            }
            catch (Exception $e){
                echo "<script>alert('Error Removing Task'); window.location.href='index.php';</script>";
                exit;
            }
        }

        if(isset($_POST['status-change'])){
            $task_id = (int)filter_input(INPUT_POST, 'task-id', FILTER_SANITIZE_NUMBER_INT);
            $user_id = (int)$_SESSION['user_id'];
            switch((int)$_POST['task-status']){
                case 0:
                    $status = "To Start";
                    break;

                case 1:
                    $status = "In Progress";
                    break;

                case 2:
                    $status = "Completed";
                    break;

                default:
                    echo "<script>alert('Invalid Status'); window.location.href='index.php';</script>";
                    break;
            }

            try{
                $stmt = $conn->prepare("UPDATE task SET status = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("sii", $status, $task_id, $user_id);
                $stmt->execute();

                if($stmt->affected_rows <= 0){
                    echo "<script>alert('Task ID does not exist'); window.location.href='index.php';</script>";
                }

                echo "<script>alert('Task Status Changed Successfully'); window.location.href='index.php';</script>";
                exit;
            }
            catch(Exception $e){
                echo "<script>alert('Error Updating Task'); window.location.href='index.php';</script>";
            }
        }
    }
?>