<? die;

if($_GET['anket_id']){
	$conf['tpl']['anket'] = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_anket WHERE id=". (int)$_GET['anket_id']), 0);
	$conf['index'] = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_index WHERE id=". (int)$conf['tpl']['anket']['index_id']), 0);
	if($conf['index']['uid'] == $conf['user']['uid']){
		$conf['tpl']['result'] = mpqn(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_result WHERE anket_id=". (int)$conf['tpl']['anket']['id']), 'vopros_id', 'variant_id');//	mpre($conf['tpl']['result']);
	}else{
		header("Location: /{$arg['modpath']}/{$conf['index']['id']}");
	} $_GET['id'] = $conf['tpl']['anket']['index_id'];
}

if($_GET['id']){
	$conf['tpl']['index'] = mpqn(mpqw("SELECT id.*, u.name AS uname FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}users AS u ON id.uid=u.id WHERE id.id=". (int)$_GET['id']));

	$conf['tpl']['type'] = mpqn(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_type"));

	$conf['tpl']['vopros'] = mpqn(mpqw("SELECT v.id AS vid, v.* FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_vopros AS v ON id.id=v.index_id WHERE id.id=". (int)$_GET['id']. " ORDER BY v.type_id, v.sort, v.id"), 'type_id', 'vid');

	$conf['tpl']['variant'] = mpqn(mpqw("SELECT vt.* FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_vopros AS v ON id.id=v.index_id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_variant AS vt ON v.id=vt.vopros_id WHERE id.id=". (int)$_GET['id']), 'vopros_id', 'id');

	foreach($conf['tpl']['vopros'] as $t){
		foreach($t as $v){
			if(!empty($v['tn'])){
				if(array_search($v['type'], array("radio", "select")) !== false){
					$conf['tpl']['variant'][ $v['id'] ] = array("0"=>array("name"=>""))+mpqn(mpqw("SELECT * FROM ". mpquot($v['tn']). " ORDER BY name"));
				}else if($v['type'] == "file"){
					$conf['tpl']['variant'][ $v['id'] ] = mpqn(mpqw("SELECT * FROM ". mpquot($v['tn']). " WHERE uid=". (int)$conf['user']['uid']. " AND ". mpquot($v['alias']). "=0 ORDER BY id"));
				}
			}
		}
	}// mpre($conf['tpl']['variant']);

	if($_POST && ($anket = $conf['tpl']['index'][ $_GET['id'] ])){
		if(array_key_exists("null", $_GET) && ($vopros = $conf['tpl']['vopros'][0][ $_POST['vopros_id'] ])){
			$insert_id = mpfdk($vopros['tn'], null, array("time"=>time(), "uid"=>$conf['user']['uid']));
			if($fn = mpfn($vopros['tn'], "file", $insert_id, $_POST['vopros_id'], array('*'=>'*'), true)){
				mpqw("UPDATE ". mpquot($vopros['tn']). " SET img=\"". mpquot($fn). "\" WHERE id=". (int)$insert_id);
			} exit(json_encode(array("id"=>$insert_id, "tn"=>explode("_", $vopros['tn'], 3))));
		}elseif($anket['captcha'] && ($_COOKIE['captcha_keystring'] != md5($_POST['captcha']))){
			$tpl['captcha'] = "false";
		}else{
			mpqw("INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_anket SET time=". time(). ", index_id=". (int)$_GET['id']. ", uid=". (int)$conf['user']['uid']);
			if($conf['tpl']['anket_id'] = mysql_insert_id()){
				foreach($conf['tpl']['vopros'] as $type_id=>$vopros){
					foreach($vopros as $vopros_id=>$v){
						if(($v['type'] == 'check') && !empty($_POST[ $vopros_id ])){
							foreach($_POST[ $vopros_id ] as $variant_id=>$val){
								$sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$conf['tpl']['anket_id']. ", vopros_id=". (int)$vopros_id. ", variant_id=". (int)$variant_id;
								mpqw($sql);
							}
						}elseif($_POST[$vopros_id] && (($v['type'] == 'text') || ($v['type'] == 'textarea'))){
							$sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$conf['tpl']['anket_id']. ", vopros_id=". (int)$vopros_id. ", val=\"". mpquot($_POST[$vopros_id]). "\"";
							mpqw($sql);
							$fds[ $v['alias'] ] = $_POST[$vopros_id];
						}elseif(($v['type'] == 'file')){
							if($v['tn']){ # Отдельная таблица для изображений
								$fds_img[] = $vopros_id;
							}else{ # Файл в связанной таблице
								mpqw($sql = "INSERT INTO ". ($tn = "{$conf['db']['prefix']}{$arg['modpath']}_result"). " SET anket_id=". (int)$conf['tpl']['anket_id']. ", vopros_id=". (int)$vopros_id);
								if(($fn = mpfn($tn, 'file', $insert_id, $vopros_id, array("*"=>"*")))){
									mpqw($sql = "UPDATE $tn SET file=\"". mpquot($fn). "\" WHERE id=". (int)$insert_id);
									$fds[ $v['alias'] ] = $fn;
								}
							}
						}elseif((int)$_POST[ $vopros_id ]){
							$sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$conf['tpl']['anket_id']. ", vopros_id=". (int)$vopros_id. ", variant_id=". (int)$_POST[$vopros_id];
							$fds[ $v['alias'] ] = (int)$_POST[$vopros_id];
							mpqw($sql);
						} $res[] = "\n#{$v['id']} {$v['name']} : ". $_POST[$vopros_id];
					}
				} mpevent("Заполнение формы", $_SERVER['REQUERT_URI'], $anket['uid'], "/{$arg['modpath']}/anket_id:{$conf['tpl']['anket_id']}", implode("<br />", $res), $_POST);
				if($fds){
					$mysql_inset_id = mpfdk($anket['tn'], null, array("time"=>time(), "uid"=>$conf['user']['uid'])+(array)$fds);
					foreach($fds_img as $v){
						$vopros = $conf['tpl']['vopros'][0][ $v ];
						mpqw("UPDATE ". mpquot($vopros['tn']). " SET ". mpquot($vopros['alias']). "=". (int)$mysql_inset_id. " WHERE uid=". (int)$conf['user']['uid']. " AND ". mpquot($vopros['alias']). "=0");
					}
				}
			}
		}
	}else{
		mpevent("Просмотр формы", $_SERVER['REQUERT_URI'], $anket['uid']);
	}
}else{
	$conf['tpl']['index'] = mpql(mpqw("SELECT o.*, u.name AS uname FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS o LEFT JOIN {$conf['db']['prefix']}users AS u ON o.uid=u.id ORDER BY o.id DESC"));
}

?>