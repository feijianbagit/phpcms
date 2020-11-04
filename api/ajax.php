<?php
defined('IN_CMS') or exit('No permission resources.');

$db = '';
$db = pc_base::load_model('content_model');
$hits_db = pc_base::load_model('hits_model');
//$modelid = $_GET['modelid'] ? $_GET['modelid'] : 1;
//$categoryid = $_GET['categoryid'] ? $_GET['categoryid'] : 6;
$pagelength = $_GET['pagelength'] ? $_GET['pagelength'] : 30;
if($_GET['modelid'] && $_GET['categoryid']) {
	$modelid = intval($_GET['modelid']);
	$db->set_model($modelid);
	$steps = isset($_GET['steps']) ? intval($_GET['steps']) : 0;
	$status = $steps ? $steps : 99;
	if(isset($_GET['reject'])) $status = 0;
	$where = 'catid='.$_GET['categoryid'].' AND status='.$status;
	if($_GET['dis']) {
		$where .= ' AND suoshucs="'.$_GET['dis'].'"';
	}
	if($_GET['spc']) {
		$where .= ' AND zhuanye="'.$_GET['spc'].'"';
	}
	if($_GET['score']) {
		$where .= ' AND fenshu="'.$_GET['score'].'"';
	}
	if($_GET['my_city']) {
		$where .= ' AND suoshucs="'.$_GET['my_city'].'"';
	}
	if($_GET['my_kaodian']) {
		$where .= ' AND suoshucs="'.$_GET['my_kaodian'].'"';
	}
	//if($_GET['my_chengji']) {
		//$where .= ' AND chengji="'.$_GET['my_chengji'].'"';
	//}
	//if($_GET['my_fav']) {
		//$where .= ' AND fax="'.$_GET['my_fav'].'"';
	//}
	if(!empty($_GET['key'])) {
		$where .= " AND `title` like '%".$_GET['key']."%'";
	}
	$datas = $db->listinfo($where,'id desc',$_GET['page'],$pagelength);
	$countr = $db->get_one($where, "COUNT(*) AS num");
	$pages = $db->pages;
	echo '[';
	foreach ($datas as $r) {
		if($r['islink']) {
			$url=$r['url'];
		} elseif(strpos($r['url'],'http://')!==false) {
			$url=$r['url'];
		} else {
			$url=$release_siteurl.$r['url'];
		}
		if($r['thumb']) {
			$thumb=$r['thumb'];
		} else {
			$thumb=IMG_PATH.'nopic.gif';
		}
		$db->set_model($modelid);
		$where = 'catid=8 AND `inputtime` > "'.strtotime(date("Y")-1 . '-' . date("m") . '-' . date("d")).'" AND `inputtime` < "'.strtotime(date("Y")+1 . '-' . date("m") . '-' . date("d")).'" AND status=99';
		$zsjzr = $db->listinfo($where,'id desc',$_GET['page'],$pagelength);
		if(count($zsjzr)>0) {
			$subscript=3;
		} else {
			$subscript=2;
		}
		$hitsid = 'c-'.$modelid.'-'.intval($r['id']);
		$hitsr = $hits_db->get_one(array('hitsid'=>$hitsid));
		echo '{"id":"'.$r['id'].'","url":"'.$url.'","name":"'.$r['title'].'","logo":"'.$thumb.'","tag":"","score_type":"400,500","browse_number":"'.$hitsr['views'].'","focus_number":"824","subscript":"'.$subscript.'"},';
	}
	echo '{"max":"'.$countr['num'].'"}]';
}
?>