<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
body{background: #fff;}
hr, p {margin: 20px 0;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content   main-content2">
                            <div class="page-body">
将网站web目录中的admin.php，重命名为任意名字，****.php<br>
那么，访问后台路径变更为：<?php echo APP_PATH;?>****.php
</div>
</div>
</div>
</div>
</body>
</html>