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
//if(array_key_exists('blocks', $_GET['m']) && array_key_exists('null', $_GET) && ($_GET['id'] == $arg['blocknum']) && $_POST){};

$online = mpql(mpqw($sql = "SELECT SQL_CALC_FOUND_ROWS u.*, s.id AS sid, s.agent FROM {$conf['db']['prefix']}sess AS s LEFT JOIN {$conf['db']['prefix']}users AS u ON s.uid=u.id WHERE s.last_time > ". (time()-$conf['settings']['sess_time']). " AND CHAR_LENGTH(sess)=32 ORDER BY s.last_time DESC LIMIT 42"));

$count = mpql(mpqw("SELECT FOUND_ROWS() AS count"), 0, 'count');

$logo = array(
	"yahoo.png"=>"Yahoo",
 	"google.png"=>"Googlebot",
	"rambler.png"=>"StackRambler",
	"yandex.png"=>"Yandex",
	"msnbot.png"=>"msnbot",
	"bing.png"=>"bing",
	"cctld.ru.png"=>"cctld.ru/bot",
	"adsbot.png"=>"adsbot",
	"archive.org.png"=>"archive.org_bot",
	"begun.png"=>"Begun",
	"bot.png"=>"bot",
);

function strpos_array($haystack, $needles) {
    if ( is_array($needles) ) {
        foreach ($needles as $img=>$str) {
            if ( is_array($str) ) {
                $pos = strpos_array($haystack, $str);
            } else {
                $pos = strpos($haystack, $str);
            }
            if ($pos !== FALSE) {
                return $img;
            }
        }
    } else {
        return strpos($haystack, $needles);
    }
}

foreach($online as $k=>$v){
	if($img = strpos_array($v['agent'], $logo)){
		$online[$k]['image'] = "/{$arg['modpath']}:img/w:40/h:40/null/{$img}";
		$online[$k]['bot'] = 1;
	}else{
		$online[$k]['image'] = "/{$arg['modpath']}:img/". ($v['name'] == $conf['settings']['default_usr'] ? "-1" : $v['id']). "/tn:index/w:40/h:40/c:1/null/img.jpg";
	}
} //echo strpos_array("Googlebot-Image/1.0", $logo);

?>
<div style="overflow:hidden;">
	<div style="clear:both;">на сайте <b><?=$count?></b> <?=mpfm($count, 'посетитель', 'посетителя', 'посетителей')?></div>
	<? foreach($online as $k=>$v): ?>
		<div style="float:left; margin:1px; border:1px solid #ddd; position:relative;">
			<a href="/<?=$arg['modname']?>/<?=($v['name'] != $conf['settings']['default_usr'] ? $v['id'] : "-{$v['sid']}")?>">
				<? if($v['bot']): ?>
					<div style="position:absolute; top:1px; right:1px; opacity:0.5;">
						<img src="/<?=$arg['modname']?>:img/w:15/h:15/null/bot.png">
					</div>
				<? endif; ?>
				<img src="<?=$v['image']?>" title="<?=$v['bot'] ? $v['agent'] : $v['name']. ($v['name'] != $conf['settings']['default_usr'] ? "" : "-{$v['sid']}")?>">
			</a>
		</div>
	<? endforeach; ?>
	<? if($count > count($online)): ?>
	<? endif; ?>
</div>
