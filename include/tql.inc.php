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
    include_once "smarty/Smarty.class.php";
    $tpl =new Smarty;
    $tpl->template_dir = WEB_ROOT.TPL_DIR;
    $tpl->compile_dir = WEB_ROOT . "templates_c/";
    $tpl->left_delimiter = '<{';
    $tpl->right_delimiter = '}>';
	$TPL_IMGURL = 'templates/images'; #模板图片URL
?>