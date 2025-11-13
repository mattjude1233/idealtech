-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 02:36 PM
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
-- Database: `hr_idealtech`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_lang`
--

CREATE TABLE `admin_lang` (
  `id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `keyid` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_lang`
--

INSERT INTO `admin_lang` (`id`, `keyword`, `keyid`, `value`, `status`) VALUES
(1, 'user|level', 'employee', 'Employee', 1),
(2, 'user|level', 'hr_admin', 'HR Admin', 1),
(3, 'user|level', 'admin', 'System Admin', 1),
(4, 'user|designation', 'software_engineer', 'Software Engineer', 1),
(5, 'user|designation', 'marketing_manager', 'Marketing Manager', 1),
(6, 'user|designation', 'sales_manager', 'Sales Manager', 1),
(7, 'user|designation', 'certified_nurse_assistant', 'Certified Nurse Assistant (CNA)', 1),
(8, 'leave|type', 'casual_leave', 'Casual Leave', 1),
(9, 'leave|type', 'sick_leave', 'Sick Leave', 1),
(10, 'leave|status', 'approved', 'Approved', 1),
(11, 'leave|status', 'denied', 'Denied', 1),
(12, 'leave|status', 'pending', 'Pending', 1),
(13, 'holiday|type', 'regular', 'Regular Holiday', 1),
(14, 'holiday|type', 'special', 'Special Holiday', 1),
(15, 'disciplinary|status', 'nte', 'Notice to Explain', 1),
(16, 'disciplinary|status', 'nda', 'Notice of Disciplinary Action', 1),
(17, 'disciplinary|status', 'nod', 'Notice of Decision', 1),
(18, 'payroll|period', '1', '15th', 1),
(19, 'payroll|period', '2', '30th', 1),
(20, 'user|level', 'customer_service', 'Customer Service', 1),
(21, 'user|level', 'graphic_designer', 'Graphic Designer', 1),
(22, 'user|level', 'supervisor', 'Supervisor', 1),
(23, 'user|level', 'clinical_coordinator', 'Clinical Coordinator', 1),
(24, 'user|level', 'executive_assistant', 'Executive Assistant', 1),
(25, 'user|level', 'medical_biller', 'Medical Biller', 1),
(26, 'user|level', 'account_manager', 'Account Manager', 1),
(27, 'user|level', 'medical_records', 'Medical Records', 1),
(28, 'user|level', 'referral_intake_representative', 'Referral Intake Representative', 1),
(29, 'user|level', 'patient_advocate', 'Patient Advocate', 1),
(30, 'user|level', 'human_resource', 'Human Resource', 1),
(31, 'user|level', 'csr', 'CSR', 1),
(32, 'attendance|type', 'present', 'Present', 1),
(33, 'attendance|type', 'home', 'Home', 1),
(34, 'attendance|type', 'absent', 'Absent', 1),
(35, 'attendance|type', 'account_holiday', 'Account Holiday', 1),
(36, 'disciplinary|status', 'nod', 'Employee Coaching', 1),
(38, 'user|designation', 'medical_biller', 'Medical Biller', 1),
(39, 'attendance|type', 'undertime', 'Undertime', 1),
(40, 'attendance|type', 'ncns', 'No Call, No Show', 1),
(41, 'attendance|type', 'vacation_leave', 'Vacation Leave', 1),
(42, 'attendance|type', 'sick_leave', 'Sick Leave', 1),
(43, 'attendance|type', 'emergency_leave', 'Emergency Leave', 1),
(44, 'attendance|type', 'leave_without_pay', 'Leave Without Pay', 1),
(45, 'attendance|type', 'half_day', 'Half-Day', 1),
(46, 'attendance|type', 'suspension', 'Suspension', 1),
(47, 'leave|type', 'vacation_leave', 'Vacation Leave', 1),
(48, 'document|category', 'coc', 'Code of Conduct', 1),
(49, 'document|category', 'memorandum', 'Memorandum', 1),
(50, 'document|category', 'policy', 'Company Policy', 1),
(51, 'document|category', 'handbook', 'Employee Handbook', 1),
(52, 'leave|type', 'lwop', 'Leave Without Pay', 1),
(53, 'leave|type', 'emergency_leave', 'Emergency Leave', 1),
(54, 'leave|type', 'loa', 'Leave of Absence', 1),
(55, 'leave|type', 'mat_leave', 'Maternity Leave', 1),
(56, 'leave|type', 'pat_leave', 'Paternity Leave', 1),
(57, 'leave|type', 'solo_parent_leave', 'Solo Parent Leave', 1),
(58, 'leave|type', 'vawc', 'Violence Against Women and Children', 1),
(59, 'leave|type', 'magnacarta_forwomen', 'Magna Carta for Women', 1),
(60, 'user|designation', 'rir', 'Referral Intake Representative\r\n', 1),
(61, 'user|designation', 'rir', 'Referral Intake Representative', 1),
(62, 'user|designation', 'cs', 'Customer Service', 1),
(63, 'user|designation', 'gd', 'Graphic Designer', 1),
(64, 'user|designation', 'sup', 'Supervisor', 1),
(65, 'user|designation', 'cc', 'Clinical Coordinator', 1),
(66, 'user|designation', 'mrs', 'Medical Records Specialist', 1),
(67, 'user|designation', 'pa', 'Patient Advocate', 1),
(68, 'user|designation', 'ppsp', 'PPSP', 1),
(69, 'user|designation', 'om', 'Operations Manager', 1),
(70, 'user|designation', 'sd', 'Site Director', 1),
(71, 'user|designation', 'hr', 'Human Resource', 1),
(72, 'user|level', 'SD', 'Site Director', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_tabs`
--

CREATE TABLE `admin_tabs` (
  `id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `grouping` int(11) NOT NULL,
  `level` text NOT NULL,
  `special_user` text NOT NULL,
  `exclude_user` text NOT NULL,
  `icon` varchar(150) NOT NULL,
  `position` int(5) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1_page, 2_function',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_tabs`
--

INSERT INTO `admin_tabs` (`id`, `keyword`, `name`, `link`, `grouping`, `level`, `special_user`, `exclude_user`, `icon`, `position`, `type`, `status`) VALUES
(1, 'tab_attendance', 'Attendance', 'attendance', 1, 'admin', '', '', 'fas fa-calendar-alt', 2, 1, 1),
(2, 'tab_employee', 'Employee', 'employee', 1, 'admin', '', '', 'fas fa-users', 3, 1, 1),
(5, 'tab_employee', 'Leaves', 'leaves', 1, 'all', '', '', 'fas fa-calendar-times', 5, 1, 1),
(6, 'leave_add', 'Add Leave', '', 0, 'admin', '', '', '', 0, 2, 1),
(7, 'tab_breakmonitoring', 'Break Monitoring', 'breakmonitoring', 1, 'all', '', '', 'fas fa-clock', 4, 1, 1),
(8, 'tab_employee', 'Holiday', 'holiday', 1, 'all', '', '', 'fas fa-candy-cane', 5, 1, 1),
(9, 'manage_holiday', 'Manage Holidays', '', 0, 'admin', '', '', '', 0, 2, 1),
(10, 'tab_employee', 'Payroll', 'payroll', 1, 'all', '', '', 'fas fa-credit-card', 5, 1, 1),
(11, 'tab_employee', 'Generate Payroll', 'payrollgenerate', 1, 'admin', '', '', 'fas fa-money-check-alt', 5, 1, 1),
(12, 'manage_disciplinary', 'Manage Disciplinary', '', 0, 'admin', '', '', '', 0, 2, 1),
(13, 'tab_employee', 'Disciplinary', 'disciplinary', 1, 'all', '', '', 'fas fa-gavel', 5, 1, 1),
(14, 'manage_payroll', 'Manage Payroll', '', 0, 'admin', '', '', '', 0, 2, 1),
(15, 'tab_attendance_record', 'Attendance Records', 'attendance/records', 1, 'all', '', '', 'fas fa-calendar-alt', 2, 1, 1),
(16, 'manage_attendance', 'Manage Attendance', '', 0, 'admin', '', '', '', 0, 2, 1),
(17, 'tab_home', 'Home', 'home', 0, 'all', '', '', 'fas fa-calendar-alt', 2, 1, 1),
(18, 'tab_salaryincrease', 'Salary Increase', 'salaryincrease', 1, 'admin', '', '', 'fas fa-coins', 5, 1, 1),
(19, 'manage_salaryincrease', 'Manage Salary Increase', '', 0, 'admin', '', '', '', 0, 2, 1),
(20, 'tab_leavesil', 'Leave (SIL)', 'leavesil', 1, 'admin', '', '', 'fas fa-coins', 5, 1, 1),
(21, 'tab_employee', 'Memorandum and COC', 'home/coc', 1, 'all', '', '', 'fas fa-gavel', 10, 1, 1),
(22, 'manage_kudos', 'Kudos Board', 'kudos', 1, 'admin', '', '', 'fas fa-star', 6, 1, 1),
(23, 'show_admin_dashboard', 'Admin Display Dashboard', '', 0, 'admin', '', '', '', 0, 2, 1),
(24, 'manage_profile', 'Manage Profile', '', 0, 'admin', '', '', '', 0, 2, 1),
(25, 'manage_leave', 'Manage Leave', '', 0, 'admin', '', '', '', 0, 2, 1),
(26, 'deactivate_employee', 'Deactivate Employee', '', 0, 'admin', '', '', '', 0, 2, 1),
(27, 'requirements_checklist', 'Requirements Checklist', '', 0, 'admin', '', '', '', 0, 2, 1),
(28, 'manage_coc_memo', 'Manage COC & Memo', '', 0, 'admin', '', '', '', 0, 2, 1),
(29, 'tab_permissions', 'Permissions Management', 'permissions', 1, 'admin', '', '', 'fas fa-shield-alt', 11, 1, 1),
(30, 'manage_permissions', 'Manage System Permissions', '', 0, 'admin', '', '', '', 0, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `shift_start` time DEFAULT NULL,
  `shift_end` time DEFAULT NULL,
  `punch_in` datetime DEFAULT NULL,
  `punch_out` datetime DEFAULT NULL,
  `late` time NOT NULL,
  `absent` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `notes` text NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_last_update` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `shift_start`, `shift_end`, `punch_in`, `punch_out`, `late`, `absent`, `type`, `notes`, `date_added`, `date_last_update`, `added_by`, `updated_by`) VALUES
(1, 59, '2025-11-10', '22:00:00', '07:00:00', '2025-11-10 21:56:07', NULL, '00:00:00', '', '', '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('coc','memorandum','policy','handbook') NOT NULL DEFAULT 'coc',
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `description`, `category`, `file_name`, `original_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `upload_date`, `is_active`, `is_featured`, `view_count`, `sort_order`, `archived`, `date_created`, `date_modified`) VALUES
(1, 'Code of Conduct and Discipline', 'Official Code of Conduct and Disciplinary guidelines for all employees', 'coc', 'IDEAL TECH STAFFING - Code of Conduct and Discipline.pdf', 'IDEAL TECH STAFFING - Code of Conduct and Discipline.pdf', 'dist/pdf/', 'application/pdf', 1024000, 1, '2025-11-08 02:26:20', 1, 0, 33, 0, 0, '2025-11-08 02:26:20', '2025-11-13 14:47:29'),
(2, 'One Legalties HR and Outsourcing Service OPC Presmat 2024-1', 'Legal compliance and HR outsourcing guidelines', 'coc', 'one-legalties-hr-and-outsourcing-service-opc-presmat-2024-1.pdf', 'one-legalties-hr-and-outsourcing-service-opc-presmat-2024-1.pdf', 'dist/pdf/', 'application/pdf', 2048000, 1, '2025-11-08 02:26:20', 1, 0, 5, 0, 1, '2025-11-08 02:26:20', '2025-11-10 09:25:22'),
(3, 'Test Coc', 'test 23', 'coc', 'dc95a912524455e5247a48963e12c099.pdf', 'file-sample_150kB.pdf', 'uploads/documents/', 'application/pdf', 376822, 1, '2025-11-08 02:34:16', 1, 1, 3, 0, 1, '2025-11-08 02:34:16', '2025-11-10 07:57:05'),
(4, 'eTiQa', '', 'memorandum', '6a9ecfba2f2079fc88d4aaaa303dac95.pdf', 'OCT10_RIS_MP+_IDEAL_TECH_STAFFING_LLC.pdf', 'uploads/documents/', 'application/pdf', 832471, 1, '2025-11-10 09:28:45', 1, 1, 51, 0, 0, '2025-11-10 09:28:45', '2025-11-13 15:53:40');

-- --------------------------------------------------------

--
-- Table structure for table `document_views`
--

CREATE TABLE `document_views` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `view_date` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_views`
--

INSERT INTO `document_views` (`id`, `document_id`, `employee_id`, `view_date`, `ip_address`) VALUES
(1, 1, 1, '2025-11-08 02:33:05', '127.0.0.1'),
(2, 3, 1, '2025-11-08 02:34:16', '127.0.0.1'),
(3, 2, 1, '2025-11-08 02:34:19', '127.0.0.1'),
(4, 1, 79, '2025-11-08 02:49:51', '127.0.0.1'),
(5, 3, 79, '2025-11-08 02:49:52', '127.0.0.1'),
(6, 2, 79, '2025-11-08 02:49:56', '127.0.0.1'),
(7, 3, 1, '2025-11-10 07:56:53', '192.168.9.101'),
(8, 1, 1, '2025-11-10 07:57:06', '192.168.9.101'),
(9, 1, 57, '2025-11-10 09:24:08', '192.168.8.168'),
(10, 2, 57, '2025-11-10 09:24:13', '192.168.8.168'),
(11, 1, 57, '2025-11-10 09:24:25', '192.168.8.168'),
(12, 1, 57, '2025-11-10 09:24:28', '192.168.8.168'),
(13, 2, 57, '2025-11-10 09:24:29', '192.168.8.168'),
(14, 1, 57, '2025-11-10 09:24:31', '192.168.8.168'),
(15, 1, 1, '2025-11-10 09:24:36', '192.168.8.168'),
(16, 2, 1, '2025-11-10 09:25:15', '192.168.8.168'),
(17, 1, 1, '2025-11-10 09:25:23', '192.168.8.168'),
(18, 4, 1, '2025-11-10 09:28:45', '192.168.8.168'),
(19, 1, 57, '2025-11-10 09:28:56', '192.168.8.168'),
(20, 4, 57, '2025-11-10 09:28:57', '192.168.8.168'),
(21, 4, 57, '2025-11-10 09:29:00', '192.168.8.168'),
(22, 1, 57, '2025-11-10 09:29:01', '192.168.8.168'),
(23, 4, 1, '2025-11-10 09:30:49', '192.168.8.168'),
(24, 4, 1, '2025-11-10 13:11:55', '192.168.8.168'),
(25, 1, 1, '2025-11-10 13:12:07', '192.168.8.168'),
(26, 4, 1, '2025-11-10 13:13:12', '192.168.8.168'),
(27, 4, 35, '2025-11-11 05:30:33', '192.168.8.98'),
(28, 4, 19, '2025-11-11 05:31:59', '192.168.8.42'),
(29, 4, 11, '2025-11-11 05:32:14', '192.168.8.37'),
(30, 4, 53, '2025-11-11 05:33:08', '192.168.8.79'),
(31, 4, 25, '2025-11-11 05:34:45', '192.168.8.118'),
(32, 1, 25, '2025-11-11 05:34:53', '192.168.8.118'),
(33, 4, 26, '2025-11-11 05:35:47', '192.168.8.162'),
(34, 4, 74, '2025-11-11 05:35:57', '192.168.8.96'),
(35, 4, 17, '2025-11-11 05:36:31', '192.168.8.49'),
(36, 1, 17, '2025-11-11 05:36:34', '192.168.8.49'),
(37, 4, 42, '2025-11-11 05:37:01', '192.168.8.26'),
(38, 4, 34, '2025-11-11 05:37:05', '192.168.8.121'),
(39, 4, 75, '2025-11-11 05:37:08', '192.168.8.57'),
(40, 1, 42, '2025-11-11 05:37:10', '192.168.8.26'),
(41, 1, 75, '2025-11-11 05:37:15', '192.168.8.57'),
(42, 4, 7, '2025-11-11 05:37:21', '192.168.8.147'),
(43, 4, 52, '2025-11-11 05:39:27', '192.168.8.110'),
(44, 4, 15, '2025-11-11 05:39:27', '192.168.8.86'),
(45, 1, 26, '2025-11-11 05:39:34', '192.168.8.162'),
(46, 4, 9, '2025-11-11 05:40:18', '192.168.8.178'),
(47, 4, 51, '2025-11-11 05:41:40', '192.168.8.103'),
(48, 4, 50, '2025-11-11 05:45:20', '192.168.8.95'),
(49, 4, 46, '2025-11-11 05:46:08', '192.168.8.139'),
(50, 1, 46, '2025-11-11 05:46:34', '192.168.8.139'),
(51, 1, 50, '2025-11-11 05:47:27', '192.168.8.95'),
(52, 4, 18, '2025-11-11 05:55:50', '192.168.8.144'),
(53, 4, 31, '2025-11-11 06:03:57', '192.168.8.153'),
(54, 4, 1, '2025-11-11 06:10:47', '192.168.8.140'),
(55, 4, 67, '2025-11-11 06:16:00', '192.168.8.138'),
(56, 1, 67, '2025-11-11 06:16:03', '192.168.8.138'),
(57, 4, 24, '2025-11-11 06:19:12', '192.168.8.109'),
(58, 1, 24, '2025-11-11 06:19:19', '192.168.8.109'),
(59, 4, 61, '2025-11-11 06:22:27', '192.168.8.108'),
(60, 1, 61, '2025-11-11 06:22:40', '192.168.8.108'),
(61, 4, 43, '2025-11-11 06:23:09', '192.168.8.159'),
(62, 1, 43, '2025-11-11 06:23:16', '192.168.8.159'),
(63, 4, 59, '2025-11-11 06:45:24', '192.168.8.122'),
(64, 1, 59, '2025-11-11 06:45:31', '192.168.8.122'),
(65, 4, 83, '2025-11-11 07:04:43', '192.168.8.181'),
(66, 4, 85, '2025-11-11 07:23:49', '192.168.8.59'),
(67, 1, 85, '2025-11-11 07:24:23', '192.168.8.59'),
(68, 4, 80, '2025-11-11 08:21:04', '192.168.8.180'),
(69, 4, 57, '2025-11-11 09:02:42', '192.168.8.168'),
(70, 1, 57, '2025-11-11 09:02:43', '192.168.8.168'),
(71, 4, 57, '2025-11-11 09:02:44', '192.168.8.168'),
(72, 1, 57, '2025-11-11 09:02:45', '192.168.8.168'),
(73, 4, 82, '2025-11-11 09:57:56', '192.168.8.90'),
(74, 1, 82, '2025-11-11 09:58:19', '192.168.8.90'),
(75, 4, 88, '2025-11-12 05:06:58', '192.168.8.154'),
(76, 4, 25, '2025-11-12 05:08:39', '192.168.8.118'),
(77, 1, 25, '2025-11-12 05:08:41', '192.168.8.118'),
(78, 4, 17, '2025-11-12 05:08:55', '192.168.8.49'),
(79, 4, 19, '2025-11-12 05:10:58', '192.168.8.42'),
(80, 4, 54, '2025-11-12 05:18:50', '192.168.8.128'),
(81, 1, 54, '2025-11-12 05:23:27', '192.168.8.128'),
(82, 4, 6, '2025-11-12 05:57:04', '192.168.8.137'),
(83, 4, 16, '2025-11-12 06:13:54', '192.168.8.48'),
(84, 1, 16, '2025-11-12 06:14:01', '192.168.8.48'),
(85, 4, 67, '2025-11-12 06:14:13', '192.168.8.138'),
(86, 1, 67, '2025-11-12 06:14:19', '192.168.8.138'),
(87, 4, 59, '2025-11-12 06:16:49', '192.168.8.122'),
(88, 4, 78, '2025-11-12 06:17:03', '192.168.8.46'),
(89, 4, 18, '2025-11-12 06:19:14', '192.168.8.144'),
(90, 4, 59, '2025-11-13 14:47:23', '127.0.0.1'),
(91, 1, 59, '2025-11-13 14:47:29', '127.0.0.1'),
(92, 4, 1, '2025-11-13 15:53:40', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `emp_supervisor` varchar(50) DEFAULT NULL,
  `badge_number` varchar(40) NOT NULL,
  `locker_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `emp_password` text NOT NULL,
  `emp_fname` text NOT NULL,
  `emp_lname` text NOT NULL,
  `emp_mname` text NOT NULL,
  `emp_suffix` varchar(100) NOT NULL,
  `salary` text NOT NULL,
  `gender` varchar(100) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `hiring_date` date DEFAULT NULL,
  `emp_level` varchar(50) NOT NULL DEFAULT 'employee' COMMENT 'employee, admin',
  `designation` varchar(50) NOT NULL,
  `account` text NOT NULL,
  `profile` text NOT NULL,
  `address_present` text NOT NULL,
  `address_permanent` text NOT NULL,
  `rest_day` varchar(15) NOT NULL DEFAULT '6,7' COMMENT '1_mon, 2_tue, 3_wed, 4_thu, 5_fri, 6_sat, 7_sun',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0-active, 1-inactive',
  `deactivation_date` datetime DEFAULT NULL,
  `deactivated_by` int(11) DEFAULT NULL,
  `deactivation_info` text NOT NULL,
  `added_by` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `emp_id`, `emp_supervisor`, `badge_number`, `locker_number`, `email`, `phone`, `emp_password`, `emp_fname`, `emp_lname`, `emp_mname`, `emp_suffix`, `salary`, `gender`, `birthdate`, `hiring_date`, `emp_level`, `designation`, `account`, `profile`, `address_present`, `address_permanent`, `rest_day`, `status`, `deactivation_date`, `deactivated_by`, `deactivation_info`, `added_by`, `date_added`) VALUES
(1, 'admin00', NULL, '', '', '', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Admin', 'System', '', '', '', '', NULL, '2025-02-01', 'admin', '', '', '', '', '', '6,7', 0, NULL, NULL, '', '', '2025-02-01 17:11:56'),
(2, '2023-00052', '2023-00005', '64473', '61', 'ayaabaigar.its@gmail.com', '0977-340-1678', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ayani Joanne', 'Abaigar', 'Siatelo', '', '0d7c557d8789ebda07c3296b9ce158ef261da7a3cb3c1567c8ac0241f7c1d178c48c778e8880daa70d5388ac39c920a440bf717efd4a7054738677083201eb04FXOBUbpxTshD4Nt6LpRZtW1c-1UOV_O3r-_Uqu1-xY4~', 'Female', '1997-10-17', '2019-02-18', 'employee', '', 'Home Health Care PH', '', '120-C Escario St Extension Cebu, City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(3, '2023-00044', NULL, '6268', '', 'nathalieabellana.its@gmail.com ', '9690447457', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Nathalie Gay', 'Abellana', 'Dela Victoria', '', '4abb94890e52f1ae1c872a03cbccb46cc83fe1c9c22cd36a31452c69ee38dbb1ffb58e15a605ee26d89d1a41a1dbcf3ba0bc983c3329bc71a8aec519ca922a317bV-m46kkt_jU5NSSMCEirXhSApgizRxGvg8VuvWVas~', 'Female', '2000-12-16', '2022-12-12', 'customer_service', '', '', '', '177-35 Fatima Bulacao Cebu City ', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(4, '2023-00035', NULL, '46725', '', 'mrjabutazil.its@gmail.com', '9066644176', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Maria Ricah Jean', 'Abutazil', 'Lomiteng', '', '', 'Female', '1996-09-06', '2023-04-24', 'customer_service', '', '', '', 'Golam Drive, Brgy. Kasambagan, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(5, '2023-00045', NULL, '53836', '', 'johnwynnabuton.its@gmail.com ', '9953950527', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'John Wynn', 'Abuton', 'Perez', '', '', 'Male', '1990-06-09', '2023-08-15', 'graphic_designer', '', '', '', '#47 Harmonis Residence H.Block, Lagtang Talisay City, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(6, '2023-00003', NULL, '11440', '', 'ronacas.its@gmail.com', '9762131695', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ronald Dave', 'Acas', 'Gabutan', '', '', 'Maleale', '1991-07-15', '2022-06-01', 'supervisor', '', '', '', 'Bldg13, Unit131, Urban Deca Homes, Kasambagan, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(7, '2023-00034', NULL, '54926', '', 'roseannakmad.its@gmail.com', '9505642012', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Rose-Ann', 'Akmad', 'Abdurahman', '', '', 'Female', '1994-03-19', '2022-07-25', 'clinical_coordinator', '', '', '', '615-T Tormis St, Urgello Sambag II Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(8, '2023-00025', NULL, '16926', '', 'andreirosauxtero.its@gmail.com', '9926795492', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Andrei Ros', 'Auxtero', 'Deiparine', '', '', 'Female', '1994-02-28', '2021-11-11', 'customer_service', '', '', '', 'Dumlog Talisay City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(9, '2023-00037', NULL, '57331', '', 'lynnauxtero.its@gmail.com', '9567505309', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Lynn', 'Auxtero', 'Deiparine', '', '', 'Female', '1995-06-26', '2022-02-07', 'customer_service', '', '', '', 'Sta Rita Dumlog , Talisay City , Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(10, '2023-00022', NULL, '19292', '', 'kbitoon.its@gmail.com', '9774234598', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Kevin Clark', 'Bitoon', 'Dumagat', '', '', 'Male', '1994-11-03', '2023-01-09', 'clinical_coordinator', '', '', '', 'Geminie Apartment, Upper Bacayan, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(11, '2023-00033', NULL, '1367', '', 'dcartalaba.its@gmail.com', '9912164197', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Daphne', 'Dayuday', 'Cartalaba', '', '', 'Female', '1982-11-10', '2022-04-01', 'customer_service', '', '', '', 'Sitio Rotonda Banilad Cebu City ', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(12, '2023-00007', NULL, '46951', '', 'nikkacartalaba.its@gmail.com', '9620834859', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Nikka', 'Cartalaba', 'Lisen', '', '', 'Female', '1991-07-10', '2023-01-27', 'supervisor', '', '', '', 'Unit 2, Samonte Apartment Inayawan Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(13, '2023-00064', NULL, '42160', '', 'paulacastaneda.its@gmail.com', '9274300269', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Chrysta Paola', 'Castañeda', 'Abesia', '', '', 'Female', '1989-11-13', '2023-11-06', 'clinical_coordinator', '', '', '', 'T\'oville apt #4, Ma. Paloma Village Labangon Cebu City 6000', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(14, '2023-00063', NULL, '56551', '', 'jessacerna.its@gmail.com', '9280384794', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jessa Joy', 'Cerna', 'Edullantes', '', '', 'Female', '1996-12-26', '2023-10-23', 'customer_service', '', '', '', 'Purok 3 Lower Kamputhaw Escario Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(15, '2023-00031', NULL, '33977', '', 'jcinco.its@gmail.com', '9476694946', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jessamine', 'Cinco', 'Rosales', '', '', 'Female', '1986-10-14', '2021-09-07', 'clinical_coordinator', '', '', '', 'B2 L20, PACIFIC GRANDE 1 SUBD., CARAJAY ST. BRGY. GUN-OB, LAPU-LAPU CITY, CEBU', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(16, '2023-00010', NULL, '52873', '', 'mcodinera.its@gmail.com', '9991705264', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Marbie', 'Codiñera', 'Obeña', '', '', 'Male', '1998-03-27', '2023-02-22', 'customer_service', '', '', '', '301- L Sikatuna St. C.c', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(17, '2023-00019', NULL, '15836', '', 'kimberlyanndanieles.its@gmail.com', '9687017639', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Kimberly Ann ', 'Danieles', 'Abella', '', '', 'Female', '2002-06-07', '2023-02-20', 'customer_service', '', '', '', 'Jones Avenue, Sambag 2, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(18, '2023-00069', NULL, '42459', '', 'rizaliedoroy.its@gmail.com', '9333226482', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Rizalie', 'Doroy', 'Gequilan', '', '', 'Female', '1990-12-30', '2023-11-20', 'customer_service', '', '', '', 'General Aviation Road Pajac Lapu Lapu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(19, '2023-00016', NULL, '5546', '', 'jesperonce.its@gmail.com', '9261530269', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jashmen ', 'Esperonce', 'Estrera', '', '', 'Female', '2003-12-12', '2023-06-28', 'customer_service', '', '', '', 'Sambag II, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(20, '2023-00055', NULL, '50621', '', 'renenespiritu.its@gmail.com', '9275039697', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Renen', 'Espiritu', 'Valencia', '', '', 'Male', '1975-04-18', '2023-01-30', 'medical_biller', '', '', '', '166-B Tres De Abril, Labangon, Cebu City 6000', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(21, '2023-00048', NULL, '0', '', 'marcgaviola.its@gmail.com ', '639272000000', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Marc Joy', 'Gaviola', 'Bascon', '', '', 'Male', '1993-07-17', '2022-12-12', 'customer_service', '', '', '', '1200 PC Hills, Apas, Cebu City, Cebu, 6000', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(22, '2023-00042', NULL, '9997', '', 'vincegerado.its@gmail.com', '9569926720', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Vince Gabrielle', 'Gerado', 'Lucero', '', '', 'Male', '1997-11-20', '2022-02-07', 'customer_service', '', '', '', '306 Holy Trinity St Hipodromo Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(23, '2023-00011', NULL, '61754', '', 'mattgetigan.its@gmail.com', '9298312887', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Matt Jude Augustine', 'Getigan', 'Uy', '', '', 'Male', '1999-08-17', '2021-08-06', 'customer_service', '', '', '', '0919 Sitio Plaza, Apas. Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(24, '2023-00040', NULL, '31861', '', 'krishatamosa.its@gmail.com ', '9569621334', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Kris Daryll', 'Hatamosa', 'Malagar', '', '', 'Male', '1991-12-17', '2023-03-21', 'customer_service', '', '', '', '17C Mabini St. San Roque, Cebu CIty', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(25, '2023-00039', NULL, '64536', '', 'mrosejusay.its@gmail.com', '9471480851', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Mary Rose', 'Jusay', 'Dingding', '', '', 'Female', '1998-09-08', '2022-10-24', 'customer_service', '', '', '', '414- E,B. Rodriguez Ext Sambag 2., Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(26, '2023-00030', NULL, '43750', '', 'gloon.its@gmail.com', '0961-885-6174', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Glieza', 'Loon', 'Molejon', '', '', 'Female', '1999-01-24', '2023-08-01', 'customer_service', '', '', '', '194-C tres de abril , Labangon Cebu city', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(27, '2023-00026', NULL, '15447', '', 'r.mahidlawon.its@gmail.com', '9090866425', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ricky', 'Mahidlawon', 'Panuncialman', '', '', 'Male', '1993-08-07', '2022-02-01', 'customer_service', '', '', '', 'Sitio Upper Manol Tisa, Cebu City 6000', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(28, '2023-00001', NULL, '', '', 'frances03.sos@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Frances', 'Manatad', 'Getigan', '', '', 'Female', '1988-10-13', '2018-01-30', 'admin', 'sd', '', '', 'Samonte Apartment, Unit 2, Inayawan, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(29, '2023-00028', NULL, '50538', '', 'pmanatad.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Patrick', 'Manatad', 'Getigan', '', '', 'Male', '1991-10-08', '2021-09-02', 'customer_service', '', '', '', '131 Cogon Poblacion Liloan Cebu ', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(30, '2023-00015', NULL, '46778', '', 'paul.morados.its@gmail.com', '9162037649', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Policarpio Ali', 'Morados III', 'Sultan', '', '', 'Male', '1990-06-28', '2023-08-01', 'customer_service', '', '', '', 'V-33 Purok 8 Kamputhaw Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(31, '2023-00032', NULL, '50976', '', 'edemeroficiar.its@gmail.com ', '9399412085', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Edemer', 'Oficiar', 'Ceballos', '', '', 'Male', '1995-07-20', '2023-03-29', 'customer_service', '', '', '', 'Lower Mansanitas Tisa Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(32, '2023-00029', NULL, '64657', '', 'mario7368.its@gmail.com', '9234041419', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Mario Jr. ', 'Papellero', 'Ca-ang', '', '', 'Male', '1980-12-20', '2022-09-29', 'customer_service', '', '', '', 'Doña Maria Village 2 Jack Street Cebu Citty', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(33, '2023-00008', NULL, '9041', '', 'judifepascubillo.its@gmail.com', '9228621220', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Judife', 'Pascubillo', 'Diana', '', '', 'Female', '1992-11-07', '2023-01-09', 'supervisor', '', '', '', '98-A Mariano Abella Corner E. Jabonero St, Labangon, C. C.', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(34, '2023-00014', NULL, '98000', '', 'michaelpimentel.its@gmail.com', '9165641008', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Michael', 'Pimentel', '', '', '', 'Male', '2000-11-17', '2021-10-18', 'customer_service', '', '', '', '01 - A Francisco Llamas Street, Tisa, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(35, '2023-00057', NULL, '363554', '', 'shennypingol.its@gmail.com', '9995798971', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Niña Shenny', 'Pingol', 'Padayon', '', '', 'Female', '1999-08-15', '2022-01-26', 'customer_service', '', '', '', '12-C Escario Ext. Brgy Kamputhaw Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(36, '2023-00046', NULL, '64569', '', 'pogoyf.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Florbeth', 'Pogoy', 'Aton', '', '', 'Male', '1990-04-15', '2022-10-24', 'customer_service', '', '', '', 'Purok Pechay I, Sitio Tiwasan Brgy. Catarman Liloan', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(37, '2023-00009', NULL, '55465', '', 'emmirredilosa.its@gmail.com', '9356084765', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Emmir', 'Redilosa', 'Marbella', '', '', 'Male', '1991-08-01', '2022-08-02', 'customer_service', '', '', '', '168 B Sabellano St. Quiot Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(38, '2023-00043', NULL, '49258', '', 'joshuaregis.its@gmail.com ', '9391341405', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Joshua Kent', 'Regis', '', '', '', 'Male', '1995-11-29', '2023-03-29', 'customer_service', '', '', '', 'Cababahay Compound Quiot Pardo Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(39, '2023-00012', NULL, '62687', '', 'alrey.reynes.its@gmail.com', '9166581765', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Alrey', 'Reynes', 'Micaros', '', '', 'Male', '1991-04-17', '2019-07-01', 'customer_service', '', '', '', 'Purok 4, Sitio Calachuchi, West Binabag Tayud Consolacion', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(40, '2023-00068', NULL, '41821', '', 'christinesarga.its@gmail.com', '0917 827 4663', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Christine Claire', 'Sarga', 'Melendres', '', '', 'Female', '1989-12-17', '2023-11-20', 'customer_service', '', '', '', 'Sarga Compound, Hernan Cortes Street Subangdaku, Mandaue City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(41, '2023-00059', NULL, '55918', '', 'emiloutamse.its@gmail.com', '9453386954', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Emilou', 'Tamse', 'Rodes', '', '', 'Female', '1997-08-27', '2019-07-01', 'customer_service', '', '', '', 'Bentley Estates,  R. Duterte St, Banawa Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(42, '2023-00017', NULL, '810', '', 'arcianekarl.its@gmail.com', '9694208515', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Arciane Karl', 'Taña', 'Quintero', '', '', 'Male', '1991-12-05', '2022-12-23', 'customer_service', '', '', '', 'M.L Quezon Ave Brgy Cabancalan Mandaue CIty', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(43, '2023-00082', NULL, '54077', '', 'mjimenez.its@gmail.com', '9776581128', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Michelle', 'Jimenez', 'Abella', '', '', 'Female', '1994-12-19', '2023-12-18', 'medical_records', '', '', '', 'Pooc Talisay, City Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(44, '2023-00083', NULL, '3744', '', 'aconing.its@gmail.com', '9651987912', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Anne Dominique', 'Coning', 'Obod', '', '', 'Female', '1998-07-14', '2023-12-18', 'medical_records', '', '', '', 'Sitio Colo Upper Inayawan, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(45, '2023-00084', NULL, '92595', '', 'gluego.its@gmail.com', '9662859907', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Grexter', 'Luego', 'Paciencia', '', '', 'Male', '1996-05-04', '2023-12-18', 'referral_intake_representative', '', '', '', 'Guadalupe Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(46, '2023-00085', NULL, '40631', '', 'nelmerconcepcion.its@gmail.com ', '9058706808', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Nelmer', 'Concepcion', 'Cabalida', '', '', 'Male', '1997-06-19', '2023-12-18', 'medical_biller', '', '', '', '6th street, Buena Hills, Guadalajara, Guadalupe Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(47, '2023-00005', NULL, '5', '', 'fatimaosabel.its@gmail.com', '0966-276-5521', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Fatima Rose', 'Osabel', '', '', '', 'Female', '1997-03-04', '2021-03-08', 'supervisor', '', '', '', 'Sitio BACA Brgy. Apas Lahug Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(48, '2023-00087', NULL, '33872', '', 'nvillarin.its@gmail.com', '9276138698', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Nikki', 'Villarin', 'Portes', '', '', 'Male', '1993-06-01', '2024-01-16', 'medical_biller', '', '', '', 'Pajo, Lapu-Lapu City, M.L. Quezon National Highway', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(49, '2023-00073', NULL, '29801', '', 'salvesalvatierra.its@gmail.com', '9318487765', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Salve', 'Salvatierra', 'Malon', '', '', 'Female', '1985-09-14', '2023-02-26', 'clinical_coordinator', '', '', '', 'Paulin Compound J. Urgello St. Sambag 1 Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(50, '2023-00088', NULL, '55098', '', 'sheryltrabado20.its@gmail.com', '9687354335', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Sheryl', 'Trabado', 'De Leon', '', '', 'Female', '1978-01-20', '2024-02-26', 'patient_advocate', '', '', '', '#28 Zone Sikwa, S. Jayme St., Pakna-an, Mandaue City, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(51, '2023-00089', NULL, '64046', '', 'reymartgalvez.its@gmail.com', '9312106814', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Reymart', 'Galvez', 'Lanac', '', '', 'Female', '1993-05-02', '2024-03-04', 'patient_advocate', '', '', '', 'Sitio Lawis Alaska Mambaling, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(52, '2023-00050', NULL, 'N/A', '', 'lovelynikkitayong.its@gmail.com', '9562548851', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Lovely Nikki', 'Tayong', 'Asmolo', '', '', 'Female', '1993-04-24', '2022-05-31', 'clinical_coordinator', '', '', '', 'Sitio Cogon, Purok Mangga 2A, Poblacion, Liloan, Cebu 6002', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(53, '2023-00058', NULL, '56272', '', 'summertahir08.its@gmail.com', '9212033381', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Mark', 'Tahir', 'Custodio', '', '', 'Male', '1996-08-08', '2022-01-27', 'customer_service', '', '', '', 'Lawis Alaska Mambaling', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(54, '2023-00090', NULL, '37737', '', 'clydeedilo.its@gmail.com', '9272997603', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Clyde', 'Edilo', 'Ladaga', '', '', 'Male', '1990-11-02', '2024-04-08', 'customer_service', '', '', '', 'Brgy. Basak-Merkado Lapu-Lapu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(55, '2023-00091', NULL, '46059', '', 'leogreeko.its@gmail.com', '9428738346', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Leo Greeko', 'Morados', 'Sultan', '', '', 'Male', '1986-03-13', '2024-04-08', 'customer_service', '', '', '', 'purok 8 camputhaw cebu city', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(56, '2023-00092', NULL, '3525', '', 'msalazar.its@gmail.com', '9105750746', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Merlito', 'Salazar Jr. ', 'Garces', '', '', 'Male', '1996-10-05', '2024-04-08', 'medical_biller', '', '', '', '6TH ST BUENA HILLS GUADALAJARA, GUADALUPE, CEBU CITY, CEBU', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(57, '2023-00093', NULL, '703', '', 'gromarate.its@gmail.com', '9690512197', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Genevieve', 'Romarate', '', '', '', 'Female', '2001-07-05', '2024-05-20', 'admin', 'hr', '', '{\"file_name\":\"Image of.png\",\"file_path\":\"uploads\\/employee_profiles\\/2025\\/11\\/11\\/2be7ccff7382b4f2dab4be3e4c577af7ce68f46c.png\"}', 'Sambag II, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(58, '2023-00094', NULL, '3708', '', 'rmaelacson.its@gmail.com', '9752666002', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Reina Mae', 'Lacson', 'Morandarte', '', '', 'Female', '1997-09-02', '2024-05-21', 'clinical_coordinator', '', '', '', 'UCMA Village, Lahug, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(59, '2023-00095', NULL, '37742', '', 'yvonb.its@gmail.com', '0966-357-4447', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Yvon Nahryl', 'Berdon', 'Tagimacruz', '', '', 'Female', '1997-09-08', '2024-07-15', 'patient_advocate', '', '', '', 'Agus Gamay Lapu-Lapu City, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(60, '2023-00096', NULL, '8743', '', 'maryjoytampus1.its@gmail.com', '9362115053', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Mary Joy', 'Tampus', 'Debo', '', '', 'Female', '2000-10-22', '2024-07-29', 'patient_advocate', '', '', '', 'Cabantan Street, corner Archbishop Reyes Avenue, Barrio Luz, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(61, '2023-00098', NULL, '43979', '', 'rwebster.its@gmail.com', '9603564609', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ray Webster', 'Obcial', 'Aliganga', '', '', 'Male', '1987-04-14', '2024-08-26', 'csr', '', '', '', 'Minglanilla, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(62, '2023-00099', NULL, '65442', '', 'mmulig.its@gmail.com', '9322454941', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Melanie ', 'Mulig', 'Manigos', '', '', 'Female', '2002-01-17', '2024-09-03', 'csr', '', '', '', 'Oprra Unit 3  kalunasan cebu city ', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(63, '2023-00103', NULL, '62364', '', 'tmagnaye.its@gmail.com', '9538550046', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ma. Theresa', 'Magnaye ', 'Belo ', '', '', 'Female', '1990-11-05', '2024-09-19', 'clinical_coordinator', '', '', '', '93C Angeles Compound Urgello St. Sambag II, Cebu City, Cebu 6000', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(64, '2023-00104', NULL, '49176', '', 'romieltapayan.its@gmail.com', '9062765979', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Romiel', 'Tapayan', 'Mascardo', '', '', 'Male', '1993-08-07', '2024-09-24', 'clinical_coordinator', '', '', '', '#30 3rd St. Palm Hill Subd., Basak, Mandaue City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(65, '2023-00105', NULL, '8924', '', 'lizaleyson.its@gmail.com', '9671941297', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Liza Mea', 'Leyson', 'Englis', '', '', 'Female', '1982-09-18', '2024-10-01', 'patient_advocate', '', '', '', 'Cabangahan, Consolacion Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(66, '2023-00107', NULL, '56921', '', 'jenifferlagria.its@gmail.com', '9913188780', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jeniffer', 'Lagria', 'T.', '', '', 'Female', '1985-02-26', '2024-10-16', 'patient_advocate', '', '', '', 'Labangon Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(67, '2023-00110', NULL, '1945', '', 'rpaclipan.its@gmail.com', '9911987827', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Rodney', 'Paclipan', 'Condino', '', '', 'Male', '1996-02-02', '2024-12-09', 'graphic_designer', '', '', '', '544-D Tupaz St., Sawang Calero, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(68, '2023-00112', NULL, '46886', '', 'pmgsanchez.its@gmail.com', '9567214638', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'PINKY MARIE GRACE ', 'SANCHEZ', 'Tayong', '', '', 'Female', '1988-03-17', '2025-01-02', 'patient_advocate', '', '', '', 'Purok Rosal 1 Poblacion, Lilo-an, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(69, '2023-00070', NULL, '47192', '', 'fabsont@caremedica.com', '9460397651', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Fabson', 'Tangpos', 'Rojas', '', '', 'Male', '1996-02-13', '2025-01-03', 'customer_service', '', '', '', 'B. Benedicto St, Gun-ob, Lapu-Lapu City, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(70, '2023-00114', NULL, '15717', '', 'dadedasi.its@gmail.com', '9953672906', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Dedasi', 'Danica Ann', '', '', '', 'Female', '1996-06-16', '2025-02-06', 'customer_service', '', '', '', '909 Purok Orchids Ayala Brgy. Yati Liloan Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(71, '2023-00116', NULL, '122', '', 'rainierjoshua1.its@gmail.com', '9604241704', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Rainier Joshua ', 'Gerado', '', '', '', 'Male', '2005-02-26', '2025-02-10', 'customer_service', '', '', '', 'Basak San Nicolas Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(72, '2023-00117', NULL, '', '', 'lawrencevios.its@gmail.com', '0919 075 8890', '76a49bdad246861ed26a0d35fe64b0e6b7820da0', 'Marvinne Lawrence', 'Vios', 'Saldo', '', '', 'Male', '1989-03-16', '2025-02-17', 'clinical_coordinator', '', '', '', 'Blk 22, Lot 11, Busay Heights, Busay, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(73, '2023-00118', NULL, '63417', '', 'richiegayo.its@gmail.com', '9682115859', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Richie', 'Gayo', '', '', '', 'Male', '1989-01-11', '2025-02-26', 'medical_biller', 'medical_biller', 'CareMedica', '', '0354 V. Ranudo St. Cogon Central Ramos, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(74, '2023-00119', NULL, '58490', '', 'lycacasquejo.its@gmail.com', '9666651920', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ma. Lyca', 'Casquejo', '', '', '', 'Female', '2002-04-02', '2025-02-26', 'customer_service', '', '', '', 'Jorge Tampus Street, Basak Lapu Lapu ', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(75, '2023-00120', NULL, '', '', 'jojielabada.its@gmail.com', '9975804336', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jojie', 'Labada', '', '', '', 'Male', '1999-09-12', '2025-04-14', 'customer_service', '', '', '', 'Buyong, Maribago Lapu-Lapu City, Cebu', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(76, '2023-00121', NULL, '', '', 'ibertagazon.its@gmail.com', '9458207038', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jan Ibert', 'Agazon', 'Clitar', '', '', 'Male', '1987-01-18', '2025-04-14', 'customer_service', '', '', '', '26-1 Ganciang St., Manbaling, Cebu City', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(77, '2023-00122', NULL, '', '', '', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Madonna Hercia', 'Rivera', '', '', '', 'Female', '0000-00-00', '2025-04-21', 'clinical_coordinator', '', '', '', '', '', '6,7', 0, NULL, NULL, '', '1', '2025-06-10 15:39:19'),
(78, '2023-00123', NULL, '', '', 'mull.cory@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Mull', 'Cory', 'Sue', '', '', 'female', '1999-07-15', '2025-11-03', 'clinical_coordinator', 'certified_nurse_assistant', '', '', '', '', '6,7', 0, NULL, NULL, '', '1', '2025-11-07 21:56:49'),
(80, '2023-00126', '2023-00005', '36850', '50', 'jowenaalmirez.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Jowena', 'Almirez', '', '', 'b79f953fe2ded4cde94f80894ff680ffcbaa49db4709bcae8010e2856502430b558f043f9d70f86b138b1273d5d4dc040f19426620c6bd1a882530b58fb3ea93-PJTvmFW1irYGPGHykBa-CTeaW4TY5xXZNVvufUFlAE~', 'Female', '1999-03-16', '2025-05-06', 'csr', 'cs', 'CareMedica - CSR', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 22:34:17'),
(81, '2023-00131', '2023-00003', '65490', '22', 'joytrodriguez.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Joy', 'Rodriguez', '', '', '250019aaf86185f7cbf55ea4841bd4f6ca353cf9a2fb966dffbfd2ee54f823693e8eac28db414a35f35d61e6e8322831977a5a422d973e8836cfff2d9dd02e6bKuMGniMWlihbKhFYQGObw0IlhHZRWKgjyDdt6WHjDQM~', 'Female', '1993-12-31', '2025-06-02', 'csr', 'cs', 'ITS - CFTHH', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 22:44:51'),
(82, '2023-00136', '2023-00007', '', '', 'deamahilum.its@gmail.com', '', 'a81d786bc8a03012b39ac069c1cf5056cf6e5766', 'Deah Honeylyn', 'Mahilum', 'Soroysoroy', '', 'e164e831d605eb327955c002e077f1284c8fdbaee0b8870ed40dbcca3b69289ea42c91bc1fa69d32b10d5c2a670b3fbb50b66e2fb99b509031524d99623fca836KbUpoF3dasEgGHHbpNIAuWEVp2rSMndt13W6g8b7mk~', 'Female', '2001-01-31', '2025-06-30', 'employee', 'pa', 'CareMedica', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 22:57:08'),
(83, '2023-00142', NULL, '', '', 'shannenpepino.its@gmail.com', '', '6b02e1082c96d6b5c6e34064096de8a1a873f119', 'Shannen', 'Pepino', '', '', '85304bbb20c2bb27c81508a6d029d36824084a61c25fe6bdbf2e2214997f91591650d2d0a0cda1e3f1d8f205b80667e4c8034ae2e4f51fb2a23a8ce2d45dba7dZTzTd-_zKiKOopfJGfAovGrbDBm_2tOIPubNtGDbqaU~', 'Female', '2000-09-12', '2025-08-13', 'employee', 'pa', 'Internal Medicine of Greater New Haven', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 23:02:57'),
(84, '2023-00144', NULL, '', '', 'jlibrorania.its@gmail.com', '', 'e265c866931baac0bb1b221271b6916fd53c15a6', 'John', 'Librorania', 'Oray', '', '0443e62f38dbaa622dcffc0cb385d50b80d8f57acca2199279b14b03a19531850302a949f875e16271b327bd8955b2dbde87b1d408f8ab47b45cf220383466c6v4lAyWMji060kr__xIOZLF3YFkphy22c_RABoYni2VY~', 'Male', '1970-02-25', '2025-09-22', 'admin', 'om', 'ITS - CFTHH', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 23:18:08'),
(85, '2023-00133', '2023-00003', '51514', '40', 'vlaride.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Vincent', 'Laride', '', '', '528f4d7ce9e119fb7d616859286921df30fe76b42a578b57877ae55c1f323219683076ab7e57e33cbf881a7eb50b815edef317031b569b7ebeddcdc7004c9fb56wO_TG1LhmBDnGaWCqgkwA9beMPDK23AcnjzwUvpwIs~', 'Male', '1997-09-18', '2025-06-11', 'patient_advocate', 'pa', 'ITS - Heart Wellness Group', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-11 23:19:02'),
(86, '2023-00148', '2023-00007', '', '44', 'jaymaris.its@gmail.com', '', '66234e412e653db40b28c3ba0af951ae25727557', 'Jay Maris', 'Roblon', '', '', 'c6359e7a9bb83d6549c3cfb14289bed8193b95e64d8ea06722101cb778a2e14f5bbb6e40cd3904a7bcf84d5bb35acc0334925006d2442c605a7fcbc3c5b4e4f9H5ISyCxFMsH10b7hKCBpJLP-hy2hgwDRDjWKzDUuA-Q~', 'Female', '2001-01-19', '2025-10-20', 'employee', 'pa', 'CareMedica - CT', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-12 01:23:52'),
(87, '2023-00149', '2023-00007', '', '49', 'cpayusan.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Cairah', 'Payusan', 'Tioxon', '', '4eaa1edab4d65c231f9166d036812b18d5cdac4e618cfcece01e23f60f7a87b32f102347c6c559f938ed0e8fefe8f0cecc8b8114836e1cc90a629146468626ceAe7OijPfgM1DGSPwc117x-q3_EeTGe8oL2HmWy4aGdk~', 'Female', '2022-11-18', '2025-10-22', 'employee', 'pa', 'CareMedica - CT', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-12 01:26:48'),
(88, '2023-00145', '2023-00007', '', '07', 'arielquirante.its@gmail.com', '', 'afe3ffbfccb0470513dbf3411f7002e6dac1f378', 'Ariel', 'Quirante', 'Cayacapan', '', 'b65e1591d1bcc10e0dac701215f63c56b464139283eb2636772a68d976b874b24a2cadaff0d12c1d679c720c35f7fa622b6aaa8418f37b54ca23b5e4682f0aa8ee8UU4zhD5SgARo4q1xLspLQnbche25EL6srvkdBQ6k~', 'Male', '2000-05-01', '2025-10-03', 'employee', 'pa', 'CareMedica - Florida', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-12 03:14:47'),
(89, '2023-00147', '2023-00007', '', '5', 'darvyntrino.its@gmail.com', '', '8d3f8945d07f45fc50179f558863d2e949d8b831', 'Darvyn', 'Triño', 'Pedrosa', '', '1aa1ab6cc1e0192d3089e10651d8f296e6bfaaa4ea70464b4d8ec73223f5ac2f7f2aaf9140a2674f53fb3a3cc433e57b5b38e6f4935b2493ca623d44fbf47ac4RFvP_1Cs_WLfjUKXDTkB1yww7OIN2aZQ98bJax05w5k~', 'Male', '1995-05-03', '2025-10-20', 'employee', 'pa', 'CareMedica - CT', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-12 21:31:57'),
(90, '2023-00150', '2023-00007', '', '52', 'lmdelapena.its@gmail.com', '', 'b788bb5c28d696713ee9357a7b9147cd480c10a9', 'Lea Mae', 'Dela Peña', 'Bacea', '', 'a92147270a220536d40f5c4042de473d6d5b41bb61a36726533234b91351337b44dc70a531bd74373ed923165d71e0ffadefbeebc14b2f9c5e7cd58df85fc953ndqnhnT7Q1sxtVhsV_L8A2rcdoJDxYUXJfYl8PgQo3M~', 'Female', '2004-03-03', '2025-11-11', 'employee', 'cs', '', '', '', '', '7,6', 0, NULL, NULL, '', '1', '2025-11-12 21:48:21');

-- --------------------------------------------------------

--
-- Table structure for table `employee_break`
--

CREATE TABLE `employee_break` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `break_start` datetime NOT NULL,
  `break_end` datetime DEFAULT NULL,
  `break_type` varchar(50) NOT NULL,
  `notes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_break`
--

INSERT INTO `employee_break` (`id`, `employee_id`, `date`, `break_start`, `break_end`, `break_type`, `notes`) VALUES
(1, 4, '2025-08-25 23:36:39', '2025-08-25 23:10:39', '2025-08-26 00:31:51', 'lunch', ''),
(2, 4, '2025-08-25 23:41:15', '2025-08-25 22:18:15', '2025-08-25 22:28:15', 'break', ''),
(3, 4, '2025-08-26 01:10:15', '2025-08-26 01:10:39', '2025-08-26 01:25:39', 'break', ''),
(4, 3, '2025-08-26 23:36:39', '2025-08-26 23:10:39', '2025-08-26 00:01:51', 'lunch', ''),
(5, 3, '2025-08-26 23:41:15', '2025-08-26 22:18:15', '2025-08-26 22:28:15', 'break', ''),
(6, 3, '2025-08-27 01:10:15', '2025-08-27 01:10:39', '2025-08-27 01:19:39', 'break', ''),
(7, 61, '2025-11-11 22:17:03', '2025-11-11 22:17:03', '2025-11-11 22:17:35', 'break', ''),
(8, 11, '2025-11-11 23:30:12', '2025-11-11 23:30:12', '2025-11-11 23:41:23', 'break', ''),
(9, 61, '2025-11-12 22:13:40', '2025-11-12 22:13:40', '2025-11-12 22:13:48', 'lunch', ''),
(10, 61, '2025-11-12 22:13:51', '2025-11-12 22:13:51', '2025-11-12 22:13:54', 'break', ''),
(11, 61, '2025-11-12 22:13:58', '2025-11-12 22:13:58', '2025-11-12 22:14:00', 'break', ''),
(12, 61, '2025-11-12 22:14:06', '2025-11-12 22:14:06', '2025-11-12 22:14:08', 'break', ''),
(13, 61, '2025-11-12 22:14:12', '2025-11-12 22:14:12', '2025-11-12 22:14:15', 'break', ''),
(14, 61, '2025-11-12 22:14:18', '2025-11-12 22:14:18', '2025-11-12 22:14:20', 'break', ''),
(15, 61, '2025-11-12 22:14:26', '2025-11-12 22:14:26', '2025-11-12 22:15:05', 'break', ''),
(16, 87, '2025-11-12 22:17:16', '2025-11-12 22:17:16', NULL, 'break', ''),
(17, 59, '2025-11-11 00:42:15', '2025-11-11 00:42:15', '2025-11-11 01:45:12', 'lunch', ''),
(18, 59, '2025-11-11 02:03:16', '2025-11-11 02:03:16', '2025-11-11 02:10:08', 'break', ''),
(19, 59, '2025-11-11 03:53:25', '2025-11-11 03:53:25', '2025-11-11 04:02:08', 'break', '');

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `detail` varchar(100) NOT NULL,
  `value` longtext NOT NULL,
  `added_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`id`, `emp_id`, `detail`, `value`, `added_by`, `updated_by`, `date_added`, `date_updated`) VALUES
(1, 2, 'employee_document', '{\"file_name\":\"Employee Resume\",\"upload_name\":\"Test Resume.txt\",\"upload_path\":\"uploads\\/employee_documents\\/2025\\/06\\/10\\/e7a802463e8eca44bfbc9ea841131f3e64dd4ccb.txt\"}', 1, 1, '2025-06-10 23:01:14', '2025-06-10 23:01:14'),
(2, 2, 'educ_background', '[{\"institution_name\":\"dsddasdas\",\"course\":\"asdsad\",\"start_date\":\"06\\/02\\/2025\",\"end_date\":\"06\\/02\\/2025\"}]', 1, 1, '2025-06-11 00:12:25', '2025-06-11 00:12:25'),
(3, 2, 'emergency_contact', '{\"primary_contact\":{\"name\":\"John Doe\",\"relationship\":\"Father\",\"phone1\":\"0000-1111\",\"phone2\":\"0000-2222\",\"address\":\"Father Location Address  \"},\"secondary_contact\":{\"name\":\"Jane Doe\",\"relationship\":\"Mother\",\"phone1\":\"0000-3333\",\"phone2\":\"0000-4444\",\"address\":\"Mother Actual Location Address  \"}}', 1, 1, '2025-06-11 00:13:40', '2025-08-29 21:28:31'),
(5, 2, 'personal_info', '{\"tin\":\"\",\"sss\":\"\",\"pag_ibig\":\"\",\"phil_health\":\"\",\"hmo_account\":\"1\"}', 1, 1, '2025-08-27 02:32:53', '2025-08-27 02:32:53'),
(6, 2, 'bank_details', '{\"primary_bank\":{\"name\":\"ITS - BPI Payroll\",\"number\":\"0429191706\"},\"secondary_bank\":{\"name\":\"BPI Personal\",\"number\":\"11111111\"}}', 1, 1, '2025-08-29 21:06:15', '2025-08-29 21:27:02'),
(7, 2, 'employee_requirements', '{\"file_name\":\"Government Numbers - TIN\",\"upload_name\":\"SSS ID.jpg\",\"upload_path\":\"uploads\\/employee_requirements\\/2025\\/11\\/03\\/f52d058f8e8cc83d24758182087f0166ada5e28e.jpg\",\"remarks\":\"Test Remarks TIN\"}', 1, 1, '2025-11-03 14:26:06', '2025-11-03 14:26:06'),
(10, 2, 'employee_requirements', '{\"file_name\":\"Employment Documents (if applicable) - Form 2316 \\/ Income Tax Return\",\"upload_name\":\"25317103053Uimsa95.jpeg\",\"upload_path\":\"uploads\\/employee_requirements\\/2025\\/11\\/07\\/bc616ce273f1319ae9e0275460f8a9e585d4bf5c.jpeg\",\"remarks\":\"\"}', 1, 1, '2025-11-07 18:45:42', '2025-11-07 18:45:42'),
(11, 2, 'employee_requirements', '{\"file_name\":\"Government Numbers - Pag-IBIG Number\",\"upload_name\":\"24516124221U86at3i.jpg\",\"upload_path\":\"uploads\\/employee_requirements\\/2025\\/11\\/07\\/8760e4635338ba57d7e770aaf8cd1a90a381d922.jpg\",\"remarks\":\"\"}', 1, 1, '2025-11-07 18:50:40', '2025-11-07 18:50:40'),
(12, 79, 'employee_requirements', '{\"file_name\":\"Government Numbers - TIN\",\"upload_name\":\"25317103053Uimsa95.jpeg\",\"upload_path\":\"uploads\\/employee_requirements\\/2025\\/11\\/07\\/74b093ac95c3cdc8828202fa7fc8001fd1da7729.jpeg\",\"remarks\":\"\"}', 1, 1, '2025-11-07 23:04:09', '2025-11-07 23:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `employee_discipline`
--

CREATE TABLE `employee_discipline` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date_of_incident` datetime DEFAULT NULL,
  `violation` varchar(250) NOT NULL,
  `violation_details` text NOT NULL,
  `nte_date` datetime DEFAULT NULL,
  `nte_deadline` datetime DEFAULT NULL,
  `employee_explanation` longtext NOT NULL DEFAULT '',
  `nte_reply_date` datetime DEFAULT NULL,
  `notice_of_decision` text NOT NULL,
  `employee_action_plan` text NOT NULL,
  `offense` varchar(255) NOT NULL,
  `offense_level` varchar(255) NOT NULL,
  `offense_sanction` varchar(255) NOT NULL,
  `suspension_dates` text NOT NULL,
  `attachments` longtext NOT NULL,
  `status` varchar(100) NOT NULL,
  `archived` int(11) NOT NULL DEFAULT 0 COMMENT '1_archived',
  `added_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `archived_by` int(11) DEFAULT NULL,
  `archived_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_discipline`
--

INSERT INTO `employee_discipline` (`id`, `employee_id`, `date_of_incident`, `violation`, `violation_details`, `nte_date`, `nte_deadline`, `employee_explanation`, `nte_reply_date`, `notice_of_decision`, `employee_action_plan`, `offense`, `offense_level`, `offense_sanction`, `suspension_dates`, `attachments`, `status`, `archived`, `added_by`, `date_added`, `archived_by`, `archived_at`) VALUES
(2, 5, '2025-06-02 00:00:00', 'ATTENDANCE ISSUE: Tardiness', '<?xml encoding=\"utf-8\" ?><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><strong><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'><span style=\"background-color:null;\">SAMPLE TEST ONLY Part 2</span></span></strong></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'>Please be advised that you were allegedly been reported for attendance issues due to your tardiness. During the month of July 2025. You were late on<b> 7/3, 7/14, 7/15, 7/18, 7/21 </b>and on<b> 7/22.</b></span></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'>Below is a screen shot of your login time report for the month of July 2025:</span></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><img src=\"http://localhost/idealtech/uploads/disciplinary_inline/2025/08/18/696e189d0b8840e53ca1c877695beacf1705d4a7.png\"></p><p class=\"MsoNormal\"><span lang=\"EN-US\" style=\"font-size:10.0pt\">As stated in the Code of Conduct, your attendance plays as a large contribution to your work.</span></p><p class=\"MsoNormal\"><span lang=\"EN-US\" style=\"font-size:10.0pt\">During your employment orientation, you are trained about company policies and provided with handbook and documents which explained in fine details company rules and regulations, especially attendance and attrition. You also signed the same documents indicating that you agreed and understand each provision that has been stipulated.</span></p>\n', '2025-07-23 00:00:00', '2025-07-28 00:00:00', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee </label></strong> Explanation <strong><label for=\"Employee_Action_Plan\">Plan</label>&nbsp;Sample: This is Test Sample Me is AA</strong></p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee </label></strong> Explanation <strong><label for=\"Employee_Action_Plan\">Plan</label>&nbsp;Sample: This is Test Sample Me is AA</strong></p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee </label></strong> Explanation <strong><label for=\"Employee_Action_Plan\">Plan</label>&nbsp;Sample: This is Test Sample Me is AA</strong></p>\r\n', '2025-07-24 00:00:00', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Notice_of_Decision\">Notice of Decision Sample 123:&nbsp;</label></strong>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aliquam dolor lorem, euismod nec ultrices a, fringilla eget urna. Aenean faucibus, orci a ornare commodo, lacus purus faucibus erat, et condimentum nisl ex vel ex. Vivamus feugiat mauris ac nunc porttitor, ac posuere enim euismod.</p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\">Mauris commodo facilisis blandit. Nam maximus diam ut turpis pulvinar interdum. Vivamus convallis varius ex, non congue nisi commodo eget. Integer in vestibulum odio, rhoncus fermentum neque. Quisque lectus magna, vehicula vitae nisl at, ultricies bibendum dolor. Nullam luctus interdum dolor. Cras maximus tincidunt mi, non egestas orci fermentum a. Aliquam posuere quam risus, eu semper turpis feugiat eget. Donec vitae orci odio.</p>\r\n', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee Action Plan</label>&nbsp;Sample: This is Test Sample Me is bb</strong></p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee Action Plan</label>&nbsp;Sample: This is Test Sample Me is bb</strong></p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee Action Plan</label>&nbsp;Sample: This is Test Sample Me is bb</strong></p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee Action Plan</label>&nbsp;Sample: This is Test Sample Me is bb</strong></p>\r\n', 'Minor', 'Test  Level of Offense', 'Test Sanction', '', '[{\"file_name\":\"Gayo-Tardiness7.23.25.docx\",\"file_path\":\"uploads\\/disciplinary_attachments\\/2025\\/08\\/08\\/10aeeaf89cc220b7c33eb37a7009276c88b1e08a.docx\"},{\"file_name\":\"Notice to Explain (NTE) \\u2014 Template - Test Now.pdf\",\"file_path\":\"uploads\\/disciplinary_attachments\\/2025\\/09\\/05\\/416567f3ac5bca1f72907465d229ebbc7643d83d.pdf\"}]', 'nod', 0, 1, '2025-08-08 17:36:35', NULL, NULL),
(3, 4, '2025-06-02 00:00:00', 'ATTENDANCE ISSUE: Tardiness', '<?xml encoding=\"utf-8\" ?><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><!--?xml encoding=\"utf-8\" ?--><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><strong><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'><span style=\"background-color:null;\">SAMPLE TEST ONLY</span></span></strong></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'>Please be advised that you were allegedly been reported for attendance issues due to your tardiness. During the month of July 2025. You were late on<b> 7/3, 7/14, 7/15, 7/18, 7/21 </b>and on<b> 7/22.</b></span></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><span lang=\"EN-US\" style=\'font-size:10.0pt;font-family:\"Calibri\",sans-serif;color:black\'>Below is a screen shot of your login time report for the month of July 2025:</span></p><p style=\"margin-bottom:8.0pt;mso-line-height-alt:.95pt\"><img src=\"http://localhost/idealtech/uploads/disciplinary_inline/2025/08/18/696e189d0b8840e53ca1c877695beacf1705d4a7.png\"></p><p class=\"MsoNormal\"><span lang=\"EN-US\" style=\"font-size:10.0pt\">As stated in the Code of Conduct, your attendance plays as a large contribution to your work.</span></p><p class=\"MsoNormal\"><span lang=\"EN-US\" style=\"font-size:10.0pt\">During your employment orientation, you are trained about company policies and provided with handbook and documents which explained in fine details company rules and regulations, especially attendance and attrition. You also signed the same documents indicating that you agreed and understand each provision that has been stipulated.</span></p>\r\n', '2025-07-23 00:00:00', '2025-07-28 00:00:00', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Explanation\">Employee Explanation Test:&nbsp;</label></strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras cursus nisi in interdum viverra. Vivamus in hendrerit sem. Ut dictum elit non tellus laoreet efficitur. In non est ex. Praesent varius vestibulum felis, ut facilisis nibh molestie luctus. Curabitur ac ultrices nunc. Vestibulum consectetur nunc lectus, a aliquet augue bibendum vel.</p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\">Curabitur eu sollicitudin metus. Suspendisse potenti. Cras vehicula accumsan dolor, ac pulvinar velit consectetur ac. Vivamus in fringilla massa. Aenean blandit malesuada luctus</p>\r\n', '2025-07-24 00:00:00', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Notice_of_Decision\">Notice of Decision Sample 123:&nbsp;</label></strong>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Aliquam dolor lorem, euismod nec ultrices a, fringilla eget urna. Aenean faucibus, orci a ornare commodo, lacus purus faucibus erat, et condimentum nisl ex vel ex. Vivamus feugiat mauris ac nunc porttitor, ac posuere enim euismod.</p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\">Mauris commodo facilisis blandit. Nam maximus diam ut turpis pulvinar interdum. Vivamus convallis varius ex, non congue nisi commodo eget. Integer in vestibulum odio, rhoncus fermentum neque. Quisque lectus magna, vehicula vitae nisl at, ultricies bibendum dolor. Nullam luctus interdum dolor. Cras maximus tincidunt mi, non egestas orci fermentum a. Aliquam posuere quam risus, eu semper turpis feugiat eget. Donec vitae orci odio.</p>\r\n', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\"><strong><label for=\"Employee_Action_Plan\">Employee Action Plan</label>&nbsp;Sample:&nbsp;</strong>Praesent quam tortor, ullamcorper ut dapibus ac, consectetur a quam. Donec id consectetur tellus, quis congue mauris. Duis tincidunt hendrerit hendrerit. Integer ullamcorper sed diam a sollicitudin. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-indent: 0px; text-transform: none; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; white-space: normal; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial;\">Integer arcu enim, dictum vitae mi et, pharetra eleifend urna. Integer fermentum lectus et mauris blandit, eu volutpat arcu eleifend. Ut fermentum venenatis augue, nec blandit eros vulputate nec. Donec fringilla mi non ipsum sagittis blandit. Vivamus eros tortor, blandit et blandit vel, feugiat sed nibh. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n', '', '', '', '', '', 'nod', 0, 1, '2025-08-08 17:36:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave`
--

CREATE TABLE `employee_leave` (
  `leave_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `sil` int(11) NOT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `reason` text NOT NULL,
  `actual_date_from` datetime DEFAULT NULL,
  `actual_date_to` datetime DEFAULT NULL,
  `sv_status` varchar(50) NOT NULL DEFAULT 'pending',
  `sv_detail` text NOT NULL,
  `mgr_status` varchar(50) NOT NULL DEFAULT 'pending',
  `mgr_detail` text NOT NULL,
  `date_filed` datetime NOT NULL,
  `archived` int(11) NOT NULL DEFAULT 0 COMMENT '1-archived',
  `archived_by` int(11) DEFAULT NULL,
  `date_archived` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `date_confirmed` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_leave`
--

INSERT INTO `employee_leave` (`leave_id`, `employee_id`, `type`, `sil`, `date_from`, `date_to`, `reason`, `actual_date_from`, `actual_date_to`, `sv_status`, `sv_detail`, `mgr_status`, `mgr_detail`, `date_filed`, `archived`, `archived_by`, `date_archived`, `status`, `date_confirmed`) VALUES
(1, 5, 'casual_leave', 8, '2025-09-10 22:00:00', '2025-09-11 07:00:00', 'Sample Leave', '2025-09-10 22:00:00', '2025-09-11 07:00:00', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-04 23:43:31\",\"approved_comment\":\"\"}', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-04 02:28:09\",\"approved_comment\":\"\"}', '2025-09-02 23:02:54', 0, NULL, NULL, 'confirmed', '2025-09-05 22:42:04'),
(3, 5, 'casual_leave', 8, '2025-09-03 22:00:00', '2025-09-04 07:00:00', 'Test Leave', '2025-09-03 22:00:00', '2025-09-04 07:00:00', 'pending', '', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-05 22:42:44\",\"approved_comment\":\"\"}', '2025-09-05 21:43:36', 0, NULL, NULL, 'confirmed', '2025-09-15 19:21:06'),
(4, 11, 'vacation_leave', 8, '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'Test Leave', '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'pending', '', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-05 22:42:44\",\"approved_comment\":\"\"}', '2025-09-01 21:43:36', 0, NULL, NULL, 'pending', NULL),
(5, 15, 'vacation_leave', 8, '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'Test Leave', '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'pending', '', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-05 22:42:44\",\"approved_comment\":\"\"}', '2025-09-01 21:43:36', 0, NULL, NULL, 'pending', NULL),
(6, 17, 'vacation_leave', 8, '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'Test Leave', '2025-09-15 22:00:00', '2025-09-16 07:00:00', 'pending', '', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-05 22:42:44\",\"approved_comment\":\"\"}', '2025-09-01 21:43:36', 0, NULL, NULL, 'pending', NULL),
(7, 23, 'vacation_leave', 8, '2025-09-25 22:00:00', '2025-09-26 07:00:00', 'Test Leave', '2025-09-25 22:00:00', '2025-09-26 07:00:00', 'pending', '', 'approved', '{\"approved_by\":\"1\",\"approved_date\":\"2025-09-15 22:42:44\",\"approved_comment\":\"\"}', '2025-09-01 21:43:36', 0, NULL, NULL, 'pending', NULL),
(8, 61, 'casual_leave', 0, '2025-11-17 22:00:00', '2025-11-18 07:00:00', 'Emergency', NULL, NULL, 'pending', '', 'pending', '', '2025-11-11 22:18:49', 0, NULL, NULL, 'pending', NULL),
(9, 61, 'vacation_leave', 0, '2025-12-01 00:00:00', '2025-12-31 00:00:00', 'asgsfghjdgjh', NULL, NULL, 'pending', '', 'pending', '', '2025-11-11 23:48:28', 0, NULL, NULL, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary`
--

CREATE TABLE `employee_salary` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `old_salary` longtext NOT NULL,
  `new_salary` longtext NOT NULL,
  `effective_date` date NOT NULL,
  `remarks` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0-pending',
  `added_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salary`
--

INSERT INTO `employee_salary` (`id`, `employee_id`, `old_salary`, `new_salary`, `effective_date`, `remarks`, `status`, `added_by`, `date_added`, `date_updated`, `updated_by`) VALUES
(2, 2, '1fae00a9093049fcdd4f94c27666fd4b08e2b678d33e065acd0ddb9c2e8bc9d5f9d282c2f9d4d2f910d34e37542026f4539e2bdbef4a9e2a4fa9dba814951b24_0C4xrW6tUwEaxF3-NNOBfCwTv5l9C-tGFH8plCwTE8~', '957ecd1cddc858a0186ce731884ff783b05d65e051dec15ce948de18103f2d91e272509aaaad37bba033964baf47693b4035f3711ff7427b23243d04b604506cuL_v9LnQjf3Z5dktYG5R6SgUScMECTKfFkOmoLJ_fTU~', '2019-02-19', '', 0, 1, '2025-09-15 17:39:57', '2025-09-15 17:39:57', 1),
(3, 3, '6e2181b19eb7bc93f9a92f2890ff2942a142891ced529c11dd122e84b3aee4df5a32c1ce61ff2bfc6223b16cefa9f602ff47fe2d7fc2f3dd54c4e39b9f6d5e13DKMZia2THFAKc1s5MP7RvcjaCE8i--Bye6jWG3L2s7g~', 'b1b25d79ea83021373553dbd626acecad25489e448a2804d4fa6aafdf67f079e505abbe99a83af1521931bd72b40caa883d3699a272e83832c919cc80f0d30f9a852hiy1LrZaXpUEjPNWEiLV8Ok0CID7SAyqfrMx4FI~', '2025-09-01', 'Starting Salary', 0, 1, '2025-09-15 17:54:15', '2025-09-15 17:54:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `employee_schedule`
--

CREATE TABLE `employee_schedule` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `schedule_start` time NOT NULL,
  `schedule_end` time NOT NULL,
  `schedule_from` date NOT NULL,
  `schedule_to` date DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(40) NOT NULL,
  `archived` int(11) NOT NULL DEFAULT 0 COMMENT '0-active',
  `added_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `name`, `date`, `type`, `archived`, `added_by`, `date_added`) VALUES
(2, 'Araw ng Kagitingan (Test)', '2025-04-09', 'regular', 1, 1, '2025-06-10 23:01:57'),
(3, 'Maundy Thursday', '2025-04-17', 'regular', 0, 1, '2025-06-10 23:01:57'),
(4, 'Good Friday', '2025-04-18', 'regular', 0, 1, '2025-06-10 23:01:57'),
(5, 'Labor Day', '2025-05-01', 'regular', 0, 1, '2025-06-10 23:01:57'),
(6, 'Independence Day', '2025-06-12', 'regular', 0, 1, '2025-06-10 23:01:57'),
(7, 'National Heroes Day', '2025-08-25', 'regular', 0, 1, '2025-06-10 23:01:57'),
(8, 'Bonifacio Day', '2025-11-30', 'regular', 0, 1, '2025-06-10 23:01:57'),
(9, 'Christmas Day', '2025-12-25', 'regular', 0, 1, '2025-06-10 23:01:57'),
(10, 'Rizal Day', '2025-12-30', 'regular', 0, 1, '2025-06-10 23:01:57'),
(11, 'Ninoy Aquino Day', '2025-08-21', 'special', 0, 1, '2025-06-10 23:01:57'),
(12, 'All Saints’ Day', '2025-11-01', 'special', 0, 1, '2025-06-10 23:01:57'),
(13, 'Feast of the Immaculate Conception', '2025-12-08', 'special', 0, 1, '2025-06-10 23:01:57'),
(14, 'Last Day of the Year', '2025-12-31', 'special', 0, 1, '2025-06-10 23:01:57'),
(15, 'All Saints’ Day Eve', '2025-10-31', 'special', 0, 1, '2025-06-10 23:01:57'),
(16, 'Christmas Eve', '2025-12-24', 'special', 0, 1, '2025-06-10 23:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `kudos`
--

CREATE TABLE `kudos` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `path` longtext NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0 COMMENT '1-active',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1-active',
  `date_added` datetime NOT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kudos`
--

INSERT INTO `kudos` (`id`, `name`, `category`, `path`, `active`, `status`, `date_added`, `added_by`) VALUES
(2, 'Kudos Test', 'Kudos Test', 'uploads/kudos/0ab09c0b1f2d1b6b8c85d5007a2ca6dfe8545bd6.jpg', 0, 1, '2025-09-05 19:54:21', 1),
(3, 'New Kudos', 'New Kudos', 'uploads/kudos/db09eddc1f8c49cbfd850552fb22286a4ded746d.jpg', 0, 1, '2025-09-05 20:17:10', 1),
(4, 'New Kudos Test', 'Kudos Test', 'uploads/kudos/7693f4aad725fa2edbffbda2637517c39afec0e3.jpg', 0, 1, '2025-09-05 20:18:16', 1),
(5, 'Winner', 'Kudos Test', 'uploads/kudos/8fe37b0572f39f260483533d695c5bdd22ef94b3.png', 0, 1, '2025-09-05 22:55:26', 1),
(6, 'New Promotted Employee', 'Promotion', 'uploads/kudos/da29fe743153dc3b203bb70fe2cc85361d25fbb2.jpg', 1, 1, '2025-09-05 22:56:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `details` longtext NOT NULL,
  `payout_month` varchar(40) DEFAULT NULL,
  `period` varchar(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0_hidden, 1_shown, 2_archived',
  `added_by` int(11) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `employee_id`, `details`, `payout_month`, `period`, `status`, `added_by`, `date_added`) VALUES
(1, 2, '{\"file_name\":\"Abaigar, Ayani Joanne - 15th - June 2025.jpg\",\"file_path\":\"payslip\\/2025\\/06\\/10\\/4e142bf1d3fb87c3a142a4cc0ba92b4b392ec88a.jpg\"}', '2025-06', '1', 1, 1, '2025-06-10 22:31:15'),
(2, 1, '{\"file_name\":\"System, Admin - 15th - June 2025.jpg\",\"file_path\":\"payslip\\/2025\\/06\\/10\\/3ac34033ea24e9a7f145a0b8720e13862ddedff1.jpg\"}', '2025-06', '1', 1, 1, '2025-06-10 22:33:13'),
(3, 1, '{\"file_name\":\"25317103042U87i39t.jpeg\",\"file_path\":\"payslip\\/2025\\/06\\/10\\/d1f66f14e1f49e94bdd05d539286b1061166903d.jpeg\"}', '2025-06', '2', 1, 1, '2025-06-10 23:49:24'),
(4, 2, '{\"file_name\":\"Test-Payroll-30th-August-2025.pdf\",\"file_path\":\"payslip\\/2025\\/08\\/06\\/9b42d723996ab65a670731898e1b65a89baa8820.pdf\"}', '2025-08', '2', 1, 1, '2025-08-06 18:46:29'),
(5, 5, '{\"file_name\":\"Payslip 30th - September 2025.PNG\",\"file_path\":\"uploads\\/payslip\\/2025\\/09\\/05\\/8f578947be7d792313821cd13951862bcf7cc4f0.PNG\"}', '2025-09', '2', 1, 1, '2025-09-05 21:58:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_lang`
--
ALTER TABLE `admin_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_tabs`
--
ALTER TABLE `admin_tabs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_archived` (`archived`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `document_views`
--
ALTER TABLE `document_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_document_id` (`document_id`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_view_date` (`view_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_break`
--
ALTER TABLE `employee_break`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_discipline`
--
ALTER TABLE `employee_discipline`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD PRIMARY KEY (`leave_id`);

--
-- Indexes for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_schedule`
--
ALTER TABLE `employee_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kudos`
--
ALTER TABLE `kudos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_lang`
--
ALTER TABLE `admin_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `admin_tabs`
--
ALTER TABLE `admin_tabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_views`
--
ALTER TABLE `document_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `employee_break`
--
ALTER TABLE `employee_break`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `employee_details`
--
ALTER TABLE `employee_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee_discipline`
--
ALTER TABLE `employee_discipline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_leave`
--
ALTER TABLE `employee_leave`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employee_salary`
--
ALTER TABLE `employee_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_schedule`
--
ALTER TABLE `employee_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kudos`
--
ALTER TABLE `kudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
