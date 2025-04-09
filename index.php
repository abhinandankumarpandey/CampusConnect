<?php
// landing.php - A landing page for your project
session_start();
$_SESSION['landing_page'] = 'set';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CampusConnect - The Ultimate Academic Hub</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts (Optional) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    /* Smooth Scrolling */
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

  <!-- Hero Section -->
  <section class="relative bg-cover bg-center" style="background-image: url('assets/hero.jpg');">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen px-4 text-center">
      <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6">Welcome to CampusConnect</h1>
      <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-3xl">
        The ultimate academic hub that brings together students, teachers, and administrators. Discover announcements, events, competitions, and more. Empowering education with innovation.
      </p>
      <div class="flex flex-col md:flex-row gap-4">
        <!-- Student Button -->
        <a href="student_login.php" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          I'm a Student
        </a>
        <!-- Teacher Button -->
        <a href="teacher_login.php" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          I'm a Teacher
        </a>
        <!-- Super DBA Button -->
        <a href="super_dba_login.php" class="px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          Super DBA Login
        </a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Why Choose CampusConnect?</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <img src="assets/announcement.png" alt="Announcements" class="w-16 h-16 mx-auto mb-4">
          <h3 class="text-2xl font-semibold text-gray-700 mb-2 text-center">Real-time Announcements</h3>
          <p class="text-gray-600 text-center">Stay updated with live academic notifications and event alerts.</p>
        </div>
        <!-- Feature 2 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <img src="assets/teacher.png" alt="Teacher Portal" class="w-16 h-16 mx-auto mb-4">
          <h3 class="text-2xl font-semibold text-gray-700 mb-2 text-center">Interactive Teacher Portal</h3>
          <p class="text-gray-600 text-center">Empower teachers with intuitive dashboards for effective communication.</p>
        </div>
        <!-- Feature 3 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <img src="assets/tech.png" alt="Modern Interface" class="w-16 h-16 mx-auto mb-4">
          <h3 class="text-2xl font-semibold text-gray-700 mb-2 text-center">Modern & Responsive</h3>
          <p class="text-gray-600 text-center">Experience a sleek, responsive design across all devices.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-16 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
    <div class="max-w-6xl mx-auto px-4">
      <div class="flex flex-col md:flex-row items-center gap-8">
        <div class="flex-1">
          <h2 class="text-4xl font-bold mb-4">Empowering Education</h2>
          <p class="text-lg mb-6">
            CampusConnect is more than just a platform – it's a community built to empower academic excellence. We bring together students, teachers, and administrators to foster collaboration and innovation.
          </p>
          <ul class="list-disc list-inside space-y-3">
            <li class="text-lg">Real-time academic notifications</li>
            <li class="text-lg">Comprehensive dashboards for teachers and students</li>
            <li class="text-lg">User-friendly interface with modern design</li>
            <li class="text-lg">Seamless integration of multimedia content</li>
          </ul>
        </div>
        <div class="flex-1">
          <img src="assets/empower.png" alt="Empowering Education" class="rounded-xl shadow-lg">
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">What Our Users Say</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Testimonial 1 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <p class="text-gray-600 italic mb-4">"CampusConnect has revolutionized how I stay updated with campus events!"</p>
          <h4 class="text-lg font-semibold text-gray-800">- Sarah M.</h4>
        </div>
        <!-- Testimonial 2 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <p class="text-gray-600 italic mb-4">"The teacher dashboard is incredibly intuitive and saves me so much time."</p>
          <h4 class="text-lg font-semibold text-gray-800">- Mr. Johnson</h4>
        </div>
        <!-- Testimonial 3 -->
        <div class="bg-gray-50 p-8 rounded-xl shadow hover:shadow-xl transition duration-300">
          <p class="text-gray-600 italic mb-4">"A modern interface that works flawlessly on any device!"</p>
          <h4 class="text-lg font-semibold text-gray-800">- Alex R.</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- Developers Section -->
  <section class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-4xl font-bold text-gray-800 mb-4">Our Developers</h2>
      <p class="text-lg text-gray-600 mb-8">
        Our team of expert developers is dedicated to creating a seamless and innovative experience for every user. Discover more about our team and their passion for excellence.
      </p>
      <a href="about.php" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
        Learn More About Our Team
      </a>
    </div>
  </section>

  <!-- Call-to-Action Section -->
  <section class="py-16 bg-indigo-600">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-4xl font-extrabold text-white mb-4">Join the Community Today</h2>
      <p class="text-xl text-indigo-200 mb-8">
        Whether you're a student, a teacher, or an administrator, CampusConnect is the platform for you. Experience the future of academic collaboration.
      </p>
      <div class="flex flex-col md:flex-row justify-center gap-4">
        <a href="student_login.php" class="px-8 py-4 bg-blue-500 hover:bg-blue-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          I'm a Student
        </a>
        <a href="teacher_login.php" class="px-8 py-4 bg-green-500 hover:bg-green-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          I'm a Teacher
        </a>
        <a href="super_dba_login.php" class="px-8 py-4 bg-purple-500 hover:bg-purple-700 text-white font-semibold rounded-md shadow-lg transition duration-300">
          Super DBA Login
        </a>
      </div>
    </div>
  </section>

  <!-- Sticky Footer Section -->
  <footer class="bg-gray-800 mt-auto">
    <div class="max-w-6xl mx-auto px-4 py-6 text-center text-gray-300">
      <p class="text-sm">&copy; <?php echo date('Y'); ?> CampusConnect. All rights reserved.</p>
      <p class="text-sm">Designed by <a href="about.php" class="text-indigo-300 hover:underline">Abhinandan &amp; Chirag</a></p>
    </div>
  </footer>
  
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
