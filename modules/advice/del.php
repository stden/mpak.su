<? die;

echo '<p>'.$sql = "DROP TABLE {$conf['db']['prefix']}{$arg['modpath']}_index";
mpqw($sql);

mpqw("DELETE FROM `{$conf['db']['prefix']}settings` WHERE `modpath`='advice'");

?>