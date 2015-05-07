CREATE TABLE IF NOT EXISTS `#__oseipm_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `status` varchar(5) NOT NULL,
  `iptype` varchar(10) NOT NULL DEFAULT 'ip',
  `ipv6` tinyint(1) NOT NULL DEFAULT '0',
  `extension` varchar(4) DEFAULT NULL,
  `extensionID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__oseipm_iptable` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `acl_id` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL DEFAULT '',
  `user_id` int(11) DEFAULT NULL,
  `host` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__oseath_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aclid` int(11) NOT NULL,
  `l1ruleids` text,
  `l2ruleids` text,
  `datetime` datetime NOT NULL,
  `score` int(3) DEFAULT NULL,
  `referer` longtext,
  `notified` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__ose_secConfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__oseath_l1rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `signature` longtext NOT NULL,
  `trimmed_value` longtext DEFAULT NULL,
  `signatureaction` tinyint(2) DEFAULT NULL,
  `target` longtext NOT NULL,
  `targetaction` tinyint(2) DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__oseath_l2rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` longtext NOT NULL,
  `trimmed_value` longtext DEFAULT NULL,
  `keyaction` tinyint(2) DEFAULT NULL,
  `target` text,
  `targetaction` tinyint(2) DEFAULT NULL,
  `times` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


