<?php
defined('IN_CMS') or exit('No permission resources.'); 

$session_storage = 'session_'.pc_base::load_config('system','session_storage');
pc_base::load_sys_class($session_storage);
$checkcode = pc_base::load_sys_class('checkcode');
if(isset($_GET['width']) && intval($_GET['width'])) $checkcode->width = intval($_GET['width']);
if(isset($_GET['height']) && intval($_GET['height'])) $checkcode->height = intval($_GET['height']);
if(isset($_GET['code_len']) && intval($_GET['code_len'])) $checkcode->code_len = intval($_GET['code_len']);
if(isset($_GET['font_size']) && intval($_GET['font_size'])) $checkcode->font_size = intval($_GET['font_size']);
if($checkcode->width > 500 || $checkcode->width < 10)  $checkcode->width = 100;
if($checkcode->height > 300 || $checkcode->height < 10)  $checkcode->height = 35;
if($checkcode->code_len > 8 || $checkcode->code_len < 2)  $checkcode->code_len = 4;
$checkcode->show_code();
$_SESSION['code'] = $checkcode->get_code();