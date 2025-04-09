<?php
session_start();
include 'db.php'; // This file should define $conn for the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize email input and retrieve the password
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Prepare an SQL statement to fetch teacher details by email
    $sql = "SELECT * FROM teacher WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        echo "SQL statement preparation failed: " . mysqli_error($conn);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the teacher exists
    if (mysqli_num_rows($result) == 1) {
        $teacher = mysqli_fetch_assoc($result);
        
        // Verify the password with the hashed password stored in the database
        if (password_verify($password, $teacher['password'])) {
            $_SESSION['teacher_user'] = $email;
            $_SESSION['user_type'] = 'teacher';
            header("Location: teacher_homepage.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <!-- Main Content Container -->
    <div class="max-w-md w-full p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Teacher Login</h2>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="teacher_login.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow">Login</button>
            </div>
        </form>
        <p class="mt-4 text-center">
            <a href="teacher_signup.php" class="text-blue-600 hover:underline">Sign Up</a> &nbsp;  &nbsp; &nbsp;
            <a href="index.php" class="text-blue-600 hover:underline">Landing page</a>
        </p>
    </div>
</body>
</html>
