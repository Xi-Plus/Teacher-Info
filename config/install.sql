SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `account` (
  `account` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `email_type` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `inuse` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `login_session` (
  `account` varchar(20) NOT NULL,
  `cookie` varchar(32) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `school_data` (
  `id` varchar(6) NOT NULL,
  `teacher_count` json NOT NULL,
  `year` smallint(6) NOT NULL,
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `updatetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `school_list` (
  `id` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `inuse` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `teacher_data` (
  `school_id` varchar(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `teacher_type` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_type` json NOT NULL,
  `year` smallint(6) NOT NULL,
  `confirm` tinyint(1) NOT NULL DEFAULT '0',
  `updatetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `teacher_list` (
  `school_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `updatetime` datetime NOT NULL,
  `hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `teacher_type` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `inuse` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `account`
  ADD UNIQUE KEY `account` (`account`);

ALTER TABLE `email_type`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `login_session`
  ADD UNIQUE KEY `time` (`time`);

ALTER TABLE `school_data`
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `school_list`
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `teacher_data`
  ADD UNIQUE KEY `hash` (`hash`);

ALTER TABLE `teacher_type`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `email_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `teacher_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
