<? if($conf['tpl']['count']): ?>
	<? for($i = 0; $i<$conf['tpl']['count']/10; $i++): ?>
		<a href=/news/pid:<?=$i?> style="border: 1px solid rgb(0, 0, 0); margin: 1px; padding: 2px;"><?=($i+1)?></a>
	<? endfor; ?>
<? endif; ?>

<!-- [settings:foto_lightbox] -->
<? foreach($conf['tpl']['news'] as $k=>$news): ?>
	<div style="overflow:hidden;">
		<div class="news_tema" style="margin-top: 20px; padding: 5px;">
			<b>
				<?=date('Y.m.d H.i.s', $news['time'])?>
				<? if(!$_GET['id']): ?><a href="/news/<?=$news['id']?>"><? endif; ?>
					<?=$news['tema']?>
				<? if(!$_GET['id']): ?></a><? endif; ?>
			</b>
		</div>
		<div class="news_img" style="padding: 5px;">
			<?// if(!empty($news['img']) && !$_GET['id']):?>
				<div id="gallery" style="float:right;">
					<a title="<?=$news['tema']?>" alt="<?=$news['tema']?>" href="/<?=$arg['modname']?>:img/<?=$news['id']?>/w:600/h:500/null/img.jpg">
						<img src="/<?=$arg['modname']?>:img/<?=$news['id']?>/w:150/h:150/null/img.jpg" />
					</a>
				</div>
			<?// endif; ?>
			<?=((int)$_GET['id'] ? $news['text'] : strip_tags($news['txt']))?>
			<? if(!$_GET['id']): ?>
				<a href="/news/<?=$news['id']?><?=($_GET['p'] ? "/p:{$_GET['p']}" : '')?>" class="news_more">Подробно</a>
			<? endif; ?>
		</div>
		<div style="padding: 5px;">
			Категория: <a href="/<?=$arg['modname']?>/kid:<?=$news['kid']?>"><?=$news['kname']?></a>; Просмотров: <?=$news['count']?>
		</div>
	</div>
		<div>
			<? if($_GET['id']): ?>
				<a href="/news<?=($_GET['p'] ? "/p:{$_GET['p']}" : '')?>" class="new_smore">К списку новостей</a>
				<div><!-- [settings:comments] --></div>
			<? endif; ?>
		</div>
<? endforeach; ?>

<? if(!$_GET['id']): ?>
	<div align=center><?=$conf['tpl']['mpager']?></div>
<? endif; ?>