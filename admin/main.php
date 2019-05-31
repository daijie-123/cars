<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：北京市普艾斯科技有限公司
 项目：simcms_锐车1.0
 电话：010-58480317
 Q  Q: 228971357
 网址：http://www.simcms.net
 simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>网站后台管理</title>
<link rel="stylesheet" type="text/css" id="css" href="static/css/admin/main.css">
<script type="text/javascript" src="static/js/jquery.js"></script>
<script type="text/javascript">  
            $(function(){  
                var pagestyle = function() {
					var rframe = $("#report_iframe");
					//ie7默认情况下会有上下滚动条，去掉上下15像素
					var h = $(window).height() - rframe.offset().top - 20;
					rframe.height(h);
				}
				//注册加载事件
				$("#report_iframe").load(pagestyle);
				//注册窗体改变大小事件   
				$(window).resize(pagestyle);
				var $div_li = $("ul.topmenu li");
				$div_li.click(function() {
					$(this).addClass("here").siblings().removeClass("here");
					var index = $div_li.index(this); 
					$("div.menu > ul").eq(index).show().siblings().hide();
				});
				$(".sidebar h3").click(function(){
					//$(this).parent().siblings().children("ul").hide();
					$(this).parent().siblings().children("h3").removeClass("selected");
					var $ul = $(this).next("ul");
					if($ul.is(":visible")){
						$(this).removeClass("selected");
						$ul.hide();
					}else{
						$(this).addClass("selected");
						$ul.show();
					}
					return false;
				 });
				$(".sidebar ul li a").click(function(){
					$(".sidebar ul li a").removeClass("selected");
					$(this).addClass("selected");
				 });
            });  
</script>
</head>
<body>
<div class="top">
	<div class="topbox clearfix">
		<div class="quickmenu">
			<ul class="clearfix">
				<li><a href="<?php echo WEB_PATH?>/" target="_blank">站点首页</a></li>
				<li><a href="<?php echo ADMIN_PAGE?>?m=main">管理首页</a></li>
				<li><a href="<?php echo ADMIN_PAGE?>?m=cache&a=del">清除缓存</a></li>
				<li><a href="<?php echo ADMIN_PAGE?>?m=login&a=logout">安全退出</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="mainw">
	<div class="main">
		<div class="mainleft">
			<div class="mt20"></div>
            <?php include('adminmenu.php');?>
		</div>
		<div class="mainright">
			<iframe id="report_iframe" frameborder="0" name="report_iframe" src="<?php echo ADMIN_PAGE?>?m=index" width="100%"></iframe>
		</div>
	</div>
</div>
</body>
</html>
