<? die; # Нуль

if ((int)$arg['confnum']){
/*	$param = unserialize(mpql(mpqw("SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['confnum']}"), 0, 'param'));
	if ($_POST) mpqw("UPDATE {$conf['db']['prefix']}blocks SET param = '".serialize($param = $_POST['param'])."' WHERE id = {$arg['confnum']}");

echo <<<EOF
	<form method="post">
		<input type="text" name="param" value="$param"> <input type="submit" value="Сохранить">
	</form>
EOF;*/

	return;
}//$param = unserialize(mpql(mpqw("SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['blocknum']}"), 0, 'param'));
//$uid = $_GET['id'] && array_key_exists('users', $_GET['m']) ? $_GET['id'] : $conf['user']['id'];
//if(array_key_exists('blocks', $_GET['m']) && array_key_exists('null', $_GET) && ($_GET['id'] == $arg['blocknum']) && $_POST){};

//$dat = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_{$arg['fn']} LIMIT 10")); //$dat

$get = mpgt($_SERVER['REQUEST_URI'], $_GET);

$uri = "/"; $m = array();
foreach($get['m']+array('sqlanaliz'=>'admin') as $k=>$v){
	$m[] = "m[$k]". ($v ? "=$v" : "");
} $uri .= "?". implode("&", $m);
foreach(array_diff_key($get, array('m'=>'')) as $k=>$v){
	$uri .= "&$k=$v";
}// echo $uri;

?>
<div style="white-space:nowrap;">
	<a href="<?=$uri?>"><?=$uri?></a>
</div>