<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="subnav">
  <div class="content-menu ib-a blue line-x">
  <a href='javascript:;' class="on"><em><?php echo L('category_manage')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo $menuid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=0', '<?php echo L('add_category')?>')" ><em><?php echo L('add_category')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=1', '<?php echo L('add_page')?>')" ><em><?php echo L('add_page')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=2', '<?php echo L('add_cat_link')?>')" ><em><?php echo L('add_cat_link')?></em></a><span>|</span><a href='?m=admin&c=category&a=public_cache&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&module=admin' ><em><?php echo L('category_cache')?></em></a><span>|</span><a href='?m=admin&c=category&a=count_items&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&' ><em><?php echo L('count_items')?></em></a><span>|</span><a href='?m=admin&c=category&a=batch_edit&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&' ><em><?php echo L('category_batch_edit')?></em></a><span>|</span><a href='?m=content&c=sitemodel_field&a=init&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&&modelid=-1' ><em><?php echo L('category_field_manage')?></em></a><span>|</span><a href='?m=content&c=sitemodel_field&a=init&menuid=<?php echo$_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&&modelid=-2' ><em><?php echo L('page_field_manage')?></em></a>
  </div>
</div>
<form name="myform" action="?m=admin&c=category&a=listorder" method="post">
<div class="pad_10">
<div class="explain-col">
<?php echo L('category_cache_tips');?>，<a href="?m=admin&c=category&a=public_cache&menuid=<?php echo$_GET['menuid'];?>&module=admin"><?php echo L('update_cache');?></a>
</div>
<div class="bk10"></div>
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th width="38"><?php echo L('listorder');?></th>
            <th width="30">catid</th>
            <th ><?php echo L('catname');?></th>
            <th align='left' width='50'><?php echo L('category_type');?></th>
            <th align='left' width="50"><?php echo L('modelname');?></th>
            <th align='center' width="40"><?php echo L('items');?></th>
            <th align='center' width="30"><?php echo L('vistor');?></th>
            <th align='center' width="80"><?php echo L('domain_help');?></th>
			<th ><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php echo $categorys;?>
    </tbody>
    </table>
    <div class="btn">
	<input type="hidden" name="pc_hash" value="<?php echo $_SESSION['pc_hash'];?>" />
	<input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
</div>
</form>
<script language="JavaScript">
<!--
function addedit(url, name) {
	artdialog('content_id',url,name,'80%','80%');
}
window.top.$('#display_center_id').css('display','none');
//-->
</script>
</body>
</html>
