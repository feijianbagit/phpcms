<?php $show_header = $show_validator = $show_scroll = 1; include $this->admin_tpl('header', 'attachment');?>
<script src="<?php echo JS_PATH?>assets/ds.min.js"></script>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo JS_PATH;?>uploadify/3.2.1/uploadifive.css"/>
<script type="text/javascript" src="<?php echo JS_PATH;?>uploadify/3.2.1/jquery.uploadifive.min.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo JS_PATH?>h5upload/handlers.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>layer/layer.js"></script>
<script type="text/javascript">
<?php echo initupload($_GET['module'],$_GET['catid'],$args,$this->userid,$this->groupid,$this->isadmin,$userid_flash)?>
</script>
<div class="pad-10">
    <div class="col-tab">
        <ul class="tabBut cu-li">
            <li id="tab_h5_1"<?php echo $tab_status?> onclick="SwapTab('h5','on','',5,1);"><?php echo L('upload_attachment')?></li>
            <li id="tab_h5_2" onclick="SwapTab('h5','on','',5,2);"><?php echo L('net_file')?></li>
            <?php if($allowupload && $this->admin_username && $_SESSION['userid']) {?>
            <li id="tab_h5_3" onclick="SwapTab('h5','on','',5,3);set_iframe('album_list','index.php?m=attachment&c=attachments&a=album_load&args=<?php echo $args?>');"><?php echo L('gallery')?></li>
            <!--<li id="tab_h5_4" onclick="SwapTab('h5','on','',5,4);set_iframe('album_dir','index.php?m=attachment&c=attachments&a=album_dir&args=<?php echo $args?>');"><?php echo L('directory_browse')?></li>-->
            <?php }?>
            <?php if($att_not_used!='') {?>
            <li id="tab_h5_5" class="on icon" onclick="SwapTab('h5','on','',5,5);"><?php echo L('att_not_used')?></li>
            <?php }?>
        </ul>
        <div id="div_h5_1" class="content pad-10<?php echo $div_status?>">
            <div>
                <div id="queue"></div>
                <input id="file_upload" name="file_upload" type="file" multiple="true">
                <div id="nameTip" class="onShow"><?php echo L('upload_up_to')?><font color="red"> <?php echo $file_upload_limit?></font> <?php echo L('attachments')?>,<?php echo L('largest')?> <font color="red"><?php echo $file_size_limit?></font></div>
                <div class="bk3"></div>
                <div class="lh24"><?php echo L('supported')?> <font style="font-family: Arial, Helvetica, sans-serif"><?php echo str_replace(array('*.',';'),array('','、'),$file_types)?></font> <?php echo L('formats')?></div>
            </div>
            <div class="bk10"></div>
            <fieldset class="blue pad-10" id="h5upload">
                <legend><?php echo L('lists')?></legend>
                <div id="fsUploadProgress"></div>
                <div class="files" id="fsUpload"></div>
            </fieldset>
        </div>
        <div id="div_h5_2" class="contentList pad-10 hidden">
            <div class="bk10"></div>
            <?php echo L('enter_address')?><div class="bk3"></div><input type="text" name="info[filename]" class="input-text" value=""  style="width:550px;"  onblur="addonlinefile(this)">
            <div class="bk10"></div>
        </div>
        <?php if($allowupload && $this->admin_username && $_SESSION['userid']) {?>
        <div id="div_h5_3" class="contentList pad-10 hidden">
            <ul class="attachment-list">
                <iframe name="album-list" src="#" frameborder="false" scrolling="no" style="overflow-x:hidden;border:none" width="100%" height="450" allowtransparency="true" id="album_list"></iframe>
            </ul>
        </div>
        <div id="div_h5_4" class="contentList pad-10 hidden">
            <ul class="attachment-list">
             <iframe name="album-dir" src="#" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="450" allowtransparency="true" id="album_dir"></iframe>
            </ul>
        </div>
        <?php }?>
        <?php if($att_not_used!='') {?>
        <div role="presentation" id="div_h5_5" class="contentList pad-10">
            <div style="margin-bottom:10px;"><span id="all" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全选</span><span id="allno" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全不选</span><span id="other" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">反选</span></div>
            <div class="explain-col"><?php echo L('att_not_used_desc')?></div>
            <div class="files" id="album">
            <?php if(is_array($att) && !empty($att)){ foreach ($att as $_v) {?>
                <div class="files_row">
                    <span class="checkbox"></span>
                    <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['aid']?>" />
                    <a class="off" title="<?php echo $_v['filename']?>"><img width="<?php echo $_v['width']?>" id="<?php echo $r['aid']?>" path="<?php echo $_v['src']?>" src="<?php echo $_v['fileimg']?>" title="<?php echo $_v['filename']?>" size="<?php echo $_v['size']?>"></a>
                    <i class="size"><?php echo $_v['size']?></i>
                    <i class="name" title="<?php echo $_v['filename']?>"><?php echo $_v['filename']?></i>
                </div>
            <?php }}?>
            </div>
        </div>   
        <?php }?>     
    <div id="att-status" class="hidden"></div>
    <div id="att-status-del" class="hidden"></div>
    <div id="att-name" class="hidden"></div>
<!-- h5 -->
</div>
</body>
<script type="text/javascript">
if ($.browser.mozilla) {
    window.onload=function(){
      if (location.href.indexOf("&rand=")<0) {
            location.href=location.href+"&rand="+Math.random();
        }
    }
}
function imgWrap(obj){
    $(obj).hasClass('on') ? $(obj).removeClass("on") : $(obj).addClass("on");
}

function SwapTab(name,cls_show,cls_hide,cnt,cur) {
    for(i=1;i<=cnt;i++){
        if(i==cur){
             $('#div_'+name+'_'+i).show();
             $('#tab_'+name+'_'+i).addClass(cls_show);
             $('#tab_'+name+'_'+i).removeClass(cls_hide);
        }else{
             $('#div_'+name+'_'+i).hide();
             $('#tab_'+name+'_'+i).removeClass(cls_show);
             $('#tab_'+name+'_'+i).addClass(cls_hide);
        }
    }
}

function addonlinefile(obj) {
    var strs = $(obj).val() ? '|'+ $(obj).val() :'';
    $('#att-status').html(strs);
}

function set_iframe(id,src){
    $("#"+id).attr("src",src); 
}
var ds = new DragSelect({
    selectables: document.getElementsByClassName('files_row'),
    multiSelectMode: true,
    //选中
    onElementSelect: function(element){
        var id = $(element).children("a").children("img").attr("id");
        var src = $(element).children("a").children("img").attr("path");
        var filename = $(element).children("a").children("img").attr("title");
        var size = $(element).children("a").children("img").attr("size");
        var num = $('#att-status').html().split('|').length;
        var file_upload_limit = '<?php echo $file_upload_limit?>';
        if(num > file_upload_limit) {
            //Dialog.alert('<?php echo L('attachment_tip1')?>'+file_upload_limit+'<?php echo L('attachment_tip2')?>');
        }else{
            $(element).children("a").addClass("on");
            //$.get('index.php?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
            $('#att-status').append('|'+src);
            $('#att-name').append('|'+filename);
            $(element).addClass('on').find('input[type="checkbox"]').prop('checked', true);
        }
    },
    //取消选中
    onElementUnselect: function(element){
        $(element).children("a").removeClass("on");
        var id = $(element).children("a").children("img").attr("id");
        var src = $(element).children("a").children("img").attr("path");
        var filename = $(element).children("a").children("img").attr("title");
        var size = $(element).children("a").children("img").attr("size");
        var imgstr = $("#att-status").html();
        var length = $("a[class='on']").children("img").length;
        var strs = filenames = '';
        //$.get('index.php?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        for(var i=0;i<length;i++){
            strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
            filenames += '|'+$("a[class='on']").children("img").eq(i).attr('title');
        }
        $('#att-status').html(strs);
        $('#att-status').html(filenames);
        $(element).removeClass('on').find('input[type="checkbox"]').prop('checked', false);
    }
});
$(function(){
    //区域内的所有可选元素
    var selects = ds.selectables;

    //全选
    $('#all').click(function(){
        ds.setSelection(selects);
    });

    //全不选
    $('#allno').click(function(){
        ds.clearSelection();
    });

    //反选
    $('#other').click(function(){
        ds.toggleSelection(selects);
    });
});
</script>
</html>
