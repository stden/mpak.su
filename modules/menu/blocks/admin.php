<? die; # Верхнее

if ((int)$arg['confnum']){
	$block = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['confnum']}"), 0);
	$param = unserialize(mpql(mpqw("SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['confnum']}"), 0, 'param'));

	if(substr($block['theme'], 0, 1) == '!'){
		$block['theme'] = mpql(mpqw("SELECT value FROM {$conf['db']['prefix']}settings WHERE name=\"theme\""), 0, 'value');
	}

	if(!empty($_POST)) $param = $_POST;
	echo "<div style=\"margin:10px;\">Текущее меню: <b>{$regions[$param]}</b>";
	echo "<form method=\"post\"><select name=\"menu\"><option value=''></option>";
	foreach(spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_region") as $k=>$v){
		echo "<option value=\"$k\"".($k == $param['menu'] ? " selected=\"selected\"" : '').">$v</option>";
	}
	echo "</select><br />";
	echo "<br /><select name=\"tpl\"><option value=''></option>";
	foreach(mpreaddir($fn = "themes/{$block['theme']}", 1) as $k=>$v){ if(substr($v, -4) != '.tpl') continue;
		echo "<option value=\"$v\"".($v == $param['tpl'] ? " selected=\"selected\"" : '').">$v</option>";
	}
	echo "</select><br /><br /><input type=\"submit\" value=\"Изменить\"></form></div>";

	mpqw($sql = "UPDATE {$conf['db']['prefix']}blocks SET param = '".serialize($param)."' WHERE id = {$arg['confnum']}");
	return;
}
$param = unserialize(mpql(mpqw($sql = "SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['blocknum']}"), 0, 'param'));

$menu = mpqn(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']} WHERE rid=". (int)(is_numeric($param) ? $param : $param['menu'])." ORDER BY orderby"), 'pid', 'id');

if($param['tpl']){ include mpopendir("themes/{$conf['settings']['theme']}/{$param['tpl']}"); return; }

if(strpos($_SERVER['REQUEST_URI'], '/admin') === 0){
	$line = array_shift($m = $menu[0]);
	header("Location: ". $line['link']); 
}

?>
<script>
	$(function(){
		$("#admin_menu a[href='<?=$_SERVER['REQUEST_URI']?>']").addClass("active");
	});
</script>
<div id="admin_menu">
	<? foreach($menu[0] as $v): ?>
		&nbsp;<a href="<?=$v['link']?>"><?=$v['name']?></a>
	<? endforeach; ?>
	<a class="out" href="/?logoff">Выход</a>
	<a class="out" href="/">На сайт →</a>
</div>