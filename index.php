<?php
session_start();
include 'db.php';

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: L&R.php');
    exit();
}

// Handle adding a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['description']) && empty($_POST['update_task_id'])) {
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id']; // User ID from session

    $query = $conn->prepare('INSERT INTO tasks (description, user_id, status) VALUES (?, ?, "pending")');
    $query->bind_param('si', $description, $user_id);
    $query->execute();
    
    header('Location: index.php'); // Redirect to main page after task is added
    exit(); // Prevent further code execution
}

// Handle deleting a task
if (isset($_GET['del_task'])) {
    $query = $conn->prepare('DELETE FROM tasks WHERE task_id = ?');
    $query->bind_param('i', $_GET['del_task']);
    $query->execute();
    header('Location: index.php');
    exit(); // Prevent further code execution
}

// Handle marking a task as done
if (isset($_GET['done_task'])) {
    $query = $conn->prepare('UPDATE tasks SET status = "done" WHERE task_id = ?');
    $query->bind_param('i', $_GET['done_task']);
    $query->execute();
    header('Location: index.php');
    exit(); // Prevent further code execution
}

// Handle updating a task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task_id']) && !empty($_POST['description'])) {
    $description = $_POST['description'];
    $task_id = $_POST['update_task_id'];

    $query = $conn->prepare('UPDATE tasks SET description = ? WHERE task_id = ?');
    $query->bind_param('si', $description, $task_id);
    $query->execute();
    
    header('Location: index.php'); // Redirect to main page after update
    exit(); // Prevent further code execution
}

// Fetch tasks for the logged-in user
function fetchTasks($conn, $user_id) {
    $query = $conn->prepare('SELECT * FROM tasks WHERE user_id = ?');
    $query->bind_param('i', $user_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Ensure username is set for the dashboard
$username = isset($_SESSION['user']) ? $_SESSION['user'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO-LIST</title>
    <link rel="icon" href="favicon/check.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .done-task {
            text-decoration: line-through;
            color: grey;
        }
        @media (max-width: 408px) {
            .mt-small {
                margin-top: 16px; /* Adjust this value as needed */
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        <header class="d-flex justify-content-between p-3 align-items-center">
            <div class="d-flex align-items-center">
                <span id='app_name' style="font-size: 24px; font-weight: bold; color: #333;">To-do List</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="user-info position-relative" id="userInfoDropdown" style="cursor: pointer;">
                   
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTF5-3YjBcXTqKUlOAeUUtuOLKgQSma2wGG1g&s" alt="User Avatar" class="rounded-circle" width="40" height="40">

                    <span><?php echo htmlspecialchars($username); ?></span>
                    <i class="fas fa-chevron-down"></i>
                    <div class="dropdown-menu position-absolute bg-white p-2 shadow-sm" id="profileDropdown" style="display: none; top: 100%; right: 0;">
                        <a href="profile.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        <a href="change_password.php" class="dropdown-item"><i class="fas fa-lock"></i> Change Password</a>
                        <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <section class="container mt-5">
            <h1><?php echo htmlspecialchars($username); ?>!</h1>
            <p>This is your dashboard where you can manage your tasks.</p>

            <div>
                <form action="index.php" method='post' id='add_task_form' class="mb-4">
                    <input type="text" name='description' placeholder='Task Description' id='description' required class="form-control" style="width: 300px; display: inline-block;">
                    <input type="submit" value='Add Task' id='add_task_submit' class="btn btn-primary mt-small">
                </form>
            </div>

            <div id="task_list">
                <?php
                $tasks = fetchTasks($conn, $_SESSION['user_id']);
                if (count($tasks) > 0) {
                    foreach ($tasks as $row) {
                        $isDone = ($row['status'] === "done") ? 'done-task' : '';
                        ?>
                        <div class='task_container mb-3 d-flex align-items-center'>
                            <?php if (isset($_GET['edit_task']) && $_GET['edit_task'] == $row['task_id']) { ?>
                                <!-- Edit Task Form -->
                                <form action="index.php" method="POST" class="d-flex align-items-center flex-grow-1">
                                    <input type="text" name="description" required class="form-control me-2" value="<?php echo htmlspecialchars($row['description']); ?>" style="width: 300px;">
                                    <input type="hidden" name="update_task_id" value="<?php echo $row['task_id']; ?>">
                                    <button type="submit" class="btn btn-success me-2"><i class="fas fa-check"></i> Update</button>
                                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                                </form>
                            <?php } else { ?>
                                <p class="flex-grow-1 mb-0 <?php echo $isDone; ?>"><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="index.php?done_task=<?php echo $row['task_id']; ?>" class="btn btn-success btn-sm me-2"><i class="fas fa-check"></i> Done</a>
                                <a href="index.php?del_task=<?php echo $row['task_id']; ?>" class="btn btn-danger btn-sm me-2"><i class="fas fa-trash"></i> Delete</a>
                                <a href="index.php?edit_task=<?php echo $row['task_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <?php } ?>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No tasks available.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userInfoDropdown = document.getElementById('userInfoDropdown');
            const profileDropdown = document.getElementById('profileDropdown');

            userInfoDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.style.display = profileDropdown.style.display === 'none' || !profileDropdown.style.display ? 'block' : 'none';
            });

            document.addEventListener('click', function() {
                profileDropdown.style.display = 'none';
            });
        });
    </script>
</body>
</html>
