<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
	public function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('admin_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->panel_db = pc_base::load_model('admin_panel_model');
	}
	
	public function init () {
		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$rolename = $roles[$_SESSION['roleid']];
		$site = pc_base::load_app_class('sites');
		$sitelist = $site->get_list($_SESSION['roleid']);
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		$site_model = param::get_cookie('site_model');
		include $this->admin_tpl('index');
	}
	
	public function login() {
		if(isset($_GET['dosubmit'])) {

			$username = isset($_POST['username']) && trim($_POST['username']) ? trim($_POST['username']) : exit(json_encode(array('code' => 0, 'msg' => L('nameerror'))));
			$code = isset($_POST['code']) && trim($_POST['code']) ? trim($_POST['code']) : exit(json_encode(array('code' => 0, 'msg' => L('input_code'))));
			if ($_SESSION['code'] != strtolower($code)) {
				$_SESSION['code'] = '';
				exit(json_encode(array('code' => 4, 'msg' => L('code_error'))));
			}
			$_SESSION['code'] = '';
			if(!is_username($username)){
				exit(json_encode(array('code' => 2, 'msg' => L('username_illegal'))));
			}
			//密码错误剩余重试次数
			$this->times_db = pc_base::load_model('times_model');
			$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
			$maxloginfailedtimes = getcache('common','commons');
			$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];

			if($rtime['times'] >= $maxloginfailedtimes) {
				$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
				if($minute>0) exit(json_encode(array('code' => 0, 'msg' => str_replace('{minute}',$minute,L('wait_1_hour')))));
			}
			//查询帐号
			$r = $this->db->get_one(array('username'=>$username));
			if(!$r) exit(json_encode(array('code' => 0, 'msg' => L('user_not_exist'))));
			$password = md5(md5(trim($_POST['password'])).$r['encrypt']);
			
			if($r['password'] != $password) {
				$ip = ip();
				if($rtime && $rtime['times'] < $maxloginfailedtimes) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
				} else {
					$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
					$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}
				exit(json_encode(array('code' => 3, 'msg' => str_replace('{times}',$times,L('password_error')))));
			}
			$this->times_db->delete(array('username'=>$username));
			
			$this->db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$_SESSION['userid'] = $r['userid'];
			$_SESSION['roleid'] = $r['roleid'];
			$_SESSION['pc_hash'] = random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789');
			$_SESSION['lock_screen'] = 0;
			$default_siteid = self::return_siteid();
			$cookie_time = SYS_TIME+86400*30;
			if(!$r['lang']) $r['lang'] = 'zh-cn';
			param::set_cookie('admin_username',$username,$cookie_time);
			param::set_cookie('siteid', $default_siteid,$cookie_time);
			param::set_cookie('userid', $r['userid'],$cookie_time);
			param::set_cookie('admin_email', $r['email'],$cookie_time);
			param::set_cookie('sys_lang', $r['lang'],$cookie_time);
			exit(json_encode(array('code' => 1, 'msg' => L('login_success'), 'url' => '?m=admin&c=index&pc_hash='.$_SESSION['pc_hash'])));
		} else {
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('login');
		}
	}
	
	public function public_card() {
		$username = $_SESSION['card_username'] ? $_SESSION['card_username'] :  showmessage(L('nameerror'),HTTP_REFERER);
		$r = $this->db->get_one(array('username'=>$username));
		if(!$r) showmessage(L('user_not_exist'),'?m=admin&c=index&a=login');
		if (isset($_GET['dosubmit'])) {
			pc_base::load_app_class('card', 'admin', 0);
			$result = card::verification($r['card'], $_POST['code'], $_POST['rand']);
			$_SESSION['card_verif'] = 1;
			header("location:?m=admin&c=index&a=login&dosubmit=1&card=1");
			exit;
		}
		pc_base::load_app_class('card', 'admin', 0);
		$rand = card::authe_rand($r['card']);
		include $this->admin_tpl('login_card');
	}
	
	// 子站客户端自动登录
	public function fclient() {

		if (!is_file(CMS_PATH.'api/fclient/sync.php')) {
			showmessage(L('fclient_not_exist'));
		}

		$sync = require CMS_PATH.'api/fclient/sync.php';
		if (!$_GET['id'] || !$_GET['sync']) {
			showmessage(L('fclient_not_sn'));
		} elseif ($_GET['id'] != md5($sync['id'])) {
			showmessage(L('fclient_not_id'));
		} elseif ($_GET['sync'] != $sync['sn']) {
			showmessage(L('fclient_sn_exist'));
		}

		$this->role_db = pc_base::load_model('admin_role_model');
		$role = $this->role_db->get_one(array('disabled'=>0),'roleid','roleid asc');
		$member = $this->db->get_one(array('roleid'=>$role['roleid']),'*','roleid asc');
		if (!$member) {
			showmessage(L('fclient_user_not_role'));
		}

		$_SESSION['userid'] = $member['userid'];
		$_SESSION['roleid'] = $member['roleid'];
		$_SESSION['pc_hash'] = random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789');
		$_SESSION['lock_screen'] = 0;
		$default_siteid = self::return_siteid();
		$cookie_time = SYS_TIME+86400*30;
		if(!$member['lang']) $member['lang'] = 'zh-cn';
		param::set_cookie('admin_username',$member['username'],$cookie_time);
		param::set_cookie('siteid', $default_siteid,$cookie_time);
		param::set_cookie('userid', $member['userid'],$cookie_time);
		param::set_cookie('admin_email', $member['email'],$cookie_time);
		param::set_cookie('sys_lang', $member['lang'],$cookie_time);

		showmessage(L('fclient_sn_succ'),'?m=admin&c=index');
	}
	
	public function public_logout() {
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		param::set_cookie('admin_username','');
		param::set_cookie('userid',0);
		
		showmessage(L('logout_success'),'?m=admin&c=index&a=login');
	}
	
	//左侧菜单
	public function public_menu_left() {
		$menuid = intval($_GET['menuid']);
		$datas = admin::admin_menu($menuid);
		if (isset($_GET['parentid']) && $parentid = intval($_GET['parentid']) ? intval($_GET['parentid']) : 10) {
			foreach($datas as $_value) {
	        	if($parentid==$_value['id']) {
	        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        		
	        	} else {
	        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        	}      	
	        }
		} else {
			include $this->admin_tpl('left');
		}
		
	}
	//当前位置
	public function public_current_pos() {
		echo admin::current_pos($_GET['menuid']);
		exit;
	}
	
	/**
	 * 设置站点ID COOKIE
	 */
	public function public_set_siteid() {
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : exit('0'); 
		param::set_cookie('siteid', $siteid);
		exit('1');
	}
	
	public function public_ajax_add_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$menuarr = $this->menu_db->get_one(array('id'=>$menuid));
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].'&'.$menuarr['data'];
		$data = array('menuid'=>$menuid, 'userid'=>$_SESSION['userid'], 'name'=>$menuarr['name'], 'url'=>$url, 'datetime'=>SYS_TIME);
		$this->panel_db->insert($data, '', 1);
		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url'].'&menuid='.$v['menuid']."&pc_hash=".$_SESSION['pc_hash']."'>".L($v['name'])."</a>  <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	
	public function public_ajax_delete_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$this->panel_db->delete(array('menuid'=>$menuid, 'userid'=>$_SESSION['userid']));

		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url']."&pc_hash=".$_SESSION['pc_hash']."'>".L($v['name'])."</a> <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	public function public_main() {
		pc_base::load_app_func('global');
		pc_base::load_app_func('admin');
		define('PC_VERSION', pc_base::load_config('version','pc_version'));
		define('PC_RELEASE', pc_base::load_config('version','pc_release'));
		define('CMS_VERSION', pc_base::load_config('version','cms_version'));
		define('CMS_RELEASE', pc_base::load_config('version','cms_release'));

		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$userid = $_SESSION['userid'];
		$rolename = $roles[$_SESSION['roleid']];
		$r = $this->db->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = $this->db->version();
		$show_header = $show_pc_hash = 1;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(PC_PATH.'base.php');
		$common_cache = getcache('common','commons');
		$logsize_warning = errorlog_size() > $common_cache['errorlog_size'] ? '1' : '0';
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), '*',20 , 'datetime');
		$programmer = '（zhaoxunzhiyin）';
 		$designer = '找寻知音';
 		$qqgroup = '551419699';
 		$qq = '297885395';
 		$tel = '17684313488';
		ob_start();
		include $this->admin_tpl('main');
		$data = ob_get_contents();
		ob_end_clean();
		system_information($data);
	}
	/**
	 * 维持 session 登陆状态
	 */
	public function public_session_life() {
		$userid = $_SESSION['userid'];
		return true;
	}
	/**
	 * 锁屏
	 */
	public function public_lock_screen() {
		$_SESSION['lock_screen'] = 1;
	}
	public function public_login_screenlock() {
		if(empty($_GET['lock_password'])) showmessage(L('password_can_not_be_empty'));
		//密码错误剩余重试次数
		$this->times_db = pc_base::load_model('times_model');
		$username = param::get_cookie('admin_username');
		$maxloginfailedtimes = getcache('common','commons');
		$maxloginfailedtimes = (int)$maxloginfailedtimes['maxloginfailedtimes'];
		
		$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
		if($rtime['times'] > $maxloginfailedtimes-1) {
			$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
			exit('3');
		}
		//查询帐号
		$r = $this->db->get_one(array('userid'=>$_SESSION['userid']));
		$password = md5(md5($_GET['lock_password']).$r['encrypt']);
		if($r['password'] != $password) {
			$ip = ip();
			if($rtime && $rtime['times']<$maxloginfailedtimes) {
				$times = $maxloginfailedtimes-intval($rtime['times']);
				$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
			} else {
				$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
				$times = $maxloginfailedtimes;
			}
			exit('2|'.$times);//密码错误
		}
		$this->times_db->delete(array('username'=>$username));
		$_SESSION['lock_screen'] = 0;
		exit('1');
	}
	
	//后台站点地图
	public function public_map() {
		 $array = admin::admin_menu(0);
		 $menu = array();
		 foreach ($array as $k=>$v) {
		 	$menu[$v['id']] = $v;
		 	$menu[$v['id']]['childmenus'] = admin::admin_menu($v['id']);
		 }
		 $show_header = true;
		 include $this->admin_tpl('map');
	}
	
	/**
	 * 
	 * 读取盛大接扣获取appid和secretkey
	 */
	public function public_snda_status() {
		//引入盛大接口
		if(!strstr(pc_base::load_config('snda','snda_status'), '|')) {
			$this->site_db = pc_base::load_model('site_model');
			$uuid_arr = $this->site_db->get_one(array('siteid'=>1), 'uuid');
			$uuid = $uuid_arr['uuid'];
			$snda_check_url = "http://open.sdo.com/cms?cmsid=".$uuid."&sitedomain=".$_SERVER['SERVER_NAME'];

			$snda_res_json = @file_get_contents($snda_check_url);
			$snda_res = json_decode($snda_res_json, 1);

			if(!isset($snda_res[err]) && !empty($snda_res['appid'])) {
				$appid = $snda_res['appid'];
				$secretkey = $snda_res['secretkey'];
				set_config(array('snda_status'=>$appid.'|'.$secretkey), 'snda');
			}
		}
	}

	/**
	 * @设置网站模式 设置了模式后，后台仅出现在此模式中的菜单
	 */
	public function public_set_model() {
		$model = $_GET['site_model'];
		if (!$model) {
			param::set_cookie('site_model','');
		} else {
			$models = pc_base::load_config('model_config');
			if (in_array($model, array_keys($models))) {
				param::set_cookie('site_model', $model);
			} else {
				param::set_cookie('site_model','');
			}
		}
		$menudb = pc_base::load_model('menu_model');
		$where = array('parentid'=>0,'display'=>1);
		if ($model) {
			$where[$model] = 1;
 		}
		$result =$menudb->select($where,'id',1000,'listorder ASC');
		$menuids = array();
		if (is_array($result)) {
			foreach ($result as $r) {
				$menuids[] = $r['id'];
			}
		}
		exit(json_encode($menuids));
	}

}
?>