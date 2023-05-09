drop table if exists `Users`, `Articles`, `Comments`, `Files`;

CREATE TABLE `Users`
(
`userId` int(20) NOT NULL AUTO_INCREMENT ,
`firstName` varchar(250) NOT NULL,
`lastName` varchar(250) NOT NULL,
`username` varchar(250) NOT NULL,
`password` varchar(250) NOT NULL,
`email` varchar(250) NOT NULL,
`type` varchar(10) NOT NULL,
`description` varchar(250), 
`country` varchar(250) NOT NULL,
`date` date NOT NULL,
PRIMARY KEY (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Articles`
(
`articleId` int(20) NOT NULL AUTO_INCREMENT ,
`title` varchar(250) NOT NULL,
`content` varchar(65535) NOT NULL,
`readTime` int(250) NOT NULL,
`writtenBy` int(20) NOT NULL,
`date` date NOT NULL,
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
