<?php
session_start();
// super_dba_homepage.php 
include 'db.php'; // This file should define $conn for the database connection

// Check if the superDBA user is logged in; if not, redirect to login.
if (!isset($_SESSION['super_dba_user'])) {
    header("Location: superdba_login.php");
    exit;
}

$message = '';
$error = '';

// ----- Token Deletion Section (for individual token deletion) -----
if (isset($_GET['delete_token'])) {
    $token_to_delete = $_GET['delete_token'];
    $sql_delete_token = "DELETE FROM teacher_token WHERE token = ?";
    $stmt_delete_token = mysqli_prepare($conn, $sql_delete_token);
    if ($stmt_delete_token) {
        mysqli_stmt_bind_param($stmt_delete_token, "s", $token_to_delete);
        if (mysqli_stmt_execute($stmt_delete_token)) {
            $message = "Token " . htmlspecialchars($token_to_delete) . " deleted successfully.";
        } else {
            $error = "Error deleting token: " . mysqli_stmt_error($stmt_delete_token);
        }
        mysqli_stmt_close($stmt_delete_token);
    } else {
        $error = "Failed to prepare token deletion: " . mysqli_error($conn);
    }
}

// ----- Token Generation Section -----
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['generate_token'])) {
    // Generate a random token (10 hexadecimal characters)
    $token = bin2hex(random_bytes(5));

    // Insert the new token into the teacher_token table with activated_token = 0 (not used)
    $sql = "INSERT INTO teacher_token (token, activated_token) VALUES (?, 0)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        if (mysqli_stmt_execute($stmt)) {
            $message = "New token generated: " . htmlspecialchars($token);
        } else {
            $error = "Error generating token: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Failed to prepare token generation: " . mysqli_error($conn);
    }
}

// ----- Teacher Deletion Section (with associated posts, profile, and token deletion) -----
if (isset($_GET['delete_teacher'])) {
    $teacher_id = intval($_GET['delete_teacher']);
    $teacher_token = '';

    // Fetch the teacher's token from the teacher table
    $sql_fetch_token = "SELECT token FROM teacher WHERE id = ?";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch_token);
    if ($stmt_fetch) {
        mysqli_stmt_bind_param($stmt_fetch, "i", $teacher_id);
        mysqli_stmt_execute($stmt_fetch);
        mysqli_stmt_bind_result($stmt_fetch, $teacher_token);
        mysqli_stmt_fetch($stmt_fetch);
        mysqli_stmt_close($stmt_fetch);
    } else {
        $error = "Failed to fetch teacher token: " . mysqli_error($conn);
    }

    // Begin a transaction so all deletions succeed together
    mysqli_begin_transaction($conn);
    $delete_success = true;

    // 1. Delete posts associated with the teacher (which will cascade delete related comments)
    $sql_delete_posts = "DELETE FROM posts WHERE teacher_id = ?";
    $stmt_delete_posts = mysqli_prepare($conn, $sql_delete_posts);
    if ($stmt_delete_posts) {
        mysqli_stmt_bind_param($stmt_delete_posts, "i", $teacher_id);
        if (!mysqli_stmt_execute($stmt_delete_posts)) {
            $delete_success = false;
            $error = "Error deleting teacher's posts: " . mysqli_stmt_error($stmt_delete_posts);
        }
        mysqli_stmt_close($stmt_delete_posts);
    } else {
        $delete_success = false;
        $error = "Failed to prepare posts deletion: " . mysqli_error($conn);
    }

    // 2. Delete teacher profile
    $sql_delete_profile = "DELETE FROM teacher_profile WHERE teacher_id = ?";
    $stmt_delete_profile = mysqli_prepare($conn, $sql_delete_profile);
    if ($stmt_delete_profile) {
        mysqli_stmt_bind_param($stmt_delete_profile, "i", $teacher_id);
        if (!mysqli_stmt_execute($stmt_delete_profile)) {
            $delete_success = false;
            $error = "Error deleting teacher profile: " . mysqli_stmt_error($stmt_delete_profile);
        }
        mysqli_stmt_close($stmt_delete_profile);
    } else {
        $delete_success = false;
        $error = "Failed to prepare teacher profile deletion: " . mysqli_error($conn);
    }

    // 3. Delete teacher record
    $sql_delete_teacher = "DELETE FROM teacher WHERE id = ?";
    $stmt_delete_teacher = mysqli_prepare($conn, $sql_delete_teacher);
    if ($stmt_delete_teacher) {
        mysqli_stmt_bind_param($stmt_delete_teacher, "i", $teacher_id);
        if (!mysqli_stmt_execute($stmt_delete_teacher)) {
            $delete_success = false;
            $error = "Error deleting teacher: " . mysqli_stmt_error($stmt_delete_teacher);
        }
        mysqli_stmt_close($stmt_delete_teacher);
    } else {
        $delete_success = false;
        $error = "Failed to prepare teacher deletion: " . mysqli_error($conn);
    }

    // 4. Delete teacher token record (if a token was associated)
    if (!empty($teacher_token)) {
        $sql_delete_token = "DELETE FROM teacher_token WHERE token = ?";
        $stmt_delete_token = mysqli_prepare($conn, $sql_delete_token);
        if ($stmt_delete_token) {
            mysqli_stmt_bind_param($stmt_delete_token, "s", $teacher_token);
            if (!mysqli_stmt_execute($stmt_delete_token)) {
                $delete_success = false;
                $error = "Error deleting teacher token: " . mysqli_stmt_error($stmt_delete_token);
            }
            mysqli_stmt_close($stmt_delete_token);
        } else {
            $delete_success = false;
            $error = "Failed to prepare teacher token deletion: " . mysqli_error($conn);
        }
    }

    if ($delete_success) {
        mysqli_commit($conn);
        $message = "Teacher with ID $teacher_id, along with associated posts, profile, and token, deleted successfully.";
    } else {
        mysqli_rollback($conn);
    }
}

// ----- Fetch Tokens for Display -----
$tokens = array();
$sql_tokens = "SELECT token, activated_token FROM teacher_token";
$result_tokens = mysqli_query($conn, $sql_tokens);
if ($result_tokens) {
    while ($row = mysqli_fetch_assoc($result_tokens)) {
        $tokens[] = $row;
    }
}

// ----- Fetch Teachers for Display -----
$teachers = array();
$sql_fetch = "SELECT id, name, email, phone FROM teacher";
$result_fetch = mysqli_query($conn, $sql_fetch);
if ($result_fetch) {
    while ($row = mysqli_fetch_assoc($result_fetch)) {
        $teachers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super DBA Homepage - Teacher Management</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-white font-bold text-xl">CampusConnect</span>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-4">
                            <a href="super_dba_student_control.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Student Management</a>
                            <a href="super_dba_homepage.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Teacher Management</a>
                            <a href="logout.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="max-w-7xl mx-auto p-4">
        <header class="my-6">
            <h1 class="text-3xl font-bold text-gray-800">Super DBA Management Interface</h1>
            <p class="text-gray-600 mt-2">Welcome, <?php echo htmlspecialchars($_SESSION['super_dba_user']); ?>!</p>
        </header>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Token Generation Form -->
        <section class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Generate New Token for Teacher Signup</h2>
            <form action="super_dba_homepage.php" method="POST" class="flex items-center space-x-4">
                <button type="submit" name="generate_token" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Generate Token</button>
            </form>
        </section>

        <!-- Token List Section -->
        <section class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Token List</h2>
            <?php if (!empty($tokens)): ?>
                <div class="overflow-x-auto">
                    <table id="tokenTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($tokens as $token): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($token['token']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo ($token['activated_token'] == 1) ? 'Used' : 'Not used'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="super_dba_homepage.php?delete_token=<?php echo htmlspecialchars($token['token']); ?>" 
                                           onclick="return confirm('Are you sure you want to delete this token?');"
                                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No tokens generated yet.</p>
            <?php endif; ?>
        </section>

        <!-- Teacher Management Section -->
        <section class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Registered Teachers</h2>
            <?php if (!empty($teachers)): ?>
                <div class="overflow-x-auto">
                    <table id="teacherTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($teacher['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($teacher['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($teacher['phone']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="super_dba_homepage.php?delete_teacher=<?php echo $teacher['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this teacher?');"
                                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No teachers registered yet.</p>
            <?php endif; ?>
        </section>
    </div>

    <!-- jQuery and DataTables JS CDNs -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
      $(document).ready(function () {
        $('#tokenTable').DataTable();
        $('#teacherTable').DataTable();
      });
    </script>
</body>
</html>
