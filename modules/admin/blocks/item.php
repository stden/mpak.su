<? die; # Нуль

if ((int)$arg['confnum']){
	$param = unserialize(mpql(mpqw("SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['confnum']}"), 0, 'param'));
	if ($_POST){
		if($_POST['type']){
			$param = array($_POST['fn']=>array($_POST['type']=>$_POST['val'])+(array)$param[$_POST['fn']])+(array)$param;
		}else{
			$param = array($_POST['param']=>$_POST['val'])+(array)$param;
		}
		mpqw("UPDATE {$conf['db']['prefix']}blocks SET param = '".serialize($param)."' WHERE id = {$arg['confnum']}");
	} if(array_key_exists("null", $_GET)) exit;

	$tab = array_keys(mpqn(mpqw("SHOW TABLES"), 'Tables_in_c0e1bd4510d1ed2'));
	$klesh = array(
/*		"Количество символов"=>0,
		"Курс доллара"=>30,
		"Список"=>array(
			1=>"Одын",
			2=>"Два",
		),
		"Город"=>spisok("SELECT id, name FROM {$conf['db']['prefix']}users_sity ORDER BY name"),*/
		"Таблица"=>array(""=>"")+array_combine($tab, $tab),
//		"Город"=>spisok("SELECT id, name FROM {$conf['db']['prefix']}users_sity ORDER BY name"),*/
	);
	if($param["Таблица"]){
		$fn = mpqn(mpqw("SHOW COLUMNS FROM ". mpquot($param["Таблица"])), "Field");
	} $type = array(""=>"", "hide"=>"Скрыто","text"=>"Текст","textarea"=>"Поле", "wysiwyg"=>"Редактор","sort"=>"Сортировка","img"=>"Изображение","file"=>"Файл");
?>
		<!-- Настройки блока -->
	<script src="/include/jquery/my/jquery.klesh.select.js"></script>
	<script>
		$(function(){
			<? foreach($klesh as $k=>$v): ?>
				<? if(gettype($v) == 'array'): ?>
					$(".klesh_<?=strtr(md5($k), array("="=>''))?>").klesh("/?m[blocks]=admin&r=mp_blocks&null&conf=<?=$arg['confnum']?>", function(){
					}, <?=json_encode($v)?>);
				<? else: ?>
					$(".klesh_<?=strtr(md5($k), array("="=>''))?>").klesh("/?m[blocks]=admin&r=mp_blocks&null&conf=<?=$arg['confnum']?>");
				<? endif; ?>
			<? endforeach; ?>
		});
	</script>
	<div style="margin-top:10px;">
		<? foreach($klesh as $k=>$v): ?>
			<div style="overflow:hidden;">
				<div style="width:200px; float:left; padding:5px; text-align:right; font-weight:bold;"><?=$k?> :</div>
				<? if(gettype($v) == 'array'): ?>
					<div class="klesh_<?=strtr(md5($k), array("="=>''))?>" param="<?=$k?>"><?=$v[ $param[$k] ]?></div>
				<? else: ?>
					<div class="klesh_<?=strtr(md5($k), array("="=>''))?>" param="<?=$k?>"><?=($param[$k] ?: $v)?></div>
				<? endif; ?>
			</div>
		<? endforeach; ?>
	</div>
	<style>
		.param > div > span {display:inline-block; width:200px;}
		.param > div > span:first-child {text-align:right; padding-right:10px;}
	</style>
	<div class="param">
		<script>
			$(function(){
				$(".klesh[type=type]").klesh("/?m[blocks]=admin&r=mp_blocks&null&conf=<?=$arg['confnum']?>", function(){
				}, <?=json_encode($type)?>)
				$(".klesh[type=name]").klesh("/?m[blocks]=admin&r=mp_blocks&null&conf=<?=$arg['confnum']?>");
			});
		</script>
		<? foreach($fn as $k=>$v): ?>
			<div>
				<span><?=$k?></span>
				<span><div class="klesh" fn="<?=$k?>" type="name"><?=$param[ $k ]["name"]?></div></span>
				<span><div class="klesh" fn="<?=$k?>" type="type"><?=$type[ $param[ $k ]["type"] ]?></div></span>
			</div>
		<? endforeach; ?>
	</div>
<? return;

} $param = unserialize(mpql(mpqw("SELECT param FROM {$conf['db']['prefix']}blocks WHERE id = {$arg['blocknum']}"), 0, 'param'));
//$uid = $_GET['id'] && array_key_exists('users', $_GET['m']) ? $_GET['id'] : $conf['user']['id'];
/*if(array_key_exists('blocks', $_GET['m']) && array_key_exists('null', $_GET) && ($_GET['id'] == $arg['blocknum']) && $_POST){
	mpre($_POST); exit();
};*/

$item = mpql(mpqw("SELECT * FROM ". mpquot($param["Таблица"]). " WHERE id=". (int)$_GET['id']), 0);

$get = mpgt($_SERVER['REQUEST_URI']);
$m = $get['m'];

$modpath = array_pop(array_flip($m));
$fn = array_pop($m);

?>
<style>
	.items_<?=$arg['blocknum']?> li span {display:inline-block;}
	.items_<?=$arg['blocknum']?> li div {margin-bottom:3px;}
	.items_<?=$arg['blocknum']?> li span:first-child {width:100px;}
	.items_<?=$arg['blocknum']?> li span:last-child {width:80%;}
</style>
<form method="post">
	<ul class="items_<?=$arg['blocknum']?>">
		<li>
			<? foreach($item as $k=>$v): if($param[ $k ]["type"] == "hide") continue; ?>
				<div>
					<span><?=(($n = $param[ $k ]["name"]) ? "<span title='$k'>$n</span>" : "<span style=color:gray>$k</span>")?></span>
					<span>
						<? if($param[ $k ]["type"] == "textarea"): ?>
							<textarea name="<?=$k?>" style="width:100%;"><?=$v?></textarea>
						<? elseif($param[ $k ]["type"] == "img"): ?>
							<div><img src="/<?=$modpath?>"></div>
						<? elseif($param[ $k ]["type"] == "wysiwyg"): ?>
							<?=mpwysiwyg($k, $v)?>
						<? else: ?>
							<input type="text" value="<?=$v?>" style="width:100%;">
						<? endif; ?>
					</span>
				</div>
			<? endforeach; ?>
		</li>
	</ul>
	<div style="text-align:right; padding:0 50px;"><input type="submit"></div>
</form>