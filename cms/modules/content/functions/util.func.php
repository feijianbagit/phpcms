<?php
/**
 * 分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $pageurls 链接地址
 * @return 分页
 */
function content_pages($num, $curr_page, $pageurls = array()) {
	$input = pc_base::load_sys_class('input');
	$multipage = '';
	$first_url = $pageurls[1][0];
	$multipage = $input->page($pageurls[2][0], $num, 1, $curr_page, $first_url);
	return $multipage;
}
/**
 * 生成静态栏目分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $pageurls 链接地址
 * @return 分页
 */
function category_pages($num, $curr_page, $perpage = 10, $pageurls = array()) {
	$input = pc_base::load_sys_class('input');
	$multipage = '';
	$first_url = $pageurls[0];
	$multipage = $input->page($pageurls[1], $num, $perpage, $curr_page, $first_url);
	return $multipage;
}
?>