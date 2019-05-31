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
$m_name = '管理员用户组管理';
// 允许操作
$ac_arr = array('list' => '管理员用户组列表', 'add' => '添加管理员用户组', 'edit' => '编辑管理员用户组', 'del' => '删除管理员用户组', 'bulkdel' => '批量删除', 'bulksort' => '更新排序');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';
// 管理员用户组分类列表
// 状态列表
$arr_status = array('禁用', '启用');

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

// 列表
if ($ac == 'list') {
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'admingroup', '1=1', '*', 20, 'id desc');
	$list = $Page -> get_data();
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('admingrouplist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> display('admin/admingroup_list.html');
	exit;
} 
// 单条删除
elseif ($ac == 'del') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('admingroup', "id=$id");
} 
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('admingroup', "id in($str_id)");
} 
// 添加
elseif ($ac == 'add' || $ac == 'edit') {
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array( 'groupname' => '用户组名称不可为空');
		can_not_be_empty($arr_not_empty, $_POST);
		$post['groupname'] = trim($_POST['groupname']);
		// 管理员用户组权限
		if (!empty($_POST['permission'])) {
			$str_permission = '';
			foreach ($_POST['permission'] as $v) {
				$str_permission .= intval($v) . ',';
			} 
			$post['permission'] = rtrim($str_permission, ',');
		} 
		
		if ($ac == 'add') {
			$rs = $db -> row_insert('admingroup', $post);
		} else {
			$rs = $db -> row_update('admingroup', $post, "id=" . intval($_POST['id']));
		} 
	} 
	// 转向添加或修改页面
	else {
		$rs_permission = $db -> row_select('permission');
		$arr_permission = array();
		if (empty($_GET['id'])) $data = array('id' => '', 'groupname' => '');
		else {
			$data = $db -> row_select_one('admingroup', "id=" . intval($_GET['id']));
			if (!$data) showmsg('错误的ID', -1);
			$arr_permission = explode(',', $data['permission']);
		} 
		$permissionlist = "<div class='permissionbox'>";
		foreach ($rs_permission as $val) {
			if ($val['pid'] != 0) continue;
			$permissionlist .= "<div class='clearfix permissiondiv'>";
			$permissionlist .= "<div class='permissiondivleft'><input type=checkbox onclick=\"_all('pid_{$val['id']}',this.checked);\"> {$val['name']}</div>";
			$permissionlist .= "<div class='permissiondivright'>";
			foreach ($rs_permission as $v) {
				if ($val['id'] != $v['pid']) continue;
				$checked = '';
				if (in_array($v['id'], $arr_permission)) $checked = 'checked';
				$permissionlist .= "<input type=checkbox name=permission[] value={$v['id']} $checked class='pid_{$v['pid']}'> {$v['name']}&nbsp;&nbsp;&nbsp;";
			} 
			$permissionlist .= "</div>";
			$permissionlist .= "</div>";
		} 
		$permissionlist .= "</div>";
		$tpl -> assign('permissionlist', $permissionlist);

		$tpl -> assign('admin', $data);
		$tpl -> display('admin/add_admingroup.html');
		exit;
	} 
} 
// 默认操作
else {
	showmsg('非法操作', -1);
} 

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list");
?>