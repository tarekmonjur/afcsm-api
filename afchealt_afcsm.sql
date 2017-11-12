-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2017 at 10:43 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `afchealt_afcsm`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `company_logo` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_license_number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_status` enum('verified','unverified') COLLATE utf8mb4_unicode_ci DEFAULT 'unverified',
  `company_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_details` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_mobile_no` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_name`) VALUES
(1, 'Square Pharmaceuticals Ltd'),
(2, 'Eskayef Bangladesh Ltd'),
(3, 'Novo Nordisk'),
(4, 'Radiant Pharmaceuticals Ltd'),
(5, 'Unimed And Unihealth Ltd'),
(6, 'Pacific Pharmaceuticals Ltd'),
(7, 'Active Fine Chemicals Ltd'),
(8, 'Aristopharma Ltd'),
(9, 'Incepta Pharmaceuticals Ltd'),
(10, 'Popular Pharmaceuticals Ltd'),
(11, 'Healthcare Pharmaceuticals Ltd'),
(12, 'Globe Pharmaceuticals Ltd'),
(13, 'Renata Ltd'),
(14, 'Opsonin Pharma Ltd'),
(15, 'ACI Ltd'),
(16, 'Ibn Sina Pharmaceutical Ind. Ltd'),
(17, 'Beacon Pharmaceuticals Ltd'),
(18, 'Beximco Pharmaceuticals Ltd'),
(19, 'Acme Laboratories Ltd'),
(20, 'Sanofi-Aventis Bangladesh Ltd'),
(21, 'General Pharmaceuticals Ltd'),
(22, 'Zuellig Pharma Bangladesh Ltd'),
(23, 'Orion Pharma Ltd'),
(24, 'GlaxoSmithKline Bangladesh Ltd'),
(25, 'Nuvista Pharma Ltd'),
(26, 'Reckitt Benkiser BD Ltd'),
(27, 'Novartis (Bangladesh) Ltd'),
(28, 'Drug International Ltd'),
(29, 'Sanofi-Aventis Bangladesh Ltd'),
(30, 'Nipro JMI Pharma Ltd.'),
(31, 'Biopharma Ltd');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `contact_mobile` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `national_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `area` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `present_address` text COLLATE utf8mb4_unicode_ci,
  `permanent_address` text COLLATE utf8mb4_unicode_ci,
  `mr_api_version` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sm_api_version` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sa_api_version` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sp_api_version` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sh_api_version` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_version_name` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_version_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuid` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firebase_id` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT '0',
  `updated_by` int(10) UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_visit_histories`
--

CREATE TABLE `doctor_visit_histories` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `doctor_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_designation` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_chamber` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_duration` float(2,2) DEFAULT NULL,
  `visit_purpose` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sp_verified` enum('Yes','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `remarks` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `education_title` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education_board` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `institute_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass_year` year(4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT '0',
  `updated_by` int(10) UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `position_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` enum('Yes','No') COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `company_verify` enum('verified','unverified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unverified',
  `company_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `user_id`, `company_id`, `position_name`, `department_name`, `start_date`, `end_date`, `is_current`, `company_verify`, `company_address`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'aaa', 'bbb', '2017-01-01', NULL, 'No', '', 'aabbb', 0, 0, '2017-10-08 18:43:39', '2017-10-08 18:43:39', NULL),
(2, 1, 2, 'aaab', 'bbb', '2017-01-01', '0000-00-00', 'Yes', '', 'aabbb', 0, 0, '2017-10-08 18:44:27', '2017-10-08 19:13:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `prescription` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_mobile` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_experiences`
--

CREATE TABLE `product_experiences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `brand_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generic_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('verified','unverified') COLLATE utf8mb4_unicode_ci DEFAULT 'unverified',
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED DEFAULT '0',
  `api_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_no` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '''own code''',
  `referrer_code` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('mr','company','doctor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('verified','unverified') COLLATE utf8mb4_unicode_ci DEFAULT 'unverified',
  `profile_status` enum('complete','incomplete') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `api_token`, `full_name`, `email`, `mobile_no`, `password`, `pin`, `referral_code`, `referrer_code`, `gender`, `user_type`, `photo`, `status`, `profile_status`, `city`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 'ZxYCSQyoz4pmW08DXezNG0hgly7C4iFI1507488096', 'tarek', 'tarek@gmail.com', '01234567890', '$2y$10$srgJW8Dlfj3gmdmIsBgKyeRy1wC7CyFqDmogSbfUEpQE6l5gH5uMi', '342312', 'tarek001908', '', NULL, 'mr', NULL, 'verified', NULL, NULL, 0, 0, '2017-10-08 18:39:02', '2017-10-08 18:41:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_companies_company1_idx` (`company_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contacts_users1_idx` (`user_id`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_details_users1_idx` (`user_id`);

--
-- Indexes for table `doctor_visit_histories`
--
ALTER TABLE `doctor_visit_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doctor_visits_users1_idx` (`user_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_educations_users1_idx` (`user_id`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_experiences_users1_idx` (`user_id`),
  ADD KEY `fk_experiences_company1_idx` (`company_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prescriptions_users1_idx` (`user_id`);

--
-- Indexes for table `product_experiences`
--
ALTER TABLE `product_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_experiences_users1_idx` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mobile_no_UNIQUE` (`mobile_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `doctor_visit_histories`
--
ALTER TABLE `doctor_visit_histories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_experiences`
--
ALTER TABLE `product_experiences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_companies_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `fk_contacts_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `fk_details_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `doctor_visit_histories`
--
ALTER TABLE `doctor_visit_histories`
  ADD CONSTRAINT `fk_doctor_visits_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `fk_educations_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `experiences`
--
ALTER TABLE `experiences`
  ADD CONSTRAINT `fk_experiences_company1` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_experiences_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_prescriptions_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `product_experiences`
--
ALTER TABLE `product_experiences`
  ADD CONSTRAINT `fk_product_experiences_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
