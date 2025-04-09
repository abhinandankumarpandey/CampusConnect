<?php
// profile.php 
session_start();
include 'db.php'; // Assumes $conn is defined

// Ensure an email is provided via GET parameter
if (!isset($_GET['email']) || empty($_GET['email'])) {
    die("No profile specified.");
}
$email = $_GET['email'];

// Attempt to retrieve a teacher profile first
$sql = "SELECT t.*, tp.description, tp.block, tp.subjects 
        FROM teacher t 
        LEFT JOIN teacher_profile tp ON t.id = tp.teacher_id 
        WHERE t.email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    // Found a teacher profile
    $profile = mysqli_fetch_assoc($result);
    $profile_type = "teacher";

    // Retrieve posts made by this teacher
    $teacher_id = $profile['id'];
    $sql_posts = "SELECT * FROM posts WHERE teacher_id = ? ORDER BY date_of_post DESC";
    $stmt_posts = mysqli_prepare($conn, $sql_posts);
    mysqli_stmt_bind_param($stmt_posts, "i", $teacher_id);
    mysqli_stmt_execute($stmt_posts);
    $result_posts = mysqli_stmt_get_result($stmt_posts);
    $posts = [];
    while ($row = mysqli_fetch_assoc($result_posts)) {
        $posts[] = $row;
    }
    mysqli_stmt_close($stmt_posts);
} else {
    // If not a teacher, check for a student profile in the users table
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $profile = mysqli_fetch_assoc($result);
        $profile_type = "student";
    } else {
        die("Profile not found.");
    }
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($profile['name']); ?>'s Profile</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-400">
  <div class="flex items-center justify-center min-h-screen flex-col space-y-10">
    <!-- Large Filled Profile Card -->
    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-4xl w-full transform transition duration-500 hover:scale-105">
      <div class="flex flex-col md:flex-row items-center">
        <div class="flex-shrink-0">
          <img class="h-40 w-40 rounded-full object-cover border-8 border-indigo-500" src="<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture">
        </div>
        <div class="mt-6 md:mt-0 md:ml-8 flex-1">
          <h2 class="text-5xl font-extrabold text-gray-800"><?php echo htmlspecialchars($profile['name']); ?></h2>
          <p class="text-xl text-gray-600 mt-2"><span class="font-bold">Email:</span> <?php echo htmlspecialchars($profile['email']); ?></p>
          <p class="text-xl text-gray-600"><span class="font-bold">Contact:</span> <?php echo htmlspecialchars($profile['phone']); ?></p>
          <?php if ($profile_type == "teacher"): ?>
            <p class="text-lg text-gray-700 mt-4"><span class="font-semibold">About Me:</span> <?php echo nl2br(htmlspecialchars($profile['description'])); ?></p>
            <p class="text-lg text-gray-700 mt-2"><span class="font-semibold">Working Block:</span> <?php echo htmlspecialchars($profile['block']); ?></p>
            <p class="text-lg text-gray-700 mt-2"><span class="font-semibold">Subjects Teaching:</span> <?php echo htmlspecialchars($profile['subjects']); ?></p>
          <?php else: ?>
            <p class="text-lg text-gray-700 mt-2"><span class="font-semibold">Roll:</span> <?php echo htmlspecialchars($profile['roll']); ?></p>
            <p class="text-lg text-gray-700 mt-2"><span class="font-semibold">Course/Subject Studying:</span> <?php echo htmlspecialchars($profile['course']); ?></p>
          <?php endif; ?>
        </div>
      </div>
      <!-- Action Buttons (if viewing own profile) -->
      <?php if ((isset($_SESSION['teacher_user']) && $_SESSION['teacher_user'] === $profile['email']) || (isset($_SESSION['user']) && $_SESSION['user'] === $profile['email'])): ?>
        <div class="mt-8 flex space-x-6 justify-center">
          <?php if (isset($_SESSION['teacher_user']) && $_SESSION['teacher_user'] === $profile['email']): ?>
            <a href="edit_profile.php" class="px-6 py-3 bg-green-500 text-white rounded-full shadow hover:bg-green-600 transition">Edit Profile</a>
          <?php endif; ?>
          <a href="change_password.php" class="px-6 py-3 bg-blue-500 text-white rounded-full shadow hover:bg-blue-600 transition">Change Password</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Announcements Section for Teacher Profiles -->
    <?php if ($profile_type == "teacher"): ?>
      <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-4xl w-full transform transition duration-500 hover:scale-105">
        <h3 class="text-4xl font-bold text-gray-800 mb-6 text-center">My Announcements</h3>
        <?php if (!empty($posts)): ?>
          <div class="space-y-6">
            <?php foreach ($posts as $post): ?>
              <div class="p-6 bg-gradient-to-r from-indigo-100 to-blue-100 rounded-xl border border-gray-300 hover:shadow-lg transition duration-200">
                <h4 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($post['title']); ?></h4>
                <p class="text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 150))); ?>...</p>
                <p class="text-sm text-gray-500 mt-2">Posted on <?php echo date("F j, Y, g:i a", strtotime($post['date_of_post'])); ?></p>
                <a href="announcement_details.php?post_id=<?php echo $post['post_id']; ?>" class="inline-block mt-4 text-indigo-600 font-semibold hover:underline">View Announcement</a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-gray-600 text-center">No announcements found.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
