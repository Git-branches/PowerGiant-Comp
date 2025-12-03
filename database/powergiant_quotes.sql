-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 04:38 AM
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
-- Database: `powergiant_quotes`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `content_type` enum('text','image') NOT NULL,
  `content_value` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `section_name`, `content_type`, `content_value`, `image_path`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'main_title', 'text', 'About Oil Product Industries with Excellence', NULL, 1, '2025-11-01 07:35:43', '2025-11-11 04:04:21'),
(2, 'main_description', 'text', 'POWER GIANT RMT IS A DYNAMIC AND INNOVATIVE ENTERPRISE SPECIALIZING IN TRANSPORT, REFINING AND DISTRIBUTION OF OIL PRODUCTS.', NULL, 2, '2025-11-01 07:35:43', '2025-11-11 04:04:21'),
(3, 'about_title', 'image', 'ABOUT COMPANY', 'uploads/about/about_6912ab24eff4f.jpg', 3, '2025-11-01 07:35:43', '2025-11-11 05:24:58'),
(4, 'about_content_1', 'text', 'POWER GIANT RMT IS A DYNAMIC AND INNOVATIVE ENTERPRISE SPECIALIZING IN TRANSPORT, REFINING AND DISTRIBUTION OF OIL PRODUCTS. WE ARE DRIVEN BY A PASSION FOR DELIVERING RELIABLE AND COST-EFFECTIVE ENERGY SOLUTIONS TO MEET THE UNIQUE NEEDS AND GROWING DEMANDS OF OUR CUSTOMERS. THROUGH STRATEGIC PARTNERSHIP, WE ENSURE THE TIMELY AND EFFICIENT DELIVERY OF OUR PRODUCTS TO LOCAL MARKETS.', NULL, 4, '2025-11-01 07:35:43', '2025-11-01 07:35:43'),
(5, 'about_content_2', 'text', 'OUR SERVICES DIVISION EXCELS IN SOURCING HIGH-QUALITY GOODS, ENSURING COMPETITIVE PRICING AND TIMELY AVAILABILITY. MEANWHILE, OUR TRANSPORT SERVICES LEVERAGE CUTTING-EDGE LOGISTICS SOLUTIONS TO GUARANTEE SAFE AND PROMPT DELIVERY. OUR SERVICES PORTFOLIO IS DESIGNED To SUPPORT BUSINESSES IN OPTIMIZING THEIR OPERATIONS, FROM SUPPLY CHAIN MANAGEMENT TO CUSTOMER SERVICE SOLUTIONS. AT POWER GIANT RMT, WE PRIDE OURSELVES ON OUR COMMITMENT TO SUSTAINABILITY, INNOVATION, AND CUSTOMER SATISFACTION, ENSURING THAT EVERY INTERACTION REFLECTS OUR CORE VALUES AND DEDICATION TO EXCELLENCE.', NULL, 5, '2025-11-01 07:35:43', '2025-11-01 07:35:43'),
(6, 'main_image', 'image', 'SS', 'uploads/about/about_6912df73ac720.jpg', 6, '2025-11-01 07:35:43', '2025-11-11 07:02:11'),
(7, 'mission_image', 'image', 'test', 'uploads/about/about_6912e16dd02a5.jpg', 7, '2025-11-01 07:35:43', '2025-11-11 07:10:37'),
(8, 'vision_image', 'image', 's', 'css/images/Vission.png', 8, '2025-11-01 07:35:43', '2025-11-11 05:05:18'),
(9, 'quality_image', 'image', 'SS', 'css/images/Quality.png', 9, '2025-11-01 07:35:43', '2025-11-11 05:05:18'),
(10, 'delivery_image', 'image', 'd', 'uploads/about/about_6912e1405e3d2.jpg', 10, '2025-11-01 07:35:43', '2025-11-11 07:09:52'),
(11, 'process_image', 'image', NULL, 'css/images/Process.png', 11, '2025-11-01 07:35:43', '2025-11-11 05:05:18'),
(12, 'certificate_image', 'image', 'A', 'uploads/about/about_6912eaf31d626.png', 12, '2025-11-01 07:35:43', '2025-11-11 07:51:15'),
(51, 'quality_policy_title', 'text', 'QUALITY POLICY', NULL, 13, '2025-11-11 04:04:21', '2025-11-11 05:42:58'),
(52, 'quality_policy_description', 'text', 'Power Giant RMT is committed to providing reliable, safe, and efficient solutions to meet the needs of our clients in trading logistics, and allied services.', NULL, 14, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(53, 'mission_title', 'text', 'Mission', NULL, 15, '2025-11-11 04:04:21', '2025-11-11 05:14:30'),
(54, 'mission_description', 'text', 'Our mission is to provide reliable, efficient, and innovative transport solutions that consistently respond to the changing needs of our clients, ensuring satisfaction, safety, and long-term trust.', NULL, 16, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(55, 'vision_title', 'text', 'Vision', NULL, 17, '2025-11-11 04:04:21', '2025-11-11 05:42:58'),
(56, 'vision_description', 'text', 'Our vision is to become a leading provider of integrated trading and transport services, recognized for our commitment to quality, professionalism, and integrity in every aspect of our operations.', NULL, 18, '2025-11-11 04:04:21', '2025-11-11 05:42:58'),
(57, 'quality_policy_card_title', 'text', 'Quality Policy', NULL, 19, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(58, 'quality_policy_card_description', 'text', 'Power Giant RMT is committed to delivering reliable, safe, and efficient trading and transport solutions that meet customer expectations. We uphold quality, comply with standards, empower our people, and continuously improve to ensure customer satisfaction and lasting trust.', NULL, 20, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(59, 'delivery_title', 'text', 'Transportation & Delivery', NULL, 21, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(60, 'delivery_description', 'text', 'Power Giant RMT provides dependable transportation and delivery services using a fleet of tanker trucks, wing vans, and lorries to ensure safe, timely, and efficient movement of goods for our valued clients.', NULL, 22, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(61, 'process_title', 'text', 'Process Flow', NULL, 23, '2025-11-11 04:04:21', '2025-11-11 04:04:21'),
(62, 'process_description', 'text', 'The process flow involves transport from the source to the generator, then to the treatment facility, and finally to the buyer, ensuring safe and efficient delivery at every stage. kulot', NULL, 24, '2025-11-11 04:04:21', '2025-11-11 05:11:17'),
(63, 'certificate_title', 'image', 'PERMIT CERTIFICATE', 'uploads/about/about_6912c22536dd0.png', 25, '2025-11-11 04:04:21', '2025-11-11 04:57:09'),
(64, 'certificate_description', 'image', 'Fully accredited and compliant with government and environmental regulations, ensuring all operations are legal, safe, and responsibly managed.', 'uploads/about/about_6912b63bd0497.png', 26, '2025-11-11 04:04:21', '2025-11-11 05:16:27');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `quote_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `quote_id`, `action`, `old_value`, `new_value`, `description`, `created_at`) VALUES
(71, 13, 'created', NULL, NULL, 'New quote request submitted', '2025-11-17 03:05:59'),
(75, 14, 'created', NULL, NULL, 'New quote request submitted', '2025-11-17 03:13:51'),
(76, 14, 'status_changed', 'new', 'quoted', 'Status changed from new to quoted', '2025-11-17 03:14:07'),
(77, 14, 'priority_changed', 'medium', 'high', 'Priority changed from medium to high', '2025-11-17 03:14:07'),
(78, 14, 'status_changed', 'quoted', 'in_progress', 'Status changed from quoted to in_progress', '2025-11-17 03:18:26'),
(79, 14, 'priority_changed', 'high', 'urgent', 'Priority changed from high to urgent', '2025-11-17 03:19:53'),
(80, 15, 'created', NULL, NULL, 'New quote request submitted', '2025-11-17 04:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(11) NOT NULL,
  `role` enum('admin','editor') NOT NULL,
  `permission` varchar(100) NOT NULL,
  `allowed` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `role`, `permission`, `allowed`) VALUES
(1, 'admin', 'view_dashboard', 1),
(2, 'admin', 'view_inquiries', 1),
(3, 'admin', 'manage_users', 1),
(4, 'admin', 'edit_pages', 1),
(5, 'admin', 'backup_maintenance', 1),
(6, 'admin', 'delete_quotes', 1),
(7, 'admin', 'view_logs', 1),
(8, 'editor', 'view_dashboard', 0),
(9, 'editor', 'view_inquiries', 0),
(10, 'editor', 'manage_users', 0),
(11, 'editor', 'edit_pages', 1),
(12, 'editor', 'backup_maintenance', 0),
(13, 'editor', 'delete_quotes', 0),
(14, 'editor', 'view_logs', 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `password`, `email`, `full_name`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'powergiant@gmail.com', '$2y$10$l.ubn5g98EozPP3pIsvSuuO9nHK6Sqw5c5ngjOk9U13Fgxv0zh3se', 'admin@powergiantrmt.com', 'Administrator', 'admin', '2025-11-17 11:26:31', '2025-10-27 14:25:29', '2025-11-17 03:26:31'),
(4, 'ej', '$2y$10$9wlAIDywgmVUNwY08snnZO6G6WecleXkyIuCgY438Fn8Fp2t73rbO', 'ejromero@gmail.com', 'Rhon Jon G. Romero', 'editor', '2025-11-02 08:34:26', '2025-11-02 00:33:21', '2025-11-02 00:34:26');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_content`
--

CREATE TABLE `compliance_content` (
  `id` int(11) NOT NULL,
  `certificate_name` varchar(255) NOT NULL,
  `certificate_description` text DEFAULT NULL,
  `issuing_authority` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `section_type` enum('certificate','framework','process') NOT NULL DEFAULT 'certificate',
  `step` varchar(10) DEFAULT NULL,
  `badges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`badges`)),
  `icon_color` varchar(50) DEFAULT NULL,
  `badge_color` varchar(50) DEFAULT NULL,
  `gradient_color` varchar(100) DEFAULT 'from-primary to-secondary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compliance_content`
--

INSERT INTO `compliance_content` (`id`, `certificate_name`, `certificate_description`, `issuing_authority`, `issue_date`, `expiry_date`, `certificate_file`, `image_path`, `display_order`, `is_active`, `created_at`, `updated_at`, `section_type`, `step`, `badges`, `icon_color`, `badge_color`, `gradient_color`) VALUES
(1, 'ISO 9001:2015 Certifications', 'Quality Management System Certification ensuring standardized processes and continuous improvement.', 'International Organization for Standardization', '2023-01-15', '2026-01-15', '', 'uploads/compliance/img_6912e33139599.jpg', 1, 1, '2025-11-01 07:35:43', '2025-11-11 07:32:30', 'certificate', NULL, NULL, NULL, NULL, NULL),
(2, 'DOE Accreditation', 'Department of Energy accreditation for oil industry operations and distribution.', 'Department of Energy - Philippines', '2023-03-20', '2025-03-20', '', 'uploads/compliance/img_6912eef9d9c32.png', 2, 1, '2025-11-01 07:35:43', '2025-11-11 08:08:25', 'certificate', NULL, NULL, NULL, NULL, NULL),
(3, 'Environmental Compliance Certificate', 'Certificate of compliance with environmental regulations and standards.', 'Environmental Management Bureau', '2023-02-10', '2026-02-10', '', 'uploads/compliance/img_6916c5e1b55ef.jpg', 3, 1, '2025-11-01 07:35:43', '2025-11-14 06:02:09', 'certificate', NULL, NULL, NULL, NULL, NULL),
(4, 'Business Permit', 'Mayor\'s permit and business license for commercial operations.', 'City Government of General Santos', '2024-01-05', '2025-01-05', '', 'uploads/compliance/img_6912e58eaa176.jpg', 4, 1, '2025-11-01 07:35:43', '2025-11-11 07:28:14', 'certificate', NULL, NULL, NULL, NULL, NULL),
(19, 'DENR Accredited', 'Hazardous Waste Transporter (HWT) & TSDF Licensesss', 'Kulot', '2025-11-11', '2025-11-12', '', '', 1, 1, '2025-11-11 08:23:57', '2025-11-11 08:52:35', 'framework', NULL, '[\"HWT-2025-001\",\"TSDF-Alpha\"]', 'from-green-500 to-emerald-600', 'bg-green-100 text-green-700', 'from-primary to-secondary'),
(20, 'DOE Registered', 'Oil Industry Participant (OIP)', NULL, NULL, NULL, NULL, NULL, 2, 1, '2025-11-11 08:23:57', '2025-11-11 08:23:57', 'framework', NULL, '[\"OIP-2025-POWERGIANT\"]', 'from-blue-500 to-cyan-600', 'bg-blue-100 text-blue-700', 'from-primary to-secondary'),
(21, 'Real-Time Monitoring', 'IoT sensors on all tankers and facilities.', 'Kulot', '2025-11-11', '2025-12-11', '', 'uploads/compliance/img_6915a8c4c953a.png', 1, 1, '2025-11-11 08:23:57', '2025-11-13 09:45:40', 'process', '1', NULL, NULL, NULL, 'from-primary to-secondary'),
(22, 'Monthly Audits', 'Internal and third-party audits.', 'International Organization for Standardization', '2025-11-13', '2025-11-13', '', 'uploads/compliance/img_6915a26bb9c8c.png', 2, 1, '2025-11-11 08:23:57', '2025-11-13 09:18:35', 'process', '2', NULL, NULL, NULL, 'from-green-500 to-emerald-600'),
(23, 'Annual Renewal', 'All permits renewed 90 days early.', NULL, NULL, NULL, NULL, NULL, 3, 1, '2025-11-11 08:23:57', '2025-11-11 08:23:57', 'process', '3', NULL, NULL, NULL, 'from-purple-500 to-pink-600');

-- --------------------------------------------------------

--
-- Table structure for table `contact_content`
--

CREATE TABLE `contact_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `content_title` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `content_type` enum('text','image','contact_info') NOT NULL,
  `content_value` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_content`
--

INSERT INTO `contact_content` (`id`, `section_name`, `content_title`, `icon`, `content_type`, `content_value`, `image_path`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'contact_title', NULL, NULL, 'text', 'Get In Touch With Us by', NULL, 1, '2025-11-01 07:35:43', '2025-11-17 03:37:54'),
(2, 'contact_description', NULL, NULL, 'text', 'Have questions about our oil products and services? We\'re here to help you with all your energy needs.', NULL, 2, '2025-11-01 07:35:43', '2025-11-17 03:52:15'),
(3, 'company_address', NULL, NULL, 'contact_info', '123 Energy Street, Industrial District, General Santos City, Philippinessss', NULL, 3, '2025-11-01 07:35:43', '2025-11-17 03:38:49'),
(4, 'phone_number', NULL, NULL, 'contact_info', 'Main: 09628955759\r\nEmergency: 09977126396\r\n24/7 Hotline Available', NULL, 4, '2025-11-01 07:35:43', '2025-11-17 04:53:21'),
(5, 'email_address', NULL, NULL, 'text', 'General: 📩powergiant\r\nrmt@gmail.com\r\nSales: 📩powergiant\r\nrmt@gmail.com', NULL, 5, '2025-11-01 07:35:43', '2025-11-17 04:53:45'),
(6, 'business_hours', NULL, NULL, 'contact_info', 'Monday - Friday: 8:00 AM - 6:00 PM\r\nSaturday: 8:00 AM - 12:00 AM', NULL, 6, '2025-11-01 07:35:43', '2025-11-17 03:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_hero`
--

CREATE TABLE `homepage_hero` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `primary_button_text` varchar(100) DEFAULT NULL,
  `primary_button_link` varchar(255) DEFAULT NULL,
  `secondary_button_text` varchar(100) DEFAULT NULL,
  `secondary_button_link` varchar(255) DEFAULT NULL,
  `background_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`background_images`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_hero`
--

INSERT INTO `homepage_hero` (`id`, `title`, `subtitle`, `description`, `primary_button_text`, `primary_button_link`, `secondary_button_text`, `secondary_button_link`, `background_images`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Delivering the Future of <span class=\"text-gradient\">Oil Solutions</span>', 'Premium Oil & Energy Solutions', 'Reliable and high-quality oil and energy solutions with nationwide coverage, trusted by industries and partners for their efficiency, safety, and consistent performance.', 'Get Quote Now', 'contact.php', 'Explore Services', 'services.php', '[\"truck4.jpg\",\"truck1.jpg\"]', 1, '2025-11-02 05:33:48', '2025-11-02 07:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_stats`
--

CREATE TABLE `homepage_stats` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `value` varchar(50) NOT NULL,
  `prefix` varchar(10) DEFAULT '',
  `suffix` varchar(10) DEFAULT '',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_stats`
--

INSERT INTO `homepage_stats` (`id`, `label`, `value`, `prefix`, `suffix`, `display_order`, `is_active`, `created_at`) VALUES
(1, 'Oil Stations', '2', '', '', 2, 1, '2025-11-02 05:33:48'),
(2, 'Years Experience', '2022', '', '+', 2, 1, '2025-11-02 05:33:48'),
(3, 'Corporate Partners', '5', '', '+', 3, 1, '2025-11-02 05:33:48'),
(4, 'Safety Compliance', '100', '', '%', 4, 1, '2025-11-02 05:33:48');

-- --------------------------------------------------------

--
-- Table structure for table `news_content`
--

CREATE TABLE `news_content` (
  `id` int(11) NOT NULL,
  `news_title` varchar(255) NOT NULL,
  `news_content` text NOT NULL,
  `news_excerpt` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_content`
--

INSERT INTO `news_content` (`id`, `news_title`, `news_content`, `news_excerpt`, `image_path`, `author`, `publish_date`, `is_published`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Power Giant RMT Opens ₱1.2B Green Fuel Depot in Visayas', 'The new facility in Cebu features solar-powered operations, waste heat recovery, and real-time emissions monitoring — setting a new standard for sustainable energy infrastructure', 'First carbon-neutral fuel terminal in Southeast Asia', 'news/news_691491f5689a1.png', 'Corporate Communications', '2025-10-28', 1, 1, '2025-11-01 11:00:27', '2025-11-12 13:56:05'),
(2, 'DENR Awards Power Giant \"Green Enterprise of the Year\"', 'Power Giant RMT receives top environmental award for innovative waste management and green energy solutions.', 'Recognition for sustainable operations', 'news/news_6914922e5225a.png', 'Environment Team', '2025-10-25', 1, 2, '2025-11-01 11:00:27', '2025-11-12 13:57:02'),
(3, '10,000 Trees Planted in Reforestation Drive', 'Massive reforestation effort across Mindanao to combat climate change and promote biodiversity.', 'Community environmental initiative', 'news/news_6914922579ab3.jpg', 'CSR Department', '2025-10-20', 1, 3, '2025-11-01 11:00:27', '2025-11-12 13:56:53'),
(4, '5 Years Zero Incident Safety Milestone', 'Company celebrates five consecutive years without any safety incidents across all operations.', 'Perfect safety record achievement', 'news/news_6914921fcd963.png', 'Safety Division', '2025-10-15', 1, 4, '2025-11-01 11:00:27', '2025-11-12 13:56:47'),
(5, 'Solar-Powered Tanker Fleet Launching', 'Innovative solar-powered tankers set new standards for sustainable fuel transportation.', 'Hybrid solar-diesel tankers reduce fuel use by 30%', 'news/news_691492090cf48.jpg', 'Marine Operations', '2025-10-10', 1, 5, '2025-11-01 11:00:27', '2025-11-12 13:56:25'),
(6, 'Strategic Alliance with Shell Philippines', 'Partnership expands marine fuel distribution network across Visayas and Mindanao.', 'Joint venture for marine fuel supply in VisMin', 'news/news_6914920fc8970.jpg', 'Business Development', '2025-10-05', 1, 6, '2025-11-01 11:00:27', '2025-11-12 13:56:31'),
(7, '40% Women in Leadership Roles', 'Company achieves significant milestone in gender diversity and inclusion.', 'Power Giant leads gender equality in energy sector', 'news/news_69149217f408a.png', 'HR Department', '2025-09-30', 1, 7, '2025-11-01 11:00:27', '2025-11-12 13:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `logo_path`, `website_url`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ACME Corp2222', 'uploads/partners/partner_69147afd64e50.png', '', 1, 1, '2025-11-02 05:33:48', '2025-11-12 12:18:05'),
(2, 'TechFlow', NULL, NULL, 2, 1, '2025-11-02 05:33:48', NULL),
(3, 'GlobalTech', NULL, NULL, 3, 1, '2025-11-02 05:33:48', NULL),
(4, 'InnovateCo', NULL, NULL, 4, 1, '2025-11-02 05:33:48', NULL),
(6, 'NextGen', NULL, NULL, 6, 1, '2025-11-02 05:33:48', NULL),
(8, 'banana', '', '', 0, 1, '2025-11-12 12:22:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects_content`
--

CREATE TABLE `projects_content` (
  `id` int(11) NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_description` text DEFAULT NULL,
  `project_category` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects_content`
--

INSERT INTO `projects_content` (`id`, `project_title`, `project_description`, `project_category`, `image_path`, `project_date`, `client_name`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Batangas Hazardous Waste Treatment Plant', '50,000 MT/year capacity | DENR TSDF Certified | Zero Spill Record', 'Hazardous Waste Management', 'projects/proj_69148795b19f0.jpg', '2024-01-15', 'San Miguel Corp', 1, 1, '2025-11-01 09:58:24', '2025-11-12 13:11:49'),
(2, 'Cebu Bulk Fuel Terminal Expansion', '100,000 KL additional capacity | DOE Approved | Q4 2025 Target', 'Fuel Infrastructure', 'projects/proj_691487a41da01.jpg', '2025-12-31', 'Petron Corp', 2, 1, '2025-11-01 09:58:24', '2025-11-12 13:12:04'),
(3, 'Nationwide GPS Fleet Tracking System', '200+ tankers | Real-time ETA | 99.9% Uptime', 'Logistics & Transport', 'projects/proj_691487ae9646a.jpg', '2023-06-20', 'Internal', 3, 1, '2025-11-01 09:58:24', '2025-11-12 13:12:14');

-- --------------------------------------------------------

--
-- Table structure for table `quote_requests`
--

CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `industry` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `delivery_location` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `attachment_path` varchar(500) DEFAULT NULL,
  `attachment_original_name` varchar(255) DEFAULT NULL,
  `status` enum('new','in_progress','quoted','completed','cancelled') DEFAULT 'new',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `service` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote_requests`
--

INSERT INTO `quote_requests` (`id`, `company_name`, `industry`, `contact_person`, `position`, `email`, `phone`, `delivery_location`, `message`, `attachment_path`, `attachment_original_name`, `status`, `priority`, `notes`, `ip_address`, `user_agent`, `created_at`, `updated_at`, `service`) VALUES
(13, 'asdas', 'transportation', 'ej romero', 'asdasd', 'ejromero294@gmail.com', '+639103443488', 'gensan', 'pagdali uy', 'uploads/quote_691a91171c694_1763348759.jpg', 'truck2.jpg', 'new', 'medium', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-17 03:05:59', '2025-11-17 03:05:59', 'Furniture'),
(14, 'asdas', 'construction', 'ej romero', 'asdasd', 'ejromero294@gmail.com', '+639103443488', 'gensan', '', NULL, NULL, 'in_progress', 'urgent', '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-17 03:13:51', '2025-11-17 03:19:53', 'Hazardous Waste'),
(15, 'asdas', 'transportation', 'ej romero', 'asdasd', 'ejromero294@gmail.com', '+639103443488', 'gensan', 'asdas', 'uploads/quote_691aa9075e424_1763354887.jpg', 'truck2.jpg', 'new', 'medium', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-17 04:48:07', '2025-11-17 04:48:07', 'Hazardous Waste');

-- --------------------------------------------------------

--
-- Table structure for table `services_content`
--

CREATE TABLE `services_content` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_icon` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services_content`
--

INSERT INTO `services_content` (`id`, `service_name`, `service_description`, `service_icon`, `image_path`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Palm Oil Distributionssss', 'Premium quality palm oil for food manufacturing, cosmetics, and biofuel industries. Temperature-controlled storage and', 'palm-oil', '', 1, 1, '2025-11-01 05:14:45', '2025-11-02 05:28:03'),
(2, 'Coconut Oil Products', 'Virgin, refined, and organic coconut oils for culinary, cosmetic, and industrial applications. Sourced from premium Philippine coconuts.', 'coconut-oil', NULL, 3, 1, '2025-11-01 05:14:45', '2025-11-01 09:35:36'),
(3, 'Transportation & Logistics', 'Nationwide fleet of tankers, wing vans, and specialized vehicles for safe, efficient cargo transport with real-time tracking.', 'transport', NULL, 5, 1, '2025-11-01 05:14:45', '2025-11-01 09:35:36'),
(4, 'Hazardous Waste Management', 'Comprehensive hazardous waste collection, treatment, and disposal services with full DENR compliance and environmental safety.', 'hazardous', NULL, 7, 1, '2025-11-01 05:14:45', '2025-11-01 09:35:36'),
(5, 'Furniture Logistics', 'White-glove delivery, assembly, and installation services for office and residential furniture with premium care and insurance coverage.', 'furniture', NULL, 9, 1, '2025-11-01 05:14:45', '2025-11-01 09:35:36'),
(6, 'Industrial Lubricants', 'High-performance lubricants for manufacturing, automotive, and heavy equipment industries. Extended equipment life and reduced maintenance.', 'lubricants', NULL, 11, 1, '2025-11-01 05:14:45', '2025-11-01 09:35:36'),
(7, 'Oil Transport Services', 'Safe and efficient transportation of oil products using our modern fleet of tanker trucks and specialized vehicles.', 'truck', NULL, 2, 1, '2025-11-01 07:35:43', '2025-11-01 09:35:36'),
(8, 'Oil Refining Solutions', 'Advanced refining processes to ensure high-quality oil products meeting industry standards and specifications.', 'refinery', NULL, 4, 1, '2025-11-01 07:35:43', '2025-11-01 09:35:36'),
(9, 'Fuel Distribution', 'Reliable distribution network ensuring timely delivery of fuel products to various industries and locations.', 'distribution', NULL, 6, 1, '2025-11-01 07:35:43', '2025-11-01 09:35:36'),
(10, 'Bulk Oil Supply', 'Supply of bulk oil products for industrial, commercial, and agricultural applications.', 'bulk', NULL, 8, 1, '2025-11-01 07:35:43', '2025-11-01 09:35:36'),
(11, 'Logistics Management', 'Comprehensive logistics solutions for efficient supply chain management and delivery optimization.', 'logistics', 'uploads/services/service_6912b4c7d7c98.jpg', 10, 1, '2025-11-01 07:35:43', '2025-11-11 04:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `staff_profiles`
--

CREATE TABLE `staff_profiles` (
  `id` int(11) NOT NULL,
  `staff_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_profiles`
--

INSERT INTO `staff_profiles` (`id`, `staff_name`, `position`, `department`, `bio`, `email`, `phone`, `photo_path`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'John Michael Santos', 'Chief Executive Officer', 'Management', 'With over 20 years of experience in the oil industry, John leads Power Giant RMT with vision and expertise.', 'john.santos@powergiantrmt.com', '+63 917 123 4567', NULL, 1, 1, '2025-11-01 07:35:43', '2025-11-01 07:35:43'),
(2, 'Maria Cristina Reyes', 'Operations Manager', 'Operations', 'Maria ensures smooth operations and efficient delivery of all our oil transport and distribution services.', 'maria.reyes@powergiantrmt.com', '+63 918 234 5678', NULL, 2, 1, '2025-11-01 07:35:43', '2025-11-01 07:35:43'),
(3, 'Robert Lim', 'Head of Logistics', 'Logistics', 'Robert manages our logistics network and ensures timely delivery to all our valued clients.', 'robert.lim@powergiantrmt.com', '+63 919 345 6789', NULL, 3, 1, '2025-11-01 07:35:43', '2025-11-01 07:35:43'),
(4, 'Sarah Gonzales', 'Quality Control Manager', 'Quality Assurance', 'Sarah oversees all quality control processes to maintain our high standards of product quality.', 'sarah.gonzales@powergiantrmt.com', '+63 920 456 7890', NULL, 4, 1, '2025-11-01 07:35:43', '2025-11-01 07:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `author_name` varchar(100) NOT NULL,
  `author_position` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `author_name`, `author_position`, `company`, `testimonial_text`, `avatar_path`, `rating`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Roberto Santos', 'Operations Director', 'Manila Steel Corp2', 'nice nice nice', 'testimonials/testimonial_6914826d7b96c.jpg', 5, 1, 1, '2025-11-02 05:33:48', '2025-11-12 20:49:49'),
(2, 'Maria Gonzales', 'Fleet Manager', 'Cebu Logistics Inc', 'NICE NICE NICE', 'testimonials/testimonial_691492b772dae.png', 5, 2, 1, '2025-11-02 05:33:48', '2025-11-12 21:59:19'),
(3, 'Capt. James Rodriguez', 'Port Operations Manager', 'Davao Marine Terminal', 'NICE NICE NICE', 'testimonials/testimonial_691492c1c5a8d.png', 5, 3, 1, '2025-11-02 05:33:48', '2025-11-12 21:59:29'),
(4, 'KULOT', 'MANAGER', 'Manila Steel Corp', 'NICE NICE NICE', 'testimonials/testimonial_6916cbea132df.png', 4, 0, 1, '2025-11-12 12:24:47', '2025-11-14 14:27:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`),
  ADD KEY `idx_content_type` (`content_type`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quote_id` (`quote_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_perm` (`role`,`permission`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `compliance_content`
--
ALTER TABLE `compliance_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `contact_content`
--
ALTER TABLE `contact_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `homepage_hero`
--
ALTER TABLE `homepage_hero`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage_stats`
--
ALTER TABLE `homepage_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_content`
--
ALTER TABLE `news_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_publish_date` (`publish_date`),
  ADD KEY `idx_is_published` (`is_published`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects_content`
--
ALTER TABLE `projects_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`project_category`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_company` (`company_name`);

--
-- Indexes for table `services_content`
--
ALTER TABLE `services_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `compliance_content`
--
ALTER TABLE `compliance_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `contact_content`
--
ALTER TABLE `contact_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `homepage_hero`
--
ALTER TABLE `homepage_hero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `homepage_stats`
--
ALTER TABLE `homepage_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news_content`
--
ALTER TABLE `news_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `projects_content`
--
ALTER TABLE `projects_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quote_requests`
--
ALTER TABLE `quote_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `services_content`
--
ALTER TABLE `services_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quote_requests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
