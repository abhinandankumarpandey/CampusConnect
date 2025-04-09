<?php
// about.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - Our Team</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Hero Section -->
  <header class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
      <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28">
        <!-- Hero Content -->
        <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8">
          <div class="sm:text-center lg:text-left">
            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
              <span class="block xl:inline">Meet Our Team</span>
            </h1>
            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
              We’re passionate developers committed to delivering an exceptional user experience. Our project integrates front-end elegance, robust back-end logic, and a sleek, responsive design.
            </p>
            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
              <div class="rounded-md shadow">
                <a href="student_homepage.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                  Go to Student Portal
                </a>
              </div>
              <div class="mt-3 sm:mt-0 sm:ml-3">
                <a href="teacher_login.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">
                  Go to Teacher Portal
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Hero Image (Right) -->
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
      <!-- Replace with any image you want -->
      <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
           src="assets/team_collab.png"
           alt="Team Collaboration">
    </div>
  </header>

  <!-- Team Section -->
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Our Dedicated Team</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Chirag Card -->
        <div class="bg-gray-100 rounded-xl shadow-lg p-8 flex flex-col items-center text-center">
          <!-- Replace with Chirag's actual image -->
          <img src="assets/chirag.png" alt="Chirag" class="w-40 h-40 rounded-full object-cover mb-4 border-4 border-blue-500">
          <h3 class="text-2xl font-semibold mb-2">Chirag Jassal</h3>
          <p class="text-gray-600 mb-4">Front-End Developer</p>
          <ul class="list-disc list-inside text-left text-gray-700">
            <li>Front-end Development</li>
            <li>Documentation</li>
            <li>UI/UX</li>
          </ul>
        </div>

        <!-- Abhinandan Card -->
        <div class="bg-gray-100 rounded-xl shadow-lg p-8 flex flex-col items-center text-center">
          <!-- Replace with Abhinandan's actual image -->
          <img src="assets/abhinandan.png" alt="Abhinandan" class="w-40 h-40 rounded-full object-cover mb-4 border-4 border-green-500">
          <h3 class="text-2xl font-semibold mb-2">Abhinandan Pandey</h3>
          <p class="text-gray-600 mb-4">Back-End Developer</p>
          <ul class="list-disc list-inside text-left text-gray-700">
            <li>Backend Development</li>
            <li>Database Connectivity</li>
            <li>Project Logic</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- About the Project Section -->
  <section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col-reverse lg:flex-row items-center gap-8">
        <div class="lg:w-1/2">
          <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Vision & Project Goals</h2>
          <p class="text-lg text-gray-600 mb-6">
            Our project aims to streamline communication between students, teachers, and administrators. From real-time announcements to interactive dashboards, we’re building a platform that simplifies day-to-day academic activities.
          </p>
          <p class="text-lg text-gray-600">
            We focus on delivering a user-friendly interface, strong data integrity, and an easily scalable system. Our vision is to revolutionize the educational experience through continuous innovation and collaboration.
          </p>
        </div>
        <!-- Replace with a relevant project image -->
        <div class="lg:w-1/2">
          <img src="assets/about_secure_announcement.png"
               alt="Project Overview"
               class="rounded-xl shadow-md">
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-gray-800 text-center mb-8">Contact Our Developers</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Chirag Contact Info -->
        <div class="bg-gray-50 p-8 rounded-xl shadow-lg text-center">
          <h3 class="text-2xl font-semibold mb-2">Chirag Jassal</h3>
          <p class="text-gray-600 mb-4">Front-End Developer</p>
          <p class="text-gray-700"><strong>Email:</strong> chirag132004@gmail.com</p>
          <p class="text-gray-700"><strong>Phone:</strong> +91 9465966269 </p>
        </div>
        <!-- Abhinandan Contact Info -->
        <div class="bg-gray-50 p-8 rounded-xl shadow-lg text-center">
          <h3 class="text-2xl font-semibold mb-2">Abhinandan Pandey</h3>
          <p class="text-gray-600 mb-4">Back-End Developer</p>
          <p class="text-gray-700"><strong>Email:</strong> abhinandanpandey540@gmail.com</p>
          <p class="text-gray-700"><strong>Phone:</strong> +91 7710795036</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="bg-gradient-to-r from-blue-500 to-indigo-600 py-16 text-white">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h2 class="text-4xl font-extrabold mb-4">Ready to Explore?</h2>
      <p class="text-xl mb-8">Experience our user-centric platform designed to make academic life easier. Jump in and see how we’re transforming education.</p>
      <div class="flex flex-col md:flex-row gap-4 justify-center">
        <a href="student_homepage.php" class="px-8 py-4 bg-blue-700 hover:bg-blue-800 rounded-md text-white font-semibold transition">Go to Student Portal</a>
        <a href="teacher_login.php" class="px-8 py-4 bg-green-700 hover:bg-green-800 rounded-md text-white font-semibold transition">Go to Teacher Portal</a>
        <a href="super_dba_login.php" class="px-8 py-4 bg-purple-700 hover:bg-purple-800 rounded-md text-white font-semibold transition">Super DBA Login</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 py-6">
    <div class="max-w-7xl mx-auto px-4 text-center text-gray-300">
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
