<? die;

echo '<p>'.$sql = "CREATE TABLE `{$conf['db']['prefix']}{$arg['modpath']}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `refer` int(11) NOT NULL,
  `last_time` int(11) NOT NULL,
  `count_time` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `cnull` int(11) NOT NULL,
  `sess` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `last_time` (`last_time`),
  KEY `uid` (`uid`,`cnull`,`count`),
  KEY `ip` (`ip`),
  KEY `agent` (`agent`),
  FULLTEXT KEY `sess` (`sess`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
mpqw($sql);

echo '<p>'.$sql = "CREATE TABLE `{$conf['db']['prefix']}{$arg['modpath']}_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `post` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
mpqw($sql);

mpqw("INSERT INTO `{$conf['db']['prefix']}settings` (`modpath`, `name`, `value`, `aid`, `description`) VALUES ('sess', 'sess_time', '3600', '1', 'Время сессии')");
mpqw("INSERT INTO `{$conf['db']['prefix']}settings` (`modpath`, `name`, `value`, `aid`, `description`) VALUES ('sess', 'del_sess', '0', '1', 'Отслеживание сессий')");

?>