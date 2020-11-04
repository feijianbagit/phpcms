<?php
// 入口文件名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);
@set_time_limit(1000);
if(phpversion() < '5.3.0') set_magic_quotes_runtime(0);
if(phpversion() < '5.2.0') exit('您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！');
include '../cms/base.php';
define('INSTALL_MODULE',true);
defined('IN_CMS') or exit('No permission resources.');
if(file_exists(CACHE_PATH.'install.lock')) exit('您已经安装过CMS,如果需要重新安装，请删除 ./caches/install.lock 文件！');
pc_base::load_sys_class('param','','','0');
pc_base::load_sys_func('global');
pc_base::load_sys_func('dir');
$steps = include CMS_PATH.'install/step.inc.php';
$step = trim($_REQUEST['step']) ? trim($_REQUEST['step']) : 1;
$pos = strpos(get_url(),'install/'.SELF);
$siteurl = substr(get_url(),0,$pos);
if(strrpos(strtolower(PHP_OS),"win") === FALSE) {
	define('ISUNIX', TRUE);
} else {
	define('ISUNIX', FALSE);
}

$mode = 0777;

switch($step)
{
    case '1': //安装许可协议
		$license = file_get_contents(CMS_PATH."install/license.txt");
		
		include CMS_PATH."install/step/step".$step.".tpl.php";

		break;
	
	case '2':  //环境检测 (FTP帐号设置）
        $PHP_GD  = '';
		if(extension_loaded('gd')) {
			if(function_exists('imagepng')) $PHP_GD .= 'png';
			if(function_exists('imagejpeg')) $PHP_GD .= ' jpg';
			if(function_exists('imagegif')) $PHP_GD .= ' gif';
		}
		$PHP_JSON = '0';
		if(extension_loaded('json')) {
			if(function_exists('json_decode') && function_exists('json_encode')) $PHP_JSON = '1';
		}
		//新加fsockopen 函数判断,此函数影响安装后会员注册及登录操作。
		if(function_exists('fsockopen')) {
			$PHP_FSOCKOPEN = '1';
		}
        $PHP_DNS = preg_match("/^[0-9.]{7,15}$/", @gethostbyname('www.baidu.com')) ? 1 : 0;
		//是否满足cms安装需求
		$is_right = (phpversion() >= '5.2.0' && extension_loaded('mysqli') && $PHP_JSON && $PHP_GD && $PHP_FSOCKOPEN) ? 1 : 0;		
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;
	
	case '3'://选择安装模块
		require CMS_PATH.'install/modules.inc.php';
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;
	
	case '4': //检测目录属性
		$selectmod = $_POST['selectmod'];
		$selectmod = isset($selectmod) ? ','.implode(',', $selectmod) : '';
		$needmod = 'admin';
		
		$chmod_file = 'chmod.txt';
		$selectmod = $needmod.$selectmod;
		$selectmods = explode(',',$selectmod);
		$files = file(CMS_PATH."install/".$chmod_file);		
		foreach($files as $_k => $file) {
			$file = str_replace('*','',$file);
			$file = trim($file);
			if(is_dir(CMS_PATH.$file)) {
				$is_dir = '1';
				$cname = '目录';
				//继续检查子目录权限，新加函数
				$write_able = writable_check(CMS_PATH.$file);
			} else {
				$is_dir = '0';
				$cname = '文件';
			}
			//新的判断
			if($is_dir =='0' && is_writable(CMS_PATH.$file)) {
				$is_writable = 1;
			} elseif($is_dir =='1' && dir_writeable(CMS_PATH.$file)){
				$is_writable = $write_able;
				if($is_writable=='0'){
					$no_writablefile = 1;
				}
			}else{
				$is_writable = 0;
 				$no_writablefile = 1;
  			}
			
			$filesmod[$_k]['file'] = $file;
			$filesmod[$_k]['is_dir'] = $is_dir;
			$filesmod[$_k]['cname'] = $cname;			
			$filesmod[$_k]['is_writable'] = $is_writable;
		}
		if(dir_writeable(CMS_PATH)) {
			$is_writable = 1;
		} else {
			$is_writable = 0;
		}
		$filesmod[$_k+1]['file'] = '网站根目录';
		$filesmod[$_k+1]['is_dir'] = '1';
		$filesmod[$_k+1]['cname'] = '目录';			
		$filesmod[$_k+1]['is_writable'] = $is_writable;						
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;

	case '5': //配置帐号 （MYSQL帐号、管理员帐号、）
		$database = pc_base::load_config('database');
		extract($database['default']);
		$selectmod = $_POST['selectmod'];
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;

	case '6': //安装详细过程
		extract($_POST);
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;

	case '7': //完成安装
		$pos = strpos(get_url(),'install/'.SELF);
		$url = substr(get_url(),0,$pos);
		//设置cms 报错信息
		set_config(array('errorlog'=>'1'),'system');			
		file_put_contents(CACHE_PATH.'install.lock', time());
		include CMS_PATH."install/step/step".$step.".tpl.php";
		break;
	
	case 'installmodule': //执行SQL
		extract($_POST);
		$GLOBALS['dbcharset'] = $dbcharset;
		$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
		$rootpath = str_replace('\\','/',dirname($PHP_SELF));	
		$rootpath = substr($rootpath,0,-7);
		$rootpath = strlen($rootpath)>1 ? $rootpath : "/";	

		if($module == 'admin') {
			mt_srand();
			$cookie_pre = random(5, 'abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ').'_';
			mt_srand();
			$auth_key = random(20, '1294567890abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ');		
			$sys_config = array('cookie_pre'=>$cookie_pre,
						'auth_key'=>$auth_key,
						'web_path'=>$rootpath,
						'errorlog'=>'0',
						'upload_url'=>$siteurl.'uploadfile/',
						'js_path'=>$siteurl.'statics/js/',
						'css_path'=>$siteurl.'statics/css/',
						'img_path'=>$siteurl.'statics/images/',
						'mobile_js_path'=>$siteurl.'mobile/statics/js/',
						'mobile_css_path'=>$siteurl.'mobile/statics/css/',
						'mobile_img_path'=>$siteurl.'mobile/statics/images/',
						'app_path'=>$siteurl,
						'mobile_path'=>$siteurl.'mobile/',
						);
			$db_config = array('hostname'=>$dbhost,
						'port'=>$dbport,
						'username'=>$dbuser,
						'password'=>$dbpw,
						'database'=>$dbname,
						'tablepre'=>$tablepre,
						'pconnect'=>$pconnect,
						'charset'=>$dbcharset,
						);
			set_config($sys_config,'system');			
			set_config($db_config,'database');
			
			$link = mysqli_connect($dbhost, $dbuser, $dbpw, null, $dbport) or die ('Not connected : ' . mysqli_connect_error());
			$version = mysqli_get_server_info($link);

			if($version > '4.1' && $dbcharset) {
				mysqli_query($link, "SET NAMES '$dbcharset'");
			}
			
			if($version > '5.0') {
				mysqli_query($link, "SET sql_mode=''");
			}
												
			if(!@mysqli_select_db($link, $dbname)){
				@mysqli_query($link, "CREATE DATABASE $dbname");
				if(@mysqli_error($link)) {
					echo 1;exit;
				} else {
					mysqli_select_db($link, $dbname);
				}
			}
			$dbfile =  'cms_db.sql';	
			if(file_exists(CMS_PATH."install/main/".$dbfile)) {
				$sql = file_get_contents(CMS_PATH."install/main/".$dbfile);
				_sql_execute($link,$sql);
				//创建网站创始人
				if(CHARSET=='gbk') $username = iconv('UTF-8','GBK',$username);
				$password_arr = password($password);
				$password = $password_arr['password'];
				$encrypt = $password_arr['encrypt'];
				$email = trim($email);
				_sql_execute($link,"INSERT INTO ".$tablepre."admin (`userid`,`username`,`password`,`roleid`,`encrypt`,`lastloginip`,`lastlogintime`,`email`,`realname`,`card`) VALUES ('1','$username','$password',1,'$encrypt','','','$email','','')");
				//设置默认站点1域名
				_sql_execute($link,"update ".$tablepre."site set `domain`='$siteurl', `mobile_domain`='".$siteurl."mobile/' where `siteid`='1'");
				
			} else {
				echo '2';//数据库文件不存在
			}							
		} else {
			//安装可选模块
			if(in_array($module,array('announce','comment','link','vote','message','mood','poster','formguide','tag','sms'))) {
				$install_module = pc_base::load_app_class('module_api','admin');
				$install_module->install($module);
			}
		}
		echo $module;
		break;
		
	//数据库测试
	case 'dbtest':
		extract($_GET);
		$link = @mysqli_connect($dbhost, $dbuser, $dbpw,null,$dbport);
		if(!$link) {
			exit('2');
		}
		$server_info = mysqli_get_server_info($link);
		if($server_info < '4.0') exit('6');
		if(!mysqli_select_db($link,$dbname)) {
			if(!@mysqli_query($link,"CREATE DATABASE `$dbname`")) exit('3');
			mysqli_select_db($link,$dbname);
		}
		$tables = array();
		$query = mysqli_query($link,"SHOW TABLES FROM `$dbname`");
		while($r = mysqli_fetch_row($query)) {
			$tables[] = $r[0];
		}
		if($tables && in_array($tablepre.'module', $tables)) {
			exit('0');
		}
		else {
			exit('1');
		}
		break;
		
	case 'cache_all':
		$cache = pc_base::load_app_class('cache_api','admin');
		$cache->cache('category');
		$cache->cache('cache_site');		 
		$cache->cache('downservers');
		$cache->cache('badword');
		$cache->cache('ipbanned');
		$cache->cache('keylink');
		$cache->cache('linkage');
		$cache->cache('position');
		$cache->cache('admin_role');
		$cache->cache('urlrule');
		$cache->cache('module');
		$cache->cache('sitemodel');
		$cache->cache('workflow');
		$cache->cache('dbsource');
		$cache->cache('member_group');
		$cache->cache('membermodel');
		$cache->cache('type','search');
		$cache->cache('special');
		$cache->cache('setting');
		$cache->cache('database');
		$cache->cache('member_setting');
		$cache->cache('member_model_field');
		$cache->cache('search_setting');
		break;

}

function format_textarea($string) {
	$chars = 'utf-8';
	if(CHARSET=='gbk') $chars = 'gb2312';
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,$chars)));
}

function _sql_execute($link,$sql,$r_tablepre = '',$s_tablepre = 'cms_') {
    $sqls = _sql_split($link,$sql,$r_tablepre,$s_tablepre);
	if(is_array($sqls))
    {
		foreach($sqls as $sql)
		{
			if(trim($sql) != '')
			{
				mysqli_query($link,$sql);
			}
		}
	}
	else
	{
		mysqli_query($link,$sqls);
	}
	return true;
}

function _sql_split($link,$sql,$r_tablepre = '',$s_tablepre='cms_') {
	global $dbcharset,$tablepre;
	$r_tablepre = $r_tablepre ? $r_tablepre : $tablepre;
	if(mysqli_get_server_info($link) > '4.1' && $dbcharset)
	{
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".$dbcharset,$sql);
	}
	$sql = str_replace('phpcms_', $s_tablepre, $sql);
	if($r_tablepre != $s_tablepre) $sql = str_replace($s_tablepre, $r_tablepre, $sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}

function dir_writeable($dir) {
	$writeable = 0;
	if(is_dir($dir)) {  
        if($fp = @fopen("$dir/chkdir.test", 'w')) {
            @fclose($fp);      
            @unlink("$dir/chkdir.test"); 
            $writeable = 1;
        } else {
            $writeable = 0; 
        } 
	}
	return $writeable;
}

function writable_check($path){
	$dir = '';
	$is_writable = '1';
	if(!is_dir($path)){return '0';}
	$dir = opendir($path);
 	while (($file = readdir($dir)) !== false){
		if($file!='.' && $file!='..'){
			if(is_file($path.'/'.$file)){
				//是文件判断是否可写，不可写直接返回0，不向下继续
				if(!is_writable($path.'/'.$file)){
 					return '0';
				}
			}else{
				//目录，循环此函数,先判断此目录是否可写，不可写直接返回0 ，可写再判断子目录是否可写 
				$dir_wrt = dir_writeable($path.'/'.$file);
				if($dir_wrt=='0'){
					return '0';
				}
   				$is_writable = writable_check($path.'/'.$file);
 			}
		}
 	}
	return $is_writable;
}

function set_config($config,$cfgfile) {
	if(!$config || !$cfgfile) return false;
	$configfile = CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.$cfgfile.'.php';
	if(!is_writable($configfile)) showmessage('Please chmod '.$configfile.' to 0777 !');
	$pattern = $replacement = array();
	foreach($config as $k=>$v) {
			$v = trim($v);
			$configs[$k] = $v;
			$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
        	$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";							
	}
	$str = file_get_contents($configfile);
	$str = preg_replace($pattern, $replacement, $str);
	return file_put_contents($configfile, $str);		
}

function remote_file_exists($url_file){
	$headers = get_headers($url_file);
	if (!preg_match("/200/", $headers[0])){
		return false;
	}
	return true;
}
?>