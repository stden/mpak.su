<? die;

echo '<p>'.$sql = "CREATE TABLE `{$conf['db']['prefix']}{$arg['modpath']}_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
mpqw($sql);

echo '<p>'.$sql = "CREATE TABLE `{$conf['db']['prefix']}{$arg['modpath']}_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `usr` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `hide` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `qw` text NOT NULL,
  `ans` text NOT NULL,
  `href` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `hide` (`hide`),
  KEY `time` (`time`),
  KEY `uid` (`uid`),
  KEY `usr` (`usr`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
mpqw($sql);

mpqw("INSERT INTO `{$conf['db']['prefix']}settings` (`modpath`, `name`, `value`, `aid`, `description`) VALUES ('faq', 'faq', '<script>\$(function(){uid=\$(\"#faq\").parents(\"[uid]\").attr(\"uid\");\$(\"#faq\").load(\"/faq:ask\"+(uid?\"/uid:\"+uid:\'\')+\"/null\")})</script><div id=\"faq\"></div>', '1', 'Установка кода часто задаваемых вопросов на сайт.')");

?>