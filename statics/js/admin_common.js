$(function(){
	if ($('.table-checkable')) {
		var table = $('.table-checkable');
		table.find('.group-checkable').change(function () {
			var set = jQuery(this).attr("data-set");
			var checked = jQuery(this).is(":checked");
			jQuery(set).each(function () {
				if (checked) {
					$(this).prop("checked", true);
					$(this).parents('tr').addClass("active");
				} else {
					$(this).prop("checked", false);
					$(this).parents('tr').removeClass("active");
				}
			});
		});
	}
});
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
function confirmurl(url,message) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	Dialog.confirm(message,function() {
		redirect(url);
	});
}
function redirect(url) {
	location.href = url;
}
function topinyin(name, from, url) {
	var val = $("#" + from).val();
	if ($("#" + name).val()) {
		return false
	}
	$.get(url+'&name='+val+'&rand='+Math.random(), function(data){
		$('#'+name).val(data);
	});
}
//滚动条
$(function(){
	$(":text").addClass('input-text');
})

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")=='checked') {
		$("input[name='"+name+"']").each(function() {
  			$(this).attr("checked","checked");
			$(this).parents('tr').addClass("active");
		});
	} else {
		$("input[name='"+name+"']").each(function() {
  			$(this).removeAttr("checked");
			$(this).parents('tr').removeClass("active");
		});
	}
}
function openwinx(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:geturlpathname()+url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.cancelText = '关闭(X)';
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
function contentopen(url,name,w,h) {
	if(!w) w='100%';
	if(!h) h='100%';
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	var diag = new Dialog({
		id:'content_id',
		title:name,
		url:geturlpathname()+url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.addButton('dosubmit','保存后自动关闭',function(){
		//var body = diag.innerFrame.contentWindow.document;
		//$(body).find('#myform').serialize()
		var form = $DW.$('#dosubmit');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload();
			} else {
				parent.location.reload();
			}
			diag.close();
		}
		return false;
	},0,1);
	diag.okText = '保存并继续发表';
	diag.onOk = function(){
		var form = $DW.$('#dosubmit_continue');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload();
			} else {
				parent.location.reload();
			}
			diag.close();
		}
		return false;
	};
	diag.cancelText = '关闭(X)';
	diag.onCancel=function(){
		var form = $DW.$('#close');
		if(form.length > 0) {
			form.click();
		} else {
			if (parent.right) {
				parent.right.location.reload();
			} else {
				parent.location.reload();
			}
			$DW.close();
		}
		return false;
	};
	diag.onClose=function(){
		if (parent.right) {
			parent.right.location.reload();
		} else {
			parent.location.reload();
		}
		$DW.close();
	};
	diag.show();
}
//弹出对话框
function artdialog(id,url,title,w,h) {
	if (typeof pc_hash == 'string') url += (url.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if(!w) w=700;
	if(!h) h=500;
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:geturlpathname()+url,
		width:w,
		height:h,
		modal:true,
		draggable:drag
	});
	diag.onOk = function(){
		var form = $DW.$('#dosubmit');
		form.click();
		return false;
	};
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
//弹出对话框
function omnipotent(id,linkurl,title,close_type,w,h) {
	if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
	if(!w) w=700;
	if(!h) h=500;
	if (w=='100%' && h=='100%') {
		var drag = false;
	} else {
		var drag = true;
	}
	var diag = new Dialog({
		id:id,
		title:title,
		url:geturlpathname()+linkurl,
		width:w,
		height:h,
		modal:true,
		draggable:drag
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
function dr_bfb_submit(title, myform, url) {
	layer.load(2, {shade:[ .3, "#fff" ],time:1000});
	$.ajax({
		type:"POST",
		dataType:"json",
		url:url,
		data:$("#"+myform).serialize(),
		success:function(json) {
			layer.closeAll("loading");
			if (json.code == 1) {
				layer.open({
					type:2,
					title:title,
					scrollbar:false,
					resize:true,
					maxmin:true,
					shade:0,
					area:[ "80%", "80%" ],
					success:function(layero, index) {
						var body = layer.getChildFrame("body", index);
						var json = $(body).html();
						if (json.indexOf('"code":0') > 0 && json.length < 150) {
							var obj = JSON.parse(json);
							layer.closeAll("loading");
							dr_tips(0, obj.msg);
						}
					},
					content:json.data.url
				});
			} else {
				dr_tips(0, json.msg, 90000);
			}
			return false;
		},
		error:function(HttpRequest, ajaxOptions, thrownError) {
			dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
		}
	});
}
function dr_tips(code, msg, time) {
	if (!time || time == "undefined") {
		time = 3000;
	} else {
		time = time * 1000;
	}
	var is_tip = 0;
	if (time < 0) {
		is_tip = 1;
	} else if (code == 0 && msg.length > 15) {
		is_tip = 1;
	}

	if (is_tip) {
		if (code == 0) {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 2
			})
		} else {
			layer.alert(msg, {
				shade: 0,
				title: "",
				icon: 1
			})
		}
	} else {
		var tip = '<i class="fa fa-info-circle"></i>';
		//var theme = 'teal';
		if (code >= 1) {
			tip = '<i class="fa fa-check-circle"></i>';
			//theme = 'lime';
		} else if (code == 0) {
			tip = '<i class="fa fa-times-circle"></i>';
			//theme = 'ruby';
		}
		layer.msg(tip+'&nbsp;&nbsp;'+msg, {time: time});
	}
}
function dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError) {
	layer.closeAll("loading");
	var msg = HttpRequest.responseText;
	if (!msg) {
		dr_tips(0, "系统错误");
	} else {
		layer.open({
			type:1,
			title:"系统错误",
			fix:true,
			shadeClose:true,
			shade:0,
			area:[ "50%", "50%" ],
			content:'<div style="padding:10px;">' + msg + "</div>"
		});
	}
}