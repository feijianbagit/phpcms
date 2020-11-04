<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo L('logon')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<meta name="author" content="zhaoxunzhiyin" />
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<link href="<?php echo CSS_PATH?>muntime/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>muntime/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles1.css" title="styles1" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles2.css" title="styles2" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles3.css" title="styles3" media="screen" />
<script type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script type="text/javascript" src="<?php echo CSS_PATH?>muntime/Particleground.js"></script>
<script src="<?php echo JS_PATH?>jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<style type="text/css">html,body{height: 100%;position: relative;}</style>
<script language="JavaScript">
if(top!=self)
if(self!=top) top.location=self.location;
</script>
</head>
<body onload="javascript:document.myform.username.focus();" id="canvas">
  <div class="admin_login">
    <form class="layui-form layui-form-pane" action="admin.php?m=admin&c=index&a=login&dosubmit=1" method="post" name="myform">
    <div class="admin_title">
       <strong>站点后台管理系统</strong>
       <em>Management System</em>
    </div>
    <div class="admin_user">
       <input type="text" id="username" name="username" placeholder="账号" lay-verify="username" class="login_txt">
    </div>
    <div class="admin_pwd">
       <input type="password" id="password" name="password" placeholder="密码" lay-verify="password" class="login_txt">
       <span class="bind-password icon icon-4"></span>
    </div>
    <div class="admin_val">
       <input type="text" id="captcha" name="code" placeholder="验证码" maxlength="4" lay-verify="code" class="login_txt left">
       <div id="yzm" class="right"><?php echo form::checkcode('code_img')?></div>
    </div>
    <div class="admin_sub">
        <input type="submit" value="立即登陆" class="submit_btn" lay-submit lay-filter="login">
    </div>
    <div class="admin_info">
       <p>&copy;&nbsp;2006-<script language="javaScript">document.write(new Date().getFullYear());</script>&nbsp;Kaixin100&nbsp;www.kaixin100.cn&nbsp;<?php echo pc_base::load_config('version','cms_version');?></p>
    </div>
    </form>
  </div>
<script>
$(document).ready(function() {
    $('body').particleground({
        dotColor: 'rgba(255,255,255,0.2)',
        lineColor: 'rgba(255,255,255,0.2)'
    });
    $.backstretch([
        "<?php echo IMG_PATH?>admin_img/bg-screen1.jpg","<?php echo IMG_PATH?>admin_img/bg-screen2.jpg","<?php echo IMG_PATH?>admin_img/bg-screen3.jpg","<?php echo IMG_PATH?>admin_img/bg-screen4.jpg","<?php echo IMG_PATH?>admin_img/bg-screen5.jpg","<?php echo IMG_PATH?>admin_img/bg-screen6.jpg","<?php echo IMG_PATH?>admin_img/bg-screen7.jpg"], {
        fade: 1000,
        duration: 8000
    });
    $('.bind-password').on('click', function () {
        if ($(this).hasClass('icon-5')) {
            $(this).removeClass('icon-5');
            $("input[name='password']").attr('type', 'password');
        } else {
            $(this).addClass('icon-5');
            $("input[name='password']").attr('type', 'text');
        }
    });
});
</script>
<script>
    layui.use('form',function(){
        var form = layui.form,$ = layui.jquery;
        //自定义表单验证
        form.verify({
            username: function (value, item) {
                if (!value){
                    item.focus();
                    return '账号不能为空！';
                }
            },
            password: function (value, item) {
                if (!value){
                    item.focus();
                    return '密码不能为空！';
                }
            },
            code: function (value, item) {
                if (!value){
                    item.focus();
                    return '验证码不能为空！';
                }
            },
        });
        //监听提交
        form.on('submit(login)', function(data){
            loading = layer.load(1, {shade: [0.1,'#fff'] });//0.1透明度的白色背景
            $.ajax({
                type: 'post',
                url: 'admin.php?m=admin&c=index&a=login&dosubmit=1',
                data: data.field,
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                            location.href = res.url;
                        });
                    /*}else if(res.code == 2){
                        $('#username').val('');
                        $('#username').focus();
                        $('#captcha').val('');
                        layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                        $('#code_img').trigger('click');
                    }else if(res.code == 3){
                        $('#password').val('');
                        $('#password').focus();
                        $('#captcha').val('');
                        layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                        $('#code_img').trigger('click');
                    }else if(res.code == 4){
                        $('#captcha').focus();
                        $('#captcha').val('');
                        layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                        $('#code_img').trigger('click');*/
                    }else{
                        $('#captcha').val('');
                        layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                        $('#code_img').trigger('click');
                    }
                }
            });
            return false;
        });
    });
</script>
</body>
</html>