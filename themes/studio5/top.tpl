<ul id="menu">
	<? foreach($menu[0] as $k=>$v): ?>
		<li><a href="<?=$v['link']?>"><?=$v['name']?></a></li>
	<? endforeach; ?>
</ul>
