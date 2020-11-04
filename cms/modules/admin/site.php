<?php
defined('IN_CMS') or exit('No permission resources.');
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('form','',0);
class site extends admin {
	private $db;
	public function __construct() {
		$this->db = pc_base::load_model('site_model');
		parent::__construct();
		$this->locate = array(
			'left-top' => L('site_att_watermark_pos_1'),
			'center-top' => L('site_att_watermark_pos_2'),
			'right-top' => L('site_att_watermark_pos_3'),

			'left-middle' => L('site_att_watermark_pos_4'),
			'center-middle' => L('site_att_watermark_pos_5'),
			'right-middle' => L('site_att_watermark_pos_6'),

			'left-bottom' => L('site_att_watermark_pos_7'),
			'center-bottom' => L('site_att_watermark_pos_8'),
			'right-bottom' => L('site_att_watermark_pos_9'),
		);
		$files = $this->file_map(CMS_PATH.'statics/images/water/font/', 1);
		foreach ($files as $t) {
			if (substr($t, -3) == 'ttf') {
				$this->waterfont[] = $t;
			}
		}
		$waterfiles = $this->file_map(CMS_PATH.'statics/images/water/', 1);
		foreach ($waterfiles as $t) {
			if (substr($t, -3) == 'png' || substr($t, -3) == 'gif' || substr($t, -3) == 'jpg' || substr($t, -3) == 'jpeg') {
				$this->waterfile[] = $t;
			}
		}
	}
	
	public function init() {
		$tablename = $this->db->db_tablepre.'site';
		if (!$this->db->field_exists('ishtml')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `ishtml` tinyint(1) unsigned NOT NULL DEFAULT \'1\' COMMENT \'首页静态\' AFTER `domain`');
		}
		if (!$this->db->field_exists('mobileauto')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `mobileauto` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'自动识别\' AFTER `ishtml`');
		}
		if (!$this->db->field_exists('mobilehtml')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `mobilehtml` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'生成静态\' AFTER `mobileauto`');
		}
		if (!$this->db->field_exists('not_pad')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `not_pad` tinyint(1) unsigned NOT NULL DEFAULT \'0\' COMMENT \'将平板端排除\' AFTER `mobilehtml`');
		}
		if (!$this->db->field_exists('mobile_domain')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `mobile_domain` char(255) DEFAULT \'\' COMMENT \'手机域名\' AFTER `not_pad`');
		}
		if (!$this->db->field_exists('style')) {
			$this->db->query('ALTER TABLE `'.$tablename.'` ADD `style` varchar(5) NOT NULL COMMENT \'\' AFTER `setting`');
		}
		$total = $this->db->count();
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$pagesize = 20;
		$offset = ($page - 1) * $pagesize;
		$list = $this->db->select('', '*', $offset.','.$pagesize);
		$pages = pages($total, $page, $pagesize);
		$show_dialog = true;
		$big_menu = array('javascript:artdialog(\'content_id\',\'?m=admin&c=site&a=add\',\''.L('add_site').'\',\'60%\',\'60%\');void(0);', L('add_site'));
		include $this->admin_tpl('site_list');
	}
	
	public function add() {
		header("Cache-control: private"); 
		if (isset($_GET['show_header'])) $show_header = 1;
		if (isset($_POST['dosubmit'])) {
			$name = isset($_POST['name']) && trim($_POST['name']) ? trim($_POST['name']) : showmessage(L('site_name').L('empty'));
			$dirname = isset($_POST['dirname']) && trim($_POST['dirname']) ? strtolower(trim($_POST['dirname'])) : showmessage(L('site_dirname').L('empty'));
			$domain = isset($_POST['domain']) && trim($_POST['domain']) ? trim($_POST['domain']) : '';
			$ishtml = isset($_POST['ishtml']) && trim($_POST['ishtml']) ? trim($_POST['ishtml']) : 0;
			$mobileauto = isset($_POST['mobileauto']) && trim($_POST['mobileauto']) ? trim($_POST['mobileauto']) : 0;
			$mobilehtml = isset($_POST['mobilehtml']) && trim($_POST['mobilehtml']) ? trim($_POST['mobilehtml']) : 0;
			$not_pad = isset($_POST['not_pad']) && trim($_POST['not_pad']) ? trim($_POST['not_pad']) : 0;
			$mobile_domain = isset($_POST['mobile_domain']) && trim($_POST['mobile_domain']) ? trim($_POST['mobile_domain']) : '';
			$site_title = isset($_POST['site_title']) && trim($_POST['site_title']) ? trim($_POST['site_title']) : '';
			$keywords = isset($_POST['keywords']) && trim($_POST['keywords']) ? trim($_POST['keywords']) : '';
			$description = isset($_POST['description']) && trim($_POST['description']) ? trim($_POST['description']) : '';
			$release_point = isset($_POST['release_point']) ? $_POST['release_point'] : '';
			$template = isset($_POST['template']) && !empty($_POST['template']) ? $_POST['template'] : showmessage(L('please_select_a_style'));
			$default_style = isset($_POST['default_style']) && !empty($_POST['default_style']) ? $_POST['default_style'] : showmessage(L('please_choose_the_default_style'));			   
			if ($this->db->get_one(array('name'=>$name), 'siteid')) {
				showmessage(L('site_name').L('exists'));
			} 
			if (!preg_match('/^\\w+$/i', $dirname)) {
				showmessage(L('site_dirname').L('site_dirname_err_msg'));
			}
			if ($this->db->get_one(array('dirname'=>$dirname), 'siteid')) {
				showmessage(L('site_dirname').L('exists'));
			}
			if (!empty($domain) && !preg_match('/http(s?):\/\/(.+)\/$/i', $domain)) {
				showmessage(L('site_domain').L('site_domain_ex2'));
			}
			if (!empty($domain) && $this->db->get_one(array('domain'=>$domain), 'siteid')) {
				showmessage(L('site_domain').L('exists'));
			}
			if (!empty($mobile_domain) && !preg_match('/http(s?):\/\/(.+)\/$/i', $mobile_domain)) {
				showmessage(L('site_domain').L('site_domain_ex2'));
			}
			if (!empty($mobile_domain) && $this->db->get_one(array('mobile_domain'=>$mobile_domain), 'siteid')) {
				showmessage(L('site_domain').L('exists'));
			}
			if (!empty($release_point) && is_array($release_point)) {
				if (count($release_point) > 4) {
					showmessage(L('release_point_configuration').L('most_choose_four'));
				}
				$s = '';
				foreach ($release_point as $key=>$val) {
					if($val) $s.= $s ? ",$val" : $val;
				}
				$release_point = $s;
				unset($s);
			} else {
				$release_point = '';
			}
			if (!empty($template) && is_array($template)) {
				$template = implode(',', $template);
			} else {
				$template = '';
			}
			$setting = trim(array2string($_POST['setting']));
			$_POST['info']['name'] = $name;
			$_POST['info']['dirname'] = $dirname;
			$_POST['info']['domain'] = $domain;
			$_POST['info']['ishtml'] = $ishtml;
			$_POST['info']['mobileauto'] = $mobileauto;
			$_POST['info']['mobilehtml'] = $mobilehtml;
			$_POST['info']['not_pad'] = $not_pad;
			$_POST['info']['mobile_domain'] = $mobile_domain;
			$_POST['info']['site_title'] = $site_title;
			$_POST['info']['keywords'] = $keywords;
			$_POST['info']['description'] = $description;
			$_POST['info']['release_point'] = $release_point;
			$_POST['info']['template'] = $template;
			$_POST['info']['setting'] = $setting;
			$_POST['info']['default_style'] = $default_style;
			require_once CACHE_MODEL_PATH.'content_input.class.php';
			require_once CACHE_MODEL_PATH.'content_update.class.php';
			$content_input = new content_input(0);
			$inputinfo = $content_input->get($_POST['info']);
			$systeminfo = $inputinfo['system'];
			$siteid = $this->db->insert($_POST['info'], true);
			$class_site = pc_base::load_app_class('sites');
			$class_site->set_cache();
			$this->db->update($systeminfo,array('siteid'=>$siteid));
			showmessage(L('operation_success'), '?m=admin&c=site&a=init', '', 'content_id');
		} else {
			$show_dialog = '';
			$locate = $this->locate;
			$waterfont = $this->waterfont;
			$waterfile = $this->waterfile;
			require CACHE_MODEL_PATH.'content_form.class.php';
			$content_form = new content_form(0);
			$forminfos = $content_form->get();
 			$formValidator = $content_form->formValidator;
 			$checkall = $content_form->checkall;
			$release_point_db = pc_base::load_model('release_point_model');
			$release_point_list = $release_point_db->select('', 'id, name');
			$show_validator = $show_scroll = $show_header = true;
			$template_list = template_list();
			include $this->admin_tpl('site_add');
		}
	}
	
	public function del() {
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : showmessage(L('illegal_parameters'), HTTP_REFERER);
		if($siteid==1) showmessage(L('operation_failure'), HTTP_REFERER);
		if ($this->db->get_one(array('siteid'=>$siteid))) {
			if ($this->db->delete(array('siteid'=>$siteid))) {
				$keyword_db = pc_base::load_model('keyword_model');
				$keyword_data_db = pc_base::load_model('keyword_data_model');
				$keyword_db->delete(array('siteid'=>$siteid));
				$keyword_data_db->delete(array('siteid'=>$siteid));
				$class_site = pc_base::load_app_class('sites');
				$class_site->set_cache();
				showmessage(L('operation_success'), HTTP_REFERER);
			} else {
				showmessage(L('operation_failure'), HTTP_REFERER);
			}
		} else {
			showmessage(L('notfound'), HTTP_REFERER);
		}
	}
	
	public function edit() {
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : showmessage(L('illegal_parameters'), HTTP_REFERER);
		if ($data = $this->db->get_one(array('siteid'=>$siteid))) {
			if (isset($_POST['dosubmit'])) {
				$name = isset($_POST['name']) && trim($_POST['name']) ? trim($_POST['name']) : showmessage(L('site_name').L('empty'));
				$dirname = isset($_POST['dirname']) && trim($_POST['dirname']) ? strtolower(trim($_POST['dirname'])) : ($siteid == 1 ? '' :showmessage(L('site_dirname').L('empty')));
				$domain = isset($_POST['domain']) && trim($_POST['domain']) ? trim($_POST['domain']) : '';
				$ishtml = isset($_POST['ishtml']) && trim($_POST['ishtml']) ? trim($_POST['ishtml']) : 0;
				$mobileauto = isset($_POST['mobileauto']) && trim($_POST['mobileauto']) ? trim($_POST['mobileauto']) : 0;
				$mobilehtml = isset($_POST['mobilehtml']) && trim($_POST['mobilehtml']) ? trim($_POST['mobilehtml']) : 0;
				$not_pad = isset($_POST['not_pad']) && trim($_POST['not_pad']) ? trim($_POST['not_pad']) : 0;
				$mobile_domain = isset($_POST['mobile_domain']) && trim($_POST['mobile_domain']) ? trim($_POST['mobile_domain']) : '';
				$site_title = isset($_POST['site_title']) && trim($_POST['site_title']) ? trim($_POST['site_title']) : '';
				$keywords = isset($_POST['keywords']) && trim($_POST['keywords']) ? trim($_POST['keywords']) : '';
				$description = isset($_POST['description']) && trim($_POST['description']) ? trim($_POST['description']) : '';
				$release_point = isset($_POST['release_point']) ? $_POST['release_point'] : '';
				$template = isset($_POST['template']) && !empty($_POST['template']) ? $_POST['template'] : showmessage(L('please_select_a_style'));
				$default_style = isset($_POST['default_style']) && !empty($_POST['default_style']) ? $_POST['default_style'] : showmessage(L('please_choose_the_default_style'));	
				if ($data['name'] != $name && $this->db->get_one(array('name'=>$name), 'siteid')) {
					showmessage(L('site_name').L('exists'));
				}
				if ($siteid != 1) {
					if (!preg_match('/^\\w+$/i', $dirname)) {
						showmessage(L('site_dirname').L('site_dirname_err_msg'));
					}
					if ($data['dirname'] != $dirname && $this->db->get_one(array('dirname'=>$dirname), 'siteid')) {
						showmessage(L('site_dirname').L('exists'));
					}
				} 
				
				if (!empty($domain) && !preg_match('/http(s?):\/\/(.+)\/$/i', $domain)) {
					showmessage(L('site_domain').L('site_domain_ex2'));
				}
				if (!empty($domain) && $data['domain'] != $domain && $this->db->get_one(array('domain'=>$domain), 'siteid')) {
					showmessage(L('site_domain').L('exists'));
				}
				if (!empty($mobile_domain) && !preg_match('/http(s?):\/\/(.+)\/$/i', $mobile_domain)) {
					showmessage(L('site_domain').L('site_domain_ex2'));
				}
				if (!empty($mobile_domain) && $data['mobile_domain'] != $mobile_domain && $this->db->get_one(array('mobile_domain'=>$mobile_domain), 'siteid')) {
					showmessage(L('site_domain').L('exists'));
				}
				if (!empty($release_point) && is_array($release_point)) {
					if (count($release_point) > 4) {
						showmessage(L('release_point_configuration').L('most_choose_four'));
					}
					$s = '';
					foreach ($release_point as $key=>$val) {
						if($val) $s.= $s ? ",$val" : $val;
					}
					$release_point = $s;
					unset($s);
				} else {
					$release_point = '';
				}
				if (!empty($template) && is_array($template)) {
					$template = implode(',', $template);
				} else {
					$template = '';
				}
				$setting = trim(array2string($_POST['setting']));
				$_POST['info']['name'] = $name;
				$_POST['info']['dirname'] = $dirname;
				$_POST['info']['domain'] = $domain;
				$_POST['info']['ishtml'] = $ishtml;
				$_POST['info']['mobileauto'] = $mobileauto;
				$_POST['info']['mobilehtml'] = $mobilehtml;
				$_POST['info']['not_pad'] = $not_pad;
				$_POST['info']['mobile_domain'] = $mobile_domain;
				$_POST['info']['site_title'] = $site_title;
				$_POST['info']['keywords'] = $keywords;
				$_POST['info']['description'] = $description;
				$_POST['info']['release_point'] = $release_point;
				$_POST['info']['template'] = $template;
				$_POST['info']['setting'] = $setting;
				$_POST['info']['default_style'] = $default_style;
				require_once CACHE_MODEL_PATH.'content_input.class.php';
				require_once CACHE_MODEL_PATH.'content_update.class.php';
				$content_input = new content_input(0);
				$inputinfo = $content_input->get($_POST['info']);
				$systeminfo = $inputinfo['system'];
				$sql = $_POST['info'];
				if ($siteid == 1) unset($sql['dirname']);
				if ($this->db->update($sql, array('siteid'=>$siteid))) {
					$class_site = pc_base::load_app_class('sites');
					$class_site->set_cache();
					$this->db->update($systeminfo,array('siteid'=>$siteid));
					showmessage(L('operation_success'), '', '', 'content_id');
				} else {
					showmessage(L('operation_failure'));
				}
			} else {
				$show_dialog = '';
				$locate = $this->locate;
				$waterfont = $this->waterfont;
				$waterfile = $this->waterfile;
				$r = $this->db->get_one(array('siteid'=>$siteid));
				$data = array_map('htmlspecialchars_decode',$r);
				require CACHE_MODEL_PATH.'content_form.class.php';
				$content_form = new content_form(0);
				$forminfos = $content_form->get($data);
				$formValidator = $content_form->formValidator;
				$checkall = $content_form->checkall;
				$show_validator = true;
				$show_header = true;
				$show_scroll = true;
				$template_list = template_list();
				$setting = string2array($data['setting']);
				$release_point_db = pc_base::load_model('release_point_model');
				$release_point_list = $release_point_db->select('', 'id, name');
				include $this->admin_tpl('site_edit');
			}
		} else {
			showmessage(L('notfound'), HTTP_REFERER);
		}
	}
	
	/**
	 * 水印图片预览
	 */
	public function preview() {
		$data = $_GET['setting'];
		$data['source_image'] = CACHE_PATH.'preview.png';
		$data['dynamic_output'] = true;
		
		pc_base::load_sys_class('image');
		$image = new image();
		$rt = $image->watermark($data, 1);
		if (!$rt) {
			echo $image->display_errors();
		}
		exit;
	}
	
	public function public_name() {
		$name = isset($_GET['name']) && trim($_GET['name']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['name'])) : trim($_GET['name'])) : exit('0');
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : '';
 		$data = array();
		if ($siteid) {
			
			$data = $this->db->get_one(array('siteid'=>$siteid), 'name');
			if (!empty($data) && $data['name'] == $name) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$name), 'siteid')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	
	public function public_dirname() {
		$dirname = isset($_GET['dirname']) && trim($_GET['dirname']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['dirname'])) : trim($_GET['dirname'])) : exit('0');
		$siteid = isset($_GET['siteid']) && intval($_GET['siteid']) ? intval($_GET['siteid']) : '';
		$data = array();
		if ($siteid) {
			$data = $this->db->get_one(array('siteid'=>$siteid), 'dirname');
			if (!empty($data) && $data['dirname'] == $dirname) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('dirname'=>$dirname), 'siteid')) {
			exit('0');
		} else {
			exit('1');
		}
	}

	private function check_gd() {
		if(!function_exists('imagepng') && !function_exists('imagejpeg') && !function_exists('imagegif')) {
			$gd = L('gd_unsupport');
		} else {
			$gd = L('gd_support');
		}
		return $gd;
	}
	
	/**
	 * 文件扫描
	 *
	 * @param	string	$source_dir		Path to source
	 * @param	int	$directory_depth	Depth of directories to traverse
	 *						(0 = fully recursive, 1 = current dir, etc)
	 * @param	bool	$hidden			Whether to show hidden files
	 * @return	array
	 */
	public function file_map($source_dir) {
		if ($fp = @opendir($source_dir)) {
			$filedata = array();
			$source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

			while (FALSE !== ($file = readdir($fp))) {
				if ($file === '.' OR $file === '..'
					OR $file[0] === '.'
					OR !@is_file($source_dir.$file)) {
					continue;
				}
				$filedata[] = $file;
			}
			closedir($fp);
			return $filedata;
		}
		return FALSE;
	}
}