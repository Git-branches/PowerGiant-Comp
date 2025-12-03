-- =======================================
-- Database Backup: Power Giant RMT
-- Generated: 2025-11-02 11:18:19
-- Host: localhost
-- Database: powergiant_quotes
-- =======================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `about_content`;
CREATE TABLE `about_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(100) NOT NULL,
  `content_type` enum('text','image') NOT NULL,
  `content_value` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`),
  KEY `idx_content_type` (`content_type`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `about_content` VALUES
('1', 'main_title', 'text', 'About Oil Product Industries with Excellence..', NULL, '1', '2025-11-01 15:35:43', '2025-11-02 00:59:03'),
('2', 'main_description', 'text', 'POWER GIANT RMT IS A DYNAMIC AND INNOVATIVE ENTERPRISE SPECIALIZING IN TRANSPORT, REFINING AND DISTRIBUTION OF OIL PRODUCTS.', NULL, '2', '2025-11-01 15:35:43', '2025-11-01 23:16:26'),
('3', 'about_title', 'text', 'ABOUT COMPANY', NULL, '3', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('4', 'about_content_1', 'text', 'POWER GIANT RMT IS A DYNAMIC AND INNOVATIVE ENTERPRISE SPECIALIZING IN TRANSPORT, REFINING AND DISTRIBUTION OF OIL PRODUCTS. WE ARE DRIVEN BY A PASSION FOR DELIVERING RELIABLE AND COST-EFFECTIVE ENERGY SOLUTIONS TO MEET THE UNIQUE NEEDS AND GROWING DEMANDS OF OUR CUSTOMERS. THROUGH STRATEGIC PARTNERSHIP, WE ENSURE THE TIMELY AND EFFICIENT DELIVERY OF OUR PRODUCTS TO LOCAL MARKETS.', NULL, '4', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('5', 'about_content_2', 'text', 'OUR SERVICES DIVISION EXCELS IN SOURCING HIGH-QUALITY GOODS, ENSURING COMPETITIVE PRICING AND TIMELY AVAILABILITY. MEANWHILE, OUR TRANSPORT SERVICES LEVERAGE CUTTING-EDGE LOGISTICS SOLUTIONS TO GUARANTEE SAFE AND PROMPT DELIVERY. OUR SERVICES PORTFOLIO IS DESIGNED To SUPPORT BUSINESSES IN OPTIMIZING THEIR OPERATIONS, FROM SUPPLY CHAIN MANAGEMENT TO CUSTOMER SERVICE SOLUTIONS. AT POWER GIANT RMT, WE PRIDE OURSELVES ON OUR COMMITMENT TO SUSTAINABILITY, INNOVATION, AND CUSTOMER SATISFACTION, ENSURING THAT EVERY INTERACTION REFLECTS OUR CORE VALUES AND DEDICATION TO EXCELLENCE.', NULL, '5', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('6', 'main_image', 'image', NULL, '/pages/about.png', '6', '2025-11-01 15:35:43', '2025-11-01 21:45:40'),
('7', 'mission_image', 'image', NULL, '/pages/Mission.png', '7', '2025-11-01 15:35:43', '2025-11-01 21:45:56'),
('8', 'vision_image', 'image', NULL, '/pages/Vission.png', '8', '2025-11-01 15:35:43', '2025-11-01 21:46:12'),
('9', 'quality_image', 'image', NULL, '/pages/Quality.png', '9', '2025-11-01 15:35:43', '2025-11-01 21:46:15'),
('10', 'delivery_image', 'image', NULL, '/pages/Delivery.png', '10', '2025-11-01 15:35:43', '2025-11-01 21:46:18'),
('11', 'process_image', 'image', NULL, '/pages/Process.png', '11', '2025-11-01 15:35:43', '2025-11-01 21:45:12'),
('12', 'certificate_image', 'image', NULL, '/pages/cert.png', '12', '2025-11-01 15:35:43', '2025-11-01 21:46:21');

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_quote_id` (`quote_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quote_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` VALUES
('1', '1', 'created', NULL, NULL, 'New quote request submitted', '2025-10-27 22:46:17'),
('2', '1', 'status_changed', 'new', 'completed', 'Status changed from new to completed', '2025-10-27 22:52:50'),
('3', '1', 'priority_changed', 'medium', 'urgent', 'Priority changed from medium to urgent', '2025-10-27 22:52:50'),
('4', '1', 'notes_updated', 'None', 'test', 'Notes updated', '2025-10-27 22:52:50'),
('29', '1', 'priority_changed', 'urgent', 'medium', 'Priority changed from urgent to medium', '2025-10-28 12:07:26'),
('38', '1', 'status_changed', 'completed', 'quoted', 'Status changed from completed to quoted', '2025-10-28 20:07:20'),
('39', '1', 'priority_changed', 'medium', 'high', 'Priority changed from medium to high', '2025-10-28 20:07:20'),
('40', '4', 'created', NULL, NULL, 'New quote request submitted', '2025-10-28 20:17:43'),
('41', '4', 'status_changed', 'new', 'completed', 'Status changed from new to completed', '2025-10-29 12:03:14'),
('42', '4', 'priority_changed', 'medium', 'urgent', 'Priority changed from medium to urgent', '2025-10-29 12:03:14'),
('53', '10', 'created', NULL, NULL, 'New quote request submitted', '2025-11-01 14:18:01'),
('58', '10', 'status_changed', 'new', 'completed', 'Status changed from new to completed', '2025-11-01 15:57:30'),
('59', '10', 'priority_changed', 'medium', 'urgent', 'Priority changed from medium to urgent', '2025-11-01 15:57:30'),
('60', '1', 'status_changed', 'quoted', 'completed', 'Status changed from quoted to completed', '2025-11-01 15:57:37'),
('61', '10', 'priority_changed', 'urgent', 'medium', 'Priority changed from urgent to medium', '2025-11-01 20:55:47');

DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('admin','editor') NOT NULL,
  `permission` varchar(100) NOT NULL,
  `allowed` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_perm` (`role`,`permission`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_permissions` VALUES
('1', 'admin', 'manage_users', '1'),
('2', 'admin', 'delete_quotes', '1'),
('3', 'admin', 'edit_all_content', '1'),
('4', 'admin', 'view_all_quotes', '1'),
('5', 'admin', 'backup_database', '1'),
('6', 'admin', 'view_logs', '1'),
('7', 'editor', 'manage_users', '0'),
('8', 'editor', 'delete_quotes', '0'),
('9', 'editor', 'edit_all_content', '1'),
('10', 'editor', 'view_all_quotes', '1'),
('11', 'editor', 'backup_database', '0'),
('12', 'editor', 'view_logs', '1');

DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_user` VALUES
('1', 'powergiant@gmail.com', '$2y$10$l.ubn5g98EozPP3pIsvSuuO9nHK6Sqw5c5ngjOk9U13Fgxv0zh3se', 'admin@powergiantrmt.com', 'Administrator', 'admin', '2025-11-02 09:02:23', '2025-10-27 22:25:29', '2025-11-02 09:02:23'),
('4', 'ej', '$2y$10$9wlAIDywgmVUNwY08snnZO6G6WecleXkyIuCgY438Fn8Fp2t73rbO', 'ejromero@gmail.com', 'Rhon Jon G. Romero', 'editor', '2025-11-02 08:34:26', '2025-11-02 08:33:21', '2025-11-02 08:34:26');

DROP TABLE IF EXISTS `compliance_content`;
CREATE TABLE `compliance_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `compliance_content` VALUES
('1', 'ISO 9001:2015 Certifications', 'Quality Management System Certification ensuring standardized processes and continuous improvement.', 'International Organization for Standardization', '2023-01-15', '2026-01-15', '', '', '1', '1', '2025-11-01 15:35:43', '2025-11-02 01:17:36'),
('2', 'DOE Accreditation', 'Department of Energy accreditation for oil industry operations and distribution.', 'Department of Energy - Philippines', '2023-03-20', '2025-03-20', '', '', '2', '1', '2025-11-01 15:35:43', '2025-11-01 23:48:23'),
('3', 'Environmental Compliance Certificate', 'Certificate of compliance with environmental regulations and standards.', 'Environmental Management Bureau', '2023-02-10', '2026-02-10', NULL, NULL, '3', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('4', 'Business Permit', 'Mayor\'s permit and business license for commercial operations.', 'City Government of General Santos', '2024-01-05', '2025-01-05', NULL, NULL, '4', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43');

DROP TABLE IF EXISTS `contact_content`;
CREATE TABLE `contact_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(100) NOT NULL,
  `content_type` enum('text','image','contact_info') NOT NULL,
  `content_value` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_name` (`section_name`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contact_content` VALUES
('1', 'contact_title', 'text', 'Get In Touch With Us', NULL, '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('2', 'contact_description', 'text', 'Have questions about our oil products and services? We\'re here to help you with all your energy needs.', NULL, '2', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('3', 'company_address', 'contact_info', '123 Energy Street, Industrial District, General Santos City, Philippines', NULL, '3', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('4', 'phone_number', 'contact_info', '+63 (083) 123-4567', NULL, '4', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('5', 'email_address', 'contact_info', 'info@powergiantrmt.com', NULL, '5', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('6', 'business_hours', 'contact_info', 'Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 8:00 AM - 12:00 PM', NULL, '6', '2025-11-01 15:35:43', '2025-11-01 15:35:43');

DROP TABLE IF EXISTS `home_content`;
CREATE TABLE `home_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `content_type` enum('text','image') NOT NULL,
  `content_value` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `home_content` VALUES
('1', 'hero_title_prefix', 'text', 'Delivering the Future of ', NULL, '1', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('2', 'hero_title_highlight', 'text', 'Oil Solutions', NULL, '2', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('3', 'hero_subtitle', 'text', 'Reliable and high-quality oil and energy solutions with nationwide coverage, trusted by industries and partners for their efficiency, safety, and consistent performance.', NULL, '3', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('4', 'hero_cta1_text', 'text', 'Get Quote Nowwwww', NULL, '4', '2025-11-02 10:57:45', '2025-11-02 11:09:07'),
('5', 'hero_cta1_link', 'text', 'contact.php', NULL, '5', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('6', 'hero_cta2_text', 'text', 'Explore Servicesss', NULL, '6', '2025-11-02 10:57:45', '2025-11-02 11:08:38'),
('7', 'hero_cta2_link', 'text', '#services', NULL, '7', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('8', 'hero_stat1_value', 'text', '1', NULL, '8', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('9', 'hero_stat1_label', 'text', 'Oil Stations', NULL, '9', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('10', 'hero_stat2_value', 'text', '20', NULL, '10', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('11', 'hero_stat2_label', 'text', 'Years Experience', NULL, '11', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('12', 'hero_stat3_value', 'text', '5', NULL, '12', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('13', 'hero_stat3_label', 'text', 'Corporate Partners', NULL, '13', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('14', 'hero_stat4_value', 'text', '100', NULL, '14', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('15', 'hero_stat4_label', 'text', 'Safety Compliance', NULL, '15', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('16', 'hero_slide', 'image', NULL, '../css/images/truck4.jpg', '1', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('17', 'hero_slide', 'image', NULL, '../css/images/truck1.jpg', '2', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('18', 'hero_slide', 'image', NULL, 'https://images.unsplash.com/photo-1623986854615-85baba27dfb6', '3', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('19', 'hero_slide', 'image', NULL, 'https://images.unsplash.com/photo-1504567534837-bf97dc86eba8', '4', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('20', 'hero_slide', 'image', NULL, 'https://images.unsplash.com/photo-1655912129521-52e3f050453b', '5', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('21', 'testimonial', 'text', 'Power Giant RMT Oil has been our reliable oil partner for 8 years. Their 24/7 delivery service and consistent quality have never let us down during critical production periods.|Roberto Santos|Operations Director|Manila Steel Corp|5|https://images.unsplash.com/photo-1719993919800-630021837af9|https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=150', NULL, '1', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('22', 'testimonial', 'text', 'The oil quality from Power Giant RMT Oil has improved our fleet efficiency by 15%. Their nationwide coverage ensures our operations run smoothly across all regions.|Maria Gonzales|Fleet Manager|Cebu Logistics Inc|5|https://images.unsplash.com/photo-1733221936268-328d9766a9e0|https://images.pixabay.com/photo/2017/06/26/02/47/man-2442565_150.jpg', NULL, '2', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('23', 'testimonial', 'text', 'Their marine oil meets all international standards. We\'ve reduced maintenance costs by 20% since switching to Power Giant RMT Oil\'s premium marine diesel.|Capt. James Rodriguez|Port Operations Manager|Davao Marine Terminal|5|https://images.unsplash.com/photo-1708121449675-c546b3775588|https://images.pixabay.com/photo/2016/11/21/12/42/beard-1845166_150.jpg', NULL, '3', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('24', 'testimonial', 'text', 'Power Giant Rmt Oil\'s technical support team helped us optimize our oil consumption. We\'ve seen a 12% reduction in operational costs while maintaining output quality.|Elena Dela Cruz|CEO|Mindanao Power Solutions|5|https://images.unsplash.com/photo-1580489944761-15a19d654956|https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=150', NULL, '4', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('25', 'testimonial', 'text', 'The consistency in oil quality from POWER GIANT RMT Oil has eliminated our equipment downtime issues. Their emergency response team is exceptionally reliable.|Michael Tan|Plant Manager|Visayas Manufacturing Corp|5|https://images.unsplash.com/photo-1472099645785-5658abf4ff4e|https://images.pexels.com/photos/2379004/pexels-photo-2379004.jpeg?auto=compress&cs=tinysrgb&w=150', NULL, '5', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('26', 'partner', 'text', 'ACME Corp', NULL, '1', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('27', 'partner', 'text', 'TechFlow', NULL, '2', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('28', 'partner', 'text', 'GlobalTech', NULL, '3', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('29', 'partner', 'text', 'InnovateCo', NULL, '4', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('30', 'partner', 'text', 'FutureTech', NULL, '5', '2025-11-02 10:57:45', '2025-11-02 10:57:45'),
('31', 'partner', 'text', 'NextGen', NULL, '6', '2025-11-02 10:57:45', '2025-11-02 10:57:45');

DROP TABLE IF EXISTS `news_content`;
CREATE TABLE `news_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(255) NOT NULL,
  `news_content` text NOT NULL,
  `news_excerpt` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_publish_date` (`publish_date`),
  KEY `idx_is_published` (`is_published`),
  KEY `idx_display_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `news_content` VALUES
('1', 'Power Giant RMT Opens ₱1.2B Green Fuel Depot in Visayas', 'The new facility in Cebu features solar-powered operations, waste heat recovery, and real-time emissions monitoring — setting a new standard for sustainable energy infrastructure.', 'First carbon-neutral fuel terminal in Southeast Asia', 'uploads/news/news_1.jpg', 'Corporate Communications', '2025-10-28', '1', '1', '2025-11-01 19:00:27', '2025-11-02 09:03:26'),
('2', 'DENR Awards Power Giant \"Green Enterprise of the Year\"', 'Power Giant RMT receives top environmental award for innovative waste management and green energy solutions.', 'Recognition for sustainable operations', 'uploads/news/news_2.jpg', 'Environment Team', '2025-10-25', '1', '2', '2025-11-01 19:00:27', '2025-11-01 19:00:27'),
('3', '10,000 Trees Planted in Reforestation Drive', 'Massive reforestation effort across Mindanao to combat climate change and promote biodiversity.', 'Community environmental initiative', 'uploads/news/news_3.jpg', 'CSR Department', '2025-10-20', '1', '3', '2025-11-01 19:00:27', '2025-11-01 19:00:27'),
('4', '5 Years Zero Incident Safety Milestone', 'Company celebrates five consecutive years without any safety incidents across all operations.', 'Perfect safety record achievement', 'uploads/news/news_4.jpg', 'Safety Division', '2025-10-15', '1', '4', '2025-11-01 19:00:27', '2025-11-01 19:00:27'),
('5', 'Solar-Powered Tanker Fleet Launching', 'Innovative solar-powered tankers set new standards for sustainable fuel transportation.', 'Hybrid solar-diesel tankers reduce fuel use by 30%', 'uploads/news/news_5.jpg', 'Marine Operations', '2025-10-10', '1', '5', '2025-11-01 19:00:27', '2025-11-02 09:03:42'),
('6', 'Strategic Alliance with Shell Philippines', 'Partnership expands marine fuel distribution network across Visayas and Mindanao.', 'Joint venture for marine fuel supply in VisMin', 'uploads/news/news_6.jpg', 'Business Development', '2025-10-05', '1', '6', '2025-11-01 19:00:27', '2025-11-01 19:00:27'),
('7', '40% Women in Leadership Roles', 'Company achieves significant milestone in gender diversity and inclusion.', 'Power Giant leads gender equality in energy sector', 'uploads/news/news_7.jpg', 'HR Department', '2025-09-30', '1', '7', '2025-11-01 19:00:27', '2025-11-01 19:00:27');

DROP TABLE IF EXISTS `projects_content`;
CREATE TABLE `projects_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_title` varchar(255) NOT NULL,
  `project_description` text DEFAULT NULL,
  `project_category` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`project_category`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `projects_content` VALUES
('1', 'Batangas Hazardous Waste Treatment Plant', '50,000 MT/year capacity | DENR TSDF Certified | Zero Spill Record', 'Hazardous Waste Management', 'uploads/projects/proj_672a1f3e8b9d2.jpg', '2024-01-15', 'San Miguel Corp', '1', '1', '2025-11-01 17:58:24', '2025-11-01 17:58:24'),
('2', 'Cebu Bulk Fuel Terminal Expansion', '100,000 KL additional capacity | DOE Approved | Q4 2025 Target', 'Fuel Infrastructure', 'uploads/projects/proj_672a1f3e8c1a5.jpg', '2025-12-31', 'Petron Corp', '2', '1', '2025-11-01 17:58:24', '2025-11-01 17:58:24'),
('3', 'Nationwide GPS Fleet Tracking System', '200+ tankers | Real-time ETA | 99.9% Uptime', 'Logistics & Transport', 'uploads/projects/proj_672a1f3e8c5d1.jpg', '2023-06-20', 'Internal', '3', '1', '2025-11-01 17:58:24', '2025-11-01 17:58:24');

DROP TABLE IF EXISTS `quote_requests`;
CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `industry` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `volume` varchar(50) DEFAULT NULL,
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
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email` (`email`),
  KEY `idx_company` (`company_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `quote_requests` VALUES
('1', 'test123', 'transportation', 'Ronel', 'Manager', 'ejrmer@gmail.cm', '09929216022', '1,000 - 5,000 L', 'gensan', 'asdasdasd', 'uploads/quote_68ff85b98a9a6_1761576377.jpg', '111.jpg', 'completed', 'high', 'test', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 22:46:17', '2025-11-01 15:57:37'),
('4', 'Seait', 'construction', 'Aron james Barrios', 'Owner', 'aronjamesbarrios49@gmail.com', '+639091311821', '100,000+ L', 'Glamang, South Cotabato', 'Test', 'uploads/quote_6900b467b8398_1761653863.jpg', '1000027778.jpg', 'completed', 'urgent', '', '192.168.254.102', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-28 20:17:43', '2025-10-29 12:03:14'),
('10', 'asdas', 'agriculture', 'Ronel', 'asdasd', 'ejromero@gmail.com', '+639103443488', '5,000 - 10,000 L', 'gensan', 'test123', 'uploads/quote_6905a6195c70e_1761977881.jpg', '111.jpg', 'completed', 'medium', '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-11-01 14:18:01', '2025-11-01 20:55:47');

DROP TABLE IF EXISTS `services_content`;
CREATE TABLE `services_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_icon` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `services_content` VALUES
('1', 'Palm Oil Distribution', 'Premium quality palm oil for food manufacturing, cosmetics, and biofuel industries. Temperature-controlled storage and kulot', 'palm-oil', '', '1', '1', '2025-11-01 13:14:45', '2025-11-02 00:58:04'),
('2', 'Coconut Oil Products', 'Virgin, refined, and organic coconut oils for culinary, cosmetic, and industrial applications. Sourced from premium Philippine coconuts.', 'coconut-oil', NULL, '3', '1', '2025-11-01 13:14:45', '2025-11-01 17:35:36'),
('3', 'Transportation & Logistics', 'Nationwide fleet of tankers, wing vans, and specialized vehicles for safe, efficient cargo transport with real-time tracking.', 'transport', NULL, '5', '1', '2025-11-01 13:14:45', '2025-11-01 17:35:36'),
('4', 'Hazardous Waste Management', 'Comprehensive hazardous waste collection, treatment, and disposal services with full DENR compliance and environmental safety.', 'hazardous', NULL, '7', '1', '2025-11-01 13:14:45', '2025-11-01 17:35:36'),
('5', 'Furniture Logistics', 'White-glove delivery, assembly, and installation services for office and residential furniture with premium care and insurance coverage.', 'furniture', NULL, '9', '1', '2025-11-01 13:14:45', '2025-11-01 17:35:36'),
('6', 'Industrial Lubricants', 'High-performance lubricants for manufacturing, automotive, and heavy equipment industries. Extended equipment life and reduced maintenance.', 'lubricants', NULL, '11', '1', '2025-11-01 13:14:45', '2025-11-01 17:35:36'),
('7', 'Oil Transport Services', 'Safe and efficient transportation of oil products using our modern fleet of tanker trucks and specialized vehicles.', 'truck', NULL, '2', '1', '2025-11-01 15:35:43', '2025-11-01 17:35:36'),
('8', 'Oil Refining Solutions', 'Advanced refining processes to ensure high-quality oil products meeting industry standards and specifications.', 'refinery', NULL, '4', '1', '2025-11-01 15:35:43', '2025-11-01 17:35:36'),
('9', 'Fuel Distribution', 'Reliable distribution network ensuring timely delivery of fuel products to various industries and locations.', 'distribution', NULL, '6', '1', '2025-11-01 15:35:43', '2025-11-01 17:35:36'),
('10', 'Bulk Oil Supply', 'Supply of bulk oil products for industrial, commercial, and agricultural applications.', 'bulk', NULL, '8', '1', '2025-11-01 15:35:43', '2025-11-01 17:35:36'),
('11', 'Logistics Management', 'Comprehensive logistics solutions for efficient supply chain management and delivery optimization.', 'logistics', NULL, '10', '1', '2025-11-01 15:35:43', '2025-11-01 17:35:36');

DROP TABLE IF EXISTS `staff_profiles`;
CREATE TABLE `staff_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_department` (`department`),
  KEY `idx_display_order` (`display_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `staff_profiles` VALUES
('1', 'John Michael Santos', 'Chief Executive Officer', 'Management', 'With over 20 years of experience in the oil industry, John leads Power Giant RMT with vision and expertise.', 'john.santos@powergiantrmt.com', '+63 917 123 4567', NULL, '1', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('2', 'Maria Cristina Reyes', 'Operations Manager', 'Operations', 'Maria ensures smooth operations and efficient delivery of all our oil transport and distribution services.', 'maria.reyes@powergiantrmt.com', '+63 918 234 5678', NULL, '2', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('3', 'Robert Lim', 'Head of Logistics', 'Logistics', 'Robert manages our logistics network and ensures timely delivery to all our valued clients.', 'robert.lim@powergiantrmt.com', '+63 919 345 6789', NULL, '3', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43'),
('4', 'Sarah Gonzales', 'Quality Control Manager', 'Quality Assurance', 'Sarah oversees all quality control processes to maintain our high standards of product quality.', 'sarah.gonzales@powergiantrmt.com', '+63 920 456 7890', NULL, '4', '1', '2025-11-01 15:35:43', '2025-11-01 15:35:43');

-- Backup completed successfully
