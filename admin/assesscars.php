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
$m_name = '评估信息列表';

// 允许操作
$ac_arr = array('list' => '车源列表','del' => '删除信息','bulkdel' => '删除信息','assess'=>'评估价格');

// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

// 列表
if ($ac == 'list') {	
	// 搜索条件
	$where = "p_id > 0 ";
	if(!empty($_GET['keywords'])) {
		$keywords = $_GET['keywords'];
		$where .= " and (p_contact_name like '%{$keywords}%' or p_allname like '%{$keywords}%')";
	} 

	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'assesscars', $where, '*', '50', 'p_id desc');
	$list = $Page -> get_data();
	$page = $Page -> page;
	foreach($list as $key => $value) {
		$list[$key]['p_addtime'] = date('Y-m-d H:i:s', $value['p_addtime']);
		$list[$key]['p_assesstime'] = date('Y-m-d H:i:s', $value['p_assesstime']);
		if(!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
		if($settings['version']==3){
			if($value['aid']!=0){
				$list[$key]['province']=$commoncache['provincelist'][$value['aid']];
			}
			if($value['cid']!=0){
				$list[$key]['city']=$commoncache['citylist'][$value['cid']];
			}
		}
	} 
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();

	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('carslist', $list);
	$tpl -> assign('page', $page);
	$tpl -> display('admin/assesscars_list.html');
	exit;
} 
elseif ($ac == 'del') {
	$p_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$car = $db -> row_select_one('assesscars', "p_id=$p_id");
	if(!empty($car['p_pics'])) {
		$pic_list = explode('|', $car['p_pics']);
		foreach($pic_list as $key => $value) {
			$pic = str_replace(WEB_PATH . '/', '', $value);
			$smallpic = str_replace('upload/upload', 'upload/small', $pic);
			unlink($pic);
			unlink($smallpic);
		} 
	} 
	$rs = $db -> row_delete('assesscars', "p_id=$p_id");
} 
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	foreach($_POST['bulkid'] as $key => $value) {
		$car = $db -> row_select_one('assesscars', "p_id=" . $value);
		if(!empty($car['p_pics'])) {
			$pic_list = explode('|', $car['p_pics']);
			foreach($pic_list as $key => $value) {
				$pic = str_replace(WEB_PATH . '/', '', $value);
				$smallpic = str_replace('upload/upload', 'upload/small', $pic);
				unlink($pic);
				unlink($smallpic);
			} 
		} 
	} 
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('assesscars', "p_id in($str_id)");
}
//添加
elseif ($ac == 'assess')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array('p_price'=>'评估价格不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post['p_price'] = $_POST['p_price'];
		$post['p_assesstime'] = time();
		$rs = $db->row_update('assesscars',$post,"p_id=".intval($_POST['id']));
    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])) {
			$data = array('p_price'=>'');
		}
		else{
			$data = $db->row_select_one('assesscars',"p_id=".intval($_GET['id']));
		}
        $tpl->assign( 'assesscar', $data );
        $tpl->display( 'admin/add_assesscar.html' );
        exit;
    }
}
else {
	showmsg('非法操作', -1);
} 
showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE."?m=$m&a=list&page=".$page_g);
?>