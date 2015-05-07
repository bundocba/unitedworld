
CREATE TABLE IF NOT EXISTS `#__fw_category`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
	  `desc` VARCHAR(255)    , 
  `ordering` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `#__fw_video`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cateid` int(11) NOT NULL,
	  `name` VARCHAR(255)    , 
	  `link` VARCHAR(255)    , 
	  `intro_image` VARCHAR(255)    , 
	  `desctiption` TEXT   , 
	  `source` VARCHAR(255)    , 
  `ordering` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);