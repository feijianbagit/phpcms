<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('center_frame').src = '?m=content&c=content&a=public_categorys&type=add&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>';
	window.top.$("#current_pos").data('clicknum',0);
}
//-->
</SCRIPT>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<div class="admin-main layui-anim layui-anim-upbit">
  <fieldset class="layui-elem-field layui-field-title">
    <legend><?php echo L('list');?></legend></fieldset>
  <blockquote class="layui-elem-quote">
<?php 
foreach($datas2 as $r) {
	echo "<a href=\"?m=content&c=content&a=initall&modelid=".$r['modelid']."&menuid=822&pc_hash=".$pc_hash."\" class=\"layui-btn layui-btn-sm";
	if($r['modelid']==$modelid) echo " layui-btn-danger";
	echo "\">".$r['name']."</a>";
}
?>
<?php echo $workflow_menu;?> <a class="layui-btn layui-btn-normal layui-btn-sm" href="javascript:;" onclick="javascript:$('#searchid').css('display','');"><?php echo L('search');?></a>
<a class="layui-btn<?php if($pagesize==20) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=initall&modelid=<?php echo $modelid;?>&pagesize=20&pc_hash=<?php echo $pc_hash;?>">20</a>
<a class="layui-btn<?php if($pagesize==100) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=initall&modelid=<?php echo $modelid;?>&pagesize=100&pc_hash=<?php echo $pc_hash;?>">100</a>
<a class="layui-btn<?php if($pagesize==200) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=initall&modelid=<?php echo $modelid;?>&pagesize=200&pc_hash=<?php echo $pc_hash;?>">200</a>
<a class="layui-btn<?php if($pagesize==300) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=initall&modelid=<?php echo $modelid;?>&pagesize=300&pc_hash=<?php echo $pc_hash;?>">300</a>
<?php
echo "<br>";
echo "<br>";
if(is_array($infos)){
	foreach($infos as $info){
		$r = $this->db->get_one(array('status'=>$status,'username'=>$info['username']), "COUNT(*) AS num");
		/*if ($info['username']<>"zgq" and $info['username']<>"wk"){*/
			echo "<a href=\"?m=content&c=content&a=initall&modelid=".$modelid."&steps=0&search=1&pc_hash=".$pc_hash."&start_time=&end_time=&posids=&searchtype=2&keyword=".$info['username']."&search=%E6%90%9C%E7%B4%A2\" class=\"layui-btn layui-btn-sm";
			if($info['username']==$keyword) echo ' layui-btn-danger';
			echo "\">";
			echo $info['realname'] ? $info['realname'] : $info['username'];
			echo "(总".$r['num'].")</a>";
		/*}*/
	}
}
echo "<br>";
echo "<br>";
if(is_array($infos)){
	foreach($infos as $info){
		$r2 = $this->db->get_one("status=".$status." and username='".$info['username']."' and `inputtime` > '".strtotime(date("Ymd", time()))."' and `inputtime` < '".strtotime(date("Ymd", strtotime('+1 day',time())))."'", "COUNT(*) AS num");
		/*if ($info['username']<>"zgq" and $info['username']<>"wk"){*/
			echo "<a href=\"?m=content&c=content&a=initall&modelid=".$modelid."&steps=0&search=1&pc_hash=".$pc_hash."&start_time=".date("Y-m-d", time())."&end_time=".date("Y-m-d", strtotime('+1 day',time()))."&posids=&searchtype=2&keyword=".$info['username']."&search=%E6%90%9C%E7%B4%A2\" class=\"layui-btn layui-btn-sm";
			if($info['username']==$keyword) echo ' layui-btn-danger';
			echo "\">";
			echo $info['realname'] ? $info['realname'] : $info['username'];
			echo "(今".$r2['num'].")</a>";
		/*}*/
	}
}
?>
</blockquote>
<div id="searchid" style="display:<?php if(!isset($_GET['search'])) echo 'none';?>">
<form name="searchform" action="" method="get" >
<input type="hidden" value="content" name="m">
<input type="hidden" value="content" name="c">
<input type="hidden" value="initall" name="a">
<input type="hidden" value="<?php echo $modelid;?>" name="modelid">
<input type="hidden" value="<?php echo $steps;?>" name="steps">
<input type="hidden" value="1" name="search">
<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
 
				<?php echo L('addtime');?>：
				<?php echo form::date('start_time',$_GET['start_time'],0,0,'false');?>- &nbsp;<?php echo form::date('end_time',$_GET['end_time'],0,0,'false');?>
				
				<select name="posids"><option value='' <?php if($_GET['posids']=='') echo 'selected';?>><?php echo L('all');?></option>
				<option value="1" <?php if($_GET['posids']==1) echo 'selected';?>><?php echo L('elite');?></option>
				<option value="2" <?php if($_GET['posids']==2) echo 'selected';?>><?php echo L('no_elite');?></option>
				</select>				
				<select name="searchtype">
					<option value='0' <?php if($_GET['searchtype']==0) echo 'selected';?>><?php echo L('title');?></option>
					<option value='1' <?php if($_GET['searchtype']==1) echo 'selected';?>><?php echo L('intro');?></option>
					<option value='2' <?php if($_GET['searchtype']==2) echo 'selected';?>><?php echo L('username');?></option>
					<option value='3' <?php if($_GET['searchtype']==3) echo 'selected';?>>ID</option>
				</select>
				
				<input name="keyword" type="text" value="<?php if(isset($keyword)) echo $keyword;?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search');?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
</div>
<form class="layui-form" name="myform" id="myform" action="" method="post">
  <table class="layui-table" lay-filter="list">
    <thead>
      <tr>
        <th lay-data="{field:'id', width:80, sort: true, fixed: 'left'}"><?php echo L('number');?></th>
        <th lay-data="{field:'title', minWidth:400, sort: true, edit: 'text'}"><?php echo L('title');?></th>
        <th lay-data="{field:'attribute', width:100}"><?php echo L('attribute');?></th>
        <th lay-data="{field:'hits', width:100, sort: true}"><?php echo L('hits');?></th>
        <th lay-data="{field:'publish_user', width:100, sort: true}"><?php echo L('publish_user');?></th>
        <th lay-data="{field:'updatetime', width:180, sort: true}"><?php echo L('updatetime');?></th>
        <th lay-data="{field:'operations_manage', width:180, align: 'center', fixed: 'right'}"><?php echo L('operations_manage');?></th>
      </tr>
    </thead>
    <tbody>
    <?php
	if(is_array($datas)) {
		$sitelist = getcache('sitelist','commons');
		$release_siteurl = $sitelist[$category['siteid']]['url'];
		$path_len = -strlen(WEB_PATH);
		$release_siteurl = substr($release_siteurl,0,$path_len);
		$this->hits_db = pc_base::load_model('hits_model');
		
		foreach ($datas as $r) {
			$hits_r = $this->hits_db->get_one(array('hitsid'=>'c-'.$modelid.'-'.$r['id']));
	?>
      <tr>
        <td class='visible-lg visible-md'><?php echo $r['id'];?></td>
        <td class='visible-lg visible-md'><?php echo $r['title'];?></td>
        <td class='visible-lg visible-md'><?php if($r['thumb']!='') {echo '<img src="'.IMG_PATH.'icon/small_img.gif" onmouseover="layer.tips(\'<img src='.$r['thumb'].'>\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();">';} if($r['posids']) {echo ' <img src="'.IMG_PATH.'icon/small_elite.png" onmouseover="layer.tips(\''.L('elite').'\',this,{tips: [1, \'#000\']});" onmouseout="layer.closeAll();">';} if($r['islink']) {echo ' <img src="'.IMG_PATH.'icon/link.png" onmouseover="layer.tips(\''.L('islink_url').'\',this,{tips: [1, \'#000\']});" onmouseout="layer.closeAll();">';}?></td>
        <td class='visible-lg visible-md'><span style="display: block;" onmouseover="layer.tips('<?php echo L('today_hits');?>：<?php echo $hits_r['dayviews'];?><br><?php echo L('yestoday_hits');?>：<?php echo $hits_r['yesterdayviews'];?><br><?php echo L('week_hits');?>：<?php echo $hits_r['weekviews'];?><br><?php echo L('month_hits');?>：<?php echo $hits_r['monthviews'];?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><?php echo $hits_r['views'];?></span></td>
        <td class='visible-lg visible-md'><?php
		if($r['sysadd']==0) {
			echo "<a href='javascript:;' onclick=\"omnipotent('member','?m=member&c=member&a=memberinfo&username=".urlencode($r['username'])."&pc_hash=".$_SESSION['pc_hash']."','".L('view_memberlinfo')."',1,700,500);\">".$r['username']."</a>";
			echo '<img src="'.IMG_PATH.'icon/contribute.png" onmouseover="layer.tips(\''.L('member_contribute').'\',this,{tips: [1, \'#000\']});" onmouseout="layer.closeAll();">';
		} else {
			echo $r['username'];
		}
		?></td>
        <td class='visible-lg visible-md'><?php echo dr_date($r['updatetime'],null,'red');?></td>
        <td><?php
		if($status==99) {
			if($r['islink']) {
				echo '<a href="'.$r['url'].'" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">';
			} elseif(strpos($r['url'],'http://')!==false || strpos($r['url'],'https://')!==false) {
				echo '<a href="'.$r['url'].'" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">';
			} else {
				echo '<a href="'.$release_siteurl.$r['url'].'" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">';
			}
		} else {
			echo '<a href="?m=content&c=content&a=public_preview&steps='.$steps.'&catid='.$r['catid'].'&id='.$r['id'].'" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">';
		}?><?php echo L('preview');?></a>
            <a href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=edit&catid=<?php echo $r['catid'];?>&id=<?php echo $r['id']?>','<?php echo L('edit').L('content');?>')" class="layui-btn layui-btn-xs"><?php echo L('edit');?></a>
            <a href="javascript:view_comment('<?php echo id_encode('content_'.$r['catid'],$r['id'],$this->siteid);?>','<?php echo safe_replace($r['title']);?>')" class="layui-btn layui-btn-danger layui-btn-xs"><?php echo L('comment');?></a>
        </td>
      </tr>
      <?php }
      }
      ?>
    </tbody>
    <tfoot></table>
	<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">
	<?php echo runhook('admin_content_init')?>
    <div id="pages"><?php echo $pages;?></div>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<script>
layui.use(['table'], function(){
    var table = layui.table, form = layui.form;
    //转换静态表格
    table.init('list', {
        //height: 410,
        limit: <?php echo $pagesize.PHP_EOL;?>
    });
    //监听单元格编辑
    table.on('edit(list)',function(obj) {
        var value = obj.value, data = obj.data, field = obj.field;
        if (field=='title' && value=='') {
            layer.tips('标题不能为空',this,{tips: [1, '#000']});
            return false;
        }else{
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=update&dosubmit=1&modelid=<?php echo $modelid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                data: {id:data.id,field:field,value:value},
                dataType: 'json',
                success: function(res) {
                    if (res.code == 1) {
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    });
});
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript"> 
<!--
function view_comment(id, name) {
	var diag = new Dialog({
		id:'view_comment',
		title:'<?php echo L('view_comment');?>：'+name,
		url:'<?php echo SELF;?>?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id+'&pc_hash='+pc_hash,
		width:800,
		height:500,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
setcookie('refersh_time', 0);
function refersh_window() {
	var refersh_time = getcookie('refersh_time');
	if(refersh_time==1) {
		location.reload();
	}
}
setInterval("refersh_window()", 3000);
//-->
</script>
</body>
</html>