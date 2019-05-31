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
// 当前模块
$m_name = '版本列表管理';
// 允许操作
$ac_arr = array('list' => '版本列表','update'=>'切换版本');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$array_cattype = array('1' => '内部分类', '2' => '外部链接');

/**
 * 递归删除栏目
 * @param  $catid 要删除的栏目id
 */

	$versiontype = isset($_GET['versiontype'])? intval($_GET['versiontype']):'';
	$array_versiontype = array('1' => '个人版本','2' => '团购版本','3' => '高级版本');
	$select_versiontype = select_make($versiontype, $array_versiontype, '版本类型');
	$tpl -> assign('select_versiontype', $select_versiontype);
// 列表
if ($ac == 'list') {

	$tpl -> display('admin/version_list.html');
	exit;
} 
else if($ac == 'update') {
	switch($versiontype) {
		case 1:
			 $rs = $db -> row_update('settings',array('v'=>$versiontype),"k='version'");
			break;
		case 2:
			 $rs = $db -> row_update('settings',array('v'=>$versiontype),"k='version'");
			break;
		case 3:
			 $rs = $db -> row_update('settings',array('v'=>$versiontype),"k='version'");
			break;
	   default:
	        $rs = $db -> row_update('settings',array('v'=>$versiontype),"k='version'");
	} 
	showmsg('应用成功', -1);
} 
// 默认操作
else {
	showmsg('非法操作', -1);
} 

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list");

?>