<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
body {background-color: #fff;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content   main-content2">
                            <div class="page-body">
<form class="form-horizontal" action="?m=slider&c=slider&a=add_type" method="post" name="myform" id="myform">
    <div class="form-body">
                <div class="form-group" id="dr_row_name">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('slider_postion_name')?></label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="name" name="type[name]" value="">
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
