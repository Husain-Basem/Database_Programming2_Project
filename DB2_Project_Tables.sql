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
`articleId` int(20) NOT NULL AUTO_INCREMENT,
`title` varchar(250) NOT NULL,
`content` MEDIUMTEXT NOT NULL,
`readTime` int(250) NOT NULL,
`writtenBy` int(20) NOT NULL,
`date` datetime NOT NULL,
`category` varchar(50) NOT NULL,
`published` boolean NOT NULL,
PRIMARY KEY (`articleId`),
FULLTEXT KEY (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

CREATE TABLE `Comments`
(
`commentId` int(20) NOT NULL AUTO_INCREMENT ,
`comment` varchar(32000),
`rating` varchar(250),
`reviewBy` int(20) NOT NULL,
`date` datetime NOT NULL,
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


START TRANSACTION;

--
-- Dumping data for table `Articles`
--

INSERT INTO `Articles` (`articleId`, `title`, `content`, `readTime`, `writtenBy`, `date`, `category`, `published`) VALUES
(1, 'Resort review: Nendaz, Switzerland', '<p><img src=\"http://localhost/~naofal/Database_Programming2_Project/uploads/7112Nendaz-1-1170x523.jpg\" style=\"display: block; margin: auto;\" data-align=\"center\" width=\"1212\" height=\"541.7824815603403\"></p><p>Nendaz is not one place but many: the Haute-Nendaz resort plus 17 surrounding villages. It is in the central part of Switzerland’s vast 4 Valleys ski area and is popular with Swiss families — many of whom have chalets or apartments here — in addition to visitors from further afield. </p><p>The majority of tourists come to Nendaz in the winter months for skiing and snowboarding, but winter sports are far from the resort’s only offering: there’s local heritage and gastronomy, and summertime guests can experience everything from trekking and trail running to cheese-making, outdoor swimming, and mountain biking.</p><p><br></p><h2><strong>Who should visit Nendaz?</strong></h2><p>Adventure seekers should put Veysonnaz high on their bucket list. This is a location with spectacular, unspoiled natural landscapes, and the combination of impressive mountains plus favourable weather conditions — plenty of powdery snow in the winter, and long, warm sunny days in summer — mean that you will want to spend as much time as possible outdoors. It doesn’t matter if you want to learn new skills or are already an accomplished skier, climber, or mountain biker, because there are opportunities for all levels of skill and fitness, plus guides and instructors who will help you reach the next level.&nbsp;</p><p><br></p><h2><strong>Winter activities in Nendaz</strong></h2><p><img src=\"https://www.thetravelmagazine.net/wp-content/uploads/4Vallees-MEP2023-FRY-067.jpg\" alt=\"Nendaz Switzerland\" style=\"display: inline; float: left; margin: 0px 1em 1em 0px;\" data-align=\"left\" width=\"330\" height=\"219.91451125070841\"></p><p>Skiing from Nendaz is fun! Yes, there are some runs that will make your knuckles turn white and certainly give you an adrenaline boost, but you can also enjoy the seven free tracks, fun park, and three snow gardens. </p><p>The exhilarating Mont Fort zip line starts at an altitude of 3,330m above sea level — the highest point in the 4 Valleys —and reaches a top speed of more than 100 km/h. If you prefer something a little more sedate, or at least slower, there are around 100 km of winter walks through the picturesque villages, pastures, and forests. </p><p>A particular highlight is night time snowshoeing from the village of Siviez, where a guide leads you up the mountain after dark and rewards your efforts with raclette melted over an open fire. It may be chilly, but it is certainly a memorable and well-earned meal.</p><p><br></p><p><br></p><p><br></p><p><br></p><h2><strong>Summer activities in Nendaz</strong></h2><p><img src=\"https://www.thetravelmagazine.net/wp-content/uploads/descente-trottinettes-panorama-ete-2022_2000.jpg\" alt=\"Cycling in Nendaz, Switzerland\" style=\"display: inline; float: left; margin: 0px 1em 1em 0px;\" data-align=\"left\" width=\"329\" height=\"219.24285480763652\"></p><p>Nendaz’s summer season begins in May when the days are already long and warm. If you are interested in local traditions, there is the annual Valais Drink Pure Alphorn Festival, where you can hear the distinctive sounds of the alphorn, a traditional musical instrument; and also a chance to learn about cheese making, cow bells, and even cow fighting when you walk along the 6 km Herens Cow Path. Visitors with more time and energy can embark on a 3-4 day self-guided walking tour, staying each night in mountain cabins, or explore some of the 200 km of mountain biking trails.</p><p><br></p><p><br></p><p><br></p><h2><br></h2><p><br></p><p><br></p><h2><strong>Where to stay in Nendaz</strong></h2><p><img src=\"https://www.thetravelmagazine.net/wp-content/uploads/H4V_ROOM1_Copyright-Hotel-Nendaz-4-Vallees-1.jpg\" alt=\"Hotel Nendaz 4 Vallees Switzerland\" style=\"display: inline; float: left; margin: 0px 1em 1em 0px;\" data-align=\"left\" width=\"331\" height=\"220.83132820698754\"></p><p>The majority of tourists who stay in Nendaz opt to rent a chalet or apartment, but there are also guesthouses, mountain cabins, B&amp;Bs, and some excellent hotels. We highly recommend <a href=\"https://www.thetravelmagazine.net/hotel-review-hotel-nendaz-4-vallees-spa-nendaz-switzerland/\" rel=\"noopener noreferrer\" target=\"_blank\">Hotel Nendaz 4 Vallees &amp; Spa</a>, which has rooms from CHF 309 / £278 in low season (rising to CHF 534 / £476 in high season), but if your budget won’t stretch quite that far, try the 3* Mad Mount Hotel which opened in December 2022. It is an eco-friendly property with charging stations for e-bikes and electric cars. </p><p>    </p><p>    </p>', 0, 1, '2023-05-25 18:09:26', 'tourism', 0);

--
-- Dumping data for table `Files`
--

INSERT INTO `Files` (`fileId`, `fileName`, `fileType`, `fileLocation`, `downloadable`, `articleId`, `userId`) VALUES
(null, '93447112Nendaz-1-1170x523.jpg', 'image/jpeg', '/uploads/93447112Nendaz-1-1170x523.jpg', 0, 8, 1);
COMMIT;

