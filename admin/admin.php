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
$m_name = '管理员管理';
// 允许操作
$ac_arr = array('list' => '管理员列表', 'add' => '添加管理员', 'edit' => '编辑管理员', 'del' => '删除管理员', 'bulkdel' => '批量删除', 'bulksort' => '更新排序');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';
// 管理员分类列表
// 状态列表
$arr_status = array('禁用', '启用');

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

if($settings['version']==3){
	$arr_admingroup = arr_admingroup();
}

// 列表
if ($ac == 'list') {
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'admin', '1=1', '*', 20, 'adminid');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['last_login_time'] = date('Y-m-d H:i:s', $value['last_login_time']);
		$list[$key]['statusname'] = $value['status'] == 0 ? "禁用" : "启用";
		if($settings['version']==3 and $value['admingroup']!=0){
			$list[$key]['admingroupname'] = $arr_admingroup[$value['admingroup']];
		}
	} 
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('adminlist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> display('admin/admin_list.html');
	exit;
} 
// 单条删除
elseif ($ac == 'del') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('admin', "adminid=$id");
} 
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('admin', "adminid in($str_id)");
} 
// 添加
elseif ($ac == 'add' || $ac == 'edit') {
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array( 'username' => '用户名不可为空');
		can_not_be_empty($arr_not_empty, $_POST);
		$post = post('username', 'status', 'description','admin_type');
		$post['username'] = htmlspecialchars(trim($post['username']));
		$post['description'] = htmlspecialchars($post['description']); 
		if($settings['version']==3){
			if(isset($_POST['admingroup'])){
				$post['admingroup'] = intval($_POST['admingroup']);
			}
			// 管理员权限
			if (!empty($_POST['permission'])) {
				$str_permission = '';
				foreach ($_POST['permission'] as $v) {
					$str_permission .= intval($v) . ',';
				} 
				$post['permission'] = rtrim($str_permission, ',');
			} 
		}

		if ($ac == 'add') {
			if ($db -> row_count('admin', "username='{$post['username']}'")) showmsg('用户已存在', -1);
			if (empty($_POST['password'])) showmsg('密码不可为空', -1);
			$post['password'] = md5(trim($_POST['password']));
			$rs = $db -> row_insert('admin', $post);
		} else {
			if (!empty($_POST['password'])) $post['password'] = md5(trim($_POST['password']));
			$rs = $db -> row_update('admin', $post, "adminid=" . intval($_POST['id']));
		} 
	} 
	// 转向添加或修改页面
	else {
		if($settings['version']==3){
			$rs_permission = $db -> row_select('permission');
			$arr_permission = array();
		}
		if (empty($_GET['id'])) $data = array('id' => '', 'username' => '', 'password' => '', 'admin_type' => '', 'status' => 1, 'description' => '');
		else {
			$data = $db -> row_select_one('admin', "adminid=" . intval($_GET['id']));
			if (!$data) showmsg('错误的ID', -1);
		} 
		if($settings['version']==3){
			$arr_permission = explode(',', $data['permission']);
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
			$select_admingroup = select_make($data['admingroup'], $arr_admingroup, '请选择用户组');
			$tpl -> assign('select_admingroup', $select_admingroup);
		}
		$select_status = select_make($data['status'], $arr_status);
		$tpl -> assign('admin', $data);
		$tpl -> assign('select_status', $select_status);
		$tpl -> display('admin/add_admin.html');
		exit;
	} 
} 
// 默认操作
else {
	showmsg('非法操作', -1);
} 

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list");
?>