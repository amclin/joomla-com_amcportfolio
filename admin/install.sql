DROP TABLE IF EXISTS `#__amcportfolio`;
DROP TABLE IF EXISTS `#__amcportfolio_images`;

CREATE TABLE `#__amcportfolio` (
  `id` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `teaser` varchar(255),
  `published` BOOLEAN NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `description` text,
  `outside_link` varchar(255),
  `outside_link_text` varchar(255),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__amcportfolio_images` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL default '0',
  `image` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE `#__amcportfolio_movies` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL default '0',
  `movie` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__amcportfolio` (`title`,`published`,`alias`) VALUES
  ('Sample Project 1', True,  'sample-project-1'),
  ('Sample Project 2', False, 'sample-project-2'),
  ('Sample Project 3', True,  'sample-project-3');

INSERT INTO `#__amcportfolio_images` (`projectid`,`image`) VALUES
  (2,'/images/edit_f2.png'),
  (2,'/images/css_f2.png'),
  (2,'/images/joomla_logo_black.jpg');
