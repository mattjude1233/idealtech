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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_tabs`
--
ALTER TABLE `admin_tabs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_tabs`
--
ALTER TABLE `admin_tabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
