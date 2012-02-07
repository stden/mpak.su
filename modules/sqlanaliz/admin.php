<? die;
// ----------------------------------------------------------------------
// mpak Content Management System
// Copyright (C) 2007 by the mpak.
// (Link: http://mp.s86.ru)
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) - (Link: http://www.gnu.org/licenses/gpl.html)http://www.gnu.org/licenses/gpl.html
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Original Author of file: Krivoshlykov Evgeniy (mpak) +7 3462 634132
// Purpose of file:
// ----------------------------------------------------------------------

mpmenu($m = array('Быстродействие', 'Структура', 'Запрос', 'Вставка', 'Статус'));

if ($m[(int)$_GET['r']] == 'Вставка'){

	echo "<form method=\"post\"><div><select name=table style=\"margin:5px 10px 0;\">";
	foreach($tables = mpql(mpqw("SHOW TABLES")) as $k=>$v){
		$tn = $v["Tables_in_{$conf['db']['name']}"];
		$fields[$tn] = array_diff_key(array_keys(mpqn(mpqw($sql = "SHOW COLUMNS FROM $tn"), 'Field')), array("id"));
		echo "<option". ($_POST['table'] == $tn ? " selected" : ""). ">$tn</option>";
	}
	echo "</select></div>";

	$json = json_encode($fields);
	echo <<<EOF
	<div>
		<span><select name="fields" style="margin:5px 10px 0;"></select></span>
		<span><select name="field" style="margin:5px 10px 0;"><option></select></span>
		<span><input name="val" type="text" value="{$_POST['val']}"></span>
	</div>
	<div>
		<script>
			var fields = $json;
			$(function(){
				$("select[name=table]").change(function(){
					tn = $(this).find("option:selected").val();// alert(tn);
					$("select[name=fields]").find("option").remove();
					$("select[name=field]").find("option").not(":first").remove();
					$.each(fields[tn], function(key, val){
						$("select[name=fields]").append("<option>"+val+"</option>");
						$("select[name=field]").append("<option>"+val+"</option>");
					});
					$("select[name=field]").change();
				}).change();
			});
			$("select[name=field]").change(function(){
				val = $(this).find("option:selected").val();
				$("input[name=val]").attr("disabled", (val ? false : true));
			}).change();
		</script>
	</div>
	<div><textarea name="data" style="height:250px; width:30%; margin:5px 10px 0;"></textarea></div>
	<div><input type="submit" value="Занести"></div>
	</form>
EOF;

	if($_POST['fields'] && $_POST['data']){
		foreach(explode("\n", $_POST['data']) as $dat){
			echo "<br />". $sql = "INSERT INTO `". mpquot($_POST['table']). "` SET `". mpquot($_POST['fields']). "`=\"". mpquot($dat). "\"". ($_POST['field'] ? ", `{$_POST['field']}`=\"". mpquot($_POST['val']). "\"" : ""); mpqw($sql);
		}
	}
}elseif ($m[(int)$_GET['r']] == 'Структура'){
	if(empty($_REQUEST['tab'])){
		if(!empty($_GET['new'])){
			echo $sql = "CREATE TABLE `".($_REQUEST['tab'] =  $_GET['new'])."` (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT) CHARACTER SET cp1251 COLLATE cp1251_general_ci ENGINE = InnoDB";
			mpqw($sql);
		}else{
			echo "<div style='margin:10px;'><input type=\"text\" id=\"new\"> <input type=\"button\" value=\"Создать\" onClick=\"javascript: location.href='/?m[sqlanaliz]=admin&r=1&new='+document.getElementById('new').value\"></div>";
		}
	}
	if($_POST){
		foreach(mpql(mpqw("DESC {$_REQUEST['tab']}")) as $k=>$v){
			$old = $v+array('After'=>'');
			$edit = $_POST['fields'][$k];
			if(array_intersect_key($old, $edit) != $edit){
				if($edit['After'] == '_'){
					$after = " FIRST";
				}elseif(!empty($edit['After'])){
					$after = " AFTER `{$edit['After']}`";
				}
				if($old['Field'] == 'id'){
					echo $sql = "DROP TABLE `{$_POST['tab']}`";
				}elseif(empty($edit['Field'])){
					echo $sql = "ALTER TABLE `{$_POST['tab']}` DROP `{$old['Field']}`";
				}else{
					$sql = "ALTER TABLE `{$_POST['tab']}` CHANGE `{$old['Field']}` `{$edit['Field']}` {$edit['Type']} ".($edit['Null'] == 'NO' ? ' NOT NULL' : '')." $after".(!empty($edit['Default']) ? " DEFAULT ". ($edit['Default'] == 'NULL' ? $edit['Default'] : "'{$edit['Default']}'") : ''	);
					echo "<div>{$sql}</div>";
				}
				mpqw($sql); echo mysql_error();
			}
		}
	}

	if($_REQUEST['tab']){
		foreach(mpql(mpqw("SHOW KEYS FROM ".mpquot($_REQUEST['tab']))) as $n=>$m){
			$keys[$m['Column_name']] = 'on';
		}
	}
	if($_POST['keys'] && $_POST['tab']){
		foreach(array_diff_key($keys, $_POST['keys']) as $n=>$m){
			unset($keys[$n]);
			mpqw($sql = "DROP INDEX $n ON `{$_POST['tab']}`");
		}
		foreach(array_diff_key($_POST['keys'], $keys) as $n=>$m){
			$keys[$n] = 'on';
			mpqw($sql = "ALTER TABLE `{$_POST['tab']}` ADD INDEX ($n)");
		} echo $sql;
	}

	if(($new = $_POST['fields'][++$k]) && $new['Field']){
		if($new['After'] == '_'){ $after = " FIRST"; }elseif(!empty($new['After'])){ $after = " AFTER `{$new['After']}`"; }
		echo $sql = "ALTER TABLE `{$_POST['tab']}` ADD `{$new['Field']}` {$new['Type']}".($new['Null'] == 'NO' ? ' NOT NULL' : '')." $after".(!empty($new['Default']) ? " DEFAULT ". ($new['Default'] == 'NULL' ? $new['Default'] : "'{$new['Default']}'") : ''	);
		mpqw($sql);
	}
		echo "<div style='margin:10px;'>";
		echo "<div style=\"color:red;\">". mysql_error(). "</div>";
		echo "<p /><select onchange=\"javascript: location.href='/?m[{$arg['modpath']}]=admin&r=1&tab='+this.options[this.selectedIndex].text;\"><option></option>";
		foreach(mpql(mpqw("SHOW TABLES")) as $k=>$v){
			$tab = array_pop($v);
			echo "<option value=\"value\"".($tab == $_REQUEST['tab'] ? " selected" : '').">$tab</option>";
		}
		echo "</select> <a href=''><img src='data:image/jpeg;base64,
/9j/4AAQSkZJRgABAQEASABIAAD//gATQ3JlYXRlZCB3aXRoIEdJTVD/2wBDAAUDBAQEAwUEBAQF
BQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBD
AQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4e
Hh4eHh4eHh7/wAARCAAUABQDASIAAhEBAxEB/8QAGQABAQEAAwAAAAAAAAAAAAAAAAcFBAYI/8QA
JhAAAgEEAgIBBAMAAAAAAAAAAQIDBAUGEQAhBxIxMlFhcRQkQf/EABgBAAIDAAAAAAAAAAAAAAAA
AAMGAAEF/8QAHhEAAgICAwEBAAAAAAAAAAAAAQIAAwQRIjFRkSH/2gAMAwEAAhEDEQA/ALz5D8mQ
WTJYrbRSLL/EO6kb6ZiPoJ/G+beHZleL1ElTU4xUxUMmvSqjPWj/AL6nsj8jf65wfJWF4GaSe83q
y1CqzlqmqoVf3Tfy7KnevudH52eQPN/JlbQ3e3xeOsgvkVvoaJKV2q2VkmKFvUiMjrSkLvQ36jrr
ZYsbEqzKQlFfIdk9fRFnIyLsLIay+3ielGt/CPPJ7BHHJ14au2dZBg8F3yGS3JPUSM0H9Yhmh0Ar
MAwGyfb4HxrjmHbSanKMRsRgquFqB1B0f2UU86/lGI49kFBNS3G2U7Cd0eSRI1WRiGB+oDfeu/3x
xyqHZLAVOjuS9Fetgw2JuQRRQQRwQRrHFGoREUaCqBoAD7ccccFDCf/Z'></a>";
		echo "</div><div style='margin:10px;'>";
	if(!empty($_REQUEST['tab'])){
		$fields = array('varchar(255)'=>'Строка', 'smallint(6)'=>'МалИнт', 'int(11)'=>'Число', 'bigint(20)'=>'БЧисло', 'float'=>'Дробное', 'text'=>'Текст', 'mediumtext'=>'Текстище');
		$stc = mpql(mpqw("DESC ".mpquot($_REQUEST['tab'])));
//		mpre($stc);
		$stc[] = array('Null'=>'NO');
		echo "<form method=\"post\"><input type=\"hidden\" name=\"tab\" value=\"{$_REQUEST['tab']}\"><table>";
		foreach($stc as $k=>$v){
			echo "<tr>";
			echo "<td><input type=\"text\" name=\"fields[$k][Field]\" value=\"{$v['Field']}\"></td>";
			echo "<td><select name=\"fields[$k][Type]\">";
			foreach($fields as $n=>$z){
				echo "<option value=\"$n\"".($n == $v['Type'] ? ' selected' : '').">$z</option>";
			}
			echo "</select></td>";
			echo "<td><select name=\"fields[$k][After]\"><option></option><option value='_'>first</option>";
			foreach($stc as $n=>$z){
				if(empty($z['Field'])) continue;
				echo "<option>{$z['Field']}</option>";
			}
			echo "</select></td>";
			echo "<td><select name=\"fields[$k][Null]\">";
			echo "<option value=\"\">NULL</option>";
			echo "<option value=\"NO\"".($v['Null'] == 'NO' ? " selected" : '').">NOT NULL</option>";
			echo "</select></td>";
			echo "<td><input type=\"text\" name=\"fields[$k][Default]\" value=\"{$v['Default']}\" style=\"width:80px;\"></td>";
			echo "<td><input type=\"checkbox\" name=\"keys[{$v['Field']}]\"". ($keys[ $v['Field'] ] ? " checked" : ''). ($keys[ $v['Field'] ] == 'PRIMARY' ? " disabled><input type=\"hidden\" name=\"keys[{$v['Field']}]\" value=\"PRIMARY\"" : ''). "></td>";
			echo "<td>{$v['Key']}</td>";
			echo "<td>{$v['Extra']}</td>";
			echo "</tr>";
		}
		echo "</table><input type=\"submit\" value=\"Сохранить\"></form>";
	}
	echo "</div>";
}elseif ($m[(int)$_GET['r']] == 'Запрос'){
	if (isset($_POST['sqlquery']) && $_POST['textarea'] == 'Длинный'){
		$microtime = microtime();
		$result['sqlquery'] = mpql(mpqw(stripslashes($_POST['sqlquery'])));
		$mess['sqlquery'] = "<p><b>".stripslashes($_POST['sqlquery'])."</b>";
		$time['sqlquery'] = "<br><i>Время исполнения</i>: ".(microtime() - $microtime)." c.<p>";
	$query_error = mysql_error();
		$mess['sqlerror'] = "<p />Ошибки ". mysql_error();
		if ($_POST['sqlquery']){
			mpqw("INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_query (`time`, `query`) VALUES ('".(microtime() - $microtime)."', '{$_POST['sqlquery']}')");
		}
	}
	if((isset($_POST['query']) && $_POST['textarea'] == 'Короткий') || ($_POST['repeatquery'] && isset($_POST['query']))){
		$microtime = microtime();
		$result['query'] = mpql(mpqw(stripslashes($_POST['query'])));
	error_reporting(E_ALL);
		$mess['query'] = "<p><b>".stripslashes($_POST['query'])."</b>";
	$sql_error = mysql_error();
	error_reporting(0);
		$time['query'] = "<br><i>Время исполнения</i>: ".(microtime() - $microtime)." c.<p>";
		$mess['queryerror'] = "<p />Ошибки ". mysql_error();
	}elseif(isset($_GET['all'])){
		$result = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_query ORDER BY id DESC LIMIT 100"));
	}
	echo "<form method=post name='form'>";
	echo "<table width=100% border=0><tr><td colspan=2><select onchange=\"document.form.sqlquery.value = this.options[selectedIndex].value;\" style='width: 100%'><option></option>";
	foreach(mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_query ORDER BY id DESC LIMIT 30")) as $k=>$v){
		echo "<option>{$v['query']}</option>";
	}
	echo "</select></td><td width=70><a href='/?m[{$arg['modpath']}]=admin&r=1&all'>Последние</a></td></tr>";
	echo "<tr><td width=1><input type='hidden' name=repeatquery value='off'><input type=checkbox name=repeatquery".(!isset($_POST['repeatquery']) || $_POST['repeatquery'] == 'on' ? ' checked' : '')."></td><td><input name='query' type='text' style='width: 100%;' value='".($_REQUEST['query'] ? stripslashes($_REQUEST['query']) : 'SHOW TABLES')."'></td>";
	echo "<td width=1><input type=submit name='textarea' value='Короткий'></td>";
	echo "</tr></table>";

	echo "<textarea name='sqlquery' style='width: 100%; height: 100px; margin:5px;'>".stripslashes(htmlspecialchars($_POST['sqlquery'], ENT_QUOTES))."</textarea>";
	echo "<input type=submit name='textarea' value='Длинный' style='margin:5px;'>";
	echo "</form>";
echo '<p /><font style="color:red;">'. $sql_error."</font>";
echo '<p /><font style="color:red;">'. $query_error."</font>";
//	echo "<pre>"; print_r($result); echo "</pre>";
	if ($result['sqlquery']){
		echo $mess['sqlquery'] ." ". $time['sqlquery'];
	error_reporting(E_ALL);
		mpqwt($result['sqlquery']);
	error_reporting(0);
	}
	if ($result['query']){
		echo $mess['query'] ." ".$time['query'];
		mpqwt($result['query']);
	}
	echo "<script>document.form.query.focus()</script>";
}elseif ($m[(int)$_GET['r']] == 'Статус'){
	echo "<div style=\"margin:10px;\">База данных: <b>{$conf['db']['name']}</b></div>";
	$data = mpql(mpqw("SHOW TABLE STATUS"));
	if ($_GET['id']){
		echo "<p>". $sql = "SELECT * FROM {$data[(int)$_GET['id']]['Name']}";
		echo "<br><textarea style='width:100%; height:400px;'>";
		foreach(mpql(mpqw($sql)) as $k=>$v){
			$rows = $val = '';
			foreach($v as $n=>$z){
				$rows .= ($rows ? ' ,' : '')."`$n`";
				$val .= ($val ? ' ,' : '')."'".strtr(htmlspecialchars($z), array("'"=>"\'"))."'";
			}
			echo "\nINSERT INTO {$data[(int)$_GET['id']]['Name']} ($rows) VALUES ($val);";
		}
		echo "</textarea>";
	}else{
		echo "<p><table border=1 cellspacing=0 cellpadding=2>";
		$hide = array('Name'=>'Имя', 'Engine'=>'Движок', 'Rows'=>'Записай', 'Avg_row_length'=>'Сред', 'Data_length'=>'Размер', 'Max_data_length'=>'Макс', 'Auto_increment'=>'Инкремент', 'Create_time'=>'Создание', 'Update_time'=>'Обновление', 'Collation'=>'Кодировка');
		foreach($data as $k=>$v){
			if (!$k){
				echo "<tr><td><b>Нум</b></td>";
				foreach($v as $n=>$z){
					if (!$hide[$n]) continue;
					echo "<td align=center><b>{$hide[$n]}</b></td>";
				}
				echo "</tr>";
			}
			echo "<tr><td>$k</td>";
			foreach($v as $n=>$z){
				if (!$hide[$n]) continue;
				echo "<td>".($n=='Name' ? "<a href=/?m[{$arg['modpath']}]=admin&r=3&id=$k>$z</a>" : $z)."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
}else{
	echo "<p>";
	foreach(mpql(mpqw("SELECT folder, name FROM {$conf['db']['prefix']}modules WHERE enabled = 2")) as $k=>$v){
		if (file_exists("modules/{$v['folder']}/admin.php"))
			$admin .= "<option value='m[{$v['folder']}]=admin&m[sqlanaliz]=admin'>{$v['name']}</option>";
		if (file_exists("modules/{$v['folder']}/index.php"))
			$index .= "<option value='m[{$v['folder']}]&m[sqlanaliz]=admin'>{$v['name']}</option>";
	}

	echo "Админ: <select onchange=\"document.location = '?' + options[selectedIndex].value;\"><option></option>$admin</select>";
	echo " Пользователь: <select onchange=\"document.location = '?' + options[selectedIndex].value;\"><option></option>$index</select>";

	echo "<p><table width='100%' border='1' cellspacing='0' cellpadding='3'>";
	foreach($conf['db']['sql'] as $k=>$v){
		echo "<tr valign='top'><td>$k</td><td>{$v['info']}&nbsp;</td><td>".htmlspecialchars($v['sql'])."</td><td>". number_format($v['time'], 4). "&nbsp;c.</td></tr>";
		$resultime += $v['time'];
		if ($v['time'] > $dolg){
			$dolgnum = $k;
			$dolg = $v['time'];
		}
	}
	echo "</table>";
	echo "<p />Общее время обращения к БД: $resultime с.";
	echo "<p />Самый долгий запрос: <font color=blue>{$conf['db']['sql'][$dolgnum]['sql']}</font> ({$conf['db']['sql'][$dolgnum]['time']} c.)";
}

?>