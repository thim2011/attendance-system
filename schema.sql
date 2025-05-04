-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-05-04 17:54:11
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `recorder`
--

-- --------------------------------------------------------

--
-- 表的结构 `annual_leave`
--

CREATE TABLE `annual_leave` (
  `Id` int(11) NOT NULL,
  `Employee_id` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  `Month` int(11) NOT NULL,
  `TotalHours` decimal(5,2) NOT NULL,
  `UseHours` decimal(5,2) NOT NULL,
  `Leave_id` int(11) NOT NULL,
  `Status` enum('reset','normal') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `attendance`
--

CREATE TABLE `attendance` (
  `Attendance_id` int(11) NOT NULL,
  `Employee_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Punch_in` time NOT NULL,
  `Punch_out` time NOT NULL,
  `Status` enum('On Time','Late','Leave') NOT NULL,
  `Working_hours` varchar(50) DEFAULT NULL,
  `Is_Attend` enum('None','Checked-in','Checked-out') NOT NULL,
  `Is_leave` tinyint(1) DEFAULT NULL,
  `Leave_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 触发器 `attendance`
--
DELIMITER $$
CREATE TRIGGER `after_attendance_delete` AFTER DELETE ON `attendance` FOR EACH ROW BEGIN
    DECLARE v_year INT;
    DECLARE v_month INT;
    DECLARE v_total_hours DECIMAL(10, 2);
    
    SET v_year = YEAR(OLD.Date);
    SET v_month = MONTH(OLD.Date);
    
    SELECT COALESCE(SUM(CAST(Working_hours AS DECIMAL(10,2))), 0) INTO v_total_hours
    FROM attendance
    WHERE Employee_id = OLD.Employee_id
      AND YEAR(Date) = v_year
      AND MONTH(Date) = v_month;

    UPDATE employee_workhour
    SET NormalWorkingHours = v_total_hours
    WHERE Employee_id = OLD.Employee_id
      AND Year = v_year
      AND Month = v_month;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_attendance_insert` AFTER INSERT ON `attendance` FOR EACH ROW BEGIN
    DECLARE v_year INT;
    DECLARE v_month INT;
    DECLARE v_total_hours DECIMAL(10, 2);
    
    -- Lấy năm và tháng từ bản ghi mới được chèn vào
    SET v_year = YEAR(NEW.Date);
    SET v_month = MONTH(NEW.Date);

    -- Tính tổng giờ làm việc cho tháng hiện tại
    SELECT COALESCE(SUM(CAST(Working_hours AS DECIMAL(10,2))), 0) INTO v_total_hours
    FROM attendance
    WHERE Employee_id = NEW.Employee_id
      AND YEAR(Date) = v_year
      AND MONTH(Date) = v_month;

    -- Cập nhật hoặc chèn dữ liệu vào bảng employee_workhours
    INSERT INTO employee_workhour (Employee_id, Year, Month, NormalWorkingHours)
    VALUES (NEW.Employee_id, v_year, v_month, v_total_hours)
    ON DUPLICATE KEY UPDATE
        NormalWorkingHours = v_total_hours;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_attendance_update` AFTER UPDATE ON `attendance` FOR EACH ROW BEGIN
    DECLARE v_year INT;
    DECLARE v_month INT;
    DECLARE v_total_hours DECIMAL(10, 2);
    
    SET v_year = YEAR(NEW.Date);
    SET v_month = MONTH(NEW.Date);
    
    SELECT COALESCE(SUM(CAST(Working_hours AS DECIMAL(10,2))), 0) INTO v_total_hours
    FROM attendance
    WHERE Employee_id = NEW.Employee_id
      AND YEAR(Date) = v_year
      AND MONTH(Date) = v_month;

    UPDATE employee_workhour
    SET NormalWorkingHours = v_total_hours
    WHERE Employee_id = NEW.Employee_id
      AND Year = v_year
      AND Month = v_month;
    
    -- Nếu ngày được cập nhật và thay đổi tháng/năm, cập nhật cả bản ghi cũ
    IF OLD.Date != NEW.Date THEN
        SET v_year = YEAR(OLD.Date);
        SET v_month = MONTH(OLD.Date);
        
        SELECT COALESCE(SUM(CAST(Working_hours AS DECIMAL(10,2))), 0) INTO v_total_hours
        FROM attendance
        WHERE Employee_id = OLD.Employee_id
          AND YEAR(Date) = v_year
          AND MONTH(Date) = v_month;

        UPDATE employee_workhour
        SET NormalWorkingHours = v_total_hours
        WHERE Employee_id = OLD.Employee_id
          AND Year = v_year
          AND Month = v_month;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `employees`
--

CREATE TABLE `employees` (
  `Employee_id` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Account` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Position` varchar(50) DEFAULT NULL,
  `Department` varchar(50) DEFAULT NULL,
  `Join_date` datetime NOT NULL,
  `Status` enum('ACTIVE','INACTIVE') NOT NULL,
  `Point` int(100) NOT NULL,
  `AnnualLeave` float DEFAULT NULL,
  `Role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `Leave_id` int(11) NOT NULL,
  `Employee_id` int(11) NOT NULL,
  `Leave_type` int(11) NOT NULL,
  `Start_date` date NOT NULL,
  `End_date` date NOT NULL,
  `Total_day` int(11) DEFAULT NULL,
  `Reason` varchar(500) NOT NULL,
  `VerifyBy` int(11) DEFAULT NULL,
  `VerifyTime` datetime DEFAULT NULL,
  `VerifyReason` varchar(500) NOT NULL,
  `Status` enum('Pending','Accepted','Rejected','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `employee_workhour`
--

CREATE TABLE `employee_workhour` (
  `WorkTime_id` int(11) NOT NULL,
  `Employee_id` int(11) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL,
  `Month` int(11) DEFAULT NULL,
  `NormalWorkingHours` int(11) NOT NULL,
  `AnnualLeave` int(11) NOT NULL,
  `Work_Overtime` int(11) NOT NULL,
  `Half_leave_hours` int(11) NOT NULL,
  `Full_leave_hours` int(11) NOT NULL,
  `Unpaid_leave_hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `fixed_leave`
--

CREATE TABLE `fixed_leave` (
  `id` int(11) NOT NULL,
  `Employee_id` int(11) NOT NULL,
  `Monday_AM` tinyint(1) DEFAULT NULL,
  `Monday_PM` tinyint(1) DEFAULT NULL,
  `Tuesday_AM` tinyint(1) DEFAULT NULL,
  `Tuesday_PM` tinyint(1) DEFAULT NULL,
  `Wednesday_AM` tinyint(1) DEFAULT NULL,
  `Wednesday_PM` tinyint(1) DEFAULT NULL,
  `Thursday_AM` tinyint(1) DEFAULT NULL,
  `Thursday_PM` tinyint(1) DEFAULT NULL,
  `Friday_AM` tinyint(1) DEFAULT NULL,
  `Friday_PM` tinyint(1) DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `holidays`
--

CREATE TABLE `holidays` (
  `Holiday_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Year` int(5) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Is_Holiday` tinyint(1) DEFAULT NULL,
  `Category` varchar(266) DEFAULT NULL,
  `Description` varchar(266) DEFAULT NULL,
  `Is_Government` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `leave_details`
--

CREATE TABLE `leave_details` (
  `LeaveDetail_id` int(11) NOT NULL,
  `Leave_id` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Start_time` time NOT NULL,
  `End_time` time NOT NULL,
  `Total_time` int(11) NOT NULL,
  `Status` enum('ON','OFF') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `leave_types`
--

CREATE TABLE `leave_types` (
  `Leave_type_id` int(11) NOT NULL,
  `Leave_type_name` varchar(100) NOT NULL,
  `Pay_type` varchar(50) NOT NULL,
  `Max_days` int(11) NOT NULL,
  `Description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `notifications`
--

CREATE TABLE `notifications` (
  `Notification_id` int(11) NOT NULL,
  `Employee_id` int(11) DEFAULT NULL,
  `Message` varchar(255) DEFAULT NULL,
  `Noti_type` enum('in-app','email','both') DEFAULT 'in-app',
  `Mes_type` enum('Punch','Leave') DEFAULT NULL,
  `Leave_id` int(11) DEFAULT NULL,
  `Status` enum('unread','read') DEFAULT 'unread',
  `Created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `Created_by` int(11) DEFAULT NULL,
  `Read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `policy`
--

CREATE TABLE `policy` (
  `Policy_id` int(11) NOT NULL,
  `Chapter` varchar(255) NOT NULL,
  `Article` varchar(255) NOT NULL,
  `Content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `reset_password`
--

CREATE TABLE `reset_password` (
  `reset_id` int(11) NOT NULL,
  `Email` varchar(256) NOT NULL,
  `Token` varchar(256) NOT NULL,
  `Expired_at` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `setting`
--

CREATE TABLE `setting` (
  `ID` int(11) NOT NULL,
  `Work_start_time` time DEFAULT NULL,
  `Work_end_time` time DEFAULT NULL,
  `Break_start_time` time DEFAULT NULL,
  `Break_end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `annual_leave`
--
ALTER TABLE `annual_leave`
  ADD PRIMARY KEY (`Id`);

--
-- 表的索引 `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`Attendance_id`);

--
-- 表的索引 `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`Employee_id`);

--
-- 表的索引 `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`Leave_id`),
  ADD KEY `Leave_type_id` (`Leave_type`),
  ADD KEY `leave_Employee_id` (`Employee_id`);

--
-- 表的索引 `employee_workhour`
--
ALTER TABLE `employee_workhour`
  ADD PRIMARY KEY (`WorkTime_id`),
  ADD UNIQUE KEY `unique_employee_month` (`Employee_id`,`Year`,`Month`);

--
-- 表的索引 `fixed_leave`
--
ALTER TABLE `fixed_leave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee_id` (`Employee_id`);

--
-- 表的索引 `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`Holiday_id`);

--
-- 表的索引 `leave_details`
--
ALTER TABLE `leave_details`
  ADD PRIMARY KEY (`LeaveDetail_id`),
  ADD KEY `Leave_id` (`Leave_id`);

--
-- 表的索引 `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`Leave_type_id`);

--
-- 表的索引 `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`Notification_id`),
  ADD KEY `Employee_id` (`Employee_id`);

--
-- 表的索引 `policy`
--
ALTER TABLE `policy`
  ADD PRIMARY KEY (`Policy_id`);

--
-- 表的索引 `reset_password`
--
ALTER TABLE `reset_password`
  ADD PRIMARY KEY (`reset_id`);

--
-- 表的索引 `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`ID`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `annual_leave`
--
ALTER TABLE `annual_leave`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `attendance`
--
ALTER TABLE `attendance`
  MODIFY `Attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `employees`
--
ALTER TABLE `employees`
  MODIFY `Employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `Leave_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `employee_workhour`
--
ALTER TABLE `employee_workhour`
  MODIFY `WorkTime_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `fixed_leave`
--
ALTER TABLE `fixed_leave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `holidays`
--
ALTER TABLE `holidays`
  MODIFY `Holiday_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `leave_details`
--
ALTER TABLE `leave_details`
  MODIFY `LeaveDetail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `Leave_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `Notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `policy`
--
ALTER TABLE `policy`
  MODIFY `Policy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `reset_password`
--
ALTER TABLE `reset_password`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `setting`
--
ALTER TABLE `setting`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- 限制导出的表
--

--
-- 限制表 `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD CONSTRAINT `employee_leaves_ibfk_1` FOREIGN KEY (`Employee_id`) REFERENCES `employees` (`Employee_id`),
  ADD CONSTRAINT `fk_Leave_type_id` FOREIGN KEY (`Leave_type`) REFERENCES `leave_types` (`Leave_type_id`);

--
-- 限制表 `employee_workhour`
--
ALTER TABLE `employee_workhour`
  ADD CONSTRAINT `employee_workhour_ibfk_1` FOREIGN KEY (`Employee_id`) REFERENCES `employees` (`Employee_id`);

--
-- 限制表 `fixed_leave`
--
ALTER TABLE `fixed_leave`
  ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`Employee_id`) REFERENCES `employees` (`Employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `leave_details`
--
ALTER TABLE `leave_details`
  ADD CONSTRAINT `leave_details_ibfk_1` FOREIGN KEY (`Leave_id`) REFERENCES `employee_leaves` (`Leave_id`);

--
-- 限制表 `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Employee_id`) REFERENCES `employees` (`Employee_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
