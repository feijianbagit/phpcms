<?php
defined('IN_CMS') or exit('No permission resources.');

$db = '';
$db = pc_base::load_model('content_model');
if($_GET['modelid'] && $_GET['categoryid']) {
	$model_arr = array();
	$model_arr = getcache('model','commons');
	$catid = $_GET['catid'] = intval($_GET['catid']);
	$modelid = intval($_GET['modelid']);
	$db->set_model($modelid);
	$steps = isset($_GET['steps']) ? intval($_GET['steps']) : 0;
	$status = $steps ? $steps : 99;
	if(isset($_GET['reject'])) $status = 0;
	$where = 'catid='.$_GET['categoryid'].' AND status='.$status;
	$datas = $db->listinfo($where,'id desc',$_GET['page'],$_GET['pagelength']);
	$pages = $db->pages;
	foreach ($datas as $r) {
		if($r['islink']) {
			$url='<a href="'.$r['url'].'">';
		} elseif(strpos($r['url'],'http://')!==false) {
			$url='<a href="'.$r['url'].'">';
		} else {
			$url='<a href="'.$release_siteurl.$r['url'].'">';
		}
		echo '<li>'.$url.'<span class="state tody date">'.mdate($r['updatetime']).'</span><span class="title">'.str_cut($r['title'],100).'</span></a></li>';
	}
}
?>