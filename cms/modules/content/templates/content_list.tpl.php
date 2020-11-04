<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('display_menu_id').style.display='';
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
<a class="layui-btn layui-btn-sm" href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=add&menuid=&catid=<?php echo $catid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>','<?php echo L('add_content');?>');"><?php echo L('add_content');?></a>
<a class="layui-btn layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" <?php if($steps==0 && !isset($_GET['reject'])) echo 'class=on';?>><?php echo L('check_passed');?></a>
<a class="layui-btn layui-btn-normal layui-btn-sm" href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>"><?php echo L('recycle');?></a>
<?php echo $workflow_menu;?> <a class="layui-btn layui-btn-normal layui-btn-sm" href="javascript:;" onclick="javascript:$('#searchid').css('display','');"><?php echo L('search');?></a> 
<?php if($category['ishtml']) {?>
<a class="layui-btn layui-btn-normal layui-btn-sm" href="?m=content&c=create_html&a=category&pagesize=30&dosubmit=1&modelid=0&catids[0]=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>&referer=<?php echo urlencode($_SERVER['QUERY_STRING']);?>"><?php echo L('update_htmls',array('catname'=>$category['catname']));?></a>
<?php }?>
<a class="layui-btn<?php if($pagesize==20) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pagesize=20&pc_hash=<?php echo $pc_hash;?>">20</a>
<a class="layui-btn<?php if($pagesize==100) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pagesize=100&pc_hash=<?php echo $pc_hash;?>">100</a>
<a class="layui-btn<?php if($pagesize==200) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pagesize=200&pc_hash=<?php echo $pc_hash;?>">200</a>
<a class="layui-btn<?php if($pagesize==300) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pagesize=300&pc_hash=<?php echo $pc_hash;?>">300</a>
</blockquote>
<div id="searchid" style="display:<?php if(!isset($_GET['search'])) echo 'none';?>">
<form name="searchform" action="" method="get" >
<input type="hidden" value="content" name="m">
<input type="hidden" value="content" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" value="<?php echo $catid;?>" name="catid">
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
<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">
  <table class="layui-table" lay-filter="list">
    <thead>
      <tr>
        <th lay-data="{type:'checkbox', width:50, fixed: 'left'}"></th>
        <th lay-data="{field:'listorder', width:80, sort: true}"><?php echo L('listorder');?></th>
        <th lay-data="{field:'id', width:80, sort: true}"><?php echo L('number');?></th>
        <th lay-data="{field:'title', minWidth:340, sort: true, edit: 'text'}"><?php echo L('title');?></th>
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
        <td></td>
        <td><input name='listorders[<?php echo $r['id'];?>]' type='text' size='10' value='<?php echo $r['listorder'];?>' class='layui-input list_order'></td>
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
			echo '<a href="?m=content&c=content&a=public_preview&steps='.$steps.'&catid='.$catid.'&id='.$r['id'].'" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal">';
		}?><?php echo L('preview');?></a>
            <a href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=edit&catid=<?php echo $catid;?>&id=<?php echo $r['id']?>','<?php echo L('edit').L('content');?>')" class="layui-btn layui-btn-xs"><?php echo L('edit');?></a>
            <a href="javascript:view_comment('<?php echo id_encode('content_'.$catid,$r['id'],$this->siteid);?>','<?php echo safe_replace($r['title']);?>')" class="layui-btn layui-btn-danger layui-btn-xs"><?php echo L('comment');?></a>
        </td>
      </tr>
      <?php }
      }
      ?>
    </tbody>
    <tfoot></table>
	<?php echo runhook('admin_content_init')?>
    <div id="pages"><?php echo $pages;?></div>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<script type="text/html" id="topBtn">
    <a href="javascript:;" onclick="myform.action='?m=content&c=content&a=listorder&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>';myform.submit();" class="layui-btn layui-btn-sm"><?php echo L('listorder');?></a>
    <?php if($category['content_ishtml']) {?>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="createhtml"><?php echo L('createhtml');?></button>
    <?php }
    if($status!=99) {?>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="passed"><?php echo L('passed_checked');?></button>
    <?php }?>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="recycle"><?php echo L('in_recycle');?></button>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="delAll"><?php echo L('thorough');?><?php echo L('delete');?></button>
    <?php if(!isset($_GET['reject'])) { ?>
    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="push"><?php echo L('push');?></button>
    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="copy"><?php echo L('copy');?></button>
    <?php if($workflow_menu) { ?><button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="reject_check"><?php echo L('reject');?></button>
    <div id='reject_content' style='background-color: #fff;border:#006699 solid 1px;position:absolute;z-index:10;padding:1px;display:none;'>
    <table cellpadding='0' cellspacing='1' border='0'><tr><tr><td colspan='2'><textarea name='reject_c' id='reject_c' style='width:300px;height:46px;' onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;"><?php echo L('reject_msg');?></textarea></td><td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="reject_check1"><?php echo L('submit');?></button></td></tr>
    </table>
    </div>
    <?php }}?>
    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="remove"><?php echo L('remove');?></button>
    <?php if (module_exists('bdts')) {?>
    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="bdts"><?php echo L('批量百度主动推送');?></button>
    <?php }?>
</script>
<script>
layui.use(['table','form'], function(){
    var table = layui.table, form = layui.form;
    //转换静态表格
    table.init('list', {
        id: 'content',
        toolbar: '#topBtn',
        //height: 450,
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
                url: '?m=content&c=content&a=update&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
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
    $('body').on('click','#delAll',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            layer.confirm('确认要删除选中的内容吗？', {icon: 3}, function(index) {
                layer.close(index);
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=delete&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                    data: {ids: ids},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
                            layer.msg(res.msg,{icon: 1, time: 1000},function(){
                                location.reload();
                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    }
                });
            });
        }
    })
    $('body').on('click','#recycle',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            layer.confirm('确认要删除选中的内容吗？您可以在回收站恢复！', {icon: 3}, function(index) {
                layer.close(index);
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                    data: {ids: ids},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
                            layer.msg(res.msg,{icon: 1, time: 1000},function(){
                                location.reload();
                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    }
                });
            });
        }
    })
    $('body').on('click','#push',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
			artdialog('contentpush','?m=content&c=push&action=position_list&catid=<?php echo $catid?>&modelid=<?php echo $modelid?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('push');?>：',800,500);
        }
    })
    $('body').on('click','#copy',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
			artdialog('contentcopy','?m=content&c=copy&a=init&module=content&classname=push_api&action=category_list_copy&tpl=copy_to_category&modelid=<?php echo $modelid?>&catid=<?php echo $catid?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('copy');?>：',800,500);
        }
    })
    <?php if (module_exists('bdts')) {?>
    $('body').on('click','#bdts',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=bdts&c=bdts&a=add&modelid=<?php echo $modelid;?>&pc_hash=<?php echo $pc_hash;?>',
                data: {ids: ids},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if (res.code==1) {
                        layer.msg(res.msg,{icon: 1, time: 1000},function(){
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    })
    <?php }?>
    $('body').on('click','#remove',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
			artdialog('contentremove','?m=content&c=content&a=remove&catid=<?php echo $catid?>&ids='+ids,'<?php echo L('remove');?>：',800,500);
        }
    })
    <?php if($category['content_ishtml']) {?>
    $('body').on('click','#createhtml',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=batch_show&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                data: {ids: ids, dosubmit: 1},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if (res.code==1) {
                        layer.msg(res.msg,{icon: 1, time: 1000},function(){
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    })
    <?php }?>
    <?php if($workflow_menu) {?>
    $('body').on('click','#reject_check',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            $('#reject_content').toggle();
        }
    })
    $('body').on('click','#reject_check1',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&reject=1&pc_hash=<?php echo $pc_hash;?>',
                data: {ids: ids, reject_c: $('#reject_c').val()},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if (res.code==1) {
                        layer.msg(res.msg,{icon: 1, time: 1000},function(){
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    })
    <?php }?>
    <?php if($status!=99) {?>
    $('body').on('click','#passed',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                data: {ids: ids},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if (res.code==1) {
                        layer.msg(res.msg,{icon: 1, time: 1000},function(){
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    })
    <?php }?>
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