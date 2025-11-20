-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 08:58 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mmgc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('scheduled','confirmed','completed','cancelled','no-show') DEFAULT 'scheduled',
  `appointment_type` enum('new','follow-up') DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `start_time`, `end_time`, `status`, `appointment_type`, `notes`, `created_at`) VALUES
(2, 1, 1, '2025-11-21', '09:00:00', '09:30:00', 'scheduled', 'new', 'Initial consultation', '2025-11-20 06:39:01'),
(3, 2, 2, '2025-11-21', '10:00:00', '10:30:00', 'confirmed', 'new', 'Routine checkup', '2025-11-20 06:39:01'),
(4, 3, 1, '2025-11-22', '11:00:00', '11:30:00', 'scheduled', 'follow-up', 'Follow-up on treatment', '2025-11-20 06:39:01'),
(5, 4, 3, '2025-11-22', '12:00:00', '12:30:00', 'completed', 'new', 'Blood test review', '2025-11-20 06:39:01'),
(6, 5, 2, '2025-11-23', '09:30:00', '10:00:00', 'cancelled', 'new', 'Cancelled by patient', '2025-11-20 06:39:01'),
(7, 1, 3, '2025-11-23', '10:30:00', '11:00:00', 'scheduled', 'follow-up', 'Post-surgery check', '2025-11-20 06:39:01'),
(8, 2, 1, '2025-11-24', '11:00:00', '11:30:00', 'confirmed', 'new', 'Consultation for symptoms', '2025-11-20 06:39:01'),
(9, 3, 2, '2025-11-24', '12:00:00', '12:30:00', 'scheduled', 'follow-up', 'Routine follow-up', '2025-11-20 06:39:01'),
(10, 4, 3, '2025-11-25', '09:00:00', '09:30:00', 'no-show', 'new', 'Patient did not attend', '2025-11-20 06:39:01'),
(11, 5, 1, '2025-11-25', '10:00:00', '10:30:00', 'scheduled', 'new', 'General consultation', '2025-11-20 06:39:01');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `specialization` varchar(150) DEFAULT NULL,
  `qualification` varchar(150) DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `full_name`, `email`, `phone`, `password`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `profile_image`, `bio`, `status`, `created_at`) VALUES
(1, 'Dr. Ahsan Malik', 'ahsan.malik@example.com', '03001234567', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Cardiologist', 'MBBS, FCPS', 8, 1500.00, 'uploads/1763617902_Doctor.jpg', 'Experienced cardiologist specializing in heart diseases.', 'active', '2025-11-20 05:54:37'),
(2, 'Dr. Sara Khan', 'sara.khan@example.com', '03014567890', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Dermatologist', 'MBBS, MD', 5, 1200.00, 'uploads/1763617902_Doctor.jpg', 'Skin specialist with focus on cosmetic dermatology.', 'active', '2025-11-20 05:54:37'),
(3, 'Dr. Bilal Ahmed', 'bilal.ahmed@example.com', '03131234566', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Neurologist', 'MBBS, FCPS', 10, 2000.00, 'uploads/1763617902_Doctor.jpg', 'Neurologist with extensive experience in brain and nerve disorders.', 'active', '2025-11-20 05:54:37'),
(4, 'Dr. Mehwish Tariq', 'mehwish.tariq@example.com', '03339876543', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Gynecologist', 'MBBS, FCPS', 7, 1800.00, 'uploads/1763617902_Doctor.jpg', 'Specialist in women health and maternity care.', 'active', '2025-11-20 05:54:37'),
(5, 'Dr. Imran Saleem', 'imran.saleem@example.com', '03025678901', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Orthopedic Surgeon', 'MBBS, MS Ortho', 12, 2500.00, 'uploads/1763617902_Doctor.jpg', 'Expert in bone fractures and joint replacement.', 'active', '2025-11-20 05:54:37'),
(6, 'Dr. Nadia Rehman', 'nadia.rehman@example.com', '03211223344', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Pediatrician', 'MBBS, DCH', 6, 1000.00, 'uploads/1763617902_Doctor.jpg', 'Child specialist with focus on infant care.', 'active', '2025-11-20 05:54:37'),
(7, 'Dr. Usman Zafar', 'usman.zafar@example.com', '03451239876', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'ENT Specialist', 'MBBS, FCPS', 9, 1600.00, 'uploads/1763617902_Doctor.jpg', 'Specialized in ear, nose, and throat treatments.', 'active', '2025-11-20 05:54:37'),
(8, 'Dr. Ayesha Siddiqui', 'ayesha.siddiqui@example.com', '03145566778', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Psychologist', 'MSc Psychology', 4, 1300.00, 'uploads/1763617902_Doctor.jpg', 'Mental health specialist focusing on therapy.', 'active', '2025-11-20 05:54:37'),
(9, 'Dr. Khalid Niaz', 'khalid.niaz@example.com', '03030098765', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'General Physician', 'MBBS', 15, 900.00, 'uploads/1763617902_Doctor.jpg', 'General physician with broad experience.', 'active', '2025-11-20 05:54:37'),
(10, 'Dr. Rabia Noor', 'rabia.noor@example.com', '03334455667', '*FB6E1F205D675BC29B052DB14CCEFE7759C5FF7E', 'Nutritionist', 'BS Nutrition, MS Clinical Nutrition', 3, 1100.00, 'uploads/1763617902_Doctor.jpg', 'Diet and nutrition expert.', 'active', '2025-11-20 05:54:37');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `day_of_week` enum('Mon','Tue','Wed','Thu','Fri','Sat','Sun') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `slot_duration_minutes` int(11) DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedules`
--

INSERT INTO `doctor_schedules` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration_minutes`) VALUES
(2, 1, 'Mon', '09:00:00', '12:00:00', 15),
(3, 1, 'Wed', '13:00:00', '17:00:00', 15),
(4, 1, 'Fri', '10:00:00', '14:00:00', 15),
(5, 2, 'Tue', '08:00:00', '12:00:00', 20),
(6, 2, 'Thu', '09:00:00', '13:00:00', 20),
(7, 2, 'Sat', '10:00:00', '14:00:00', 20),
(8, 3, 'Mon', '14:00:00', '18:00:00', 30),
(9, 3, 'Wed', '09:00:00', '12:00:00', 30),
(10, 3, 'Fri', '08:00:00', '11:00:00', 30),
(11, 3, 'Sun', '10:00:00', '14:00:00', 30);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(30) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` enum('appointment','procedure','lab_test','consultation','other') NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL,
  `payment_status` enum('pending','partial','paid','refunded') DEFAULT 'pending',
  `issued_date` date DEFAULT curdate(),
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `patient_id`, `description`, `type`, `total_amount`, `discount`, `net_amount`, `payment_status`, `issued_date`, `due_date`, `created_at`) VALUES
(14, 'INV-1001', 1, 'General Consultation', 'consultation', 2000.00, 0.00, 2000.00, 'paid', '2025-11-01', '2025-11-10', '2025-11-20 07:52:23'),
(15, 'INV-1002', 2, 'Blood Test', 'lab_test', 1500.00, 100.00, 1400.00, 'partial', '2025-11-02', '2025-11-12', '2025-11-20 07:52:23'),
(16, 'INV-1003', 3, 'Ultrasound Scan', 'procedure', 5000.00, 500.00, 4500.00, 'pending', '2025-11-03', '2025-11-13', '2025-11-20 07:52:23'),
(17, 'INV-1004', 4, 'C-Section Surgery', 'procedure', 25000.00, 2000.00, 23000.00, 'paid', '2025-11-04', '2025-11-20', '2025-11-20 07:52:23'),
(18, 'INV-1005', 5, 'X-Ray Imaging', 'lab_test', 1000.00, 0.00, 1000.00, 'paid', '2025-11-05', '2025-11-15', '2025-11-20 07:52:23'),
(19, 'INV-1006', 1, 'Follow-up Consultation', 'consultation', 1500.00, 0.00, 1500.00, 'pending', '2025-11-06', '2025-11-16', '2025-11-20 07:52:23'),
(20, 'INV-1007', 2, 'MRI Scan', 'lab_test', 12000.00, 1000.00, 11000.00, 'partial', '2025-11-07', '2025-11-17', '2025-11-20 07:52:23'),
(21, 'INV-1008', 3, 'Normal Delivery', 'procedure', 20000.00, 0.00, 20000.00, 'paid', '2025-11-08', '2025-11-22', '2025-11-20 07:52:23'),
(22, 'INV-1009', 4, 'Gynaecology Consultation', 'consultation', 2500.00, 0.00, 2500.00, 'paid', '2025-11-09', '2025-11-19', '2025-11-20 07:52:23'),
(23, 'INV-1010', 5, 'Lab Combo Package', 'lab_test', 3000.00, 300.00, 2700.00, 'pending', '2025-11-10', '2025-11-25', '2025-11-20 07:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `lab_categories`
--

CREATE TABLE `lab_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `default_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_categories`
--

INSERT INTO `lab_categories` (`id`, `name`, `description`, `default_price`, `created_at`) VALUES
(1, 'Complete Blood Count', 'Basic blood test for RBC, WBC, platelets', 50.00, '2025-11-20 06:51:54'),
(2, 'Blood Sugar', 'Fasting and postprandial sugar levels', 30.00, '2025-11-20 06:51:54'),
(3, 'Lipid Profile', 'Cholesterol, triglycerides, HDL, LDL', 80.00, '2025-11-20 06:51:54'),
(4, 'Liver Function Test', 'ALT, AST, bilirubin levels', 70.00, '2025-11-20 06:51:54'),
(5, 'Kidney Function Test', 'Creatinine, BUN, electrolytes', 60.00, '2025-11-20 06:51:54'),
(6, 'Urine Analysis', 'Routine urine test', 25.00, '2025-11-20 06:51:54'),
(7, 'Thyroid Profile', 'TSH, T3, T4 levels', 90.00, '2025-11-20 06:51:54'),
(8, 'Vitamin D Test', '25(OH) Vitamin D levels', 120.00, '2025-11-20 06:51:54'),
(9, 'Electrolytes Test', 'Sodium, potassium, chloride', 40.00, '2025-11-20 06:51:54'),
(10, 'Pregnancy Test', 'HCG hormone detection', 20.00, '2025-11-20 06:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `lab_reports`
--

CREATE TABLE `lab_reports` (
  `id` int(11) NOT NULL,
  `lab_test_id` int(11) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('pdf','image','text') NOT NULL,
  `uploaded_by` int(11) NOT NULL COMMENT 'staff.id or doctor.id',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `doctor_approved` tinyint(1) DEFAULT 0,
  `doctor_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `test_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `lab_staff_id` int(11) DEFAULT NULL,
  `sample_collected_at` datetime DEFAULT NULL,
  `report_status` enum('pending','processing','ready','delivered') DEFAULT 'pending',
  `is_urgent` tinyint(1) DEFAULT 0,
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `patient_id`, `doctor_id`, `category_id`, `test_name`, `price`, `lab_staff_id`, `sample_collected_at`, `report_status`, `is_urgent`, `booked_at`) VALUES
(2, 1, 1, 1, 'Complete Blood Count', 50.00, 2, '2025-11-21 09:00:00', 'pending', 0, '2025-11-20 06:58:54'),
(3, 2, 2, 2, 'Blood Sugar Test', 30.00, 2, '2025-11-21 09:30:00', 'pending', 0, '2025-11-20 06:58:54'),
(4, 3, 1, 3, 'Lipid Profile', 80.00, 2, '2025-11-21 10:00:00', 'processing', 1, '2025-11-20 06:58:54'),
(5, 4, 3, 4, 'Liver Function Test', 70.00, 2, '2025-11-21 10:30:00', 'ready', 0, '2025-11-20 06:58:54'),
(6, 5, 2, 5, 'Kidney Function Test', 60.00, 2, '2025-11-21 11:00:00', 'delivered', 0, '2025-11-20 06:58:54'),
(7, 6, 1, 6, 'Urine Analysis', 25.00, 2, '2025-11-21 11:30:00', 'pending', 0, '2025-11-20 06:58:54'),
(8, 7, 3, 7, 'Thyroid Profile', 90.00, 2, '2025-11-21 12:00:00', 'processing', 1, '2025-11-20 06:58:54'),
(9, 8, 4, 8, 'Vitamin D Test', 120.00, 2, '2025-11-21 12:30:00', 'ready', 0, '2025-11-20 06:58:54'),
(10, 9, 5, 9, 'Electrolytes Test', 40.00, 2, '2025-11-21 13:00:00', 'pending', 0, '2025-11-20 06:58:54'),
(11, 10, 1, 10, 'Pregnancy Test', 20.00, 2, '2025-11-21 13:30:00', 'delivered', 0, '2025-11-20 06:58:54'),
(12, 2, 2, 1, 'Blood Test', 500.00, NULL, NULL, 'pending', 0, '2025-11-20 07:54:52'),
(13, 2, 3, 1, 'X-Ray', 1000.00, NULL, NULL, 'pending', 0, '2025-11-20 07:54:52'),
(14, 4, 2, 2, 'MRI Scan', 5000.00, NULL, NULL, 'pending', 0, '2025-11-20 07:54:52'),
(15, 1, 3, 2, 'Urine Test', 300.00, NULL, NULL, 'pending', 0, '2025-11-20 07:54:52'),
(16, 3, 4, 1, 'ECG', 700.00, NULL, NULL, 'pending', 0, '2025-11-20 07:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `mr_number` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_phone` varchar(15) DEFAULT NULL,
  `registration_date` date DEFAULT curdate(),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `mr_number`, `name`, `email`, `phone`, `password`, `date_of_birth`, `gender`, `blood_group`, `address`, `guardian_name`, `guardian_phone`, `registration_date`, `status`, `created_at`) VALUES
(1, 'MR1001', 'Ali Khan', 'ali.khan@example.com', '03001234501', 'ali123', '1990-05-12', 'male', 'A+', '123 Street, City', 'Ahmed Khan', '03001112222', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(2, 'MR1002', 'Sara Ahmed', 'sara.ahmed@example.com', '03001234502', 'sara123', '1988-07-22', 'female', 'B+', '456 Street, City', 'Rashid Ahmed', '03003334444', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(3, 'MR1003', 'Omar Farooq', 'omar.farooq@example.com', '03001234503', 'omar123', '2000-01-15', 'male', 'O-', '789 Street, City', 'Farooq Ahmed', '03005556666', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(4, 'MR1004', 'Ayesha Iqbal', 'ayesha.iqbal@example.com', '03001234504', 'ayesha123', '1995-03-18', 'female', 'AB+', '101 Street, City', 'Iqbal Khan', '03007778888', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(5, 'MR1005', 'Hassan Ali', 'hassan.ali@example.com', '03001234505', 'hassan123', '1992-12-05', 'male', 'B-', '102 Street, City', 'Ali Khan', '03009990000', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(6, 'MR1006', 'Maryam Khan', 'maryam.khan@example.com', '03001234506', 'maryam123', '1998-09-23', 'female', 'A-', '103 Street, City', 'Saeed Khan', '03001110011', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(7, 'MR1007', 'Zain Malik', 'zain.malik@example.com', '03001234507', 'zain123', '2001-04-11', 'male', 'O+', '104 Street, City', 'Malik Ahmed', '03002220022', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(8, 'MR1008', 'Fatima Noor', 'fatima.noor@example.com', '03001234508', 'fatima123', '1997-11-30', 'female', 'AB-', '105 Street, City', 'Noor Ahmed', '03003330033', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(9, 'MR1009', 'Usman Shah', 'usman.shah@example.com', '03001234509', 'usman123', '1999-02-02', 'male', 'A+', '106 Street, City', 'Shahzad Ahmed', '03004440044', '2025-11-20', 'active', '2025-11-20 06:34:59'),
(10, 'MR1010', 'Hina Raza', 'hina.raza@example.com', '03001234510', 'hina123', '1996-06-16', 'female', 'B+', '107 Street, City', 'Raza Ahmed', '03005550055', '2025-11-20', 'active', '2025-11-20 06:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_mode` enum('cash','bank_transfer','card','online') NOT NULL,
  `transaction_ref` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedures`
--

CREATE TABLE `procedures` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `procedure_type` enum('Normal Delivery','C-Section','Ultrasound','Gynaecology Treatment','Surgery','OPD Consultation','IPD Admission','Other') NOT NULL,
  `procedure_name` varchar(200) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `nurse_id` int(11) DEFAULT NULL,
  `procedure_date` date NOT NULL,
  `procedure_time` time DEFAULT NULL,
  `status` enum('planned','in-progress','completed','cancelled') DEFAULT 'planned',
  `treatment_notes` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `outcome` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `procedures`
--

INSERT INTO `procedures` (`id`, `patient_id`, `procedure_type`, `procedure_name`, `doctor_id`, `nurse_id`, `procedure_date`, `procedure_time`, `status`, `treatment_notes`, `prescription`, `outcome`, `created_at`) VALUES
(12, 1, 'Normal Delivery', 'Normal Delivery for Patient 1', 1, 2, '2025-11-21', '09:00:00', 'planned', 'Monitor vitals', 'Painkillers', 'Successful', '2025-11-20 06:50:03'),
(13, 2, 'C-Section', 'C-Section for Patient 2', 2, 2, '2025-11-22', '10:30:00', 'planned', 'Check incision', 'Antibiotics', 'Successful', '2025-11-20 06:50:03'),
(14, 3, 'Ultrasound', 'Ultrasound scan for Patient 3', 3, 2, '2025-11-23', '11:00:00', 'planned', 'Abdominal scan', 'None', 'Normal', '2025-11-20 06:50:03'),
(15, 4, 'Gynaecology Treatment', 'Gynaecology consultation for Patient 4', 4, 2, '2025-11-24', '12:00:00', 'planned', 'Routine check', 'Vitamins', 'Improved', '2025-11-20 06:50:03'),
(16, 5, 'Surgery', 'Minor surgery for Patient 5', 5, 2, '2025-11-25', '13:00:00', 'planned', 'Pre-op care', 'Painkillers', 'Successful', '2025-11-20 06:50:03'),
(17, 1, 'OPD Consultation', 'OPD consultation for Patient 1', 1, 2, '2025-11-26', '14:00:00', 'planned', 'Check symptoms', 'None', 'Resolved', '2025-11-20 06:50:03'),
(18, 2, 'IPD Admission', 'IPD admission for Patient 2', 2, 2, '2025-11-27', '15:00:00', 'planned', 'Monitor vitals', 'IV Fluids', 'Ongoing', '2025-11-20 06:50:03'),
(19, 3, 'Other', 'Follow-up for Patient 3', 3, 2, '2025-11-28', '16:00:00', 'planned', 'Review recovery', 'Supplements', 'Good', '2025-11-20 06:50:03'),
(20, 4, 'Ultrasound', 'Ultrasound scan for Patient 4', 4, 2, '2025-11-29', '09:30:00', 'planned', 'Check fetus', 'None', 'Normal', '2025-11-20 06:50:03'),
(21, 5, 'C-Section', 'C-Section for Patient 5', 5, 2, '2025-11-30', '11:00:00', 'planned', 'Post-op care', 'Antibiotics', 'Successful', '2025-11-20 06:50:03'),
(22, 2, 'Ultrasound', 'Abdominal Ultrasound', 2, NULL, '2025-11-20', NULL, 'planned', NULL, NULL, NULL, '2025-11-20 07:54:52'),
(23, 1, 'C-Section', 'Cesarean Section', 3, NULL, '2025-11-20', NULL, 'planned', NULL, NULL, NULL, '2025-11-20 07:54:52'),
(24, 3, 'Normal Delivery', 'Normal Delivery', 4, NULL, '2025-11-20', NULL, 'planned', NULL, NULL, NULL, '2025-11-20 07:54:52'),
(25, 4, 'Surgery', 'Minor Surgery', 5, NULL, '2025-11-20', NULL, 'planned', NULL, NULL, NULL, '2025-11-20 07:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `staff_role` enum('nurse','reception','lab','accounts') NOT NULL,
  `join_date` date DEFAULT NULL,
  `shift` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `full_name`, `email`, `phone`, `password`, `staff_role`, `join_date`, `shift`, `status`, `created_at`) VALUES
(2, 'Alice Johnson', 'alice.johnson@example.com', '03001234501', 'alice123', 'nurse', '2022-01-10', 'Morning', 'active', '2025-11-20 06:04:31'),
(3, 'Bob Smith', 'bob.smith@example.com', '03001234502', 'bob123', 'reception', '2021-06-15', 'Evening', 'active', '2025-11-20 06:04:31'),
(4, 'Carla Brown', 'carla.brown@example.com', '03001234503', 'carla123', 'lab', '2020-03-20', 'Night', 'active', '2025-11-20 06:04:31'),
(5, 'David Lee', 'david.lee@example.com', '03001234504', 'david123', 'accounts', '2023-02-05', 'Morning', 'active', '2025-11-20 06:04:31'),
(6, 'Eva Green', 'eva.green@example.com', '03001234505', 'eva123', 'nurse', '2021-11-12', 'Evening', 'inactive', '2025-11-20 06:04:31'),
(7, 'Frank White', 'frank.white@example.com', '03001234506', 'frank123', 'reception', '2022-07-18', 'Night', 'active', '2025-11-20 06:04:31'),
(8, 'Grace Kim', 'grace.kim@example.com', '03001234507', 'grace123', 'lab', '2020-12-01', 'Morning', 'terminated', '2025-11-20 06:04:31'),
(9, 'Henry Adams', 'henry.adams@example.com', '03001234508', 'henry123', 'accounts', '2023-01-22', 'Evening', 'active', '2025-11-20 06:04:31'),
(10, 'Ivy Scott', 'ivy.scott@example.com', '03001234509', 'ivy123', 'nurse', '2022-05-30', 'Night', 'active', '2025-11-20 06:04:31'),
(11, 'Jack Turner', 'jack.turner@example.com', '03001234510', 'jack123', 'reception', '2021-08-17', 'Morning', 'active', '2025-11-20 06:04:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `lab_categories`
--
ALTER TABLE `lab_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_test_id` (`lab_test_id`),
  ADD KEY `procedure_id` (`procedure_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `lab_staff_id` (`lab_staff_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mr_number` (`mr_number`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `procedures`
--
ALTER TABLE `procedures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `nurse_id` (`nurse_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `lab_categories`
--
ALTER TABLE `lab_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lab_reports`
--
ALTER TABLE `lab_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `procedures`
--
ALTER TABLE `procedures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD CONSTRAINT `doctor_schedules_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD CONSTRAINT `lab_reports_ibfk_1` FOREIGN KEY (`lab_test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_reports_ibfk_2` FOREIGN KEY (`procedure_id`) REFERENCES `procedures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_reports_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_tests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_tests_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `lab_categories` (`id`),
  ADD CONSTRAINT `lab_tests_ibfk_4` FOREIGN KEY (`lab_staff_id`) REFERENCES `staff` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `procedures`
--
ALTER TABLE `procedures`
  ADD CONSTRAINT `procedures_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `procedures_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `procedures_ibfk_3` FOREIGN KEY (`nurse_id`) REFERENCES `staff` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
