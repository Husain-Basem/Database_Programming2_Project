drop table if exists `Users`, `Articles`, `Comments`, `Files`;

CREATE TABLE `Users`
(
`uId` int(20) NOT NULL AUTO_INCREMENT ,
`fName` varchar(250) NOT NULL,
`lName` varchar(250) NOT NULL,
`username` varchar(250) NOT NULL,
`password` varchar(250) NOT NULL,
`email` varchar(250) NOT NULL,
`type` varchar(10) NOT NULL,
`description` varchar(250), 
`date` varchar(250) NOT NULL,
`country` varchar(250) NOT NULL,
PRIMARY KEY (`uId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Articles`
(
`articleId` int(20) NOT NULL AUTO_INCREMENT ,
`title` varchar(250) NOT NULL,
`content` varchar(32000) NOT NULL,
`readTime` int(250) NOT NULL,
`writtenBy` int(20) NOT NULL,
`date` date NOT NULL,
PRIMARY KEY (`articleId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `Comments`
(
`cId` int(20) NOT NULL AUTO_INCREMENT ,
`comment` varchar(32000) NOT NULL,
`rating` varchar(250) NOT NULL,
`reviewBy` int(20) NOT NULL,
`date` date NOT NULL,
PRIMARY KEY (`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `Files` (
`fId` int(20) NOT NULL AUTO_INCREMENT ,
`fName` varchar(250) NOT NULL,
`fType` varchar(250) NOT NULL,
`fLocation` varchar(250) NOT NULL,
`downloadable` boolean NOT NULL,
`articleId` int(20) NOT NULL,
`uId` int(20) NOT NULL,
PRIMARY KEY (`fId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



/* In users database 
	*type of user is categorized into three:
//-Viewer 
//-News Writer/Author <- can add a description 
//-Admin,

*/
