<p><center><b><?=$conf['settings']['gbook_title']?></b></center>
<form method='post'>
	<input type='hidden' name='gbook[md5]' value='<?=$conf['tpl']['md5']?>'>
	<table width=100%>
		<tr>
			<td width=30><nobr>Сообщ \ Пользов</nobr></td>
			<td width=60%>
				<input type='text' name='gbook[name]' style='width:100%;' value='<?=$conf['user']['uname']?>'>
			</td>
			<td>
				<img src='/<?=$arg['modpath']?>:kod/kod:<?=$conf['tpl']['md5']?>/null/kod.jpg' border=1>
			</td>
			<td>
				<input type='text' name='gbook[kod]' style='width:50px;'>
			</td>
			<td width=1>
				<input type='submit' value='Добавить'>
			</td>
		</tr>
		<tr>
			<td colspan=5>
				<textarea style='width:100%;' name='gbook[text]'></textarea>
			</td>
		</tr>
	</table>
</form>

<? foreach($conf['tpl']['mess'] as $k=>$v): ?>
<p>
	<table border=0 width=100%>
		<tr valign=top>
			<td colspan=2><?=($conf['tpl']['admin'] ? "<a onclick=\"javascript: if (confirm('Вы уверенны?')){return obj.href;}else{return false;}\" href=?m[{$arg['modpath']}]=admin&del={$v['id']}><img src=/img/del.png border=0></a>&nbsp;
			<a href=?m[{$arg['modpath']}]=admin&edit={$v['id']}><img src=/img/edit.png border=0></a>&nbsp;" : '')?><font color=blue><b><?=(strlen($v['name']) ? strtr($v['name'], array(' '=>'&nbsp;')) : $v['uname'])?></b></font>: <?=$v['text']?>
			</td>
		</tr>
		<tr>
			<td>
				<div style='margin-left: 100px;'><?(strlen($v['otvet']) ? "&nbsp;<font color=blue><b>{$conf['settings']['gbook_admin_site']}</b></font>".($v['otime'] ? date(' (d.m.Y H:i)', $v['otime']) : '').": " : "")?><i><?=$v['otvet']?></i></div>
			</td>
		</tr>
	</table>
<? endforeach; ?>