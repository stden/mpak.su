<? die;

if($_GET['id'] > 0){
	$tn = array(''=>'');
	$sql = "SELECT img FROM {$conf['db']['prefix']}{$arg['modpath']}{$tn[$_GET['tn']]} WHERE id=".(int)$_GET['id'];
	$file_name = mpopendir("include/". ($fn = mpql(mpqw($sql), 0, 'img')));
	header ("Content-type: image/". array_pop(explode('.', $file_name)));
	echo mprs($fn ? $file_name : mpopendir("modules/{$arg['modpath']}/img/unknown.png"), $_GET['w'], $_GET['h'], $_GET['c']);
}elseif(file_exists($fn = mpopendir("modules/{$arg['modpath']}/img/". basename($_GET[''])))){
	header ("Content-type: image/". array_pop(explode('.', $file_name)));
	echo mprs($fn, $_GET['w'], $_GET['h'], $_GET['c']);
}elseif(($guest = mpql(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']} WHERE name=\"". mpquot($conf['settings']['default_usr']). "\""), 0)) && $guest['img']){
	$file_name = mpopendir("include/". $guest['img']);
	header ("Content-type: image/". array_pop(explode('.', $file_name)));
	echo mprs($file_name, $_GET['w'], $_GET['h'], $_GET['c']);
}elseif($fn = mpopendir("modules/{$arg['modpath']}/img/unknown.png")){
	header ("Content-type: image/". array_pop(explode('.', $fn)));
	echo mprs($fn, $_GET['w'], $_GET['h'], $_GET['c']);
}

?>