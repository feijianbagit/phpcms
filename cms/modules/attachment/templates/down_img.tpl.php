<?php
defined('IN_CMS') or exit('No permission resources.');
defined('IS_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','attachment');?>
<style type="text/css">
body{background: #ffffff;}
</style>
<form class="form-horizontal" role="form" id="myform">
    <?php echo dr_form_hidden();?>
    <div style="padding: 20px;">
        <ul class="list-group">
            <?php if(is_array($list) && !empty($list)){ foreach ($list as $id=>$t) {?>
            <li class="list-group-item" style="overflow: hidden">
                <input type="hidden" name="data[<?php echo $id;?>]" value="" id="aid_<?php echo $id;?>">
                <a href="<?php echo $t;?>" target="_blank"><?php echo $t;?></a>
                <a id="content_<?php echo $id;?>" style="margin-top:0px;float: right;"> </a>
            </li>
            <script type="text/javascript">
                function dr_down_<?php echo $id;?>(){
                    $('#content_<?php echo $id;?>').html('<img width="15" src="<?php echo JS_PATH;?>layer/theme/default/loading-2.gif">');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $down_url;?>&id=<?php echo $id;?>",
                        dataType: "json",
                        success: function (json) {
                            if (json.code == 0) {
                                $('#content_<?php echo $id;?>').addClass("badge badge-danger");
                                $('#content_<?php echo $id;?>').html(json.msg);
                                $('#content_<?php echo $id;?>').attr("onclick", "dr_down_<?php echo $id;?>()");
                            } else {
                                $('#content_<?php echo $id;?>').removeClass("badge-danger");
                                $('#content_<?php echo $id;?>').addClass("badge badge-success");
                                $('#content_<?php echo $id;?>').html('<?php echo L('成功');?>');
                                $('#aid_<?php echo $id;?>').val(json.msg);
                            }
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            dr_ajax_alert_error(HttpRequest, this, thrownError);
                        }
                    });
                }
                $(function () {
                    dr_down_<?php echo $id;?>();
                });
            </script>
            <?php }}?>
        </ul>
    </div>
</form>
</body>
</html>