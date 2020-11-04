<?php
defined('IN_CMS') or exit('No permission resources.');
/**
 * 点击统计
 */
$db = '';
$db = pc_base::load_model('hits_model');
if($_GET['modelid'] && $_GET['id']) {
	$model_arr = array();
	$model_arr = getcache('model','commons');
	$modelid = intval($_GET['modelid']);
	$hitsid = 'c-'.$modelid.'-'.intval($_GET['id']);
	$r = get_count($hitsid);
	if(!$r) exit;
	extract($r);
	//hits($hitsid);
	echo "\$('#todaydowns".$_GET['id']."').html('$dayviews');";
	echo "\$('#weekdowns".$_GET['id']."').html('$weekviews');";
	echo "\$('#monthdowns".$_GET['id']."').html('$monthviews');";
} elseif($_GET['module'] && $_GET['id']) {
	$module = $_GET['module'];
	if((preg_match('/([^a-z0-9_\-]+)/i',$module))) exit('1');
	$hitsid = $module.'-'.intval($_GET['id']);
	$r = get_count($hitsid);
	if(!$r) exit;
	extract($r);
	//hits($hitsid);
}


/**
 * 获取点击数量
 * @param $hitsid
 */
function get_count($hitsid) {
	global $db;
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) return false;
	return $r;
}

/**
 * 点击次数统计
 * @param $contentid
 */
function hits($hitsid) {
	global $db;
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) return false;
	$views = $r['views'] + 1;
	$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
	$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayviews'] + 1) : 1;
	$weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekviews'] + 1) : 1;
	$monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthviews'] + 1) : 1;
	$sql = array('views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME);
	return $db->update($sql, array('hitsid'=>$hitsid));
}

?>
$('#hits<?php echo $_GET['id']?>').html('<?php echo $views?>');