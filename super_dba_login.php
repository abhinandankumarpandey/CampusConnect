<?php
//super_dba_login.php;
session_start();
include 'db.php'; // This file should define $conn for the database connection

$error = null; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation/sanitization (Consider more robust validation)
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Email and password are required.";
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password']; // Password will be verified using password_verify

        // Prepare SQL statement to fetch the user with the given email
        $sql = "SELECT * FROM super_dba WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            // More informative error for debugging, but careful in production
            $error = "Database error: Could not prepare statement. " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if a user with the provided email exists
            if ($result && mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                // Verify the password with the hashed password stored in the database
                // Ensure the 'password' column exists and contains a valid hash
                if (isset($user['password']) && password_verify($password, $user['password'])) {
                    // Regenerate session ID upon successful login for security
                    session_regenerate_id(true);
                    $_SESSION['super_dba_user'] = $user['email']; // Store email or user ID
                    $_SESSION['user_role'] = 'super_dba'; // Set role
                    header("Location: super_dba_homepage.php");
                    exit;
                } else {
                    $error = "Invalid credentials provided."; // Generic error
                }
            } else {
                $error = "Invalid credentials provided."; // Generic error
            }
            mysqli_stmt_close($stmt); // Close statement
        }
        mysqli_close($conn); // Close connection
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super DBA Login - Secure Access</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Optional: Add custom base styles or component styles here if needed */
        /* For instance, a subtle background pattern or gradient */
        body {
            /* Example subtle gradient background */
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-900 to-slate-800 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="bg-white w-full max-w-md p-8 md:p-10 rounded-xl shadow-2xl">

        <!-- Logo Placeholder -->
        <!-- <div class="text-center mb-6">
            <img src="/path/to/your/logo.svg" alt="Logo" class="h-12 w-auto inline-block">
        </div> -->

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            Super DBA Access Portal
        </h2>

        <?php if (isset($error) && $error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Login Failed:</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error); // Escape output 
                                                ?></span>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); // Prevent XSS 
                        ?>" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="admin@example.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="••••••••">
                <!-- Optional: Add forgot password link here -->
                <!-- <div class="text-right mt-1">
                     <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                 </div> -->
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Secure Login
                </button>
            </div>
        </form>

        <!-- Optional Footer Note -->
        <p class="mt-8 text-center text-xs text-gray-500">
            Access restricted to authorized personnel only.
        </p>

    </div>

</body>

</html>