<?php
// edit_profile.php
session_start();
include 'db.php';

$message = "";
$error = "";
$profileType = "";

// Determine if a teacher or a student is logged in.
if (isset($_SESSION['teacher_user'])) {
    // Teacher is logged in (session holds teacher email).
    $profileType = "teacher";
    $email = $_SESSION['teacher_user'];
    
    // Retrieve teacher data (join teacher_profile for additional details).
    $sql = "SELECT t.name, t.email, t.phone, t.profile_picture, 
                   tp.description, tp.block, tp.subjects
            FROM teacher t 
            LEFT JOIN teacher_profile tp ON t.id = tp.teacher_id 
            WHERE t.email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $profile = mysqli_fetch_assoc($result);
    } else {
        $error = "Profile not found.";
    }
    mysqli_stmt_close($stmt);
    
} elseif (isset($_SESSION['student_user'])) {
    // Student is logged in (session holds student email).
    $profileType = "student";
    $email = $_SESSION['student_user'];
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $profile = mysqli_fetch_assoc($result);
    } else {
        $error = "Profile not found.";
    }
    mysqli_stmt_close($stmt);
} else {
    // If no user is logged in, redirect to the login page.
    header("Location: login.php");
    exit;
}

// Function to handle image upload and conversion logic.
function processProfilePictureUpload($file) {
    $target_dir = "uploads/profiles/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $originalFileName = basename($file['name']);
    $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    // Generate a unique filename; if PNG, change extension to .jpg.
    $newFileName = time() . "_" . uniqid() . ($fileExtension === 'png' ? ".jpg" : "." . $fileExtension);
    $target_file = $target_dir . $newFileName;
    
    if ($fileExtension === 'png') {
        // Convert PNG to JPEG using the GD library.
        $image = imagecreatefrompng($file["tmp_name"]);
        if ($image !== false) {
            if (!imagejpeg($image, $target_file, 80)) {
                imagedestroy($image);
                return false;
            }
            imagedestroy($image);
        } else {
            return false;
        }
    } else {
        // For non-PNG images, simply move the uploaded file.
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            return false;
        }
    }
    return $target_file;
}

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($profileType == "teacher") {
        // Sanitize teacher input.
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $block = mysqli_real_escape_string($conn, $_POST['block']);
        $subjects = mysqli_real_escape_string($conn, $_POST['subjects']);
        
        // Handle profile picture upload.
        $profile_picture = $profile['profile_picture']; // Default value.
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $processedFile = processProfilePictureUpload($_FILES['profile_picture']);
            if ($processedFile !== false) {
                $profile_picture = $processedFile;
            }
        }
        
        // Update teacher table.
        $sql_update_teacher = "UPDATE teacher SET name = ?, phone = ?, profile_picture = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql_update_teacher);
        mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $profile_picture, $email);
        if (!mysqli_stmt_execute($stmt)) {
            $error = "Error updating teacher info: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
        
        // Get teacher_id.
        $sql_id = "SELECT id FROM teacher WHERE email = ?";
        $stmt_id = mysqli_prepare($conn, $sql_id);
        mysqli_stmt_bind_param($stmt_id, "s", $email);
        mysqli_stmt_execute($stmt_id);
        mysqli_stmt_bind_result($stmt_id, $teacher_id);
        mysqli_stmt_fetch($stmt_id);
        mysqli_stmt_close($stmt_id);
        
        // Update teacher_profile table (insert if not exists).
        $sql_check = "SELECT * FROM teacher_profile WHERE teacher_id = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "i", $teacher_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($result_check) > 0) {
            // Update existing record.
            $sql_update_profile = "UPDATE teacher_profile SET description = ?, block = ?, subjects = ? WHERE teacher_id = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update_profile);
            mysqli_stmt_bind_param($stmt_update, "sssi", $description, $block, $subjects, $teacher_id);
            if (!mysqli_stmt_execute($stmt_update)) {
                $error = "Error updating teacher profile: " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            // Insert new record.
            $sql_insert_profile = "INSERT INTO teacher_profile (teacher_id, description, block, subjects) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $sql_insert_profile);
            mysqli_stmt_bind_param($stmt_insert, "isss", $teacher_id, $description, $block, $subjects);
            if (!mysqli_stmt_execute($stmt_insert)) {
                $error = "Error inserting teacher profile: " . mysqli_stmt_error($stmt_insert);
            }
            mysqli_stmt_close($stmt_insert);
        }
        $message = "Profile updated successfully!";
        header("Location: profile.php?email=" . urlencode($email));
        exit;
        
    } else {
        // Student profile update.
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $roll = mysqli_real_escape_string($conn, $_POST['roll']);
        $course = mysqli_real_escape_string($conn, $_POST['course']);
        
        // Handle profile picture update.
        $profile_picture = $profile['profile_picture'];
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $processedFile = processProfilePictureUpload($_FILES['profile_picture']);
            if ($processedFile !== false) {
                $profile_picture = $processedFile;
            }
        }
        
        // Update student profile in the users table.
        $sql_update_student = "UPDATE users SET name = ?, phone = ?, roll = ?, course = ?, profile_picture = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql_update_student);
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $phone, $roll, $course, $profile_picture, $email);
        if (!mysqli_stmt_execute($stmt)) {
            $error = "Error updating student profile: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
        $message = "Profile updated successfully!";
        header("Location: profile.php?email=" . urlencode($email));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <style>
    .container { width: 80%; margin: 0 auto; }
    form { border: 1px solid #ccc; padding: 20px; }
    label { display: block; margin-top: 10px; }
    input[type="text"], input[type="email"], input[type="file"], textarea, select {
      width: 100%; padding: 8px;
    }
    button { margin-top: 15px; padding: 10px 20px; }
    .message { color: green; }
    .error { color: red; }
  </style>
</head>
<body>
<div class="container">
  <h1>Edit Profile</h1>
  <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
  <?php endif; ?>
  <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($profile['name']); ?>">
    
    <!-- Display email as read-only and include it as a hidden input -->
    <label for="email">Email (not editable):</label>
    <input type="email" id="email_display" disabled value="<?php echo htmlspecialchars($profile['email']); ?>">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>">
    
    <label for="phone">Contact:</label>
    <input type="text" id="phone" name="phone" required value="<?php echo htmlspecialchars($profile['phone']); ?>">
    
    <label for="profile_picture">Profile Picture (upload to change):</label>
    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
    
    <?php if ($profileType == "teacher"): ?>
      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($profile['description']); ?></textarea>
      
      <label for="block">Working Block:</label>
      <input type="text" id="block" name="block" value="<?php echo htmlspecialchars($profile['block']); ?>">
      
      <label for="subjects">Subjects Teaching (comma-separated):</label>
      <input type="text" id="subjects" name="subjects" value="<?php echo htmlspecialchars($profile['subjects']); ?>">
    <?php else: ?>
      <label for="roll">Roll:</label>
      <input type="text" id="roll" name="roll" required value="<?php echo htmlspecialchars($profile['roll']); ?>">
      
      <label for="course">Course/Subject Studying:</label>
      <input type="text" id="course" name="course" required value="<?php echo htmlspecialchars($profile['course']); ?>">
    <?php endif; ?>
    
    <button type="submit">Update Profile</button>
  </form>
</div>
</body>
</html>
