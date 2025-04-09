<?php
session_start();
include 'db.php'; // Assumes $conn is defined

// Check if a user is logged in
if (!isset($_SESSION['teacher_user']) && !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Determine the user type and email
if (isset($_SESSION['teacher_user'])) {
    $user_email = $_SESSION['teacher_user'];
    $user_type = "teacher";
} elseif (isset($_SESSION['user'])) {
    $user_email = $_SESSION['user'];
    $user_type = "student";
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirmation do not match.";
    } else {
        // Set the table based on the user type
        $table = ($user_type === "teacher") ? "teacher" : "users";

        // Fetch the current hashed password from the database
        $sql = "SELECT password FROM $table WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $user_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $db_hashed_password = $row['password'];
            // Verify the current password using password_verify
            if (!password_verify($current_password, $db_hashed_password)) {
                $message = "Current password is incorrect.";
            } else {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Update the password in the database
                $update_sql = "UPDATE $table SET password = ? WHERE email = ?";
                $stmt_update = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($stmt_update, "ss", $new_hashed_password, $user_email);
                if (mysqli_stmt_execute($stmt_update)) {
                    $message = "Password updated successfully.";
                } else {
                    $message = "Error updating password. Please try again.";
                }
                mysqli_stmt_close($stmt_update);
            }
        } else {
            $message = "User not found.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <style>
    body { font-family: Arial, sans-serif; }
    .container { width: 400px; margin: 50px auto; }
    form { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
    input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; }
    input[type="submit"] { padding: 10px 20px; background-color: #4CAF50; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    .message { margin: 10px 0; color: red; }
    .button {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Change Password</h2>
  <?php if (!empty($message)): ?>
    <p class="message"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>
  <form action="change_password.php" method="post">
    <label for="current_password">Current Password:</label>
    <input type="password" name="current_password" id="current_password" required>
    
    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" id="new_password" required>
    
    <label for="confirm_password">Confirm New Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" required>
    
    <input type="submit" value="Change Password">
  </form>
  <br>
  <a class="button" href="profile.php?email=<?php echo urlencode($user_email); ?>">Back to Profile</a>
</div>
</body>
</html>
