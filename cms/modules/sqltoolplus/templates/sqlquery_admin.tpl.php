<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
	 		<?php foreach($plugin_menus as $_num => $menu) {?>
            <li <?php if($menu['url']==$_GET['a']) {?>class="on"<?php }?> <?php if($menu['extend']) {?>onclick="loadfile('<?php echo$menu['url'] ?>')"<?php }?> ><a href="?m=sqltoolplus&c=index&a=<?php echo $menu['url']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo $menu['name']?></a></li>
            <?php }?>
</ul>
<div id="tab-content">
<div class="contentList pad-10">
<form action="?m=sqltoolplus&c=index&a=sqlquery" method="post" id="myform">
<table width="100%" class="table_form">
  	<tr>
	    <th class="align_r"><?php echo L('select_pdo')?></th>
	    <td colspan="3"><?php echo form::select($pdos,$pdo_name,'name="pdo_select"',L('select_pdo'))?></td>
  	</tr>
  <tr>
    <th width="120"><?php echo L('select_sql')?></th>
    <td class="y-bg">
		<textarea style="width:500px;" name="sqls" rows="10" cols="85"></textarea>	
	</td>
  </tr> 
 
</table>
<div class="bk15"></div>
<input type="hidden" value="<?php echo $_SESSION['pc_hash']?>" name="pc_hash">
<input name="pluginsubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</div>
</div>
</body>
</html>