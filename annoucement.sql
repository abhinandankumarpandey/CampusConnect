-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2025 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `annoucement`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `email`, `parent_comment_id`, `content`, `created_at`) VALUES
(17, 6, 'chirag@gmail.com', NULL, 'hiii', '2025-04-04 11:13:39'),
(18, 6, 'chirag@gmail.com', NULL, 'jii', '2025-04-05 05:40:25'),
(19, 6, 'chirag@gmail.com', 18, 'gg', '2025-04-05 05:40:41');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `announcement_type` enum('academic','job','competition','sport','fees','exams','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `place_of_event` varchar(255) DEFAULT NULL,
  `date_of_event` date DEFAULT NULL,
  `date_of_post` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_post_time` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `multiple_pictures` text DEFAULT NULL,
  `video_related_to_post` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `specific_course` enum('ALL','BA','BCA','BBA','MSC','MCOM') DEFAULT NULL,
  `specific_block` enum('ALL','A','B','C','D') DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `announcement_type`, `title`, `description`, `place_of_event`, `date_of_event`, `date_of_post`, `modified_post_time`, `multiple_pictures`, `video_related_to_post`, `comments`, `specific_course`, `specific_block`, `teacher_id`) VALUES
(6, 'competition', 'Hackathon update', 'in PCTE hackathon', 'PCTE College', '2025-04-23', '2025-04-04 16:02:55', '2025-04-04 17:46:15', 'uploads/posts/1743762775_WhatsApp Image 2025-04-03 at 12.47.37 PM.jpeg,uploads/posts/1743762775_WhatsApp Image 2025-03-10 at 6.09.07 PM (1).jpeg', '', NULL, 'BCA', 'D', 3),
(7, 'exams', 'May exam', 'asklfjakjjf', 'college', '2025-04-30', '2025-04-05 11:13:50', '2025-04-05 11:13:50', 'uploads/posts/1743831830_ChatGPT Image Apr 3, 2025, 04_18_48 PM.png,uploads/posts/1743831830_piclumen-1743338791862.png,uploads/posts/1743831830_ChatGPT Image Mar 30, 2025, 06_04_03 PM.png', '', NULL, 'ALL', 'ALL', 3);

-- --------------------------------------------------------

--
-- Table structure for table `student_profile`
--

CREATE TABLE `student_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `subjects` varchar(255) DEFAULT NULL,
  `other_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `super_dba`
--

CREATE TABLE `super_dba` (
  `name` text NOT NULL,
  `email` varchar(70) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `super_dba`
--

INSERT INTO `super_dba` (`name`, `email`, `password`) VALUES
('Abhinandan Pandey', 'abhijanop4@gmail.com', '$2y$10$C9dO7XdeH8i/Ed1a6ZtFuuouK9Tl.62WTbwSPjFLp3dSAz1qTH2B2');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`id`, `name`, `email`, `phone`, `gender`, `password`, `profile_picture`, `token`) VALUES
(3, 'Sakshi Jain', 'sakshijain@gmail.com', '9815113890', 'female', '$2y$10$bofsmvm9a.CSIO6lttdNC.JJ0mRpFRBQ1fLY/GYASEx3XroBVv4Am', 'uploads/1743761830_4step.webp', '557c9ce08f');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_profile`
--

CREATE TABLE `teacher_profile` (
  `teacher_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `block` varchar(50) DEFAULT NULL,
  `subjects` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_token`
--

CREATE TABLE `teacher_token` (
  `token` varchar(255) NOT NULL,
  `activated_token` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_token`
--

INSERT INTO `teacher_token` (`token`, `activated_token`) VALUES
('2011965ee7', 0),
('557c9ce08f', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `roll` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` enum('male','female','prefer_not_to_say') NOT NULL,
  `course` enum('BA','BCA','BBA','MSc','MCOM') NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `roll`, `phone`, `gender`, `course`, `password`, `profile_picture`, `created_at`) VALUES
(3, 'Chirag', 'chirag@gmail.com', '7206', '7710795086', 'male', 'BCA', '$2y$10$F/YfIWxRPOQjJOa3yDV6OOwsRID64PBkbcR9Ot0aY1B3JaDK4Tboy', 'uploads/1743763333_image (42).webp', '2025-04-04 10:42:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `student_profile`
--
ALTER TABLE `student_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `teacher_profile`
--
ALTER TABLE `teacher_profile`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_token`
--
ALTER TABLE `teacher_token`
  ADD PRIMARY KEY (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_profile`
--
ALTER TABLE `student_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`);

--
-- Constraints for table `student_profile`
--
ALTER TABLE `student_profile`
  ADD CONSTRAINT `student_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`token`) REFERENCES `teacher_token` (`token`);

--
-- Constraints for table `teacher_profile`
--
ALTER TABLE `teacher_profile`
  ADD CONSTRAINT `teacher_profile_fk` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
