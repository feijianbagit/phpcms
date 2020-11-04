<?php
defined('IN_CMS') or exit('No permission resources.'); 
include CMS_PATH.'/api/phpqrcode.php';

$content_db = pc_base::load_model('content_model');
if ($_GET['callback']=='jsonpCallback') {
	if($_GET['modelid'] && $_GET['id']) {
		$content_db->set_model($_GET['modelid']);
		$data = $content_db->get_one(array('id'=>$_GET['id'],'status'=>99));
		$logo = APP_PATH.'statics/images/erweimalogo.png';
		$png = APP_PATH.'api.php?op=erweima&id='.$_GET['id'].'&modelid='.$_GET['modelid'].'&callback=jsonpCallback';
		$QR = imagecreatefrompng($png);
		if($logo !== FALSE)
		{
			$logo = imagecreatefromstring(file_get_contents($logo));
			 
			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR);
			 
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			 
			$logo_qr_width = $QR_width / 5;
			$scale = $logo_width / $logo_qr_width;
			$logo_qr_height = $logo_height / $scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			 
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		header('Content-type: image/png');
		imagepng($QR);
		imagedestroy($QR);
	}
}
?>