<?

mb_internal_encoding("UTF-8");
date_default_timezone_set('Europe/Moscow');
if(basename(dirname(dirname(__FILE__))) == "mpak.cms"){
	$conf['fs']['path'] = "../".(substr($_SERVER['SERVER_NAME'], 0, 4) != 'www.' ? 'www.' : '')."{$_SERVER['SERVER_NAME']}:../mpak.cms";
}else{
	$conf['fs']['path'] = dirname(dirname(__FILE__));
}
$conf['db']['conn'] = null;
$conf['db']['type'] = 'mysql';
$conf['db']['prefix'] = 'mp_';
$conf['db']['host'] = 'localhost';
$conf['db']['login'] = 'mpak.cms';
$conf['db']['name'] = 'mpak.cms';
$conf['db']['pass'] = 'password';

?>