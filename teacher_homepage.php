<?php
// teacher_homepage.php
session_start();
include 'db.php'; // Database connection

// Check if teacher is logged in (teacher_user stores the teacher's email)
if (!isset($_SESSION['teacher_user'])) {
    header("Location: teacher_login.php"); // Redirect if not logged in
    exit;
}
$_SESSION['user_type'] = 'teacher';

// Profile redirect: encode the email to be URL-safe and generate the profile link
$encoded_email = urlencode($_SESSION['teacher_user']);
$profile_url = "profile.php?email=" . $encoded_email;

// Define allowed announcement types and courses
$allowed_types = array('academic', 'job', 'competition', 'sport', 'fees', 'exams', 'general');
$allowed_courses = array('ALL', 'BA', 'BCA', 'BBA', 'MSC', 'MCOM');

// Check for filter parameters in GET request
$filter_type = isset($_GET['announcement_type']) && in_array($_GET['announcement_type'], $allowed_types) ? $_GET['announcement_type'] : '';
$filter_course = isset($_GET['course']) && in_array($_GET['course'], $allowed_courses) ? $_GET['course'] : '';

$conditions = array();
if ($filter_type != '') {
    $conditions[] = "p.announcement_type = '$filter_type'";
}
if ($filter_course != '') {
    $conditions[] = "p.specific_course = '$filter_course'";
}

$sql = "SELECT p.*, t.name AS teacher_name, t.email AS teacher_email 
        FROM posts p 
        LEFT JOIN teacher t ON p.teacher_id = t.id";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY p.date_of_post DESC";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching posts: " . mysqli_error($conn));
}
$posts = array();
while ($row = mysqli_fetch_assoc($result)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard - Announcements</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Enable smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-in-out both;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-6">
                    <span class="text-white font-bold text-xl">CampusConnect</span>
                    <a href="teacher_homepage.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    <a href="<?php echo $profile_url; ?>" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">My Profile</a>
                </div>
                <div>
                    <a href="logout.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between my-6">
            <h1 class="text-3xl font-bold text-gray-800">Teacher Dashboard - Announcements</h1>
            <a href="teacher_post_form.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Post Announcement</a>
        </div>

        <!-- Filter Form (Improved UI) -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-xl shadow mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="announcement_type" class="w-full rounded-md border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">All Types</option>
                    <?php foreach ($allowed_types as $type): ?>
                        <option value="<?php echo $type; ?>" <?php if($filter_type == $type) echo 'selected'; ?>><?php echo ucfirst($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Course</label>
                <select name="course" class="w-full rounded-md border border-gray-300 p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="">All Courses</option>
                    <?php foreach ($allowed_courses as $course): ?>
                        <option value="<?php echo $course; ?>" <?php if($filter_course == $course) echo 'selected'; ?>><?php echo $course; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md w-full">Apply</button>
            </div>
        </form>

        <!-- Announcements List -->
        <?php if(count($posts) > 0): ?>
            <div class="space-y-6">
                <?php foreach ($posts as $post): ?>
                    <div class="animate-fadeIn bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row">
                        <!-- Left Column: Text Content -->
                        <div class="flex-1 p-6 border-r border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p class="text-sm text-gray-500 mb-2">
                                Posted on <?php echo date("F j, Y, g:i a", strtotime($post['date_of_post'])); ?> 
                                by <?php echo htmlspecialchars($post['teacher_name'] ?? 'Unknown'); ?>
                            </p>
                            <p class="mb-2"><strong>Type:</strong> <?php echo ucfirst($post['announcement_type']); ?></p>
                            <?php if (!empty($post['place_of_event'])): ?>
                                <p class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($post['place_of_event']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($post['date_of_event'])): ?>
                                <p class="mb-2"><strong>Date:</strong> <?php echo date("F j, Y", strtotime($post['date_of_event'])); ?></p>
                            <?php endif; ?>
                            <p class="mb-4 text-gray-700"><?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 150))); ?>...</p>
                            <?php if (!empty($post['specific_course']) || !empty($post['specific_block'])): ?>
                                <p class="text-sm text-gray-600 mb-4">
                                    <strong>Target:</strong> 
                                    <?php echo !empty($post['specific_course']) ? $post['specific_course'] : 'All Courses'; ?>, 
                                    Block <?php echo !empty($post['specific_block']) ? $post['specific_block'] : 'All'; ?>
                                </p>
                            <?php endif; ?>
                            <div class="flex space-x-3">
                                <a href="announcement_details.php?post_id=<?php echo $post['post_id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">View Full</a>
                                <?php if (isset($post['teacher_email']) && $post['teacher_email'] === $_SESSION['teacher_user']): ?>
                                    <a href="edit_announcement.php?post_id=<?php echo $post['post_id']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Edit</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Right Column: Media -->
                        <div class="w-full md:w-72 p-6 flex flex-col gap-4">
                            <?php if (!empty($post['multiple_pictures'])): ?>
                                <?php 
                                    $pictures = explode(",", $post['multiple_pictures']);
                                    $pic = trim($pictures[0]); // Featured image (first picture)
                                    if ($pic):
                                ?>
                                    <img src="<?php echo htmlspecialchars($pic); ?>" alt="Announcement Image" class="w-full h-40 object-cover rounded-md shadow cursor-pointer hover:opacity-80" onclick="openModal('image', '<?php echo htmlspecialchars($pic); ?>')">
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($post['video_related_to_post'])): ?>
                                <video class="w-full h-40 object-cover rounded-md shadow cursor-pointer" controls onclick="openModal('video', '<?php echo htmlspecialchars($post['video_related_to_post']); ?>')">
                                    <source src="<?php echo htmlspecialchars($post['video_related_to_post']); ?>" type="video/mp4">
                                    Video not supported.
                                </video>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No announcements available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Modal for Full Image/Video -->
    <div id="mediaModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50" onclick="closeModal()">
        <div class="relative">
            <button class="absolute top-0 right-0 m-2 text-white text-2xl font-bold" onclick="closeModal()">×</button>
            <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-screen hidden rounded">
            <video id="modalVideo" controls class="max-w-full max-h-screen hidden rounded">
                <source src="" type="video/mp4">
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
            document.getElementById('modalVideo').pause();
        }
    </script>
</body>
</html>
