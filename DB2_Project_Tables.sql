SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `Articles`;
CREATE TABLE `Articles` (
  `articleId` int(20) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` longtext NOT NULL,
  `readTime` int(250) NOT NULL,
  `writtenBy` int(20) NOT NULL,
  `date` datetime NOT NULL,
  `category` varchar(50) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `thumbnail` varchar(1000) DEFAULT NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `Comments`;
CREATE TABLE `Comments` (
  `commentId` int(20) NOT NULL,
  `comment` mediumtext DEFAULT NULL,
  `rating` varchar(250) DEFAULT NULL,
  `reviewBy` int(20) NOT NULL,
  `date` datetime NOT NULL,
  `articleId` int(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `Files`;
CREATE TABLE `Files` (
  `fileId` int(20) NOT NULL,
  `fileName` varchar(250) NOT NULL,
  `fileType` varchar(250) NOT NULL,
  `fileLocation` varchar(250) NOT NULL,
  `fileSize` int(20) NOT NULL,
  `downloadable` tinyint(1) NOT NULL,
  `articleId` int(20) DEFAULT NULL,
  `userId` int(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `userId` int(20) NOT NULL,
  `firstName` varchar(250) NOT NULL,
  `lastName` varchar(250) NOT NULL,
  `userName` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `type` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `country` varchar(250) NOT NULL,
  `date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Users` (`userId`, `firstName`, `lastName`, `userName`, `password`, `email`, `type`, `description`, `country`, `date`) VALUES
(1, 'tom', 'tomson', 'tom', '$2y$10$g9w77TbXNRpN06KcD03e6OVvL.qvW8jWJz6QVNbNx5.Z6MSCibBxe', 'tom@tom.com', 'AUTHOR', 'Journalist based in bahrain', 'Bahrain', '2023-05-16'),
(2, 'bob', 'bobson', 'bob', '$2y$10$ZVUkzGvaTQ5q55gRIMMl8uyh9ZmzAPR9OscieIchbuao0OgRcOW/6', 'bob@bob.com', 'VIEWER', NULL, 'Bahrain', '2023-05-21'),
(3, 'Amin', 'AlAmin', 'admin1', '$2y$10$g9w77TbXNRpN06KcD03e6OVvL.qvW8jWJz6QVNbNx5.Z6MSCibBxe', 'amin@amin.com', 'ADMIN', NULL, 'Bahrain', '2023-05-21');


ALTER TABLE `Articles`
  ADD PRIMARY KEY (`articleId`);
ALTER TABLE `Articles` ADD FULLTEXT KEY `title` (`title`,`content`);

ALTER TABLE `Comments`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `articleId` (`articleId`);

ALTER TABLE `Files`
  ADD PRIMARY KEY (`fileId`),
  ADD KEY `articleId` (`articleId`),
  ADD KEY `userId` (`userId`);

ALTER TABLE `Users`
  ADD PRIMARY KEY (`userId`);


ALTER TABLE `Articles`
  MODIFY `articleId` int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Comments`
  MODIFY `commentId` int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Files`
  MODIFY `fileId` int(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Users`
  MODIFY `userId` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

DELIMITER $$
CREATE PROCEDURE `GetArticles`()
SELECT Articles.*, 
CONCAT(Users.firstName ,' ', Users.lastName) as author
FROM Articles JOIN Users on (Users.userId = Articles.writtenBy)
ORDER BY date DESC$$
DELIMITER ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
