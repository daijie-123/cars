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
$m_name = '预约卖车列表';

// 允许操作
$ac_arr = array('list' => '车源列表','del' => '删除信息','bulkdel' => '删除信息','assess'=>'评估价格');

// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

// $tpl->assign( 'mod_name', $m_name );
// $tpl->assign( 'ac_arr', $ac_arr );
// $tpl->assign( 'ac', $ac );

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

// 列表
if ($ac == 'list') {
	// 搜索条件
	$where = " 1=1 ";
	if(!empty($_GET['keywords'])) {
		$keywords = $_GET['keywords'];
		$where .= " and (contact_tel like '%{$keywords}%' or car_license like '%{$keywords}%')";
	}

	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'appointment_sell', $where, '*', '50', 'id desc');
	$list = $Page -> get_data();
	$page = $Page -> page;
	foreach($list as $key => $value) {
        $rs_user = $db->row_select_one('member',"id={$value['dealer_id']} AND isdealer=2", 'company');

		$list[$key]['company'] = $rs_user['company'];
		$list[$key]['add_license_time'] = date('Y-m-d H:i:s', $value['add_license_time']);
		$list[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();

	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('carslist', $list);
	$tpl -> assign('page', $page);
	$tpl -> display('admin/appointment_sell_list.html');
	exit;
}
elseif ($ac == 'del') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('appointment_sell', "id=$id");
}
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('appointment_sell', "id in($str_id)");
}
else {
	showmsg('非法操作', -1);
}
showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list&page=".$page_g);