<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
body{background: #fff;}
hr, p {margin: 20px 0;}
</style>
<div  class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content   main-content2">
                            <div class="page-body">
<p style="white-space: normal;">
    <?php echo CMS_PATH;?>index.php，文件设置为只读权限，防止被非法篡改
</p>
</div>
</div>
</div>
</div>
</body>
</html>