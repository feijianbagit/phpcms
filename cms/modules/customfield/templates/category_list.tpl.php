<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form action="?m=customfield&c=customfield&a=category_save" method="post" id="myform">
<input type="hidden" value="<?php echo $siteid ?>" name="siteid" />
<div class="pad-10 myfbody">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		标签：{get_cache('fieldlist', 'data', $siteid, '变量名')}
		</div>
		</td>
		</tr>
    </tbody>
</table>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
			<div class="explain-col"> 
				<?php echo L('cm_site')?>: &nbsp;&nbsp;
				<?php
					if(is_array($sitelist)){
					foreach($sitelist as $site){
				?>
					<a href="?m=customfield&c=customfield&a=category_list&siteid=<?php echo $site['siteid'];?>&menuid=<?php echo intval($this->input->get('menuid')) ?>"><?php echo $site['name'];?></a>&nbsp;
				<?php }}?>
			</div>
		</td>
		</tr>
    </tbody>
</table>

<div class="col-tab">
<div class="contentList pad-10 ">
<div class="table-list" id="listtable">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center" width="80"><?php echo L('listorder')?></th>
			<th><?php echo L('cm_cate_name')?></th>
			<th align="center" width="80"><?php echo L('cm_status')?></th>
			<th align="center" width="80"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
	$j = 1;
	foreach($cate_list as $f){
?>
	<tr id="tr<?php echo $j ?>">
		<td align="center">
			<input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j ?>][id]" />
			<input type="hidden" value="1" name="postdata[<?php echo $j ?>][options]" class="dataoptions" />
			<input name="postdata[<?php echo $j ?>][listorder]" type='text' size='3' value='<?php echo $f['listorder']?>' class="input-text-c" />
		</td>
		<td align="center"><label><input name="postdata[<?php echo $j ?>][description]" type='text' value='<?php echo $f['description']?>' class="input-text" style="width:auto!important;" /></label></td>
		<td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
		<td align="center"><a class="btn btn-xs red" href="javascript:;" onclick="delTr('tr<?php echo $j ?>')"><?php echo L('delete');?></a></td>
	</tr>
<?php $j++;} ?>

<tr>
<td colspan="6" align="center"><input type="button" value=" <?php echo L('cm_add');?> " class="button" onclick="addTr()" /></td>
</tr>

</tbody>
</table>
</div>
</div>
<div class="portlet-body form myfooter">
	<div class="form-actions text-center">
		<button type="button" onclick="dr_ajax_submit('?m=customfield&c=customfield&a=category_save', 'myform', '2000', '<?php echo dr_now_url();?>')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('cm_save')?></button>
	</div>
</div>
</div>

</div>
</form>
</body>
<script type="text/javascript">
//添加行
var addnum = <?php echo $j+1 ?>;
function addTr(){
	var trHtml   =	"<tr id='ntr" + addnum + "'>";
	trHtml  +=	"<td align='center'>";
	trHtml  +=	"<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
	trHtml  +=	"<input name='postdata[" + addnum + "][listorder]' type='text' size='3' value='0' class='input-text-c' />";
	trHtml  +=	"</td>";
	trHtml  +=	"<td align='center'><label><input name='postdata[" + addnum + "][description]' type='text' value='' class='input-text' style='width:auto!important;' /></label></td>";
	trHtml  +=	"<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
	trHtml  +=	"<td align='center'><a class=\"btn btn-xs red\" href='javascript:;' onclick=\"delTr('ntr"+ addnum +"')\"><?php echo L('delete');?></a></td>";
	trHtml  +=	"</tr>";
	addnum++;
	var $tr=$("#listtable tr").eq(-2);
	if($tr.size() <= 1){
		$tr=$("#listtable tr").eq(-1);
		$tr.before(trHtml);
	}else{$tr.after(trHtml);}
}
//删除行
function delTr(trid){
	$("#"+trid).hide();
	$("#"+trid+" .dataoptions").val(3);
}
</script>
</html>