<?php
	/**
	 * 返回附件类型图标
	 * @param $file 附件名称
	 * @param $type png为大图标，gif为小图标
	 */
	function file_icon($file,$type = 'png') {
		$ext_arr = array('doc','docx','ppt','xls','txt','pdf','mdb','jpg','gif','png','bmp','jpeg','rar','zip','swf','flv','mp4','mp3');
		$ext = fileext($file);
		if($type == 'png') {
			if($ext == 'zip' || $ext == 'rar') $ext = 'rar';
			elseif($ext == 'doc' || $ext == 'docx') $ext = 'doc';
			elseif($ext == 'xls' || $ext == 'xlsx') $ext = 'xls';
			elseif($ext == 'ppt' || $ext == 'pptx') $ext = 'ppt';
			elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb') $ext = 'flv';
			elseif($ext == 'mp4') $ext = 'mp4';
			elseif($ext == 'mp3') $ext = 'mp3';
			else $ext = 'do';
		}
		if(in_array($ext,$ext_arr)) return 'statics/images/ext/'.$ext.'.'.$type;
		else return 'statics/images/ext/blank.'.$type;
	}
	
	/**
	 * 附件目录列表，暂时没用
	 * @param $dirpath 目录路径
	 * @param $currentdir 当前目录
	 */
	function file_list($dirpath,$currentdir) {
		$filepath = $dirpath.$currentdir;
		$list['list'] = glob($filepath.DIRECTORY_SEPARATOR.'*');
		if(!empty($list['list'])) rsort($list['list']);
		$list['local'] = str_replace(array(PC_PATH, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('',DIRECTORY_SEPARATOR), $filepath);
		return $list;
	}
	
	/**
	 * flash上传初始化
	 * 初始化h5upload上传中需要的参数
	 * @param $module 模块名称
	 * @param $catid 栏目id
	 * @param $args 传递参数
	 * @param $userid 用户id
	 * @param $groupid 用户组id
	 * @param $isadmin 是否为管理员模式
	 */
	function initupload($module, $catid,$args, $userid, $groupid = '8', $isadmin = '0',$userid_flash='0'){
		$grouplist = getcache('grouplist','member');
		if($isadmin==0 && !$grouplist[$groupid]['allowattachment']) return false;
		extract(getswfinit($args));
		$siteid = param::get_cookie('siteid');
		$site_setting = get_site_setting($siteid);
		$file_size_limit = $site_setting['upload_maxsize'];
		if ($file_upload_limit==1) {
			$multi = 'false';
		} else {
			$multi = 'true';
		}
		$sess_id = SYS_TIME;
		$admin_url = pc_base::load_config('system','admin_url');
		$upload_path = empty($admin_url) ? APP_PATH : SITE_PROTOCOL.$admin_url.'/';
		$swf_auth_key = md5(pc_base::load_config('system','auth_key').$sess_id);
		$init = '
		$(document).ready(function(){
			$(\'#file_upload\').uploadifive({
				\'auto\' : true, 
				\'width\' : 65,
				\'height\' : 25,
				\'multi\' : '.$multi.',
				\'dnd\' : true,
				\'queueSizeLimit\' : '.$file_upload_limit.', 
				\'fileSizeLimit\' : '.$file_size_limit.',
				\'buttonText\' : \''.L('select_file').'\',
				\'removeCompleted\' : true,
				\'formData\' : {\'H5UPLOADSESSID\' : \''.$sess_id.'\',"module":"'.$module.'","catid":"'.$_GET['catid'].'","userid":"'.$userid.'","siteid":"'.$siteid.'","dosubmit":"1","thumb_width":"'.$thumb_width.'","thumb_height":"'.$thumb_height.'","watermark_enable":"'.$watermark_enable.'","image_reduce":"'.$image_reduce.'","filetype_post":"'.$file_types_post.'","swf_auth_key":"'.$swf_auth_key.'","isadmin":"'.$isadmin.'","groupid":"'.$groupid.'","userid_flash":"'.$userid_flash.'",},
				\'queueID\' : \'queue\',
				\'uploadScript\' : \''.$upload_path.'index.php?m=attachment&c=attachments&a=h5upload&dosubmit=1\',
				\'onFallback\' : function() {
					layer.alert("您的浏览器不支持HTML5，请升级或更换浏览器！");
				},
				\'onUploadComplete\' : uploadSuccess
			});
		})';
		return $init;
	}		
	/**
	 * 获取站点配置信息
	 * @param  $siteid 站点id
	 */
	function get_site_setting($siteid) {
		$siteinfo = getcache('sitelist', 'commons');
		return string2array($siteinfo[$siteid]['setting']);
	}
	/**
	 * 读取h5upload配置类型
	 * @param array $args flash上传配置信息
	 */
	function getswfinit($args) {
		$siteid = get_siteid();
		$site_setting = get_site_setting($siteid);
		$site_allowext = $site_setting['upload_allowext'];
		$args = explode(',',$args);
		$arr['file_upload_limit'] = intval($args[0]) ? intval($args[0]) : '8';
		$args['1'] = ($args[1]!='') ? $args[1] : $site_allowext;
		$arr_allowext = explode('|', $args[1]);
		foreach($arr_allowext as $k=>$v) {
			$v = '*.'.$v;
			$array[$k] = $v;
		}
		$upload_allowext = implode(';', $array);
		$arr['file_types'] = $upload_allowext;
		$arr['file_types_post'] = $args[1];
		$arr['allowupload'] = intval($args[2]);
		$arr['thumb_width'] = intval($args[3]);
		$arr['thumb_height'] = intval($args[4]);
		$arr['watermark_enable'] = ($args[5]=='') ? 1 : intval($args[5]);
		$arr['image_reduce'] = intval($args[6]);
		return $arr;
	}	
	/**
	 * 判断是否为图片
	 */
	function is_images($file) {
		$ext_arr = array('jpg','gif','png','bmp','jpeg','tiff');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}
	
	/**
	 * 判断是否为视频
	 */
	function is_video($file) {
		$ext_arr = array('rm','mpg','avi','mpeg','wmv','flv','asf','rmvb');
		$ext = fileext($file);
		return in_array($ext,$ext_arr) ? $ext_arr :false;
	}

?>