<?php
// teacher_post_form.php
session_start();
include 'db.php'; // This file should define $conn for the database connection

// Check if the teacher is logged in; if not, redirect to the teacher login page.
if (!isset($_SESSION['teacher_user'])) {
    header("Location: teacher_login.php");
    exit;
}
$_SESSION['user_type'] = 'teacher';
$message = "";
$error = "";

// Retrieve teacher's email from session
$teacher_email = $_SESSION['teacher_user'];

// Query the teacher table to get the teacher's id based on the email
$sqlTeacher = "SELECT id FROM teacher WHERE email = ?";
$stmtTeacher = mysqli_prepare($conn, $sqlTeacher);
if ($stmtTeacher) {
    mysqli_stmt_bind_param($stmtTeacher, "s", $teacher_email);
    mysqli_stmt_execute($stmtTeacher);
    mysqli_stmt_bind_result($stmtTeacher, $teacher_id);
    mysqli_stmt_fetch($stmtTeacher);
    mysqli_stmt_close($stmtTeacher);
} else {
    $error = "Error retrieving teacher ID: " . mysqli_error($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $announcement_type = mysqli_real_escape_string($conn, $_POST['announcement_type']);
    $title             = mysqli_real_escape_string($conn, $_POST['title']);
    $description       = mysqli_real_escape_string($conn, $_POST['description']);
    $place_of_event    = mysqli_real_escape_string($conn, $_POST['place_of_event']);
    $date_of_event     = mysqli_real_escape_string($conn, $_POST['date_of_event']);
    $specific_course   = mysqli_real_escape_string($conn, $_POST['specific_course']);
    $specific_block    = mysqli_real_escape_string($conn, $_POST['specific_block']);
    
    // Handle multiple pictures upload with PNG conversion logic
    $uploaded_files = array();
    if (isset($_FILES['multiple_pictures']) && $_FILES['multiple_pictures']['error'][0] == 0) {
        $files = $_FILES['multiple_pictures'];
        $target_dir = "uploads/posts/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        for ($i = 0; $i < count($files['name']); $i++) {
            $originalFileName = basename($files['name'][$i]);
            $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            // Create a new unique filename. If PNG, change extension to .jpg.
            $newFileName = time() . "_" . uniqid() . ($fileExtension === 'png' ? ".jpg" : "." . $fileExtension);
            $target_file = $target_dir . $newFileName;
            
            if ($fileExtension === 'png') {
                // Convert PNG to JPEG using GD library
                $image = imagecreatefrompng($files['tmp_name'][$i]);
                if ($image !== false) {
                    // Save image as JPEG with quality 80
                    if (imagejpeg($image, $target_file, 80)) {
                        $uploaded_files[] = $target_file;
                    } else {
                        $error .= "Error converting PNG to JPEG for file: " . htmlspecialchars($originalFileName) . ". ";
                    }
                    imagedestroy($image);
                } else {
                    $error .= "Error reading PNG file: " . htmlspecialchars($originalFileName) . ". ";
                }
            } else {
                // For non-PNG files, move the uploaded file as is
                if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                    $uploaded_files[] = $target_file;
                } else {
                    $error .= "Error uploading file: " . htmlspecialchars($originalFileName) . ". ";
                }
            }
        }
    }
    // Store uploaded picture paths as a comma-separated string
    $multiple_pictures = implode(",", $uploaded_files);

    // Handle video file upload if provided
    $video_related_to_post = "";
    if (isset($_FILES['video_related_to_post']) && $_FILES['video_related_to_post']['error'] == 0) {
        $video = $_FILES['video_related_to_post'];
        $target_dir_video = "uploads/videos/";
        if (!is_dir($target_dir_video)) {
            mkdir($target_dir_video, 0777, true);
        }
        $video_filename = time() . "_" . basename($video['name']);
        $target_file_video = $target_dir_video . $video_filename;
        if (move_uploaded_file($video['tmp_name'], $target_file_video)) {
            $video_related_to_post = $target_file_video;
        } else {
            $error .= "Error uploading video file. ";
        }
    }
    
    // Insert the announcement into the posts table with the retrieved teacher_id.
    $sql = "INSERT INTO posts (announcement_type, title, description, place_of_event, date_of_event, multiple_pictures, video_related_to_post, specific_course, specific_block, teacher_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $error .= "SQL prepare error: " . mysqli_error($conn);
    } else {
        // Bind parameters including teacher_id as an integer ("i")
        mysqli_stmt_bind_param($stmt, "sssssssssi", $announcement_type, $title, $description, $place_of_event, $date_of_event, $multiple_pictures, $video_related_to_post, $specific_course, $specific_block, $teacher_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Announcement posted successfully!";
            // After successful posting, redirect to "teacher_homepage.php"
            header("Location: teacher_homepage.php");
            exit; // Prevent further execution after redirect
        } else {
            $error .= "Error inserting announcement: " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-purple-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-white font-bold text-xl">Super DBA</span>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="teacher_homepage.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="teacher_post_form.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Post Announcement</a>
                            <a href="logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="max-w-3xl mx-auto mt-10 p-8 bg-white rounded-lg shadow-xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Post Announcement</h1>

        <!-- Display messages -->
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="teacher_post_form.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="announcement_type" class="block text-sm font-medium text-gray-700">Announcement Type:</label>
                <select name="announcement_type" id="announcement_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="academic">Academic</option>
                    <option value="job">Job</option>
                    <option value="competition">Competition</option>
                    <option value="sport">Sport</option>
                    <option value="fees">Fees</option>
                    <option value="exams">Exams</option>
                    <option value="general">General</option>
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                <input type="text" id="title" name="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea id="description" name="description" rows="5" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <div>
                <label for="place_of_event" class="block text-sm font-medium text-gray-700">Place of Event:</label>
                <input type="text" id="place_of_event" name="place_of_event" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="date_of_event" class="block text-sm font-medium text-gray-700">Date of Event:</label>
                <input type="date" id="date_of_event" name="date_of_event" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="multiple_pictures" class="block text-sm font-medium text-gray-700">Upload Multiple Pictures:</label>
                <input type="file" id="multiple_pictures" name="multiple_pictures[]" accept="image/*" multiple class="mt-1 block w-full text-gray-900">
            </div>

            <div>
                <label for="video_related_to_post" class="block text-sm font-medium text-gray-700">Upload Video (optional):</label>
                <input type="file" id="video_related_to_post" name="video_related_to_post" accept="video/*" class="mt-1 block w-full text-gray-900">
            </div>

            <div>
                <label for="specific_course" class="block text-sm font-medium text-gray-700">Specific Course:</label>
                <select name="specific_course" id="specific_course" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="ALL">All</option>
                    <option value="BA">BA</option>
                    <option value="BCA">BCA</option>
                    <option value="BBA">BBA</option>
                    <option value="MSC">MSC</option>
                    <option value="MCOM">MCOM</option>
                </select>
            </div>

            <div>
                <label for="specific_block" class="block text-sm font-medium text-gray-700">Specific Block:</label>
                <select name="specific_block" id="specific_block" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="ALL">All</option>
                    <option value="A">Block A</option>
                    <option value="B">Block B</option>
                    <option value="C">Block C</option>
                    <option value="D">Block D</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow">Post Announcement</button>
            </div>
        </form>
    </div>
</body>
</html>
