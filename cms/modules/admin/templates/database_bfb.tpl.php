<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style type="text/css">
body {background-color: #f5f6f8;}
.page-container {margin: 0;padding: 0;position: relative;}
.progress-bar {float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;background-color: #36c6d3;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;}
.page-content-wrapper {float: left;width: 100%;}
.page-content-wrapper .page-content {margin-top: 0;padding: 25px 20px 10px;}
.text-center {text-align: center;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none}
.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus {outline: dotted thin;outline: -webkit-focus-ring-color auto 5px;outline-offset: -2px}
.btn.focus,.btn:focus,.btn:hover {color: #333;text-decoration: none}
.btn.active,.btn:active {outline: 0;-webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);box-shadow: inset 0 3px 5px rgba(0,0,0,.125)}
.btn.disabled,.btn[disabled],fieldset[disabled] .btn {cursor: not-allowed;opacity: .65;filter: alpha(opacity=65);-webkit-box-shadow: none;box-shadow: none}
.btn.green-meadow:not(.btn-outline) {color: #FFF;background-color: #1BBC9B;border-color: #1BBC9B;}
.btn:not(.btn-sm):not(.btn-lg) {line-height: 1.44;}
.btn {outline: 0!important;}
.btn, .form-control {box-shadow: none!important;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;
font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
.margin-top-20 {margin-top: 20px!important;}
.btn,.btn-group,.btn-group-vertical,.caret,.checkbox-inline,.radio-inline,img {vertical-align: middle}
.well {background-color: #ffffff!important;}
.well {border: 0;padding: 20px;-webkit-box-shadow: none!important;-moz-box-shadow: none!important;box-shadow: none!important;}
.well {min-height: 20px;margin-bottom: 20px;background-color: #f1f4f7;border-radius: 4px;}
#dr_check_bf p, #dr_check_html p {margin: 10px 0;clear: both;}
#dr_check_html .p_error {color: red;}
#dr_check_html .rleft {float: left;}
#dr_check_bf .rright, #dr_check_html .rright {float: right;}
label {font-weight: 400;}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>layer/layer.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content  ">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="text-center">
    <button type="button" id="dr_check_button" onclick="dr_checking();" class="btn green-meadow"> <i class="fa fa-refresh"></i> 开始执行</button>
</div>
<div id="dr_check_result" class=" margin-top-20">
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">

        </div>
    </div>
</div>
<div id="dr_check_div"  class="well margin-top-20">
    <div class="scroller" style="height: 300px; overflow-x: hidden; overflow-y: auto; width: auto;" data-rail-visible="1"  id="dr_check_html">

    </div>
</div>
<script>
    function dr_checking() {
        $('#dr_check_button').attr('disabled', true);
        $('#dr_check_button').html('<i class="fa fa-refresh"></i> 准备中');
        $('#dr_check_bf').html("");
        $('#dr_check_html').html("正在准备中");
        dr_ajax2ajax(1);
    }
    dr_checking();
    function dr_ajax2ajax(page) {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "<?php echo $todo_url;?>&page="+page,
            success: function (json) {

                $('#dr_check_html').append(json.msg);
                document.getElementById('dr_check_html').scrollTop = document.getElementById('dr_check_html').scrollHeight;

                if (json.code == 0) {
                    $('#dr_check_button').attr('disabled', false);
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新执行');
                    dr_tips(0, '发现异常');
                } else {
                    $('#dr_check_result .progress-bar-success').attr('style', 'width:'+json.code+'%');
                    if (json.code == 100) {
                        $('#dr_check_button').attr('disabled', true);
                        $('#dr_check_button').html('<i class="fa fa-refresh"></i> 执行完毕');
                        var isxs = 0;
                        $("#dr_check_html .rbf").each(function(){
                            $('#dr_check_bf').append('<p>'+$(this).html()+'</p>');
                            isxs = 1;
                        });
                    } else {
                        $('#dr_check_button').html('<i class="fa fa-refresh"></i> 执行中 '+json.code+'%');
                        dr_ajax2ajax(json.code);
                    }
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
            }
        });
    }
</script>
</div>
</div>
</div>
</div>
</body>
</html>