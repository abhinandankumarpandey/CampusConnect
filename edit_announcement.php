<?php
session_start();
include 'db.php';

if (!isset($_SESSION['teacher_user'])) {
    header("Location: teacher_login.php");
    exit;
}
$_SESSION['user_type'] = 'teacher';
$message = "";
$error = "";

$teacher_email = $_SESSION['teacher_user'];

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

if (!isset($_GET['post_id']) || empty($_GET['post_id'])) {
    die("Invalid post ID.");
}
$post_id = intval($_GET['post_id']);

$sqlPost = "SELECT * FROM posts WHERE post_id = ? AND teacher_id = ?";
$stmtPost = mysqli_prepare($conn, $sqlPost);
if ($stmtPost) {
    mysqli_stmt_bind_param($stmtPost, "ii", $post_id, $teacher_id);
    mysqli_stmt_execute($stmtPost);
    $resultPost = mysqli_stmt_get_result($stmtPost);
    if (mysqli_num_rows($resultPost) == 0) {
        die("Announcement not found or you are not authorized to edit this post.");
    }
    $post = mysqli_fetch_assoc($resultPost);
    mysqli_stmt_close($stmtPost);
} else {
    die("Error retrieving post: " . mysqli_error($conn));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $announcement_type = mysqli_real_escape_string($conn, $_POST['announcement_type']);
    $title             = mysqli_real_escape_string($conn, $_POST['title']);
    $description       = mysqli_real_escape_string($conn, $_POST['description']);
    $place_of_event    = mysqli_real_escape_string($conn, $_POST['place_of_event']);
    $date_of_event     = mysqli_real_escape_string($conn, $_POST['date_of_event']);
    $specific_course   = mysqli_real_escape_string($conn, $_POST['specific_course']);
    $specific_block    = mysqli_real_escape_string($conn, $_POST['specific_block']);

    $multiple_pictures = $post['multiple_pictures'];
    if (isset($_FILES['multiple_pictures']) && $_FILES['multiple_pictures']['error'][0] == 0) {
        $uploaded_files = array();
        $files = $_FILES['multiple_pictures'];
        $target_dir = "uploads/posts/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        for ($i = 0; $i < count($files['name']); $i++) {
            $filename = time() . "_" . basename($files['name'][$i]);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($files['tmp_name'][$i], $target_file)) {
                $uploaded_files[] = $target_file;
            }
        }
        if (!empty($uploaded_files)) {
            $multiple_pictures = implode(",", $uploaded_files);
        }
    }

    $video_related_to_post = $post['video_related_to_post'];
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
        }
    }

    $sqlUpdate = "UPDATE posts SET announcement_type = ?, title = ?, description = ?, place_of_event = ?, date_of_event = ?, multiple_pictures = ?, video_related_to_post = ?, specific_course = ?, specific_block = ? WHERE post_id = ? AND teacher_id = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
    if (!$stmtUpdate) {
        $error = "SQL prepare error: " . mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmtUpdate, "sssssssssii", $announcement_type, $title, $description, $place_of_event, $date_of_event, $multiple_pictures, $video_related_to_post, $specific_course, $specific_block, $post_id, $teacher_id);
        if (mysqli_stmt_execute($stmtUpdate)) {
            $message = "Announcement updated successfully!";
            header("Location: teacher_homepage.php");
            exit;
        } else {
            $error = "Error updating announcement: " . mysqli_stmt_error($stmtUpdate);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Announcement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-2xl">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Edit Announcement</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 text-green-700 border border-green-400 rounded p-4 mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 border border-red-400 rounded p-4 mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="edit_announcement.php?post_id=<?php echo $post_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Announcement Type</label>
                <select name="announcement_type" class="w-full p-2 border rounded" required>
                    <?php
                    $types = ["academic", "job", "competition", "sport", "fees", "exams", "general"];
                    foreach ($types as $type) {
                        $selected = ($post['announcement_type'] === $type) ? 'selected' : '';
                        echo "<option value='$type' $selected>" . ucfirst($type) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" class="w-full p-2 border rounded" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Description</label>
                <textarea name="description" rows="5" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($post['description']); ?></textarea>
            </div>

            <div>
                <label class="block mb-1 font-medium">Place of Event</label>
                <input type="text" name="place_of_event" value="<?php echo htmlspecialchars($post['place_of_event']); ?>" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block mb-1 font-medium">Date of Event</label>
                <input type="date" name="date_of_event" value="<?php echo htmlspecialchars($post['date_of_event']); ?>" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block mb-1 font-medium">Update Pictures</label>
                <input type="file" name="multiple_pictures[]" multiple class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block mb-1 font-medium">Update Video</label>
                <input type="file" name="video_related_to_post" class="w-full p-2 border rounded">
            </div>

            <div>
                <label class="block mb-1 font-medium">Specific Course</label>
                <select name="specific_course" class="w-full p-2 border rounded" required>
                    <?php
                    $courses = ["ALL", "BA", "BCA", "BBA", "MSC", "MCOM"];
                    foreach ($courses as $course) {
                        $selected = ($post['specific_course'] == $course) ? 'selected' : '';
                        echo "<option value='$course' $selected>$course</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Specific Block</label>
                <select name="specific_block" class="w-full p-2 border rounded" required>
                    <?php
                    $blocks = ["ALL", "A", "B", "C", "D"];
                    foreach ($blocks as $block) {
                        $selected = ($post['specific_block'] == $block) ? 'selected' : '';
                        echo "<option value='$block' $selected>Block $block</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition duration-300">
                Update Announcement
            </button>
        </form>
    </div>

</body>
</html>
5