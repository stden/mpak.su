<? die;

echo '<p>'.$sql = "DROP Table {$conf['db']['prefix']}{$arg['modpath']}";
mpqw($sql);

echo '<p>'.$sql = "DROP Table {$conf['db']['prefix']}{$arg['modpath']}_desc";
mpqw($sql);

?>