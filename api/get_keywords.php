<?php
/**
 * 获取关键字接口
 */
defined('IN_CMS') or exit('No permission resources.');

echo get_keywords($_POST['data']);

function get_keywords($kw) {
	if (!$kw) {
		return '';
	}
	$cfg_bdqc_qcnum = pc_base::load_config('system', 'baidu_qcnum') ? pc_base::load_config('system', 'baidu_qcnum') : 10;
	if (pc_base::load_config('system', 'keywordapi')==1) {
		$baiduapi = pc_base::load_sys_class('baiduapi');
		$data = array(
			'title' => $kw,
			'content' => $kw,
		);
		$data = mb_convert_encoding(json_encode($data), 'GBK', 'UTF8');
		$baidu = $baiduapi->get_data('https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword', $data, 1);
		if ($baidu && $baidu['data']['items']) {
			$n = 0;
			$resultstr = '';
			foreach ($baidu['data']['items'] as $t) {
				$resultstr .= ','.$t['tag'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
		}
		return trim($resultstr, ',');
	} else if (pc_base::load_config('system', 'keywordapi')==2) {
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
					'text' => $kw,
				)),
				'timeout' => 15*60
			)
		))), true);
		if (!$rt) {
			//showmessage('讯飞接口访问失败');
			return '';
		} elseif ($rt['code']) {
			//showmessage('讯飞接口: '.$rt['desc']);
			return '';
		} else {
			$n = 0;
			$resultstr = '';
			foreach ($rt['data']['ke'] as $t) {
				$resultstr .= ','.$t['word'];
				$n++;
				if( $n >= $cfg_bdqc_qcnum ) break;
			}
			return trim($resultstr, ',');
		}
	} else {
		$phpanalysis = pc_base::load_sys_class('phpanalysis');
		$phpanalysis = new phpanalysis('utf-8', 'utf-8', false);
		$phpanalysis->LoadDict();
		$phpanalysis->SetSource($kw);
		$phpanalysis->StartAnalysis(true);
		return $phpanalysis->GetFinallyKeywords($cfg_bdqc_qcnum);
	}
	return '';
}
?>