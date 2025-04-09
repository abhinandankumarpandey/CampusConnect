<?php
// announcement_details.php
session_start();
include 'db.php';

// Check if either a teacher or student is logged in
if (!isset($_SESSION['teacher_user']) && !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Set user type based on session variables
if (isset($_SESSION['teacher_user']) && $_SESSION['teacher_user']) {
    $_SESSION['user_type'] = 'teacher';
} elseif (isset($_SESSION['user']) && $_SESSION['user']) {
    $_SESSION['user_type'] = 'student';
}

// Helper function: Get username by email from teacher or users table
function get_username_by_email($conn, $email) {
    $sql = "SELECT name FROM teacher WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return $name;
        }
        mysqli_stmt_close($stmt);
    }
    $sql = "SELECT name FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $name);
        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            return $name;
        }
        mysqli_stmt_close($stmt);
    }
    return $email;
}

// Check if a valid post_id is provided
if (!isset($_GET['post_id']) || empty($_GET['post_id'])) {
    die("Invalid announcement ID.");
}
$post_id = intval($_GET['post_id']);

// Determine the logged-in user's email (teacher or student)
$user_email = isset($_SESSION['teacher_user']) ? $_SESSION['teacher_user'] : $_SESSION['user'];

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_content'])) {
    $comment_content = mysqli_real_escape_string($conn, $_POST['comment_content']);
    $parent_comment_id = (isset($_POST['parent_comment_id']) && is_numeric($_POST['parent_comment_id'])) 
        ? intval($_POST['parent_comment_id']) 
        : "NULL";

    $sql_insert = "INSERT INTO comments (post_id, email, parent_comment_id, content) 
                   VALUES ($post_id, '$user_email', " . ($parent_comment_id !== "NULL" ? $parent_comment_id : "NULL") . ", '$comment_content')";
    $result_insert = mysqli_query($conn, $sql_insert);
    if (!$result_insert) {
        echo "Error adding comment: " . mysqli_error($conn);
    } else {
        header("Location: announcement_details.php?post_id=" . $post_id);
        exit;
    }
}

// Fetch the announcement details along with the teacher's name
$sql_post = "SELECT p.*, t.name AS teacher_name 
             FROM posts p 
             LEFT JOIN teacher t ON p.teacher_id = t.id 
             WHERE p.post_id = $post_id";
$result_post = mysqli_query($conn, $sql_post);
if (!$result_post || mysqli_num_rows($result_post) == 0) {
    die("Announcement not found.");
}
$post = mysqli_fetch_assoc($result_post);

// Fetch all comments for this announcement (ordered by creation time)
$sql_comments = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY created_at ASC";
$result_comments = mysqli_query($conn, $sql_comments);
$all_comments = array();
while ($row = mysqli_fetch_assoc($result_comments)) {
    $all_comments[] = $row;
}

/**
 * Build a nested (threaded) comment tree.
 */
function buildCommentTree($comments, $parent_id = null) {
    $branch = [];
    foreach ($comments as $comment) {
        if (
            ($parent_id === null && ($comment['parent_comment_id'] === null || $comment['parent_comment_id'] == 0)) ||
            (is_numeric($parent_id) && $comment['parent_comment_id'] == $parent_id)
        ) {
            $children = buildCommentTree($comments, $comment['comment_id']);
            $comment['children'] = $children ? $children : [];
            $branch[] = $comment;
        }
    }
    return $branch;
}

/**
 * Display comments recursively with indentation.
 */
function displayComments($comments, $conn, $level = 0) {
    $indent_class = "ml-" . (30 * $level);
    foreach ($comments as $comment) {
        $commenter_name = get_username_by_email($conn, $comment['email']);
        $profile_link = "profile.php?email=" . urlencode($comment['email']);
        echo '<div class="border-t border-dashed border-gray-300 pt-2 mt-2 ' . ($level > 0 ? "ml-6" : "") . '">';
        echo '<p class="text-sm"><strong><a href="'.$profile_link.'" class="text-blue-600 hover:underline">'.htmlspecialchars($commenter_name).'</a></strong> commented on '.date("F j, Y, g:i a", strtotime($comment['created_at'])).':</p>';
        echo '<p class="whitespace-pre-line text-gray-800">'.htmlspecialchars($comment['content']).'</p>';
        echo '<p><span class="text-sm text-blue-500 cursor-pointer hover:underline" onclick="replyToComment('.$comment['comment_id'].', \''.htmlspecialchars($commenter_name).'\')">Reply</span></p>';
        if (!empty($comment['children'])) {
            displayComments($comment['children'], $conn, $level + 1);
        }
        echo '</div>';
    }
}

$comment_tree = buildCommentTree($all_comments, null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($post['title']); ?> - Announcement Details</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Include Alpine.js for simple reactivity (optional) -->
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6">
    <!-- Announcement Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="text-sm text-gray-600 mb-2">
            Posted on <?php echo date("F j, Y, g:i a", strtotime($post['date_of_post'])); ?>
            <?php if (!empty($post['teacher_name'])): ?>
                by <?php echo htmlspecialchars($post['teacher_name']); ?>
            <?php else: ?>
                by Unknown Teacher
            <?php endif; ?>
        </p>
        <p class="mb-4"><strong>Type:</strong> <?php echo ucfirst($post['announcement_type']); ?></p>
        <?php if (!empty($post['place_of_event'])): ?>
            <p class="mb-2"><strong>Event Location:</strong> <?php echo htmlspecialchars($post['place_of_event']); ?></p>
        <?php endif; ?>
        <?php if (!empty($post['date_of_event'])): ?>
            <p class="mb-2"><strong>Event Date:</strong> <?php echo date("F j, Y", strtotime($post['date_of_event'])); ?></p>
        <?php endif; ?>
        <div class="mb-4">
            <p class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
        </div>
        
        <!-- Pictures Section -->
        <?php if (!empty($post['multiple_pictures'])): ?>
            <div class="mb-4">
                <strong class="block text-gray-700 mb-2">Pictures:</strong>
                <div class="flex flex-wrap gap-4">
                    <?php 
                    $pictures = explode(",", $post['multiple_pictures']);
                    foreach ($pictures as $pic):
                        if (trim($pic) != ""):
                    ?>
                        <img src="<?php echo htmlspecialchars($pic); ?>" alt="Announcement Image" class="w-32 h-auto object-cover rounded cursor-pointer hover:opacity-75 transition" onclick="openModal('image', '<?php echo htmlspecialchars($pic); ?>')">
                    <?php 
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Video Section -->
        <?php if (!empty($post['video_related_to_post'])): ?>
            <div class="mb-4">
                <strong class="block text-gray-700 mb-2">Video:</strong>
                <div class="cursor-pointer" onclick="openModal('video', '<?php echo htmlspecialchars($post['video_related_to_post']); ?>')">
                    <video class="w-64 h-auto rounded" muted>
                        <source src="<?php echo htmlspecialchars($post['video_related_to_post']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p class="text-sm text-blue-600 mt-1 hover:underline">Tap to view full video</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Target Audience -->
        <?php if (!empty($post['specific_course']) || !empty($post['specific_block'])): ?>
            <p class="mb-0">
                <strong>Target Audience:</strong>
                <?php 
                $course = !empty($post['specific_course']) ? $post['specific_course'] : "All Courses";
                $block = !empty($post['specific_block']) ? $post['specific_block'] : "All Blocks";
                echo "$course, Block $block";
                ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Comments Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Comments</h2>
        <?php if (!empty($comment_tree)): ?>
            <?php displayComments($comment_tree, $conn, 0); ?>
        <?php else: ?>
            <p class="text-gray-600">No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>

    <!-- Comment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Add a Comment</h3>
        <form action="announcement_details.php?post_id=<?php echo $post_id; ?>" method="POST" class="space-y-4">
            <textarea id="comment_content" name="comment_content" rows="4" placeholder="Your comment..." required class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
            <input type="hidden" id="parent_comment_id" name="parent_comment_id" value="">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md shadow">Submit Comment</button>
        </form>
    </div>
  </div>

  <!-- Modal for Full Image/Video -->
  <div id="mediaModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50" onclick="closeModal()">
      <div class="relative">
          <button class="absolute top-0 right-0 m-2 text-white text-2xl font-bold" onclick="closeModal()">×</button>
          <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-screen hidden rounded">
          <video id="modalVideo" controls class="max-w-full max-h-screen hidden rounded">
              <source src="" type="video/mp4">
              Your browser does not support the video tag.
          </video>
      </div>
  </div>

  <script>
    // Function to open modal with image or video
    function openModal(type, src) {
      const modal = document.getElementById('mediaModal');
      const modalImage = document.getElementById('modalImage');
      const modalVideo = document.getElementById('modalVideo');
      
      if (type === 'image') {
        modalVideo.classList.add('hidden');
        modalVideo.pause();
        modalImage.src = src;
        modalImage.classList.remove('hidden');
      } else if (type === 'video') {
        modalImage.classList.add('hidden');
        modalVideo.querySelector('source').src = src;
        modalVideo.load();
        modalVideo.classList.remove('hidden');
      }
      modal.classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('mediaModal').classList.add('hidden');
    }

    // JavaScript function to handle replying to a comment.
    function replyToComment(commentId, username) {
        document.getElementById('parent_comment_id').value = commentId;
        document.getElementById('comment_content').placeholder = "Replying to " + username + "...";
        document.getElementById('comment_content').focus();
    }
  </script>
</body>
</html>
