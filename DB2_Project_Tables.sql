drop table if exists `Users`, `Articles`, `Comments`, `Files`;

CREATE TABLE `Users`
(
`userId` int(20) NOT NULL AUTO_INCREMENT ,
`firstName` varchar(250) NOT NULL,
`lastName` varchar(250) NOT NULL,
`userName` varchar(250) NOT NULL,
`password` varchar(250) NOT NULL,
`email` varchar(250) NOT NULL,
`type` varchar(10) NOT NULL,
`description` varchar(250), 
`country` varchar(250) NOT NULL,
`date` date NOT NULL,
PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Add builtin users
INSERT INTO `Users` (`userId`, `firstName`, `lastName`, `userName`, `password`, `email`, `type`, `description`, `country`, `date`) VALUES
(NULL, 'tom', 'tomson', 'tom', '$2y$10$g9w77TbXNRpN06KcD03e6OVvL.qvW8jWJz6QVNbNx5.Z6MSCibBxe', 'tom@tom.com', 'AUTHOR', 'Journalist based in bahrain', 'Bahrain', '2023-05-16 10:52:31'),
(NULL, 'bob', 'bobson', 'bob', '$2y$10$ZVUkzGvaTQ5q55gRIMMl8uyh9ZmzAPR9OscieIchbuao0OgRcOW/6', 'bob@bob.com', 'VIEWER', NULL, 'Bahrain', '2023-05-21 11:46:09'),
(NULL, 'Amin', 'AlAmin', 'admin1', '$2y$10$g9w77TbXNRpN06KcD03e6OVvL.qvW8jWJz6QVNbNx5.Z6MSCibBxe', 'amin@amin.com', 'ADMIN', NULL, 'Bahrain', '2023-05-21 15:11:57');

CREATE TABLE `Articles`
(
`articleId` int(20) NOT NULL AUTO_INCREMENT ,
`title` varchar(250) NOT NULL,
`content` TEXT NOT NULL,
`readTime` int(250) NOT NULL,
`writtenBy` int(20) NOT NULL,
`date` date NOT NULL,
`category` varchar(50) NOT NULL,
`published` boolean NOT NULL
PRIMARY KEY (`articleId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Comments`
(
`commentId` int(20) NOT NULL AUTO_INCREMENT ,
`comment` varchar(32000),
`rating` varchar(250),
`reviewBy` int(20) NOT NULL,
`date` date NOT NULL,
`articleId` int(20),
PRIMARY KEY (`commentId`),
FOREIGN KEY (articleId) REFERENCES Articles(articleId)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `Files` (
`fileId` int(20) NOT NULL AUTO_INCREMENT ,
`fileName` varchar(250) NOT NULL,
`fileType` varchar(250) NOT NULL,
`fileLocation` varchar(250) NOT NULL,
`downloadable` boolean NOT NULL,
`articleId` int(20),
`userId` int(20),
PRIMARY KEY (`fileId`),
FOREIGN KEY (articleId) REFERENCES Articles(articleId),
FOREIGN KEY (userId) REFERENCES Users(userId)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



/* 
In Users table database type of user is categorized into three:
-Viewer
-News Writer/Author (Can add a description) 
-Admin
*/
