<?php
// student_signup.php
session_start();
include 'db.php'; // This file should define $conn for the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $roll = mysqli_real_escape_string($conn, $_POST['roll']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $password = $_POST['password']; // Password will be hashed, so no need to escape here
    $confirm_password = $_POST['confirm_password'];

    // Validate that the password and confirm password match
    if ($password !== $confirm_password) {
        echo "Passwords do not match. Please go back and try again.";
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
        
        // Get the original file name and determine the extension
        $originalFileName = basename($profile_picture["name"]);
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        
        // Generate a unique filename; if PNG, change extension to .jpg
        $newFileName = time() . '_' . uniqid() . ($fileExtension === 'png' ? ".jpg" : "." . $fileExtension);
        $target_file = $target_dir . $newFileName;
        
        if ($fileExtension === 'png') {
            // Convert PNG to JPEG using the GD library
            $image = imagecreatefrompng($profile_picture["tmp_name"]);
            if ($image !== false) {
                // Save the image as JPEG with quality 80
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
    
    // Hash the password for secure storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an SQL statement to prevent SQL injection
    $sql = "INSERT INTO users (name, email, roll, phone, gender, course, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        echo "SQL statement preparation failed: " . mysqli_error($conn);
        exit;
    }
    
    // Bind the parameters to the SQL query
    mysqli_stmt_bind_param($stmt, "ssssssss", $name, $email, $roll, $phone, $gender, $course, $hashed_password, $target_file);
    
    // Execute the statement and check for success
    if (mysqli_stmt_execute($stmt)) {
        // Set session variables and redirect to index.php
        $_SESSION['user'] = $email;
        $_SESSION['user_type'] = 'student';
        header("Location: index.php");
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
  <title>Student Registration</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen flex items-center">
  <div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Student Registration</h2>
    <form action="student_signup.php" method="POST" enctype="multipart/form-data" class="space-y-5">
      <div>
        <label for="name" class="block text-gray-700 font-medium">Full Name</label>
        <input type="text" id="name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="email" class="block text-gray-700 font-medium">Email</label>
        <input type="email" id="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="roll" class="block text-gray-700 font-medium">Class Roll No</label>
        <input type="text" id="roll" name="roll" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="phone" class="block text-gray-700 font-medium">Phone No</label>
        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="gender" class="block text-gray-700 font-medium">Gender</label>
        <select name="gender" id="gender" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="prefer_not_to_say">Prefer not to say</option>
        </select>
      </div>
      <div>
        <label for="course" class="block text-gray-700 font-medium">Course</label>
        <select id="course" name="course" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <option value="BA">BA</option>
          <option value="BCA">BCA</option>
          <option value="BBA">BBA</option>
          <option value="MSc">MSc</option>
          <option value="MCOM">Mcom</option>
        </select>
      </div>
      <div>
        <label for="password" class="block text-gray-700 font-medium">Password</label>
        <input type="password" id="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="confirm_password" class="block text-gray-700 font-medium">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label for="profile_picture" class="block text-gray-700 font-medium">Profile Picture</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required class="mt-1 block w-full text-gray-900">
      </div>
      <div>
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow">Submit</button>
      </div>
    </form>
  </div>
</body>
</html>
