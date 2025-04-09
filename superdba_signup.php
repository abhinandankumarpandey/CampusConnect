<?php
session_start();
include 'db.php'; // This file should define $conn for the database connection

$error = null; // Initialize error variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation (can be more robust)
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $error = "All fields are required.";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Sanitize form inputs AFTER basic validation
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($password !== $confirm_password) {
            $error = "Passwords do not match. Please try again.";
        } else {
            // Check if email already exists
            $check_email_sql = "SELECT email FROM super_dba WHERE email = ?"; // Only need to select one column
            $stmt_check = mysqli_prepare($conn, $check_email_sql);

            if (!$stmt_check) {
                // Log detailed error, show generic one
                error_log("Signup Prepare Error (Email Check): " . mysqli_error($conn));
                $error = "Database error occurred. Please try again later.";
            } else {
                mysqli_stmt_bind_param($stmt_check, "s", $email);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check); // Store result to check num_rows

                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    // Email already in use
                    $error = "This email address is already registered.";
                    mysqli_stmt_close($stmt_check); // Close statement early
                } else {
                    mysqli_stmt_close($stmt_check); // Close check statement

                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Check if hashing failed (though rare)
                    if ($hashed_password === false) {
                        error_log("Signup Password Hashing Failed for email: " . $email);
                        $error = "Could not process registration. Please try again.";
                    } else {
                         // Insert the new super_dba record
                        $insert_sql = "INSERT INTO super_dba (name, email, password) VALUES (?, ?, ?)";
                        $stmt_insert = mysqli_prepare($conn, $insert_sql);

                        if (!$stmt_insert) {
                             error_log("Signup Prepare Error (Insert): " . mysqli_error($conn));
                             $error = "Database error occurred during registration.";
                        } else {
                            mysqli_stmt_bind_param($stmt_insert, "sss", $name, $email, $hashed_password);
                            if (mysqli_stmt_execute($stmt_insert)) {
                                // Optionally log the user in immediately or redirect to login
                                // Redirecting to login is often preferred after signup
                                // For immediate login:
                                // session_regenerate_id(true);
                                // $_SESSION['super_dba_user'] = $email;
                                // $_SESSION['user_role'] = 'super_dba';
                                // header("Location: super_dba_homepage.php");
                                // exit;

                                // Redirect to login page with a success message (optional)
                                header("Location: super_dba_login.php?signup=success"); // Add query param
                                exit;
                            } else {
                                error_log("Signup Execute Error (Insert): " . mysqli_stmt_error($stmt_insert));
                                $error = "Error registering account. Please try again later.";
                            }
                            mysqli_stmt_close($stmt_insert); // Close insert statement
                        }
                    }
                }
            }
        }
    }
    if (isset($conn)) { mysqli_close($conn); } // Close connection if open
}

// Check for signup success message from redirect (optional)
$success_message = null;
if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
    $success_message = "Registration successful! Please log in.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super DBA Signup</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional custom styles */
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-slate-800 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="bg-white w-full max-w-md p-8 md:p-10 rounded-xl shadow-2xl">

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
           Register Super DBA Account
        </h2>

        <?php if(isset($error) && $error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
              <strong class="font-bold">Registration Failed:</strong>
              <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if(isset($success_message) && $success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
              <strong class="font-bold">Success!</strong>
              <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                    autocomplete="name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="e.g., Alex Jordan"
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; // Preserve input on error ?>"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="admin@example.com"
                     value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; // Preserve input on error ?>"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="Choose a strong password"
                >
                 <!-- Optional: Add password strength indicator here -->
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition duration-150 ease-in-out"
                    placeholder="Re-enter your password"
                >
            </div>


            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                >
                    Register Account
                </button>
            </div>
        </form>

        <!-- Link to Login Page -->
        <p class="mt-8 text-center text-sm text-gray-600">
            Already have an account?
            <a href="super_dba_login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                Log In Here
            </a>
        </p>

    </div>

</body>
</html>