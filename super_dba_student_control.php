<?php
session_start();
include 'db.php'; // This file should define $conn for the database connection

// Check if the superDBA user is logged in; if not, redirect to login.
if (!isset($_SESSION['super_dba_user'])) {
    header("Location: superdba_login.php");
    exit;
}

$message = '';
$error = '';

// ----- Student Deletion Section -----
if (isset($_GET['delete_student'])) {
    $student_id = intval($_GET['delete_student']);
    $sql_delete = "DELETE FROM users WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $student_id);
        if (mysqli_stmt_execute($stmt_delete)) {
            $message = "Student with ID $student_id deleted successfully.";
        } else {
            $error = "Error deleting student: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $error = "Failed to prepare delete statement: " . mysqli_error($conn);
    }
}

// ----- Fetch Students for Display -----
$students = array();
$sql_fetch = "SELECT id, name, email, roll, phone, gender, course, profile_picture, created_at FROM users";
$result_fetch = mysqli_query($conn, $sql_fetch);
if ($result_fetch) {
    while ($row = mysqli_fetch_assoc($result_fetch)) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super DBA - Student Management</title>
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
                            <a href="super_dba_student.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Student Management</a>
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
            <h1 class="text-3xl font-bold text-gray-800">Super DBA - Student Management</h1>
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

        <!-- Student Management Section -->
        <section class="bg-white shadow rounded p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Registered Students</h2>
            <?php if (!empty($students)): ?>
                <div class="overflow-x-auto">
                    <table id="myTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($student['profile_picture'])): ?>
                                            <img class="w-12 h-auto rounded" src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture">
                                        <?php else: ?>
                                            <span class="text-gray-500">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['roll']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['phone']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['gender']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['created_at']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md" 
                                           href="super_dba_student.php?delete_student=<?php echo $student['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this student?');">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-600">No students registered yet.</p>
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
        $('#myTable').DataTable();
      });
    </script>
</body>
</html>
