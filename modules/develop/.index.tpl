<style>
.my_left {
	padding-bottom:1px;
	position:absolute;
	width:90px;
	height: 98%;
	float:left;
	border:1px solid #c2dfbc;
	border-bottom:1px solid #c2dfbc;
	background-color: #e2ffec;
/*	padding:5px;*/
	text-align:center;
	-webkit-border-top-left-radius: 20px;
	-webkit-border-bottom-left-radius: 20px;
	-moz-border-radius-topleft: 20px;
	-moz-border-radius-bottomleft: 20px;
	border-top-left-radius: 20px;
	border-bottom-left-radius: 20px;
}
.golos {
	margin:5px 5px;
	padding:3px 7px;
	background-color:green;
	color:white;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
}
.my_right {
	overflow:hidden;
	min-height:120px;
	margin-left:80px;
	border:1px solid #c2dfbc;
	border-left: none;
	padding:5px 20px;
	-webkit-border-top-right-radius: 20px;
	-webkit-border-bottom-right-radius: 20px;
	-moz-border-radius-topright: 20px;
	-moz-border-radius-bottomright: 20px;
	border-top-right-radius: 20px;
	border-bottom-right-radius: 20px;
}
.mn {
	float:right;
	padding:2px 7px;
	margin:1px;
	position:relative;
	top:3px;
	border:1px solid gray;
	border-bottom: 1px solid green;
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	border-top-left-radius: 5px;
	border-top-right-radius: 5px;
}

.conmment input[type='text'],textarea {width:100%;}
.com {margin-top:10px;}
.com > div {overflow:hidden; margin-top:10px;}
.com > div > div:first-child {float:right; margin:0 0 0 10px; /*font-style:italic; */font-weight:bold;}
.com > div > div:nth-child(2) {float:left; margin:0 10px 0 0; color:blue;}
</style>
<script src="/include/jquery/my/jquery.klesh.select.js"></script>
<script>
	$(function(){
		$(".klesh").klesh("/<?=$arg['modpath']?>/null", function(){
		}, <?=json_encode($conf['tpl']['cat'])?>)
		$('.golos a').click(function (){
			id = $(this).attr('name');
			$("a[name="+id+"]").html("Принят");
			$.post("/<?=$arg['modpath']?>/null", {golos:id}, function(data){
				if(isNaN(data)){
					alert(data);
				}else{
					$('#est_'+id).html(parseInt($('#est_'+id).html())+1);
				}
			});
		});
		$(".com_open").click(function(){
			$(this).parents(".comment").find("> div:eq(1)").slideToggle();
		});
		$(".btn_submit").mousedown(function(){
			plan_id = $(this).parents("[plan_id]").attr("plan_id");// alert(plan_id);
			text = $(this).parents(".comment").find("textarea").val();// alert(text);
			com = $(this).parents("[plan_id]").find(".com");
			$(this).parents(".comment").find("textarea").val('');
			$(this).parents(".comment").find(">div:eq(1)").hide(300);
			$.post("/<?=$arg['modpath']?>/null", {plan_id:plan_id, text:text}, function(data){
				if(isNaN(data)){// alert(data) }else{
					dat = $.parseJSON(data);
					$("<div>")
						.append("<div>"+dat.time+"</div>")
						.append("<div>"+dat.name+"</div>")
						.append("<div>"+dat.text+"</div>")
						.prependTo(com);
				}
			});
		});
	});
</script>

<script type="text/javascript" src="/include/jquery/jquery.jeditable.js"></script>

<div style="border-bottom:1px solid black; overflow:hidden; margin:0 15px;">
	<? foreach($conf['tpl']['cat'] as $k=>$v): ?>
		<div class="mn"<?=(isset($_GET['kid']) && $_GET['kid'] == $k ? " style='background-color:#eee;'" : '')?>>
			<a href="/<?=$arg['modname']?>/kid:<?=(int)$k?>"><?=$v?></a> [<? $summ+=$conf['tpl']['cc'][ $k ]; echo (int)$conf['tpl']['cc'][ $k ] ?>]
		</div>
	<? endforeach; ?>
	<div class="mn"<?=(!isset($_GET['kid']) ? " style='background-color:#eee;'" : '')?>>
		<a href="/<?=$arg['modname']?>">Все</a> [<?=$summ?>]
	</div>
</div>

<? if(empty($_GET['id'])): ?>
	<div style="padding:5px; overflow:hidden;">
		<form method="post" action="/<?=$arg['modname']?>/kid:0">
			<div id="new" style="margin-top:3px; text-align:right;">
				<textarea name="plan" style="width:100%;" title="Ваше предложение"></textarea>
				<input type="submit" name="submit" value="Добавить">
			</div>
		</form>
		<? echo mpager($conf['tpl']['pcount']); ?>
	</div>
<? endif; ?>

<? foreach($conf['tpl']['dev'] as $k=>$v): ?>
	<div plan_id="<?=$v['id']?>" style="margin:5px; overflow:hidden; position:relative;">
		<div class="my_left">
			<div id="est_<?=$v['id']?>" style="font-size:200%; margin-top:15px;"><?=(int)$conf['tpl']['golos'][ $v['id'] ]?></div>голосов
			<div class="golos">
				<? if($conf['tpl']['mygolos'][ $v['id'] ]): ?>
					Принят
				<? else: ?>
					<a href="javascript: return false;" name="<?=$v['id']?>" style="color:white;">Голосовать</a>
				<? endif; ?>
			</div>
		</div>
		<div class="my_right">
			<div style="float:right;">
				<?=date('Y.m.d H:i:s', $v['time'])?>
			</div>
			<div style="float:left;">
				<div plan_id="<?=$v['id']?>" class="klesh"><?=$conf['tpl']['cat'][ $v['kid'] ]?></div>
			</div>
			<div style="margin-top:30px; font-weight:bold;">
				<? if($arg['access'] > 3): ?>
					<span>
						<a href="/?m[develop]=admin&r=mp_develop_plan&where[id]=<?=$v['id']?>">
							<img src="/img/aedit.png">
						</a>
					</span>
				<? endif; ?>
				<?=$v['plan']?>
			</div>
			<div class="comment" style="margin-top:10px;">
				<div style="text-align:right; padding:0 15px;"><a class="com_open" href="javascript:void(0);">Коммент [<?=count($conf['tpl']['work'][ $v['id'] ])?>]</a></div>
				<div style="display:none; overflow:hidden;">
					<div style="float:right; width:120px; text-align:center;">
						<div><?=$conf['user']['uname']?></div>
						<div><input class="btn_submit" type="button" value="Добавить"></div>
					</div>
					<div style="margin-right:120px;"><textarea style="height:37px;"></textarea></div>
				</div>
			</div>
			<div class="com">
				<? if($conf['tpl']['work'][ $v['id'] ]) foreach($conf['tpl']['work'][ $v['id'] ] as $w): ?>
					<div>
						<div><?=date("Y.m.d H:i:s", $w['time'])?></div>
						<div>
							<? if($arg['access'] > 3): ?>
								<a href="/?m[develop]=admin&r=mp_develop_work&where[id]=<?=$w['id']?>">
									<img src="/img/aedit.png">
								</a>
							<? endif; ?>
							<?=($w['uid'] > 0 ? $w['name'] : $conf['settings']['default_usr']. $w['uid'])?>
						</div>
						<div><?=$w['description']?></div>
					</div>
				<? endforeach; ?>
			</div>
		</div>
	</div>
<? endforeach; ?>
<div style="padding:5px;"><?=mpager($conf['tpl']['pcount'])?></div>
