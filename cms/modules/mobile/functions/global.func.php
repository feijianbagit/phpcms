<?php
/**
 * 生成Tag URL
 */
function mobile_tag_url($keyword, $siteid){
	$input = pc_base::load_sys_class('input');
	$sitelist = getcache('sitelist','commons');
	!$siteid && $siteid = $input->get('siteid') && (intval($input->get('siteid')) > 0) ? intval(trim($input->get('siteid'))) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=tag&a=lists&tag='.urlencode($keyword).'&siteid='.$siteid;
}
/**
 * 解析手机分类url路径
 */
function list_url($url, $catid = '') {
	if (!$url || !$catid) {
		return '';
	}
	$input = pc_base::load_sys_class('input');
	$sitelist = getcache('sitelist','commons');
	$siteids = getcache('category_content','commons');
	$catid && $siteid = $siteids[$catid];
	!$catid && $siteid = $input->get('siteid') && (intval($input->get('siteid')) > 0) ? intval(trim($input->get('siteid'))) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	if ($sitelist[$siteid]['mobilehtml']==1) {
		return str_replace(array((string)$sitelist[$siteid]['domain'], 'm=content'), array((string)$sitelist[$siteid]['mobile_domain'], 'm=mobile'), (string)$url);
	} else {
		return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=lists&catid='.$catid;
	}
}

/**
 * 解析手机内容url路径
 */
function show_url($url, $catid = '', $id = '') {
	if (!$url || !$catid || !$id) {
		return '';
	}
	$input = pc_base::load_sys_class('input');
	$sitelist = getcache('sitelist','commons');
	$siteids = getcache('category_content','commons');
	$catid && $siteid = $siteids[$catid];
	!$catid && $siteid = $input->get('siteid') && (intval($input->get('siteid')) > 0) ? intval(trim($input->get('siteid'))) : (param::get_cookie('siteid') ? param::get_cookie('siteid') : 1);
	$setting = dr_string2array(dr_cat_value($catid, 'setting'));
	$content_ishtml = $setting['content_ishtml'];
	if ($sitelist[$siteid]['mobilehtml']==1) {
		if ($content_ishtml) {
			if (!$sitelist[$siteid]['mobilemode']) {
				return SYS_MOBILE_ROOT.$url;
			} else {
				return substr($sitelist[$siteid]['mobile_domain'], 0, -1).$url;
			}
		}
		return str_replace(array((string)$sitelist[$siteid]['domain'], 'm=content'), array((string)$sitelist[$siteid]['mobile_domain'], 'm=mobile'), (string)$url);
	} else {
		return $sitelist[$siteid]['mobile_domain'].'index.php?m=mobile&c=index&a=show&catid='.$catid.'&id='.$id;
	}
}

/**
 * 过滤内容为wml格式
 */
function wml_strip($string) {
    $string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', '&'), array(' ', '&', '"', "'", '“', '”', '—', '{<}', '{>}', '·', '…', '&amp;'), $string);
	return str_replace(array('{<}', '{>}'), array('&lt;', '&gt;'), $string);
}

function strip_selected_tags($text) {
    $tags = array('em','font','h1','h2','h3','h4','h5','h6','hr','i','ins','li','ol','p','pre','small','span','strike','strong','sub','sup','table','tbody','td','tfoot','th','thead','tr','tt','u','div','span');
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text)) : (array)$tags;
    foreach ($tags as $tag){
        if( preg_match_all( '/<'.$tag.'[^>]*>([^<]*)<\/'.$tag.'>/iu', $text, $found) ){
            $text = str_replace($found[0],$found[1],$text);
        }
    }
    return $text;
}
?>