<?php
/**
 *  index.php CMS 入口
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-1
 */
//declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN'); //防止被站外加入iframe中浏览

// 入口文件名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

 //CMS根目录
define('CMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('PHPCMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

include CMS_PATH.'/cms/base.php';

// 开始，自动进入安装界面监测代码 
if (!is_file(CACHE_PATH.'install.lock')) {
	header("Location: install");
	exit;
}
// 结束，安装之后可以删除此段代码

if (file_exists('install') && is_file(CACHE_PATH.'install.lock')) {
	pc_base::load_sys_func('dir');
	dir_delete('install');
}

pc_base::creat_app();
?>