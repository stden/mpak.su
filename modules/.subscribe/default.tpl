<? if($_GET['unsubscribe']): ?>
	<div style="margin:100px; text-align:center;">
		<? if($conf['tpl']['unsubscribe'] == 1): ?>
			Вы успешно отписались от рассылки
		<? elseif($conf['tpl']['unsubscribe'] == 2): ?>
			Вы не подписаны на рассылку
		<? else: ?>
			Ошибка
		<? endif; ?>
	</div>
<? else: ?>
	<script language="javascript">
		$(function(){
			$(".chk").change(function(){
				if(mail = $(this).is(":checked")){
					subject = $("#subject").val();// alert(subject);
					text = $("#text").val();// alert(text);
					uid = $(this).attr("uid");// alert(uid);
					send = $("#send").is(":checked") ? 1 : 0;
					$.post("/<?=$arg['modpath']?>:<?=$arg['fn']?>/null", {uid:uid, send:send, subject:subject, text:text}, function(data){
						if(isNaN(data)){
							$(".chk[uid="+uid+"]").attr("checked", "");
							alert(data);
						}
					});
				}
			});
		});
		var timeout = true;
		function update(sto){
			if($("#update").is(":checked") && timeout){
				obj = $(".chk:not(:checked):not(:disabled):first")
				if($(obj).attr("uid")){
					obj.attr("checked", "checked").delay(100).change();
				}else{
					timeout = false;
					current = $("#filter").find("option:selected").val();
					next = $("#filter").find("option:selected").next().val();
					if(next != "undefined"){
						$("#filter").find("option:selected").next().attr('selected', 'selected').change();// alert(filter);
					}
				}
			} if (sto) to = setTimeout("update(true); ",3000);
		} update(true);
	</script>
	<script>
		$(function(){
			$("#filter").change(function(){
				filter = $(this).find("option:selected").val();
				uri = "/<?=$arg['modpath']?>:<?=$arg['fn']?>/to:<?=($_GET['to'] ?: 10000)?>/filter:"+filter+"/auto:"+($("#update").is(":checked") ? 1 : 0);
				document.location.href=uri;
			});
			$("#text").change(function(){
				text_id = $(this).find("option:selected").val();// alert(text_id);
				$.post("/<?=$arg['modpath']?>:<?=$arg['fn']?>/null", {text:{text_id:text_id}}, function(data){
					$("#text_text").html(data);
				});
			}).change();
		});
	</script>
	<a name="<?=$arg['modpath']?>"></a>
	<div style="margin:10px;">
		<span style="float:right;">
			контактов: <?=$conf['tpl']['count']?>
			<select id="filter">
				<? for($i=0; $i<=$conf['tpl']['count']; $i+=100): ?>
					<option value="<?=$i?>" <?=($_GET['filter'] == $i ? "selected" : "")?>><?=$i+1?> - <?=$i+100?></option>
				<? endfor; ?>
			</select>
		</span>
		<input id="update" type="checkbox" <?=($_GET['auto'] ? "checked" : "")?>> Автопилот
	</div>
	<div style="margin:20px 0;">
		<div>
			<select id="text">
				<? foreach($conf['tpl']['text'] as $k=>$v): ?>
					<option value="<?=$v['id']?>"><?=$v['name']?></option>
				<? endforeach; ?>
			</select>
		</div>
		<div id="text_text" style="margin-top:10px; padding:10px; border:1px dashed #ddd;"></div>
	</div>
	<div style="margin:10px;">
		<? foreach($conf['tpl']['subscribe'] as $k=>$v): ?>
			<div style="float:left; width:25%; white-space:nowrap; overflow:hidden;">
				<input class="chk" uid="<?=$v['id']?>" type="checkbox" <?=($v['disable'] ? "disabled" : "")?>>
				<span title="<?=$v['name']?>"> [<?=$v['count']?>]<?=$v['mail']?></span>
			</div>
		<? endforeach; ?>
	</div>
<? endif; ?>
