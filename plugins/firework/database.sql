#----------------------------------------
# Backup Web Database 
# Version 2.0 by vnTRUST  
# http://trust.vn  
# DATABASE:  cms_test
# Date/Time:  Saturday 03rd  January 2015 11:52:31
#----------------------------------------

DROP TABLE IF EXISTS plugins;
CREATE TABLE `plugins` (  `id` int(11) NOT NULL auto_increment,  `name` varchar(150) NOT NULL,  `title` varchar(250) NOT NULL,  `folder` varchar(150) NOT NULL,  `params` text NOT NULL,  `ordering` int(11) NOT NULL,  `display` tinyint(4) NOT NULL,  PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
INSERT INTO plugins VALUES ('2','snow','Hiệu ứng tuyết rơi','snow','a:9:{s:9:\"imgfolder\";s:4:\"snow\";s:6:\"usePNG\";s:1:\"0\";s:10:\"flakeTypes\";s:1:\"6\";s:9:\"flakesMax\";s:2:\"60\";s:4:\"vMax\";s:3:\"2.5\";s:10:\"flakeWidth\";s:1:\"5\";s:11:\"flakeHeight\";s:1:\"5\";s:11:\"snowCollect\";s:1:\"0\";s:10:\"showStatus\";s:1:\"0\";}','0','0'), ('3','trustvn_player','Flash nhạc  cho website ','trustvn_player','a:5:{s:5:\"width\";s:3:\"500\";s:6:\"height\";s:3:\"400\";s:8:\"autoplay\";s:1:\"0\";s:14:\"default_volume\";s:2:\"50\";s:10:\"screentext\";s:18:\"TRUSTvn mp3 player\";}','0','0'), ('4','firework','Hiệu ứng bắn pháo bông','firework','a:4:{s:4:\"bits\";s:2:\"90\";s:5:\"speed\";s:2:\"33\";s:5:\"bangs\";s:1:\"7\";s:7:\"colours\";s:34:\"#03f,#f03,#0e0,#93f,#0cf,#f93,#f0c\";}','0','1');
