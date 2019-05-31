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
$m_name = '广告管理';
// 允许操作
$ac_arr = array('list' => '广告列表', 'add' => '添加广告', 'edit' => '编辑广告', 'code' => '生成广告代码', 'del' => '删除广告', 'bulkdel' => '批量删除');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);
// 列表
if ($ac == 'list') {
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'ad');
	$list = $Page -> get_data();
	foreach ($list as $key => $value) {
		if($value['isshow']==1){
			if($value['endtime']-time()<=0){
				$list[$key]['status'] = "<span class='orange01'>已过期</span>";
			}
			else{
				$list[$key]['status'] = "启用";
			}
		}
		else{
			$list[$key]['status'] = "未启用";
		}
		$list[$key]['endtime'] = date("Y-m-d H:i:s",$value['endtime']);

	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('adlist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> display('admin/ad_list.html');
	exit;
} 
// 单条删除
elseif ($ac == 'del') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('ad', "id=$id");
} 
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('ad', "id in($str_id)");
} 
// 添加
elseif ($ac == 'add' || $ac == 'edit') {
	// 添加或修改
	if (submitcheck('a')) {
		if (intval($_POST['adtype']) == 0) {
			$post = post('adtype', 'name', 'url', 'url_note');
			$arr_not_empty = array('name' => '广告名称不可为空', 'url' => '链接地址不能为空');
		} else {
			$post = post('adtype', 'name', 'picwidth', 'picheight', 'url', 'url_note');
			$arr_not_empty = array('name' => '广告名称不可为空', 'url' => '链接地址不能为空', 'picwidth' => '图片宽度不可为空', 'picheight' => '图片高度不可为空');
		} 
		can_not_be_empty($arr_not_empty, $_POST);

		$post['starttime'] = strtotime($_POST['starttime']);
		$post['endtime'] = strtotime($_POST['endtime']);

		$post['pic'] = $_POST['pic'];

		if (isset($_POST['isshow']) and intval($_POST['isshow'])==1) {
			$post['isshow']=1;
		} 
		else{
			$post['isshow']=0;
		}

		if ($ac == 'add') {
			$rs = $db -> row_insert('ad', $post);
		} else {
			$rs = $db -> row_update('ad', $post, "id=" . intval($_POST['id']));
		} 
	} 
	// 转向添加或修改页面
	else {
		if (empty($_GET['id'])) {
			$data = array('id' => '', 'name' => '', 'pic' => '', 'picwidth' => '', 'picheight' => '', 'url' => '', 'url_note' => '', 'adtype' => '');
			$data['starttime'] = date("Y-m-d",time());
			$data['endtime'] = date("Y-m-d",mktime(date("H"),date("i"),date("s"),date("m")+1,date("d"),date("Y")));
		}
		else{ 
			$data = $db -> row_select_one('ad', "id=" . intval($_GET['id']));
			$data['starttime'] = date("Y-m-d",$data['starttime']);
			$data['endtime'] = date("Y-m-d",$data['endtime']);
		}
		$select_adtype = select_make($data['adtype'], array('文字', '图片'));
		$tpl -> assign('ad', $data);
		$tpl -> assign('selectadtype', $select_adtype);
		$tpl -> assign('ac', $ac);
		$tpl -> display('admin/add_ad.html');
		exit;
	} 
} 
// 添加
elseif ($ac == 'code') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('ad', "id=".$id);
	$code = "<script language=\"javascript\" ";
	$code .= 'src="'.WEB_PATH.'/index.php?m=ad&id='.$data['id'].'"';
	$code .= "></script>";
	$tpl -> assign('ac', $ac);
	$tpl -> assign('code', $code);
	$tpl -> display('admin/add_ad_code.html');
	exit;
} 
// 默认操作
else {
	showmsg('非法操作', -1);
} 

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list");
?>