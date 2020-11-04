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
<a class="layui-btn layui-btn-sm" href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" <?php if($steps==0 && !isset($_GET['reject'])) echo 'class=on';?>><?php echo L('check_passed');?></a>
<a class="layui-btn<?php if($pagesize==20) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pagesize=20&pc_hash=<?php echo $pc_hash;?>">20</a>
<a class="layui-btn<?php if($pagesize==100) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pagesize=100&pc_hash=<?php echo $pc_hash;?>">100</a>
<a class="layui-btn<?php if($pagesize==200) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pagesize=200&pc_hash=<?php echo $pc_hash;?>">200</a>
<a class="layui-btn<?php if($pagesize==300) echo ' layui-btn-danger';?> layui-btn-sm" href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pagesize=300&pc_hash=<?php echo $pc_hash;?>">300</a>
</blockquote>
<form class="layui-form" name="myform" id="myform" action="" method="post">
<input type="hidden" value="<?php echo $pc_hash;?>" name="pc_hash">
  <table class="layui-table" lay-filter="list">
    <thead>
      <tr>
        <th lay-data="{type:'checkbox', width:50, fixed: 'left'}"></th>
        <th lay-data="{field:'listorder', width:80, sort: true}"><?php echo L('listorder');?></th>
        <th lay-data="{field:'id', width:80, sort: true}"><?php echo L('number');?></th>
        <th lay-data="{field:'title', width:340, sort: true, edit: 'text'}"><?php echo L('title');?></th>
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
        <td class='visible-lg visible-md'><span onmouseover="layer.tips('<?php echo L('today_hits');?>：<?php echo $hits_r['dayviews'];?><br><?php echo L('yestoday_hits');?>：<?php echo $hits_r['yesterdayviews'];?><br><?php echo L('week_hits');?>：<?php echo $hits_r['weekviews'];?><br><?php echo L('month_hits');?>：<?php echo $hits_r['monthviews'];?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><?php echo $hits_r['views'];?></span></td>
        <td class='visible-lg visible-md'><?php
		if($r['sysadd']==0) {
			echo "<a href='?m=member&c=member&a=memberinfo&username=".urlencode($r['username'])."&pc_hash=".$_SESSION['pc_hash']."' >".$r['username']."</a>"; 
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
            <a class="layui-btn layui-btn-xs" lay-event="restore"><?php echo L('restore');?></a>
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
    <?php if($status==100) {?>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="recycle"><?php echo L('还原');?></button>
    <?php }?>
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="delAll"><?php echo L('thorough');?><?php echo L('delete');?></button>
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
    table.on('tool(list)', function(obj) {
        var data = obj.data;
        if(obj.event === 'restore'){
            layer.confirm('确定要还原此内容吗？', {icon: 3}, function(index){
                layer.close(index);
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=0&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
                    data: {id:data.id},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
	                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                                location.reload();
                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    }
                });
            });
        }
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
            layer.confirm('确认要还原选中的内容吗？', {icon: 3}, function(index) {
                layer.close(index);
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=0&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
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
});
</script>
</body>
</html>