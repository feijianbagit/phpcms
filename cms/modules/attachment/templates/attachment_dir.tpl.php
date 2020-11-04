<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="bk15"></div>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">
		<a href="?m=attachment&c=manage<?php echo '&menuid='.$_GET['menuid']?>"><?php echo L('database_schema')?></a>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<div class="table-list">
<table width="100%" cellspacing="0">
<tr>
<td align="left"><?php echo L("local_dir")?>：<?php echo $local?></td><td></td>
</tr>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left"><a href="<?php echo '?m=attachment&c=manage&a=dir&dir='.stripslashes(dirname($dir)).'&menuid='.$_GET['menuid']?>"><img src="<?php echo IMG_PATH?>folder-closed.gif" /><?php echo L("parent_directory")?></a></td><td></td>
</tr>
<?php endif;?>
<?php 
if(is_array($list)) {
	foreach($list as $v) {
	$filename = basename($v)
?>
<tr>
<?php if (is_dir($v)) {
	echo '<td align="left"><img src="'.IMG_PATH.'folder-closed.gif" /> <a href="?m=attachment&c=manage&a=dir&dir='.(isset($_GET['dir']) && !empty($_GET['dir']) ? stripslashes($_GET['dir']).'/' : '').$filename.'&menuid='.$_GET['menuid'].'"><b>'.$filename.'</b></a></td><td width="10%"></td>';
} else {
	echo '<td align="left" ><img src="'.file_icon($filename,'gif').'" /><a rel="'.$local.'/'.$filename.'">'.$filename.'</a></td><td width="10%"><a href="javascript:;" onclick="preview(\''.$local.'/'.$filename.'\')">'.L('preview').'</a> | <a href="javascript:;" onclick="att_delete(this,\''.urlencode($filename).'\',\''.urlencode($local).'\')">'.L('delete').'</a> </td>';
}?>
</tr>
<?php 
	}
}
?>
</table>
</div>
</div>
</body>
<script type="text/javascript">
function preview(filepath) {
	if(IsImg(filepath)) {
		var diag = new Dialog({
			title:'<?php echo L('preview')?>',
			html:'<img src="'+filepath+'" onload="$(this).LoadImage(true, 500, 500,\'<?php echo IMG_PATH?>s_nopic.gif\');"/>',
			modal:true,
			autoClose:5
		});
		diag.show();
	} else if(IsMp4(filepath)) {
		var diag = new Dialog({
			title:'<?php echo L('preview')?>',
			html:'<video controls="true" src="'+filepath+'" width="420" height="238"></video>',
			modal:true
		});
		diag.show();
	} else if(IsMp3(filepath)) {
		var diag = new Dialog({
			title:'<?php echo L('preview')?>',
			html:'<audio src="'+filepath+'" controls="controls"></audio>',
			modal:true
		});
		diag.show();
	} else {
		var diag = new Dialog({
			title:'<?php echo L('preview')?>',
			html:'<a href="'+filepath+'" target="_blank"/><img src="<?php echo IMG_PATH?>admin_img/down.gif"><?php echo L('click_open')?></a>',
			modal:true
		});
		diag.show();
	}
}
function att_delete(obj,filename,localdir){
	Dialog.confirm('<?php echo L('del_confirm')?>', function(){$.get('?m=attachment&c=manage&a=pulic_dirmode_del&filename='+filename+'&dir='+localdir+'&pc_hash='+pc_hash,function(data){if(data == 1) $(obj).parent().parent().fadeOut("slow");})});
};
function IsImg(url){
	  var sTemp;
	  var b=false;
	  var opt="jpg|gif|png|bmp|jpeg";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
	    sTemp=url.substr(url.length-s[i].length-1);
	    sTemp=sTemp.toUpperCase();
	    s[i]="."+s[i];
	    if (s[i]==sTemp){
	      b=true;
	      break;
	    }
	  }
	  return b;
}
function IsMp4(url){
	  var sTemp;
	  var b=false;
	  var opt="mp4";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
	    sTemp=url.substr(url.length-s[i].length-1);
	    sTemp=sTemp.toUpperCase();
	    s[i]="."+s[i];
	    if (s[i]==sTemp){
	      b=true;
	      break;
	    }
	  }
	  return b;
}
function IsMp3(url){
	  var sTemp;
	  var b=false;
	  var opt="mp3";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
	    sTemp=url.substr(url.length-s[i].length-1);
	    sTemp=sTemp.toUpperCase();
	    s[i]="."+s[i];
	    if (s[i]==sTemp){
	      b=true;
	      break;
	    }
	  }
	  return b;
}
</script>
</html>