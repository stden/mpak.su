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

$conf['settings'] += array(
	"{$arg['modpath']}_index"=>"Мозайка",
	"{$arg['modpath']}_slide"=>"Слайды",
	"{$arg['modpath']}_region"=>"Регионы",
);

foreach(mpql(mpqw("SHOW TABLES WHERE Tables_in_{$conf['db']['name']} LIKE \"{$conf['db']['prefix']}{$arg['modpath']}%\"")) as $k=>$v){
	$val = ($conf['settings'][$fn = substr($v["Tables_in_{$conf['db']['name']}"], strlen($conf['db']['prefix']))] ?: $fn);
	$m["{$conf['db']['prefix']}". $fn] = $val;
} mpmenu($m); if(!$_GET['r']) $_GET['r'] = array_shift(array_keys($m));

if ($_GET['r'] == "{$conf['db']['prefix']}{$arg['modpath']}_index"){
	stable(
		array(
//			'dbconn' => $conf['db']['conn'],
			'url' => "/?m[{$arg['modpath']}]=admin&r={$_GET['r']}", # Ссылка для редактирования
			'name' => $_GET['r'], # Имя таблицы базы данных
//			'where' => '', # Условия отбора содержимого
			'order' => 'id DESC', # Сортировка вывода таблицы
//			'debug' => false, # Вывод всех SQL запросов
			'acess' => array( # Разрешение записи на таблицу
				'add' => array('*'=>true), # Добавление
				'edit' => array('*'=>true), # Редактирование
				'del' => array('*'=>true), # Удаление
				'cp' => array('*'=>true), # Копирование
			),
//			'edit'=>'list',
//			'count_rows' => 12, # Количество записей в таблице
//			'page_links' => 10, # Количество ссылок на страницы в обе стороны

//			'top' => array('tr'=>'<tr>', 'td'=>'<td>', 'result'=>'<b><center>{result}</center></b>'), # Формат заголовка таблицы
//			'middle' => array('tr'=>'<tr>', 'td'=>'<td>', 'shablon'=>"<tr><td>{sql:name}</td><td>&nbsp;{sql:img}</td><td>&nbsp;{sql:description}</td><td align='right'>{config:row-edit}</td></tr>"), # Формат записей таблицы
//			'bottom' => array('tr'=>'<tr>', 'td'=>"<td valign='top'>", 'shablon'=>'<tr><td>{config:url}</td></tr>'), # Формат записей таблицы

			'title' => array('name'=>'Имя', 'width'=>"ШирПикс", 'height'=>"ВысПикс", 'w'=>'ГорШтук', 'h'=>"ВертШтук", 'margin'=>'Отступ', 'slide'=>"Слайдов"), # Название полей
//			'etitle'=> array(),
			'type' => array('img'=>'file', 'description'=>'textarea', 'text'=>'wysiwyg'), # Тип полей
			'ext' => array('img'=>array('image/png'=>'.png', 'image/pjpeg'=>'.jpg', 'image/jpeg'=>'.jpg', 'image/gif'=>'.gif', 'image/bmp'=>'.bmp')),
//			'set' => array('orderby'=>$orderby), # Значение которое всегда будет присвоено полю. Исключает любое изменение
			'shablon' => array(
//				'num'=>array('*'=>'<a target=_blank href=http://stom-firms.ru/clinics.php?i={f:{f}}>http://stom-firms.ru/clinics.php?i={f:{f}}</a>'),
//				'name'=>array('*'=>"<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_index&where[rid]={f:id}>{f:{f}}</a>"),
//				'img'=>array('*'=>"<img src='/{$arg['modpath']}:img/{f:id}/tn:". (array_pop(explode('_', $_GET['r']))). "/w:120/h:100/null/img.jpg'>"),
				($fn = 'slide')=>array('*'=>"<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]={f:id}>Нет</a>")+spisok("SELECT r.id, CONCAT('<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]=', r.id, '>', COUNT(*), '_{$fn}</a>') FROM {$_GET['r']} AS r, {$conf['db']['prefix']}{$arg['modpath']}_{$fn} AS fn WHERE r.id=fn.". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_"))). "_id GROUP BY r.id"),
			), # Шаблон вывода в замене участвуют только поля запроса имеен приоритет перед полем set
//			'disable' => array('orderby'), # Выключенные для записи поля
//			'hidden' => array('name', 'enabled'), # Скрытые поля
			'spisok' => array( # Список для отображения и редактирования
				'uid' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}users")),
//				($fn = 'index'). '_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_{$fn}")),
//				'metro_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_metro")),
//				'kuzov_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_kuzov_type")),
//				'zagruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_zagruzka_type")),
//				'vygruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_vygruzka_type")),
//				'select'=>array('*'=>array('0'=>'Скрыто', '1'=>'Доступно')),
			),
			'default' => array(
				'uid'=>$conf['user']['id'],
//				'time'=>date('Y.m.d H:i:s'),
//				($f = 'type_id')=>array('*'=>max($_GET['where'][$f], $_POST[$f])),
			), # Значение полей по умолчанию
//			'maxsize' => array('bdesc'=>array('*'=>'50'),), # Максимальное количество символов в поле
		)
	);
}elseif ($_GET['r'] == "{$conf['db']['prefix']}{$arg['modpath']}_slide"){
	stable(
		array(
//			'dbconn' => $conf['db']['conn'],
			'url' => "/?m[{$arg['modpath']}]=admin&r={$_GET['r']}", # Ссылка для редактирования
			'name' => $_GET['r'], # Имя таблицы базы данных
//			'where' => '', # Условия отбора содержимого
			'order' => 'id DESC', # Сортировка вывода таблицы
//			'debug' => false, # Вывод всех SQL запросов
			'acess' => array( # Разрешение записи на таблицу
				'add' => array('*'=>true), # Добавление
				'edit' => array('*'=>true), # Редактирование
				'del' => array('*'=>true), # Удаление
				'cp' => array('*'=>true), # Копирование
			),
//			'edit'=>'list',
//			'count_rows' => 12, # Количество записей в таблице
//			'page_links' => 10, # Количество ссылок на страницы в обе стороны

//			'top' => array('tr'=>'<tr>', 'td'=>'<td>', 'result'=>'<b><center>{result}</center></b>'), # Формат заголовка таблицы
//			'middle' => array('tr'=>'<tr>', 'td'=>'<td>', 'shablon'=>"<tr><td>{sql:name}</td><td>&nbsp;{sql:img}</td><td>&nbsp;{sql:description}</td><td align='right'>{config:row-edit}</td></tr>"), # Формат записей таблицы
//			'bottom' => array('tr'=>'<tr>', 'td'=>"<td valign='top'>", 'shablon'=>'<tr><td>{config:url}</td></tr>'), # Формат записей таблицы

			'title' => array('index_id'=>'Блок', 'img'=>'Слайдер', 'region'=>'Регионов', 'description'=>'Описание'), # Название полей
//			'etitle'=> array(),
			'type' => array('img'=>'file', 'description'=>'textarea', 'text'=>'wysiwyg'), # Тип полей
			'ext' => array('img'=>array('image/png'=>'.png', 'image/pjpeg'=>'.jpg', 'image/jpeg'=>'.jpg', 'image/gif'=>'.gif', 'image/bmp'=>'.bmp')),
//			'set' => array('orderby'=>$orderby), # Значение которое всегда будет присвоено полю. Исключает любое изменение
			'shablon' => array(
//				'num'=>array('*'=>'<a target=_blank href=http://stom-firms.ru/clinics.php?i={f:{f}}>http://stom-firms.ru/clinics.php?i={f:{f}}</a>'),
//				'name'=>array('*'=>"<a href=/news/slide_id:{f:id}>{f:{f}}</a>"),
				'img'=>array('*'=>"<a href=/news/slide_id:{f:id}><img src='/{$arg['modpath']}:img/{f:id}/tn:". (array_pop(explode('_', $_GET['r']))). "/w:120/h:100/null/img.jpg'></a>"),
				($fn = 'region')=>array('*'=>"<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]={f:id}>Нет</a>")+spisok("SELECT r.id, CONCAT('<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]=', r.id, '>', COUNT(*), '_{$fn}</a>') FROM {$_GET['r']} AS r, {$conf['db']['prefix']}{$arg['modpath']}_{$fn} AS fn WHERE r.id=fn.". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_"))). "_id GROUP BY r.id"),
			), # Шаблон вывода в замене участвуют только поля запроса имеен приоритет перед полем set
//			'disable' => array('orderby'), # Выключенные для записи поля
//			'hidden' => array('name', 'enabled'), # Скрытые поля
			'spisok' => array( # Список для отображения и редактирования
//				'uid' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}users")),
				($fn = 'index'). '_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_{$fn}")),
//				'metro_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_metro")),
//				'kuzov_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_kuzov_type")),
//				'zagruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_zagruzka_type")),
//				'vygruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_vygruzka_type")),
				'hide'=>array('*'=>array('0'=>'Видимо', '1'=>'Скрыто')),
			),
			'default' => array(
//				'uid'=>$conf['user']['id'],
//				'time'=>date('Y.m.d H:i:s'),
				($f = 'index_id')=>array('*'=>max($_GET['where'][$f], $_POST[$f])),
			), # Значение полей по умолчанию
//			'maxsize' => array('bdesc'=>array('*'=>'50'),), # Максимальное количество символов в поле
		)
	);
}elseif ($_GET['r'] == "{$conf['db']['prefix']}{$arg['modpath']}_region"){
	stable(
		array(
//			'dbconn' => $conf['db']['conn'],
			'url' => "/?m[{$arg['modpath']}]=admin&r={$_GET['r']}", # Ссылка для редактирования
			'name' => $_GET['r'], # Имя таблицы базы данных
//			'where' => '', # Условия отбора содержимого
			'order' => 'id DESC', # Сортировка вывода таблицы
//			'debug' => false, # Вывод всех SQL запросов
			'acess' => array( # Разрешение записи на таблицу
				'add' => array('*'=>true), # Добавление
				'edit' => array('*'=>true), # Редактирование
				'del' => array('*'=>true), # Удаление
				'cp' => array('*'=>true), # Копирование
			),
//			'edit'=>'list',
//			'count_rows' => 12, # Количество записей в таблице
//			'page_links' => 10, # Количество ссылок на страницы в обе стороны

//			'top' => array('tr'=>'<tr>', 'td'=>'<td>', 'result'=>'<b><center>{result}</center></b>'), # Формат заголовка таблицы
//			'middle' => array('tr'=>'<tr>', 'td'=>'<td>', 'shablon'=>"<tr><td>{sql:name}</td><td>&nbsp;{sql:img}</td><td>&nbsp;{sql:description}</td><td align='right'>{config:row-edit}</td></tr>"), # Формат записей таблицы
//			'bottom' => array('tr'=>'<tr>', 'td'=>"<td valign='top'>", 'shablon'=>'<tr><td>{config:url}</td></tr>'), # Формат записей таблицы

//			'title' => array('name'=>'Имя', 'cnt'=>'Клиник', 'description'=>'Описание'), # Название полей
//			'etitle'=> array(),
			'type' => array('img'=>'file', 'description'=>'textarea', 'text'=>'wysiwyg'), # Тип полей
			'ext' => array('img'=>array('image/png'=>'.png', 'image/pjpeg'=>'.jpg', 'image/jpeg'=>'.jpg', 'image/gif'=>'.gif', 'image/bmp'=>'.bmp')),
//			'set' => array('orderby'=>$orderby), # Значение которое всегда будет присвоено полю. Исключает любое изменение
			'shablon' => array(
//				'num'=>array('*'=>'<a target=_blank href=http://stom-firms.ru/clinics.php?i={f:{f}}>http://stom-firms.ru/clinics.php?i={f:{f}}</a>'),
//				'name'=>array('*'=>"<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_index&where[rid]={f:id}>{f:{f}}</a>"),
				'img'=>array('*'=>"<img src='/{$arg['modpath']}:img/{f:id}/tn:". (array_pop(explode('_', $_GET['r']))). "/w:120/h:100/null/img.jpg'>"),
//				($fn = 'cnt')=>array('*'=>"<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]={f:id}>Нет</a>")+spisok("SELECT r.id, CONCAT('<a href=/?m[{$arg['modpath']}]=admin&r={$conf['db']['prefix']}{$arg['modpath']}_{$fn}&where[". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_")). "_id"). "]=', r.id, '>', COUNT(*), '_{$fn}</a>') FROM {$_GET['r']} AS r, {$conf['db']['prefix']}{$arg['modpath']}_{$fn} AS fn WHERE r.id=fn.". (substr($_GET['r'], strlen("{$conf['db']['prefix']}{$arg['modpath']}_"))). "_id GROUP BY r.id"),
			), # Шаблон вывода в замене участвуют только поля запроса имеен приоритет перед полем set
//			'disable' => array('orderby'), # Выключенные для записи поля
//			'hidden' => array('name', 'enabled'), # Скрытые поля
			'spisok' => array( # Список для отображения и редактирования
//				'uid' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}users")),
				($fn = 'slide'). '_id' => array('*'=>spisok("SELECT id, CONCAT('#', id) FROM {$conf['db']['prefix']}{$arg['modpath']}_{$fn}")),
//				'metro_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_metro")),
//				'kuzov_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_kuzov_type")),
//				'zagruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_zagruzka_type")),
//				'vygruzka_type_id' => array('*'=>spisok("SELECT id, name FROM {$conf['db']['prefix']}{$arg['modpath']}_vygruzka_type")),
//				'select'=>array('*'=>array('0'=>'Скрыто', '1'=>'Доступно')),
			),
			'default' => array(
//				'uid'=>$conf['user']['id'],
//				'time'=>date('Y.m.d H:i:s'),
				($f = 'slide_id')=>array('*'=>max($_GET['where'][$f], $_POST[$f])),
			), # Значение полей по умолчанию
//			'maxsize' => array('bdesc'=>array('*'=>'50'),), # Максимальное количество символов в поле
		)
	);
}else{ echo "{$_GET['r']}:". __LINE__;
	stable(
		array(
			'url' => "/?m[{$arg['modpath']}]=admin&r={$_GET['r']}", # Ссылка для редактирования
			'name' => $_GET['r'], # Имя таблицы базы данных
			'order' => 'id DESC', # Сортировка вывода таблицы
			'acess' => array( # Разрешение записи на таблицу
				'add' => array('*'=>true), # Добавление
				'edit' => array('*'=>true), # Редактирование
				'del' => array('*'=>true), # Удаление
				'cp' => array('*'=>true), # Копирование
			),
		)
	);
}

?>