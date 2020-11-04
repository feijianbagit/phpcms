<?php
defined('IN_CMS') or exit('No permission resources.');
function spider_keywords($data) {
	if (!$data) {
		return '';
	}
	if (pc_base::load_config('system', 'keywordapi')==0) {
		require_once(CMS_PATH.'/api/Aip/AipNlp.php');
		$cfg_bdqc_appid = pc_base::load_config('system', 'baidu_aid');
		$cfg_bdqc_apikey = pc_base::load_config('system', 'baidu_skey');
		$cfg_bdqc_arcretkey = pc_base::load_config('system', 'baidu_arcretkey');
		$cfg_bdqc_qcnum = pc_base::load_config('system', 'baidu_qcnum') ? pc_base::load_config('system', 'baidu_qcnum') : 10;
		if(empty($cfg_bdqc_appid) || empty($cfg_bdqc_apikey) || empty($cfg_bdqc_arcretkey))
		{
			$data = array('code'=>0, 'data'=>'', 'msg'=>'未添加百度分词系统变量');
			//exit(json_encode($data));
			return '';
		}
		$client = new AipNlp($cfg_bdqc_appid, $cfg_bdqc_apikey, $cfg_bdqc_arcretkey);
		$lexer = $client->lexer($data);
		$tagstr = '';
		if (isset($lexer['error_code'])) {
			$data = array('code'=>0, 'data'=>'', 'msg'=>"请检查百度分词API设置：".$lexer['error_msg']);
			//exit(json_encode($data));
			return '';
		}
		if (isset($lexer['items'])) {
			$items = $lexer['items'];
			$qcnum = $cfg_bdqc_qcnum ? $cfg_bdqc_qcnum : rand(1,count($items));
			$itemcount = count($items) > $qcnum ? $qcnum : count($items);
			$result = array_rand($items, $itemcount == 0 ? 1 : $itemcount);
			$resultstr = array();
			if (is_array($result)) {
				foreach($result as $k => $v)
				{
					$resultstr[] = $items[$v]['item'];
				}
				$tagstr = implode(',', $resultstr);
			} else {
				$tagstr = $items[$result]['item'];
			}
		}
		$data = array('code'=>1, 'data'=>$tagstr, 'msg'=>'分词成功');
		//exit(json_encode($data));
		if ($data && $data['data']) {
			return trim($data['data'], ',');
		}
	} else {
		$XAppid = pc_base::load_config('system', 'xunfei_aid');
		$Apikey = pc_base::load_config('system', 'xunfei_skey');
		$fix = 0; //如果错误日志提示【time out|ilegal X-CurTime】，需要把$fix变量改为 100 、200、300、等等，按实际情况调试，只要是数字都行
		$XParam = base64_encode(json_encode(array(
			"type"=>"dependent",
		)));
		$XCurTime = SYS_TIME - $fix;
		$XCheckSum = md5($Apikey.$XCurTime.$XParam);
		$headers = array();
		$headers[] = 'X-CurTime:'.$XCurTime;
		$headers[] = 'X-Param:'.$XParam;
		$headers[] = 'X-Appid:'.$XAppid;
		$headers[] = 'X-CheckSum:'.$XCheckSum;
		$headers[] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';
		$rt = json_decode(file_get_contents("http://ltpapi.xfyun.cn/v1/ke", false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => $headers,
				'content' => http_build_query(array(
					'text' => $data,
				)),
				'timeout' => 15*60
			)
		))), true);
		if (!$rt) {
			//showmessage('讯飞接口访问失败');
			//return '讯飞接口访问失败';
		} elseif ($rt['code']) {
			//showmessage('讯飞接口: '.$rt['desc']);
			//return '讯飞接口: '.$rt['desc'];
		} else {
			$msg = '';
			foreach ($rt['data']['ke'] as $t) {
				$msg.= ','.$t['word'];
			}
			return trim($msg, ',');
		}
	}
	return '';
}