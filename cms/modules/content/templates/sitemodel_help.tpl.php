<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="container">
        <h2>后台显示字段回调</h2>
        <div class="content-text">
    <p>回调是用于在列表显示时对其值进行格式化，如果不填写回调函数，那么就会原样显示数据库储存内容。<br/></p><p>CMS默认的回调函数有：</p><pre class="brush:html;toolbar:false">标题：title
标题带推荐位图标：ptitle
标题带推荐位：position
栏目：catid
日期时间：datetime
日期：date
会员信息：author
用于列表显示单缩略图：image
用于列表显示多缩略图：images
用于列表显示单文件：file
用于列表显示多文件：files
用于列表关联主题：ctitle
用于列表显示状态：status
文本显示：text
地区联动：linkage_address
联动菜单（单选）名称: linkage_name
联动菜单（多选）名称: linkages_name
单选字段名称：radio_name
下拉字段名称：select_name
复选框字段名称：checkbox_name
实时存储时间值：save_time_value
实时存储文本值：save_text_value
实时存储选择值：save_select_value
用于列表显示价格：price
用于列表显示价格：money
用于列表显示积分：score
头像：avatar</pre></div>


    <p> &nbsp;&nbsp;</p>
</div>
<style>
body {
    background: #fff;
    font-family: Menlo,Monaco,Consolas,"Courier New",monospace;
}
img {max-width: 80%}
h2 {
    padding-bottom: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e7e7eb;
}
.h1, .h2, .h3, h1, h2, h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
}
img,video {
    border: 2px solid #f1f3f4;
    padding: 10px;
    border-radius: 5px;
    margin: 5px;
}
p {
    margin: 0 0 10px;
}
.container {
    width: 100%;
    padding: 0px 28px;
}
.content-text table {
    border: 1px solid #000000;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100% !important;
    word-break: break-all;
}
.content-text table th {
    padding: 8px !important;
    line-height: 30px !important;
    border: 1px solid #000000;
    background-color: rgb(191, 191, 191);
}
.content-text table td {
    word-wrap: break-word;
    border: 1px solid #000000;
    padding: 4px 8px !important;
    font-size: 12px;
    line-height: 30px !important;
    vertical-align: middle;
}
</style>
</body>
</html>