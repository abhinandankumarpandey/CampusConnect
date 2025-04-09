<?php
//557c9ce08f
session_start();
include 'db.php'; // This file should define $conn for the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        echo "Passwords do not match. Please go back and try again.";
        exit;
    }

    // Check if the token exists and is not activated
    $token_query = "SELECT * FROM teacher_token WHERE token = '$token' AND activated_token = 0 LIMIT 1";
    $token_result = mysqli_query($conn, $token_query);

    if (mysqli_num_rows($token_result) == 0) {
        echo "Invalid or already used token.";
        exit;
    }

    // Handle the file upload for profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $_FILES['profile_picture'];
        $target_dir = "uploads/";

        // Create the uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Get file extension and original filename
        $originalFileName = basename($profile_picture["name"]);
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        // Generate a unique filename; if PNG, change extension to .jpg
        $newFileName = time() . '_' . uniqid() . ($fileExtension === 'png' ? ".jpg" : "." . $fileExtension);
        $target_file = $target_dir . $newFileName;

        if ($fileExtension === 'png') {
            // Convert PNG to JPEG using the GD library
            $image = imagecreatefrompng($profile_picture["tmp_name"]);
            if ($image !== false) {
                // Save as JPEG with quality 80
                if (!imagejpeg($image, $target_file, 80)) {
                    echo "Error converting PNG to JPEG.";
                    imagedestroy($image);
                    exit;
                }
                imagedestroy($image);
            } else {
                echo "Error reading PNG file.";
                exit;
            }
        } else {
            // For non-PNG images, simply move the uploaded file
            if (!move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
                echo "Error uploading the profile picture.";
                exit;
            }
        }
    } else {
        echo "Profile picture is required.";
        exit;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert teacher details into the database
    $sql = "INSERT INTO teacher (name, email, phone, gender, password, profile_picture, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "SQL statement preparation failed: " . mysqli_error($conn);
        exit;
    }

    // Bind parameters and execute statement
    mysqli_stmt_bind_param($stmt, "sssssss", $name, $email, $phone, $gender, $hashed_password, $target_file, $token);

    if (mysqli_stmt_execute($stmt)) {
        // Mark the token as used
        $update_token_query = "UPDATE teacher_token SET activated_token = 1 WHERE token = '$token'";
        mysqli_query($conn, $update_token_query);

        // Redirect to homepage after successful registration
        $_SESSION['teacher_user'] = $email;
        $_SESSION['user_type'] = 'teacher';
        header("Location: teacher_homepage.php");
        exit;
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Registration</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Main Content Container -->
    <div class="max-w-4xl mx-auto p-4">
        <header class="my-6">
            <h2 class="text-3xl font-bold text-gray-800">Teacher Registration Form</h2>
            <p class="text-gray-600 mt-2">Fill in your details to create your account.</p>
        </header>

        <!-- Registration Form -->
        <div class="bg-white shadow rounded p-6">
            <form action="teacher_signup.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name:</label>
                    <input type="text" id="name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone No:</label>
                    <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender:</label>
                    <select name="gender" id="gender" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="prefer_not_to_say">Prefer not to say</option>
                    </select>
                </div>

                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700">Token:</label>
                    <input type="text" id="token" name="token" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture:</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required class="mt-1 block w-full text-gray-900">
                </div>

                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
