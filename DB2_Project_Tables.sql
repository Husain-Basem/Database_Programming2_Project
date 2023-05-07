DROP TABLE IF EXISTS `Users`, `Articles`, `Comments`, `Files`;

CREATE TABLE `Users` (
  `uid` int(20) NOT NULL PRIMARY KEY, 
  `fname` varchar(250) NOT NULL, 
  `lname` varchar(250) NOT NULL, 
  `username` varchar(250) NOT NULL, 
  `password` varchar(250) NOT NULL, 
  `email` varchar(250) NOT NULL, 
  `type` varchar(10) NOT NULL, 
  `description` varchar(250), 
  `date` varchar(250) NOT NULL, 
  `country` varchar(250) NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

/*
type of user is categorized into three:
 -Viewer 
 -News Writer/Author <- can add a description 
 -Admin,
*/

CREATE TABLE `Articles` (
  `articleid` int(20) NOT NULL PRIMARY KEY, 
  `title` varchar(250) NOT NULL, 
  `content` varchar(32000) NOT NULL, 
  `readtime` int(250) NOT NULL, 
  `writtenby` int(20) NOT NULL, 
  `date` date NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

CREATE TABLE `Comments`(
  `cid` int(20) NOT NULL PRIMARY KEY, 
  `comment` varchar(32000) NOT NULL, 
  `rating` varchar(250) NOT NULL, 
  `reviewby` int(20) NOT NULL, 
  `date` date NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

CREATE TABLE `Files` (
  `fid` int(20) NOT NULL PRIMARY KEY, 
  `fname` varchar(250) NOT NULL, 
  `ftype` varchar(250) NOT NULL, 
  `flocation` varchar(250) NOT NULL, 
  `downloadable` boolean NOT NULL, 
  `articleid` int(20) NOT NULL, 
  `uid` int(20) NOT NULL
) ENGINE = MyISAM DEFAULT CHARSET = latin1;
