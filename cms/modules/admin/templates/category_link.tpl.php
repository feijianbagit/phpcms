<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
	var charset = '<?php echo CHARSET;?>';
	var uploadurl = '<?php echo pc_base::load_config('system','upload_url')?>';
//-->
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>colorpicker.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">var catid=0</script>
<script type="text/javascript"> 
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#catname").formValidator({onshow:"<?php echo L('input_catname');?>",onfocus:"<?php echo L('input_catname');?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L('input_catname');?>"})<?php if(ROUTE_A=='edit') echo '.defaultPassed()';?>;
		$("#url").formValidator({onshow:"<?php echo L('input_linkurl');?>",onfocus:"<?php echo L('input_linkurl');?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L('input_linkurl');?>"})<?php if(ROUTE_A=='edit') echo '.defaultPassed()';?>;
	})
//-->
</script>

<form name="myform" id="myform" action="?m=admin&c=category&a=<?php echo ROUTE_A;?>" method="post">
<div class="pad-10">
<div class="col-tab">

<ul class="tabBut cu-li">
            <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',2,1);"><?php echo L('catgory_basic');?></li>
            <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',2,2);"><?php echo L('field_manage');?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">

<table width="100%" class="table_form ">
	<tr>
        <th width="200"><?php echo L('parent_category')?>：</th>
        <td>
		<?php echo form::select_category('category_content_'.$this->siteid,$parentid,'name="info[parentid]"',L('please_select_parent_category'),0,-1);?>
		</td>
      </tr>
      <tr>
        <th><?php echo L('catname')?>：</th>
        <td><input type="text" name="info[catname]" id="catname" class="input-text" value="<?php echo $catname;?>"></td>
      </tr>

	<tr>
        <th><?php echo L('catgory_img')?>：</th>
        <td><?php echo form::images('info[image]', 'image', $image, 'content');?></td>
      </tr>
		<tr>
        <th><?php echo L('link_url')?>：</th>
        <td><input type="text" name="info[url]" id="url" size="50" class="input-text" value="<?php echo $url;?>"></td>
      </tr>
</table>

</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
<table width="100%" class="table_form">
<?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
	 if($info['isomnipotent']) continue;
	 if($info['formtype']=='omnipotent') {
		foreach($forminfos['base'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
		foreach($forminfos['senior'] as $_fm=>$_fm_value) {
			if($_fm_value['isomnipotent']) {
				$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
			}
		}
	}
 ?>
	<tr>
      <th width="200"><?php if($info['star']){ ?> <font color="red">*</font><?php } ?> <?php echo $info['name']?>
	  </th>
      <td class="y-bg"><?php echo $info['form']?>  <?php echo $info['tips']?></td>
    </tr>
<?php
} }
?>
</table>   
</div>
 <div class="bk15"></div>
	<input name="catid" type="hidden" value="<?php echo $catid;?>">
	<input name="type" type="hidden" value="<?php echo $type;?>">
    <input name="dosubmit" id="dosubmit" type="submit" value="<?php echo L('submit')?>" class="dialog">

</form>
</div>

</div>
<!--table_form_off-->
</div>

<script language="JavaScript">
<!--
	window.top.$('#display_center_id').css('display','none');
	function SwapTab(name,cls_show,cls_hide,cnt,cur){
		for(i=1;i<=cnt;i++){
			if(i==cur){
				 $('#div_'+name+'_'+i).show();
				 $('#tab_'+name+'_'+i).attr('class',cls_show);
			}else{
				 $('#div_'+name+'_'+i).hide();
				 $('#tab_'+name+'_'+i).attr('class',cls_hide);
			}
		}
	}

//-->
</script>