<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class custom extends admin {
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('custom_model');
	}

	public function init() {
		$where = array('siteid'=>$this->get_siteid());
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'id DESC',$page, $pages = '30');
		$pages = $this->db->pages;
		
		$big_menu = array('javascript:artdialog(\'add\',\'?m=custom&c=custom&a=add\',\''.L('custom_add').'\',760,380);void(0);', L('custom_add'));
		include $this->admin_tpl('custom_list');
	}

	
	//添加
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
	 		if(empty($_POST['custom']['title'])){
				showmessage(L('custom_title_no_input'));
	 		}
	 		if(empty($_POST['custom']['content'])){
				showmessage(L('custom_content_no_input'));
	 		}
			$_POST['custom']['inputtime'] = SYS_TIME;
			$_POST['custom']['siteid'] = $this->get_siteid();
			
			$data = $_POST['custom'];
			$customid = $this->db->insert($data,true);
			if(!$customid) return FALSE; 
 			$siteid = $this->get_siteid();
			showmessage(L('operation_success'),HTTP_REFERER,'', 'edit');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
 			include $this->admin_tpl('custom_add');
		}

	}
	
	
	public function edit() {
		if(isset($_POST['dosubmit'])){
 			$id = intval($_GET['id']);
			if($id < 1) return false;
			if(!is_array($_POST['custom']) || empty($_POST['custom'])) return false;
			if((!$_POST['custom']['title']) || empty($_POST['custom']['content'])) return false;
			$this->db->update($_POST['custom'],array('id'=>$id));
			showmessage(L('operation_success'),'?m=custom&c=custom&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			
			//解出链接内容
			$info = $this->db->get_one(array('id'=>$_GET['id']));
			if(!$info) showmessage(L('custom_exit'));
			extract($info); 
 			include $this->admin_tpl('custom_edit');
		}

	}
	
	

	/**
	 * 删除 
	 * @param	intval	$sid	幻灯片ID，递归删除
	 */
	public function delete() {
  		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $id_arr) {
 					//批量删除幻灯片
					$this->db->delete(array('id'=>$id_arr));
					//更新附件状态
					if(pc_base::load_config('system','attachment_stat')) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_delete('custom-'.$id_arr);
					}
				}
				showmessage(L('operation_success'),'?m=custom&c=custom');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除幻灯片
				$result = $this->db->delete(array('id'=>$id));
				
				if($result){
					showmessage(L('operation_success'),'?m=custom&c=custom');
				}else {
					showmessage(L("operation_failure"),'?m=custom&c=custom');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	 
	public function view_content(){
		$id=intval($_GET['id']);
		$info = $this->db->get_one(array('id'=>$id));

		if(!$info) showmessage(L('custom_exit'));
		$content=$info['content'];
 		include $this->admin_tpl('custom_content');
	}

	public function view_lable(){
		$id=intval($_GET['id']);
		$info = $this->db->get_one(array('id'=>$_GET['id']));
		if(!$info) showmessage(L('custom_exit'));
		extract($info); 
 		include $this->admin_tpl('custom_get_lable');
	}
    
	
	/**
	 * 说明:对字符串进行处理
	 * @param $string 待处理的字符串
	 * @param $isjs 是否生成JS代码
	 */
	function format_js($string, $isjs = 1){
		$string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
		return $isjs ? 'document.write("'.$string.'");' : $string;
	}
 
 
	
}
?>