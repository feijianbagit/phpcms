<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET?>" />
<title><?php echo L('admin_site_title')?></title>
<meta name="author" content="zhaoxunzhiyin" />
<link href="<?php echo CSS_PATH?>muntime/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>muntime/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles1.css" title="styles1" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles2.css" title="styles2" media="screen" />
<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>muntime/styles3.css" title="styles3" media="screen" />

<link href="<?php echo CSS_PATH?>table_form.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH?>layui/css/layui.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>Dialog/main.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>hotkeys.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.sgallery.js"></script>
<script src="<?php echo JS_PATH?>jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript">
var pc_hash = '<?php echo $_SESSION['pc_hash']?>'
</script>
</head>
<body scroll="no" class="objbody">
<div class="btns btns2" id="btnx">
<div class="bg_btn"></div>
<?php $model_types = pc_base::load_config('model_config');?>
<h6><?php echo L('panel_switch');?></h6>
<ul id="Site_model" class="pd4">
		<li onclick="_Site_M();" class="ac"><span><?php echo L('full_menu')?></span></li>
		<?php if (is_array($model_types)) { foreach ($model_types as $mt => $mn) {?>
		<li onclick="_Site_M('<?php echo $mt;?>');"><span><?php echo $mn;?></span></li>
		<?php } }?>
	</ul>
</div>
<div id="ew-lock-screen-group" style="display :<?php if(isset($_SESSION['lock_screen']) && $_SESSION['lock_screen']==0) echo 'none';?>">
    <div class="lock-screen-wrapper">
        <div class="lock-screen-time"></div>
        <div class="lock-screen-date"></div>
        <div class="lock-screen-form">
            <input id="lock_password" placeholder="<?php echo L('lockscreen_status');?>" class="lock-screen-psw" maxlength="20" type="password">
            <i class="layui-icon layui-icon-right lock-screen-enter"></i>
            <br>
            <div class="lock-screen-tip"></div>
        </div>
        <div class="lock-screen-tool">
            <div class="lock-screen-tool-item">
                <i class="layui-icon layui-icon-logout" ew-event="logout" data-confirm="false" data-url="?m=admin&c=index&a=public_logout"></i>
                <div class="lock-screen-tool-tip"><?php echo L('exit_login');?></div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#ew-lock-screen-group').backstretch([
        "<?php echo IMG_PATH?>admin_img/bg-screen1.jpg","<?php echo IMG_PATH?>admin_img/bg-screen2.jpg","<?php echo IMG_PATH?>admin_img/bg-screen3.jpg","<?php echo IMG_PATH?>admin_img/bg-screen4.jpg","<?php echo IMG_PATH?>admin_img/bg-screen5.jpg","<?php echo IMG_PATH?>admin_img/bg-screen6.jpg","<?php echo IMG_PATH?>admin_img/bg-screen7.jpg"], {
        fade: 1000,
        duration: 8000
    });
    // 获取各个组件
    var $form = $('.lock-screen-form');
    var $psw = $('.lock-screen-psw');
    var $tip = $('.lock-screen-tip');
    var $time = $('.lock-screen-time');
    var $date = $('.lock-screen-date');
    var $tool = $('.lock-screen-tool-item');

    // 监听enter键
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            doVer();
        } else if (event.keyCode === 8 && !$psw.val()) {
            restForm();
            if (event.preventDefault) event.preventDefault();
            if (event.returnValue) event.returnValue = false;
        }
    });

    // 监听输入
    $psw.on('input', function () {
        var psw = $psw.val();
        if (psw) {
            $form.removeClass('show-back');
            $tip.text('');
        } else {
            $form.addClass('show-back');
        }
    });

    // 监听按钮点击
    $form.find('.lock-screen-enter').click(function (e) {
        doVer(true);
    });

    // 处理事件
    function doVer(emptyRest) {
        if ($form.hasClass('show-psw')) {
            $psw.focus();
            var psw = $psw.val();
            if (!psw) {
                emptyRest ? restForm() : $tip.text('<?php echo L('lockscreen_status_password');?>');
            } else {
                $.get("?m=admin&c=index&a=public_login_screenlock", { lock_password: psw},function(data){
                    if(data==1) {
                        $('#ew-lock-screen-group').css('display','none');
                        restForm();
                    } else if(data==3) {
                        $psw.val('');
                        $tip.text('<?php echo L('wait_1_hour_lock');?>');
                        $form.addClass('show-back');
                    } else {
                        strings = data.split('|');
                        $psw.val('');
                        $tip.text('<?php echo L('password_error_lock');?>'+strings[1]+'<?php echo L('password_error_lock2');?>');
                        $form.addClass('show-back');
                    }
                });
            }
        } else {
            $form.addClass('show-psw show-back');
            $psw.focus();
        }
    }

    // 重置
    function restForm() {
        $psw.blur();
        $psw.val('');
        $tip.text('');
        $form.removeClass('show-psw show-back');
    }
    
    $tool.on('click', function () {
        var tool = $tool.children().attr("data-url");
        location.href = tool;
    });

    // 时间、日期
    function setDate() {
        var FIRSTYEAR = 1998;
        var LASTYEAR = 2031;
        var dateObj = new Date(); //表示当前系统时间的Date对象
        var year = dateObj.getFullYear(); //当前系统时间的完整年份值
        var month = dateObj.getMonth()+1; //当前系统时间的月份值
        var date = dateObj.getDate(); //当前系统时间的月份中的日
        var day = dateObj.getDay(); //当前系统时间中的星期值
        var weeks = ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
        var week = weeks[day]; //根据星期值，从数组中获取对应的星期字符串
        var hour = dateObj.getHours(); //当前系统时间的小时值
        var minute = dateObj.getMinutes(); //当前系统时间的分钟值
        var second = dateObj.getSeconds(); //当前系统时间的秒钟值
        var LunarCal = [
        new tagLunarCal( 27,  5, 3, 43, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1, 1, 0, 1 ),
        new tagLunarCal( 46,  0, 4, 48, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1 ), /* 88 */
        new tagLunarCal( 35,  0, 5, 53, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1 ), /* 89 */
        new tagLunarCal( 23,  4, 0, 59, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 42,  0, 1,  4, 1, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 31,  0, 2,  9, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 21,  2, 3, 14, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1 ), /* 93 */
        new tagLunarCal( 39,  0, 5, 20, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 28,  7, 6, 25, 1, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 1 ),
        new tagLunarCal( 48,  0, 0, 30, 0, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 1 ),
        new tagLunarCal( 37,  0, 1, 35, 1, 0, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1 ), /* 97 */
        new tagLunarCal( 25,  5, 3, 41, 1, 1, 0, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
        new tagLunarCal( 44,  0, 4, 46, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1 ),
        new tagLunarCal( 33,  0, 5, 51, 1, 0, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 22,  4, 6, 56, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ), /* 101 */
        new tagLunarCal( 40,  0, 1,  2, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 30,  9, 2,  7, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 49,  0, 3, 12, 0, 1, 0, 0, 1, 0, 1, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 38,  0, 4, 17, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0 ), /* 105 */
        new tagLunarCal( 27,  6, 6, 23, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1 ),
        new tagLunarCal( 46,  0, 0, 28, 0, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 0 ),
        new tagLunarCal( 35,  0, 1, 33, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ),
        new tagLunarCal( 24,  4, 2, 38, 0, 1, 1, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1 ), /* 109 */
        new tagLunarCal( 42,  0, 4, 44, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 31,  0, 5, 49, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0 ),
        new tagLunarCal( 21,  2, 6, 54, 0, 1, 0, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1 ),
        new tagLunarCal( 40,  0, 0, 59, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1 ), /* 113 */
        new tagLunarCal( 28,  6, 2,  5, 1, 0, 1, 0, 0, 1, 0, 1, 0, 1, 1, 1, 0 ),
        new tagLunarCal( 47,  0, 3, 10, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 1, 0, 1 ),
        new tagLunarCal( 36,  0, 4, 15, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0, 1 ),
        new tagLunarCal( 25,  5, 5, 20, 1, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 0 ), /* 117 */
        new tagLunarCal( 43,  0, 0, 26, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 0, 1 ),
        new tagLunarCal( 32,  0, 1, 31, 1, 1, 0, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0 ),
        new tagLunarCal( 22,  3, 2, 36, 0, 1, 1, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0 ) ];
        /* 民國年每月之日數 */
        SolarCal = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
        /* 民國年每月之累積日數, 平年與閏年 */ 
        SolarDays = [  0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365, 396,  0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366, 397 ];
        AnimalIdx = ["马", "羊", "猴", "鸡", "狗", "猪", "鼠", "牛", "虎", "兔", "龙", "蛇" ];
        LocationIdx = [ "南", "东", "北", "西" ];
        if ( year <= FIRSTYEAR || year > LASTYEAR ) return 1;
        sm = month - 1;
        if ( sm < 0 || sm > 11 ) return 2;
        leap = GetLeap( year );
        if ( sm == 1 )
        d = leap + 28;
        else
        d = SolarCal[sm];
        if ( date < 1 || date > d ) return 3;
        y = year - FIRSTYEAR;
        acc = SolarDays[ leap*14 + sm ] + date;
        kc = acc + LunarCal[y].BaseKanChih;
        Kan = kc % 10;
        Chih = kc % 12;
        Location = LocationIdx[kc % 4];
        Age = kc % 60;
        if ( Age < 22 )
        Age = 22 - Age;
        else
        Age = 82 - Age;
        Animal = AnimalIdx[ Chih ];
        if ( acc <= LunarCal[y].BaseDays ) {
        y--;
        LunarYear = year - 1;
        leap = GetLeap( LunarYear );
        sm += 12;
        acc = SolarDays[leap*14 + sm] + date;
        }
        else
        LunarYear = year;
        l1 = LunarCal[y].BaseDays;
        for ( i=0; i<13; i++ ) {
        l2 = l1 + LunarCal[y].MonthDays[i] + 29;
        if ( acc <= l2 ) break;
        l1 = l2;
        }
        LunarMonth = i + 1;
        LunarDate = acc - l1;
        im = LunarCal[y].Intercalation;
        if ( im != 0 && LunarMonth > im ) {
        LunarMonth--;
        if ( LunarMonth == im ) LunarMonth = -im;
        }
        if ( LunarMonth > 12 ) LunarMonth -= 12;
        var months = ["正","二","三","四","五","六","七","八","九","十","十一","腊"];
        var days = ["初一","初二","初三","初四","初五","初六","初七","初八","初九","初十","十一","十二","十三","十四","十五","十六","十七","十八","十九","二十","廿一","廿二","廿三","廿四","廿五","廿六","廿七","廿八","廿九","三十"];
        if (LunarMonth < 0) {
        LunarMonth = "闰" + months[-LunarMonth-1];
        }else{
        LunarMonth = months[LunarMonth-1];
        }
        LunarDate = days[LunarDate-1];
        var timeValue = "" +((hour >= 12) ? (hour >= 18) ? "晚上好！" : "下午好！" : "上午好！" ); //当前时间属于上午、晚上还是下午
        newDate = dateFilter(year)+"年"+dateFilter(month)+"月"+dateFilter(date)+"日，"+week+"【农历" + LunarMonth + "月" + LunarDate + "】";
        $time.text(dateFilter(hour)+":"+dateFilter(minute)+":"+dateFilter(second));
        $date.text(timeValue+newDate);
    }
    
    //值小于10时，在前面补0
    function dateFilter(date){
        if(date < 10){return "0"+date;}
        return date;
    }
    /* 求此民國年是否為閏年, 返回 0 為平年, 1 為閏年 */
    function GetLeap( year ) {
        if ( year % 400 == 0 )
        return 1;
        else if ( year % 100 == 0 )
        return 0;
        else if ( year % 4 == 0 )
        return 1;
        else
        return 0;
    }
    function tagLunarCal( d, i, w, k, m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13) {
        this.BaseDays = d;         /* 到民國 1 月 1 日到農曆正月初一的累積日數 */
        this.Intercalation = i;    /* 閏月月份. 0==此年沒有閏月 */
        this.Baseday = w;      /* 此年民國 1 月 1 日為星期幾再減 1 */
        this.BaseKanChih = k;      /* 此年民國 1 月 1 日之干支序號減 1 */
        this.MonthDays = [ m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, m13 ]; /* 此農曆年每月之大小, 0==小月(29日), 1==大月(30日) */
    }
    
    setInterval(function () {setDate();}, 1000);

});
</script>
<div class="header">
	<div class="logo lf"><a href="<?php echo $currentsite['domain']?>" target="_blank"><span class="invisible"><?php echo L('title')?></span></a></div>
    <div class="rt-col">
    	<div class="tab_style white cut_line text-r"><a href="javascript:;" onclick="lock_screen()"><i class="layui-icon layui-icon-password"></i> <?php echo L('lockscreen')?></a>
    <ul id="Skin">
		<li class="s1 styleswitch" rel="styles1"></li>
		<li class="s2 styleswitch" rel="styles2"></li>
		<li class="s3 styleswitch" rel="styles3"></li>
	</ul>
        </div>
    </div>
    <div class="col-auto">
    	<div class="log white cut_line"><?php echo L('hello'),$admin_username?>  [<?php echo $rolename?>]<span>|</span><a href="javascript:;" onclick="Dialog.confirm('<?php echo L('confirm_exit_login');?>', function(){location.href='?m=admin&c=index&a=public_logout';});">[<?php echo L('exit')?>]</a><span>|</span>
    		<a href="<?php echo $currentsite['domain']?>" target="_blank" id="site_homepage"><?php echo L('site_homepage')?></a><span>|</span>
    		<a href="index.php?m=member" target="_blank"><?php echo L('member_center')?></a><span>|</span>
    		<a href="index.php?m=search" target="_blank" id="site_search"><?php echo L('search')?></a>
    	</div>
        <ul class="nav white" id="top_menu">
        <?php
        $array = admin::admin_menu(0);
        foreach($array as $_value) {
        	if($_value['id']==10) {
        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
        		
        	} else {
        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
        	}      	
        }
        ?>
            <li class="tab_web"><a href="javascript:;"><span><?php echo $currentsite['name']?></span></a></li>
        </ul>
    </div>
</div>
<div id="content">
	<div class="col-left left_menu">
    	<div id="Scroll"><div id="leftMain"></div></div>
        <a href="javascript:;" id="openClose" onmouseover="layer.tips('<?php echo L('spread_or_closed')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" style="outline-style: none; outline-color: invert; outline-width: medium;" hideFocus="hidefocus" class="open"><i class="fa fa-chevron-left"></i></a>
    </div>
    <div class="col-1 lf cat-menu" id="display_center_id" style="display:none" height="100%">
        <div class="content center_frame">
            <iframe name="center_frame" id="center_frame" src="" frameborder="false" scrolling="auto" style="border:none" width="100%" height="auto" allowtransparency="true"></iframe>
        </div>
    </div>
    <div class="col-1 lf cat-menu-on" id="display_menu_id" style="display:none" height="100%">
        <a href="javascript:;" id="frameopenClose" onmouseover="layer.tips('<?php echo L('spread_or_closed')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" style="outline-style: none; outline-color: invert; outline-width: medium;" hideFocus="hidefocus" class="open"><i class="fa fa-chevron-left"></i></a>
    </div>
    <div class="col-auto mr8">
    <div class="crumbs">
    <div class="shortcut cu-span"><a href="?m=content&c=create_all_html&a=all_update&pc_hash=<?php echo $_SESSION['pc_hash'];?>" target="right"><i class="fa fa-file-code-o"></i> <span><?php echo L('create_all')?></span></a><a href="?m=content&c=create_html&a=public_index&pc_hash=<?php echo $_SESSION['pc_hash'];?>" target="right"><i class="fa fa-file-code-o"></i> <span><?php echo L('create_index')?></span></a><a href="?m=admin&c=cache_all&a=init&pc_hash=<?php echo $_SESSION['pc_hash'];?>" target="right"><i class="fa fa-refresh"></i> <span><?php echo L('update_backup')?></span></a><a href="javascript:void(0);" onclick="var diag = new Dialog({id:'map',title:'<?php echo L('background_map')?>',url:'<?php echo SELF;?>?m=admin&c=index&a=public_map&pc_hash='+pc_hash,width:710,height:500,modal:true});diag.show();"><i class="fa fa-sitemap"></i> <span><?php echo L('background_map')?></span></a><?php echo runhook('admin_top_left_menu')?></div>
    <?php echo L('current_position')?><span id="current_pos"></span></div>
    	<div class="col-1">
        	<div class="content" style="position:relative; overflow:hidden">
                <iframe name="right" id="rightMain" src="?m=admin&c=index&a=public_main" frameborder="false" scrolling="auto" style="border:none; margin-bottom:30px" width="100%" height="auto" allowtransparency="true"></iframe>
                <div class="fav-nav">
					<div id="panellist">
						<?php foreach($adminpanel as $v) {?>
								<span>
								<a onclick="paneladdclass(this);" target="right" href="<?php echo $v['url'].'menuid='.$v['menuid'].'&pc_hash='.$_SESSION['pc_hash'];?>"><?php echo L($v['name'])?></a>
								<a class="panel-delete" href="javascript:delete_panel(<?php echo $v['menuid']?>, this);"></a></span>
						<?php }?>
					</div>
					<div id="paneladd"></div>
					<input type="hidden" id="menuid" value="">
					<input type="hidden" id="bigid" value="" />
                    <div id="help" class="fav-help"></div>
				</div>
        	</div>
        </div>
    </div>
</div>
<div class="tab-web-panel hidden" style="position:absolute; z-index:999; background:#fff">
<ul>
<?php foreach ($sitelist as $key=>$v):?>
	<li style="margin:0"><a href="javascript:site_select(<?php echo $v['siteid']?>, '<?php echo new_addslashes($v['name'])?>', '<?php echo $v['domain']?>', '<?php echo $v['siteid']?>')"><?php echo $v['name']?></a></li>
<?php endforeach;?>
</ul>
</div>
<div class="scroll"><a href="javascript:;" class="per" onmouseover="layer.tips('<?php echo L('setting_scroll')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" onclick="menuScroll(1);"></a><a href="javascript:;" class="next" onmouseover="layer.tips('<?php echo L('setting_scroll')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" onclick="menuScroll(2);"></a></div>
<script type="text/javascript">
if(!Array.prototype.map)
Array.prototype.map = function(fn,scope) {
	var result = [],ri = 0;
	for (var i = 0,n = this.length; i < n; i++){
		if(i in this){
			result[ri++]  = fn.call(scope ,this[i],i,this);
		}
	}
	return result;
};

var getWindowSize = function(){
return ["Height","Width"].map(function(name){
  return window["inner"+name] ||
	document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
});
}
window.onload = function (){
	if(!+"\v1" && !document.querySelector) { // for IE6 IE7
	  document.body.onresize = resize;
	} else { 
	  window.onresize = resize;
	}
	function resize() {
		wSize();
		return false;
	}
}
function wSize(){
	//这是一字符串
	var str=getWindowSize();
	var strs= new Array(); //定义一数组
	strs=str.toString().split(","); //字符分割
	var heights = strs[0]-148,Body = $('body');$('#rightMain').height(heights);   
	//iframe.height = strs[0]-46;
	if(strs[1]<980){
		$('.header').css('width',980+'px');
		$('#content').css('width',980+'px');
		Body.attr('scroll','');
		Body.removeClass('objbody');
	}else{
		$('.header').css('width','auto');
		$('#content').css('width','auto');
		Body.attr('scroll','no');
		Body.addClass('objbody');
	}
	
	//修改框架改变后的数值
	var openClose = $("#rightMain").height()+44;
	$('#center_frame').height(openClose-8);
	$('#display_menu_id').height(openClose-7);
	$(".left_menu").height(openClose-14);
	//$("#openClose").height(openClose+30);
	$("#Scroll").height(openClose-14);
	windowW();
}
wSize();
function windowW(){
	if($('#Scroll').height()<$("#leftMain").height()){
		$(".scroll").show();
	}else{
		$(".scroll").hide();
	}
}
windowW();
//站点下拉菜单
$(function(){
	var offset = $(".tab_web").offset();
	var tab_web_panel = $(".tab-web-panel");
	$(".tab_web").mouseover(function(){
			tab_web_panel.css({ "left": +$(this).offset().left+4, "top": +offset.top+$('.tab_web').height()});
			tab_web_panel.show();
			if(tab_web_panel.height() > 200){
				tab_web_panel.children("ul").addClass("tab-scroll");
			}
		});
	$(".tab_web span").mouseout(function(){hidden_site_list_1()});
	$(".tab-web-panel").mouseover(function(){clearh();$('.tab_web a').addClass('on')}).mouseout(function(){hidden_site_list_1();$('.tab_web a').removeClass('on')});
	//默认载入左侧菜单
	$("#leftMain").load("?m=admin&c=index&a=public_menu_left&menuid=10");

	//修改面板切换
	// $("#btnx").removeClass("btns2");
	// $("#Site_model,#btnx h6").css("display","none");
	// $("#btnx").hover(function(){$("#Site_model,#btnx h6").css("display","block");$(this).addClass("btns2");$(".bg_btn").hide();},function(){$("#Site_model,#btnx h6").css("display","none");$(this).removeClass("btns2");$(".bg_btn").show();});
	// $("#Site_model li").hover(function(){$(this).toggleClass("hvs");},function(){$(this).toggleClass("hvs");});
	// $("#Site_model li").click(function(){$("#Site_model li").removeClass("ac"); $(this).addClass("ac");});
	$("#btnx").toggle(function(){_Site_M('project1');},function(){_Site_M();}); 
})
//站点选择
function site_select(id,name, domain, siteid) {
	$(".tab_web span").html(name);
	$.get("?m=admin&c=index&a=public_set_siteid&siteid="+id,function(data){
		if (data==1){
				window.top.right.location.reload();
				window.top.center_frame.location.reload();
				$.get("?m=admin&c=index&a=public_menu_left&menuid=0&parentid="+$("#bigid").val(), function(data){$('.top_menu').remove();$('#top_menu').prepend(data)});
			}
		});
	$('#site_homepage').attr('href', domain);
	$('#site_search').attr('href', 'index.php?m=search&siteid='+siteid);
}
//隐藏站点下拉。
var s = 0;
var h;
function hidden_site_list() {
	s++;
	if(s>=3) {
		$('.tab-web-panel').hide();
		clearInterval(h);
		s = 0;
	}
}
function clearh(){
	if(h)clearInterval(h);
}
function hidden_site_list_1() {
	h = setInterval("hidden_site_list()", 1);
}

//左侧开关
$("#openClose").click(function(){
	if($(this).data('clicknum')==1) {
		$("html").removeClass("on");
		$(".left_menu").removeClass("left_menu_on");
		$(this).removeClass("close");
		$(this).children('i').addClass("fa-chevron-left").removeClass("fa-chevron-right");
		$(this).data('clicknum', 0);
		$(".scroll").show();
	} else {
		$(".left_menu").addClass("left_menu_on");
		$(this).addClass("close");
		$("html").addClass("on");
		$(this).children('i').addClass("fa-chevron-right").removeClass("fa-chevron-left");
		$(this).data('clicknum', 1);
		$(".scroll").hide();
	}
	return false;
});
//左侧开关
$("#frameopenClose").click(function(){
	if($(this).data('clicknum')==1) {
		$(".cat-menu").show();
		$(this).children('i').addClass("fa-chevron-left").removeClass("fa-chevron-right");
		$(this).data('clicknum', 0);
	} else {
		$(".cat-menu").hide();
		$(this).children('i').addClass("fa-chevron-right").removeClass("fa-chevron-left");
		$(this).data('clicknum', 1);
	}
	return false;
});

function _M(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#bigid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
	$("#leftMain").load("?m=admin&c=index&a=public_menu_left&menuid="+menuid, {limit: 25}, function(){
		windowW();
	});
	$("#rightMain").attr('src', targetUrl+'&pc_hash='+pc_hash);
	$('.top_menu').removeClass("on");
	$('#_M'+menuid).addClass("on");
	$.get("?m=admin&c=index&a=public_current_pos&menuid="+menuid, function(data){
		$("#current_pos").html(data);
	});
	//当点击顶部菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
	$('#display_menu_id').css('display','none');
	//显示左侧菜单，当点击顶部时，展开左侧
	$(".left_menu").removeClass("left_menu_on");
	$("#openClose").removeClass("close");
	$("html").removeClass("on");
	$("#openClose").data('clicknum', 0);
	$("#current_pos").data('clicknum', 1);
}
function _MP(menuid,targetUrl) {
	$("#menuid").val(menuid);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
	//当点击顶部菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
	$('#display_menu_id').css('display','none');
	$("#rightMain").attr('src', targetUrl+'&menuid='+menuid+'&pc_hash='+pc_hash);
	$('.sub_menu').removeClass("on fb blue");
	$('#_MP'+menuid).addClass("on fb blue");
	$.get("?m=admin&c=index&a=public_current_pos&menuid="+menuid, function(data){
		$("#current_pos").html(data+'<span id="current_pos_attr"></span>');
	});
	$("#current_pos").data('clicknum', 1);
}

function add_panel() {
	var menuid = $("#menuid").val();
	$.ajax({
		type: "POST",
		url: "?m=admin&c=index&a=public_ajax_add_panel",
		data: "menuid=" + menuid,
		success: function(data){
			if(data) {
				$("#panellist").html(data);
			}
		}
	});
}
function delete_panel(menuid, id) {
	$.ajax({
		type: "POST",
		url: "?m=admin&c=index&a=public_ajax_delete_panel",
		data: "menuid=" + menuid,
		success: function(data){
			$("#panellist").html(data);
		}
	});
}

function paneladdclass(id) {
	$("#panellist span a[class='on']").removeClass();
	$(id).addClass('on')
}
setInterval("session_life()", 160000);
function session_life() {
	$.get("?m=admin&c=index&a=public_session_life");
}

//修改锁屏界面
function lock_screen() {
	$.get("?m=admin&c=index&a=public_lock_screen");
	$('#ew-lock-screen-group').css('display','');
	$('#lock_password').attr("placeholder","<?php echo L('setting_input_password');?>");
}

(function(){
    var addEvent = (function(){
             if (window.addEventListener) {
                return function(el, sType, fn, capture) {
                    el.addEventListener(sType, fn, (capture));
                };
            } else if (window.attachEvent) {
                return function(el, sType, fn, capture) {
                    el.attachEvent("on" + sType, fn);
                };
            } else {
                return function(){};
            }
        })(),
    Scroll = document.getElementById('Scroll');
    // IE6/IE7/IE8/IE10/IE11/Opera 10+/Safari5+
    addEvent(Scroll, 'mousewheel', function(event){
        event = window.event || event ;  
		if(event.wheelDelta <= 0 || event.detail > 0) {
				Scroll.scrollTop = Scroll.scrollTop + 29;
			} else {
				Scroll.scrollTop = Scroll.scrollTop - 29;
		}
    }, false);

    // Firefox 3.5+
    addEvent(Scroll, 'DOMMouseScroll',  function(event){
        event = window.event || event ;
		if(event.wheelDelta <= 0 || event.detail > 0) {
				Scroll.scrollTop = Scroll.scrollTop + 29;
			} else {
				Scroll.scrollTop = Scroll.scrollTop - 29;
		}
    }, false);
	
})();
function menuScroll(num){
	var Scroll = document.getElementById('Scroll');
	if(num==1){
		Scroll.scrollTop = Scroll.scrollTop - 60;
	}else{
		Scroll.scrollTop = Scroll.scrollTop + 60;
	}
}
function _Site_M(project) {
	var id = '';
	$('#top_menu li').each(function (){
		var S_class = $(this).attr('class');
		if ($(this).attr('id')){
			$(this).hide();
		}
		if (S_class=='on top_menu' || S_class=='top_menu on'){
			id = $(this).attr('id');
		}
	});
	$('#'+id).show();
	id = id.substring(2, id.length);
	if (!project){
		project = 0;
	}
	$.ajaxSettings.async = false; 
	$.getJSON('index.php', {m:'admin', c:'index', a:'public_set_model', 'site_model':project, 'time':Math.random()}, function (data){
		$.each(data, function(i, n){
			$('#_M'+n).show();
		})
	})
	$("#leftMain").load("?m=admin&c=index&a=public_menu_left&menuid="+id+'&time='+Math.random());
}

<?php if($site_model) { ?> _Site_M('<?php echo $site_model?>'); <?php }?>
layui.use(['layer'], function () {
    var layer = layui.layer;
});
</script>
</body>
</html>