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

$diff = array('id', 'name', 'pass', 'param', 'flush', 'refer', 'tid', 'img', 'ref', 'reg_time', 'last_time', 'uid', 'sess', 'gid', 'email', 'uname');

if(array_key_exists('blocks', $_GET['m']) && array_key_exists('null', $_GET) && ($_GET['id'] == $arg['blocknum']) && ($conf['user']['uid'] == $arg['uid']) && $_POST){
	mpqw("UPDATE {$conf['db']['prefix']}users SET ". mpquot($_POST['f']). "=\"". mpquot($_POST['val']). "\" WHERE id=". (int)$conf['user']['uid']);
	exit;
};

foreach($conf['user'] as $k=>$v){
	if(substr($k, -3) == '_id'){
		$conf['tpl']['select'][$k] = spisok("SELECT id, name FROM {$conf['db']['prefix']}users_". substr($k, 0, strlen($k)-3). " ORDER BY name");
	}
} $user = mpql(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}users WHERE id=". (int)$arg['uid']), 0);

?>
<? if($conf['user']['uid'] == $arg['uid']): ?>
	<script src="/include/jquery/my/jquery.klesh.select.js"></script>
	<script>
		$(function(){
			$(".klesh_<?=$arg['blocknum']?>").klesh("/blocks/<?=$arg['blocknum']?>/null");
			<? foreach((array)$conf['tpl']['select'] as $k=>$v): ?>
				$(".klesh_<?=$k?>_<?=$arg['blocknum']?>[f='<?=$k?>']").klesh("/blocks/<?=$arg['blocknum']?>/null", function(){
				}, <?=json_encode($v)?>);
			<? endforeach; ?>
		});
	</script>
<? endif; ?>
<style>
	#f_<?=$arg['blocknum']?> > div {padding-top:5px;}
	#f_<?=$arg['blocknum']?> > div > div {white-space:nowrap;}
	#f_<?=$arg['blocknum']?> > div > div:first-child {float:left; width:150px;}
</style>
<div style="overflow:hidden;">
	<div style="float:left; width:200px; text-align:center;">
		<img src="/users:img/<?=$user['id']?>/tn:index/w:200/h:200/null/img.jpg">
		<h3 style="text-align:center;"><?=$user['name']?></h3>
		<div><a href="/<?=$conf['modules']['messages']['modname']?>:письмо/uid:<?=$user['id']?>">Написать личное сообщение</a></div>
	</div>
	<? if($user['name'] != $conf['settings']['default_usr']): ?>
		<div id="f_<?=$arg['blocknum']?>" style="margin-left:210px;">
			<? foreach(array_diff_key($user, array_flip($diff)) as $k=>$v): ?>
				<div style="overflow:hidden;">
					<div><?=($conf['settings'][$f = "users_field_$k"] ?: $f)?>:</div>
					<? if(substr($k, -3) == '_id'): ?>
						<div f="<?=$k?>" class="klesh_<?=$k?>_<?=$arg['blocknum']?>"><?=$conf['tpl']['select'][ $k ][ $v ]?></div>
					<? else: ?>
						<div f="<?=$k?>" class="klesh_<?=$arg['blocknum']?>"><?=$user[$k]?></div>
					<? endif; ?>
				</div>
			<? endforeach; ?>
		</div>
	<? endif; ?>
</div>