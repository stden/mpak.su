<? die;

echo '<p>'.$sql = "CREATE TABLE `{$conf['db']['prefix']}{$arg['modpath']}_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
mpqw($sql);

?>