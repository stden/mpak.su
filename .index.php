<?

// ----------------------------------------------------------------------
// Жираф cms Content Management System
// Copyright (C) 2007-2010 by the mpak.
// (Link: http://mpak.su)
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) - (Link: http://www.gnu.org/licenses/gpl.html)http://www.gnu.org/licenses/gpl.html
// Original Author of file: Krivoshlykov Evgeniy (mpak) +7 929 1140042
// ----------------------------------------------------------------------


if(!function_exists('mp_require_once')){
	function mp_require_once($link){
		global $conf, $arg, $tpl;
		foreach(explode(':', $conf['fs']['path'], 2) as $k=>$v){
			if (!file_exists($file_name = "$v/$link")) continue;
			include_once($file_name); return;
		}
	}
}

require_once("config/config.php"); # Конфигурация

mp_require_once("config/config.php"); # Конфигурация
mp_require_once("include/mpfunc.php"); # Функции системы

$_GET += mpgt($_SERVER['REQUEST_URI'], $_GET);

if (!isset($index) && file_exists($index = array_shift(explode(':', $conf['fs']['path'], 2)). '/index.php')){
	include($index);
	if($content) die;
}

if(array_search('admin', (array)$_GET['m']))
	mp_require_once("include/func.php"); # Функции таблиц

if(!function_exists('mysql_connect')){ echo "no function mysql"; die; }
$conf['db']['conn'] = @mysql_connect($conf['db']['host'], $conf['db']['login'], $conf['db']['pass']); # Соединение с базой данных
if (strlen($conf['db']['error'] = mysql_error())){
#	echo "Ошибка соединения с базой данных<p>";
}else{
	mysql_select_db($conf['db']['name'], $conf['db']['conn']);
	mpqw("SET NAMES 'utf8'");
} unset($conf['db']['pass']); $conf['db']['sql'] = array();
ini_set('display_errors', 1); error_reporting(E_ALL ^ E_NOTICE);

if ((!array_key_exists('null', $_GET) && !empty($conf['db']['error'])) || !count(mpql(mpqw("SHOW TABLES", 'Проверка работы базы')))){ echo mpct('include/install.php'); die; }

if(array_key_exists('themes', (array)$_GET['m']) && empty($_GET['m']['themes']) && array_key_exists('null', $_GET)){
	if(empty($_GET['theme'])){
		$_GET['theme'] = mpql(mpqw("SELECT value FROM {$conf['db']['prefix']}settings WHERE name=\"theme\""), 0, 'value');
	} $ex = array('css'=>'text/css', 'js'=>'text/javascript', 'swf'=>'application/x-shockwave-flash', 'ico' => 'image/x-icon', '.svg'=>'font/svg+xml', '.tpl'=>'text/html');
	$fn = "themes/{$_GET['theme']}/{$_GET['']}";
	$ext = array_pop(explode('.', $fn));
	header("Content-type: ". ($ex[$ext] ?: "image/$ext"));
	if($ex[$ext]){
		readfile(mpopendir($fn));
	}else{
		echo mprs(mpopendir($fn), $_GET['w'], $_GET['h'], $_GET['c']);
	} die;
}/*elseif(array_key_exists('users', (array)$_GET['m']) && ($_GET['m']['users'] == 'img') && ($_GET['tn'] == 'index') && array_key_exists('null', $_GET)){
	if(!($img = mpql(mpqw($sql = "SELECT img FROM {$conf['db']['prefix']}users WHERE id=". (int)$_GET['id']), 0, 'img')) && !($fn = mpopendir("include/$img"))){
		$img = ($_GET['id'] ? "unknown.png" : $_GET['']);
		$fn = "modules/users/img/". basename($img);
	} echo $img; exit;
	$ex = array('css'=>'text/css', 'js'=>'text/javascript', 'swf'=>'application/x-shockwave-flash', 'ico' => 'image/x-icon', '.svg'=>'font/svg+xml');
	$ext = array_pop(explode('.', $fn));
	header("Content-type: ". ($ex[$ext] ?: "image/$ext"));
	if($ex[$ext]){
		readfile(mpopendir($fn));
	}else{
		echo mprs(mpopendir($fn), $_GET['w'], $_GET['h'], $_GET['c']);
	} die;
}*/


$conf['db']['info'] = 'Загрузка свойств модулей';
$conf['settings'] = array('http_host'=>$_SERVER['HTTP_HOST'])+spisok("SELECT `name`, `value` FROM `{$conf['db']['prefix']}settings`");
if($conf['settings']['users_log']) $conf['event'] = mpqn(mpqw("SELECT * FROM {$conf['db']['prefix']}users_event"), "name");

$conf['settings']['access_array'] = array('0'=>'Запрет', '1'=>'Чтение', '2'=>'Добавл', '3'=>'Запись', '4'=>'Модер', '5'=>'Админ');
if ($conf['settings']['microtime']) $conf['settings']['microtime'] = microtime();

if($conf['settings']['del_sess']){
	$func = create_function('&$val, $key','$val = strtr(stripslashes($val), array("\\\\"=>"&#92;", \'"\'=>"&#34;", "\'"=>"&#39;"));');
	array_walk ($get = $_GET, $func); $post = $_POST;
	if (isset($post['pass'])) $post['pass'] = 'hide';
	if (isset($post['pass2'])) $post['pass2'] = 'hide';
	array_walk ($post, $func); array_walk ($files = $_FILES, $func); array_walk ($server = $_SERVER, $func);
	$request = serialize(array('$_POST'=>$post, '$_GET'=>$get, '$_FILES'=>$files, '$_SERVER'=>$server));
} setlocale (LC_ALL, "Russian"); putenv("LANG=ru_RU");// bindtextdomain("messages", "./locale"); textdomain("messages"); bind_textdomain_codeset('messages', 'CP1251'); //setlocale(LC_ALL, "ru_RU.CP1251")

if (!$gid = mpql(mpqw("SELECT id as gid FROM {$conf['db']['prefix']}users WHERE name='{$conf['settings']['default_usr']}'", 'Получаем свойства пользователя гость'), 0, 'gid')){ # Создаем пользователя в случае если его нет
	mpqw("INSERT INTO {$conf['db']['prefix']}users (name, pass, reg_time, last_time) VALUES ('{$conf['settings']['default_usr']}', 'nopass', ".time().", ".time().")");
	$gid = mysql_insert_id();
}

$sess = mpql(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}sess WHERE `ip`='{$_SERVER['REMOTE_ADDR']}' AND last_time>=".(time()-$conf['settings']['sess_time'])." AND `agent`=\"".mpquot($_SERVER['HTTP_USER_AGENT']). "\" AND (". ($_COOKIE["{$conf['db']['prefix']}sess"] ? "sess=\"". mpquot($_COOKIE["{$conf['db']['prefix']}sess"]). "\"" : "uid=". (int)$gid).") ORDER BY id DESC", 'Получаем свойства текущей сессии'), 0);
if(!$sess){
	$sess = array('uid'=>$gid, 'sess'=>md5("{$_SERVER['REMOTE_ADDR']}:".microtime()), 'ref'=>mpidn(urldecode($_SERVER['HTTP_REFERER'])), 'ip'=>$_SERVER['REMOTE_ADDR'], 'agent'=>$_SERVER['HTTP_USER_AGENT'], 'url'=>$_SERVER['REQUEST_URI']);
	mpqw("INSERT INTO {$conf['db']['prefix']}sess (uid, ref, sess, last_time, ip, agent, url) VALUES ($gid, '{$sess['ref']}', '{$sess['sess']}', ".time().", '{$sess['ip']}', '".mpquot($sess['agent'])."', '".mpquot($sess['url'])."')");
	$sess['id'] = mysql_insert_id();
}

if($_COOKIE["{$conf['db']['prefix']}sess"] != $sess['sess']){
	$sess['sess'] = md5("{$_SERVER['REMOTE_ADDR']}:".microtime());
	setcookie("{$conf['db']['prefix']}sess", $sess['sess'], 0, "/");
}

if ($conf['settings']['del_sess'] && ($conf['settings']['del_sess'] != 3 || $_SERVER['REQUEST_METHOD'] != 'GET' )){
	mpqw("INSERT INTO {$conf['db']['prefix']}sess_post (sid, url, time, method, post) VALUE ({$sess['id']}, '{$_SERVER['QUERY_STRING']}', ".time().", '{$_SERVER['REQUEST_METHOD']}', '$request')", 'Обновляем свойства сессии');
}

//if(!array_key_exists("null", $_GET)){ # Обновление информации о сессии При запррсе ресурса не обязательна
	mpqw("UPDATE {$conf['db']['prefix']}sess SET count_time = count_time+".time()."-last_time, last_time=".time().", ".(isset($_GET['null']) ? 'cnull=cnull' : 'count=count')."+1, sess=\"". mpquot($sess['sess']). "\" WHERE id=". (int)$sess['id']);
//}

if (strlen($_POST['name']) && strlen($_POST['pass']) && $_POST['reg'] == 'Аутентификация' && $uid = mpql(mpqw("SELECT id FROM {$conf['db']['prefix']}users WHERE tid=1 AND name = \"".mpquot($_POST['name'])."\" AND pass='".mphash($_POST['name'], $_POST['pass'])."'", 'Проверка существования пользователя'), 0, 'id')){# Авторизация пользователя
	mpqw($sql = "UPDATE {$conf['db']['prefix']}sess SET uid=".($sess['uid'] = $uid)." WHERE id=". (int)$sess['id']);// echo $sql;
	mpqw("UPDATE {$conf['db']['prefix']}users SET last_time=". time(). " WHERE id=".(int)$uid);
	header("Location: ". $_SERVER['REQUEST_URI']); exit;
}elseif(isset($_GET['logoff'])){ # Если пользователь покидает сайт
	mpqw("UPDATE {$conf['db']['prefix']}sess SET sess = '!". mpquot($sess['sess']). "' WHERE id=". (int)$sess['id'], 'Выход пользователя');
	if(!empty($_SERVER['HTTP_REFERER'])){
		header("Location: ". ($conf['settings']['users_logoff_location'] ?: $_SERVER['HTTP_REFERER'])); exit;
	}// if($conf['settings']['del_sess'] == 0){ # Стираем просроченные сессии
	mpqw($sql = "DELETE FROM {$conf['db']['prefix']}sess WHERE last_time < ".(time() - $conf['settings']['sess_time']), 'Удаление сессий');
	mpqw($sql = "DELETE FROM {$conf['db']['prefix']}sess_post WHERE time < ".(time() - $conf['settings']['sess_time']), 'Удаление данных сессии');
//	}
}

$user = mpql(mpqw("SELECT *, id AS uid, name AS uname FROM {$conf['db']['prefix']}users WHERE id={$sess['uid']}", 'Проверка пользователя'));
list($k, $conf['user']) = each($user);
if($conf['user']['uname'] == $conf['settings']['default_usr']){
	$conf['user']['uid'] = -$sess['id'];
} $conf['settings']['users_uid'] = $conf['user']['uid'];
$conf['db']['info'] = 'Получаем информацию о группах в которые входит пользователь';
$conf['user']['gid'] = spisok("SELECT g.id, g.name FROM {$conf['db']['prefix']}users_grp as g, {$conf['db']['prefix']}users_mem as m WHERE g.id = m.gid AND m.uid = {$sess['uid']}");
$conf['user']['sess'] = $sess;

$content = mpct(mpopendir("include/init.php"), array()); # Установка предварительных переменных

if ($conf['settings']['start_mod'] && !$_GET['m']){
	if (strpos($conf['settings']['start_mod'], 'array://') === 0){
		$_GET = unserialize(substr($conf['settings']['start_mod'], 8));
	}else{
//		header("HTTP/1.1 302 Temporary Redirect");
//		header("Location: {$conf['settings']['start_mod']}");
		$_GET = mpgt($conf['settings']['start_mod']);
	}
}

list($m, $f) = (array)each($_GET['m']); # Отображение меню с выбором раздела для модуля администратора

foreach(mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}modules WHERE enabled = 2", 'Информация о модулях')) as $k=>$v){
	if (array_search($conf['user']['uname'], explode(',', $conf['settings']['admin_usr'])) !== false) $v['access'] = 5; # Права суперпользователя
	$conf['modules'][ $v['folder'] ] = $v;
	$conf['modules'][ $v['folder'] ]['modname'] = (strpos($_SERVER['HTTP_HOST'], "xn--") !== false) ? mb_strtolower($v['name']) : $v['folder'];
	$conf['modules'][ $v['name'] ] = &$conf['modules'][ $v['folder'] ];
	$conf['modules'][ mb_strtolower($v['name']) ] = &$conf['modules'][ $v['folder'] ];
	$conf['modules'][ $v['id'] ] = &$conf['modules'][ $v['folder'] ];
}


foreach((array)mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}modules_gaccess", 'Права доступа группы к модулю')) as $k=>$v){
	if ( $conf['user']['gid'][ $v['gid'] ] && array_search($conf['user']['uname'], explode(',', $conf['settings']['admin_usr'])) === false)
		$conf['modules'][ $v['mid'] ]['access'] = $v['access'];
}
foreach((array)mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}modules_uaccess ORDER BY uid", 'Права доступа пользователя к модулю')) as $k=>$v){
	if ($conf['user']['uid'] == $v['uid'] && array_search($conf['user']['uname'], explode(',', $conf['settings']['admin_usr'])) === false)
		$conf['modules'][ $v['mid'] ]['access'] = $v['access'];
}

if (!function_exists('bcont')){
	function bcont($bid = null){# Загружаем список блоков и прав доступа
		global $theme, $conf;
		$conf['db']['info'] = "Выборка шаблонов блоков";
		$shablon = spisok("SELECT id, shablon FROM {$conf['db']['prefix']}blocks_shablon");

		foreach($_GET['m'] as $k=>$v){
			if($conf['modules'][ $k ]['id']){
				$mid[ $conf['modules'][ $k ]['id'] ] = $v;
				$sid[] = "r.mid=". (int)$conf['modules'][ $k ]['id']. " AND (r.fn=\"". mpquot($v ?: "index"). "\" OR r.fn=\"\")";
			}
		} $sid[] = "r.mid=0";
		$sid[] = "r.mid=". (int)$conf['modules']['blocks']['id']. " AND r.fn=\"index\"";
		
		$regions = mpqn(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}blocks_reg AS r WHERE r.reg_id=0 AND (". implode(') OR (', $sid). ")"));

		$blocks = mpql(mpqw($sql = "SELECT b.*, r.reg_id FROM {$conf['db']['prefix']}blocks_reg AS r INNER JOIN {$conf['db']['prefix']}blocks AS b ON r.id=b.rid WHERE b.enabled=1 AND (b.theme = \"". mpquot($conf['settings']['theme']). "\" OR b.theme='*' OR b.theme='' OR (SUBSTR(b.theme, 1, 1)='!' AND b.theme<>\"!". mpquot($conf['settings']['theme']). "\")) ". ($bid ? " AND b.id=". (int)$bid : " AND ((r.id IN (". implode(',', array_keys($regions)). ") OR r.reg_id IN (". implode(',', array_keys($regions)). ")) AND (". implode(') OR (', $sid). "))"). " ORDER BY b.`orderby`", 'Информация о блоках'));// mpre($sql);

		$gt = mpgt(urldecode(array_pop(explode("/{$_SERVER['HTTP_HOST']}", $_SERVER['HTTP_REFERER']))));
		$uid = array_key_exists('blocks', $_GET['m']) ? $gt['id'] : $_GET['id'];
		$uid = (array_intersect_key(array($conf['modules']['users']['folder']=>1, $conf['modules']['users']['modname']=>2), (array_key_exists('blocks', $_GET['m']) ? (array)$gt['m'] : array())+$_GET['m']) && $uid) ? $uid : $conf['user']['uid'];

		foreach($blocks as $k=>$v){
			$conf['blocks']['info'][$v['id']] = $v;
			if(($v['access'] < 0)){
				$bac = (int)$conf['modules'][array_shift(explode('/', $v['file']))]['access'];
				$conf['blocks']['info'][ $v['id'] ]['access'] = $bac;
			}
		}
		foreach(mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}blocks_gaccess ORDER BY id", 'Права доступа группы к блоку')) as $k=>$v)
			if ($conf['user']['gid'][ $v['gid'] ]) $conf['blocks']['info'][ $v['bid'] ]['access'] = $v['access'];
		foreach(mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}blocks_uaccess ORDER BY id", 'Права доступа пользователя к блоку')) as $k=>$v)
			if ($conf['user']['uid'] == $v['uid'] || (!$v['uid'] && ($conf['user']['uid'] == $uid)))
				$conf['blocks']['info'][ $v['bid'] ]['access'] = $v['access'];

		foreach($blocks as $k=>$v){
			$conf['db']['info'] = "Блок '{$conf['blocks']['info'][ $v['id'] ]['name']}'";
			$mod = $conf['modules'][ $modpath = basename(dirname(dirname($v['file']))) ];
			$modname = $mod['modname'];
			if ($conf['blocks']['info'][ $v['id'] ]['access'] && strlen($cb = mpeval("modules/{$v['file']}", $arg = array('blocknum'=>$v['id'], 'modpath'=>$modpath, 'modname'=>$modname, 'fn'=>basename(array_shift(explode('.', $v['file']))), 'uid'=>$uid, 'access'=>$conf['blocks']['info'][ $v['id'] ]['access']) ))){
				if($bid){ $result = $cb; }else{
					if (!is_numeric($v['shablon']) && file_exists($file_name = mpopendir("themes/{$conf['settings']['theme']}/". ($v['shablon'] ?: "block.html")))){
						$shablon[ $v['shablon'] ] = file_get_contents($file_name);
					}
					$cb = str_replace('<!-- [block:content] -->', $cb, $shablon[ $v['shablon'] ]);
					$cb = str_replace('<!-- [block:id] -->', $v['id'], $cb);
					$cb = str_replace('<!-- [block:modpath] -->', $arg['modpath'], $cb);
					$cb = str_replace('<!-- [block:fn] -->', $arg['fn'], $cb);
					$result["<!-- [blocks:{$v['rid']}] -->"] .= str_replace('<!-- [block:title] -->', $v['name'], $cb);
					if($v['reg_id']) $result["<!-- [blocks:{$v['reg_id']}] -->"] .= str_replace('<!-- [block:title] -->', $v['name'], $cb);
				}
			}
		} return $result;
	}
}

if (!function_exists('mcont')){
	function mcont($content){ # Загрузка содержимого модуля
		global $conf, $arg;
		foreach($_GET['m'] as $k=>$v){ $k = urldecode($k);
			$mod = $conf['modules'][ $k ];
			ini_set("include_path" ,mpopendir("modules/{$mod['folder']}"). ":./modules/{$mod['folder']}:". ini_get("include_path"));
			if($conf['settings']['modules_title']){
				$conf['settings']['title'] = $conf['modules'][ $k ]['name']. ' : '. $conf['settings']['title'];
			}

			$v = $v != 'del' && $v != 'init' && $v != 'sql' && strlen($v) ? $v : 'index';
			if ( ((strpos($v, 'admin') === 0) ? $conf['modules'][$k]['access'] >= 4 : $conf['modules'][$k]['access'] >= 1) ){
				$conf['db']['info'] = "Модуль '". ($name = $mod['name']). "'";

				if(($glob = glob(mpopendir("modules/{$mod['folder']}"). "/*{$v}.*php"))
					|| ($glob = glob("modules/{$mod['folder']}/*{$v}.*php"))){
					$glob = basename(array_pop($glob));
					$g = explode(".", $glob);
					$v = array_shift($g);
				}

				$fe = ((strpos($_SERVER['HTTP_HOST'], "xn--") !== false) && (count($g) > 1)) ? array_shift($g) : $v;
				$arg = array('modpath'=>$mod['folder'], 'modname'=>$mod['modname'], 'fn'=>$v, "fe"=>$fe, 'access'=>$mod['access']);

				if($glob){
					$content .= mpct("modules/{$mod['folder']}/$glob", $arg);
				}elseif(($tmp = mpct("modules/{$mod['folder']}/". ($fn = $v). ".php", $arg)) === false){
					$content .= mpct("modules/{$mod['folder']}/". ($fn = "default"). ".php", $arg);
				}else{
					$content .= $tmp;
				}

				if (mpopendir("modules/{$mod['folder']}/$v.tpl")){# Проверяем модуль на файл шаблона
					ob_start();
					mp_require_once("modules/{$mod['folder']}/$v.tpl");
					$content .= ob_get_contents();
					ob_end_clean();
				}else{# Проверяем модуль на файл шаблона
					ob_start();
					mp_require_once("modules/{$mod['folder']}/$fn.tpl");
					$content .= ob_get_contents();
					ob_end_clean();
				}

			}else{
				if (file_exists(mpopendir("modules/{$mod['folder']}/deny.php"))){
					$content = mpct("modules/{$mod['folder']}/deny.php", $conf['arg'] = array('modpath'=>$mod['folder']));
				}else{
					header('HTTP/1.0 404 Unauthorized');
					header("Location: /themes:404");
//					$content = "<div style=\"text-align:center; margin: 100px 10px;\">Недостаточно прав доступа</div>";
				}
			}
		}
		return $content;
	}
}

$m = array_shift(array_keys($_GET['m']));
$f = array_shift(array_values($_GET['m']));

if (empty($f)) $f = 'index';
if (!empty($conf['settings']["theme/*:$f"])) $conf['settings']['theme'] = $conf['settings']["theme/*:$f"];
if (!empty($conf['settings']["theme/$m:*"])) $conf['settings']['theme'] = $conf['settings']["theme/$m:*"];
if (!empty($conf['settings']["theme/$m:$f"])) $conf['settings']['theme'] = $conf['settings']["theme/$m:$f"];
if ((strpos($f, "admin") === 0) && $conf['settings']["theme/*:admin"])
	$conf['settings']['theme'] = $conf['settings']["theme/*:admin"];

if(isset($_GET['theme']) && $_GET['theme'] != $conf['user']['sess']['theme']){
	$conf['user']['sess']['theme'] = $conf['settings']['theme'] = basename($_GET['theme']);
//	mpqw($sql = "UPDATE {$conf['db']['prefix']}sess SET theme='". mpquot($conf['user']['sess']['theme'] = $conf['settings']['theme'] = basename($_GET['theme'])). "' WHERE id=". (int)$conf['user']['sess']['id']);
}elseif($conf['user']['sess']['theme']){
	$conf['settings']['theme'] = $conf['user']['sess']['theme'];
}

if (is_numeric($conf['settings']['theme'])){
	$sql = "SELECT b.theme as btheme, t.* FROM mp_themes as t LEFT JOIN mp_themes_blk as b ON t.id=b.tid WHERE t.id=".(int)$conf['settings']['theme']." ORDER BY b.sort";
	$theme = mpql(mpqw($sql, 'Запрос темы'), 0);
	$tc = $theme['theme'];
}else{
	$tc = file_get_contents(mpopendir("themes/{$conf['settings']['theme']}/index.html"));
}

if (!array_key_exists('null', $_GET) || !empty($_GET['m']['users'])){
	if (isset($_GET['m']['sqlanaliz'])) $zblocks = bcont();
} $content .= mcont($content);
if (!array_key_exists('null', $_GET) || !empty($_GET['m']['users'])){
	if (!isset($_GET['m']['sqlanaliz'])) $zblocks = bcont();
	if (strpos($tc, '<!-- [modules] -->')){
		if(!array_key_exists('null', $_GET)){
			$content = str_replace('<!-- [modules] -->', $content, $tc);
		} $content = strtr($content, (array)$zblocks);
	}
}

if ($conf['settings']['microtime']){
	$conf['settings']['microtime'] = (substr(microtime(), strpos(microtime(), ' ')) - substr($conf['settings']['microtime'], strpos($conf['settings']['microtime'], ' ')) + microtime() - $conf['settings']['microtime']);
}

$aid = spisok("SELECT id, aid FROM {$conf['db']['prefix']}settings");
foreach($conf['settings'] as $k=>$v){
	$content = str_replace("<!-- [settings:$k] -->", $v, $content);
}
/*if ($conf['settings']['settints_vcomments']){
	$content = str_replace('<!--', '&lt;!--', $content);
	$content = str_replace('-->', '--&gt;', $content);
}*/
/*if(preg_match_all("/search=(.*)/", $_SERVER['HTTP_REFERER'], $out, PREG_PATTERN_ORDER) && empty($_POST) && (array_search('admin', $_GET['m']) == null)){
	$search = urldecode($out[1][0]);
	$content = str_ireplace(" $search ", "<span style=background:yellow;>{$search}</span>", $content);
}*/
echo $content;

?>