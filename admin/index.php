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
if (!defined('APP_IN')) exit('Access Denied');

$arr_admin_type = array('administrator' => '超级管理员', 'admin' => '管理员');

$data = $db->row_select_one('admin',"adminid=".$_SESSION['ADMIN_UID']);
$data['admintype'] = $arr_admin_type[$data['admin_type']];
$data['last_login_time'] = date('Y-m-d H:i:s',$data['last_login_time']);
$tpl -> assign('admin', $data);

$sysinfo = array();
$sysinfo['system'] = PHP_OS;
$sysinfo['software'] = $_SERVER['SERVER_SOFTWARE'];
$sysinfo['mysql'] = mysql_get_server_info();
$tpl -> assign('sysinfo', $sysinfo);

$tpl -> display('admin/index.html');
?>