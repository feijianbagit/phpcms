/**
 * 会员中心公用js
 *
 */
function geturlpathname() {
	var url = document.location.toString();
	var arrUrl = url.split("//");
	var start = arrUrl[1].indexOf("/");
	var relUrl = arrUrl[1].substring(start);
	if(relUrl.indexOf("?") != -1){
		relUrl = relUrl.split("?")[0];
	}
	return relUrl;
}
/**
 * 隐藏html element
 */
function hide_element(name) {
	$('#'+name+'').fadeOut("slow");
}

/**
 * 显示html element
 */
function show_element(name) {
	$('#'+name+'').fadeIn("slow");
}

/*$(document).ready(function(){
　　$("input.input-text").blur(function () { this.className='input-text'; } );
　　$(":text").focus(function(){this.className='input-focus';});
});*/

/**
 * url跳转
 */
function redirect(url) {
	if(url.indexOf('://') == -1 && url.substr(0, 1) != '/' && url.substr(0, 1) != '?') url = $('base').attr('href')+url;
	location.href = url;
}
function image_priview(img) {
	var diag = new Dialog({
		id:'image_priview',
		title:'图片查看',
		html:'<img src="'+img+'" />',
		modal:true,
		autoClose:5
	});
	diag.show();
}

function remove_div(id) {
	$('#'+id).html(' ');
}

function select_catids() {
	$('#addbutton').attr('disabled',false);

}

//商业用户会添加 num，普通用户默认为5
function transact(update,fromfiled,tofiled, num) {
	if(update=='delete') {
		var fieldvalue = $('#'+tofiled).val();

		$("#"+tofiled+" option").each(function() {
		   if($(this).val() == fieldvalue){
			$(this).remove();
		   }
		});
	} else {
		var fieldvalue = $('#'+fromfiled).val();
		var have_exists = 0;
		var len = $("#"+tofiled+" option").size();
		if(len>=num) {
			alert('最多添加 '+num+' 项');
			return false;
		}
		$("#"+tofiled+" option").each(function() {
		   if($(this).val() == fieldvalue){
			have_exists = 1;
			alert('已经添加到列表中');
			return false;
		   }
		});
		if(have_exists==0) {
			obj = $('#'+fromfiled+' option:selected');
			text = obj.text();
			text = text.replace('│', '');
			text = text.replace('├ ', '');
			text = text.replace('└ ', '');
			text = text.trim();
			fieldvalue = "<option value='"+fieldvalue+"'>"+text+"</option>"
			$('#'+tofiled).append(fieldvalue);
			$('#deletebutton').attr('disabled','');
		}
	}
}

function omnipotent(id,linkurl,title,close_type,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if(!w) w=700;
	if(!h) h=500;
	var diag = new Dialog({
		id:id,
		title:title,
		url:geturlpathname()+linkurl,
		width:w,
		height:h,
		modal:true
	});
	diag.onOk = function(){
		if(close_type==1) {
			diag.close();
		} else {
			var form = $DW.$('#dosubmit');
			form.click();
		}
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

String.prototype.trim = function() {
	var str = this,
	whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
	for (var i = 0,len = str.length; i < len; i++) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(i);
			break;
		}
	}
	for (i = str.length - 1; i >= 0; i--) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}