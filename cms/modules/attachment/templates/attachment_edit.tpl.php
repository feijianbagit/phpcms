<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<style type="text/css">
body {background-color: #fff;overflow-y: hidden;overflow-x: hidden;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content   main-content2">
                            <div class="page-body" style="margin-top:20px;margin-bottom:30px;">
<form class="form-horizontal" method="post" role="form" id="myform">
    <div class="form-body">

        <div class="form-group">
            <div class="col-xs-12">
                <input type="text" placeholder="<?php echo L('filename');?>" type="text" name="name" value="<?php echo $filename;?>" class="form-control ">
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