<? die;

if($_FILES){
	if($fn = mpfn($tn = "{$conf['db']['prefix']}{$arg['modpath']}_index", "img", $id = mpfdk($tn, null, array("uid"=>$conf['user']['uid'])))){
		mpfdk($tn, array("id"=>$id), null, array("img"=>$fn));
		$cmd = "cuneiform -l rus -o /tmp/2.txt /srv/www/vhosts/mpak.cms/". array_shift(explode(":", $conf['fs']['path'])). "/include/$fn";
		if(`$cmd`){
			echo json_encode(array(
				"id"=>$id,
				"text"=>file_get_contents("/tmp/2.txt"),
			));
		}
	}else{
		mpqw("DELETE FROM $tn WHERE id=". (int)$id);
	} exit;
}

?>