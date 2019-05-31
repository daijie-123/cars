<?php
if (!defined('APP_IN')) exit('Access Denied');

include ('page.php');
include(INC_DIR . 'html.func.php');

$tpl -> assign('menustate', 3);

if (submitcheck('action')) {
	// 不可为空
	if(empty($_SESSION['USER_ID'])){
		$arr_not_empty = array('p_brand' => '请选择品牌', 'p_subbrand' => '请选择车系', 'p_subsubbrand' => '请选择款式', 'p_tel' => '请输入您的联系电话');
	}
	else{
		$arr_not_empty = array('p_brand' => '请选择品牌', 'p_subbrand' => '请选择车系','p_subsubbrand' => '请选择款式');
	}

	can_not_be_empty($arr_not_empty, $_POST); 

	
	$post = post('p_brand','p_subbrand','p_subsubbrand','p_tel');

  
	if(isset($_COOKIE['city'])){
		$post['cid'] = $_COOKIE['city'];
		if($_COOKIE['city']!=0){
			$area = $db->row_select_one('area','id='.$_COOKIE['city']);
			$post['aid'] = $area['parentid'];
		}
	}

	if(empty($_SESSION['USER_ID'])){
		$post['p_tel'] = trim($_POST['p_tel']);
	}

	$post['p_brand'] = intval($post['p_brand']);
	$post['p_subbrand'] = intval($post['p_subbrand']);
	$post['p_subsubbrand'] = intval($post['p_subsubbrand']);
	$post['p_allname'] = "";
	if(!empty($post['p_subbrand'])){
		$bname = $commoncache['brandlist'][$post['p_brand']];
		$subbname = arr_brandname($post['p_subbrand']);
		$compareword = strstr($subbname,$bname);
		if(!empty($compareword)){
			$post['p_allname'] .= arr_brandname($post['p_subbrand']);
		}
		else{
			$post['p_allname'] .= $bname ." ".arr_brandname($post['p_subbrand']);
		}
	}
	if(!empty($post['p_subsubbrand'])){
		$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
	}

	$post['p_addtime'] = TIMESTAMP;
	$post['listtime'] = TIMESTAMP;
	$post['p_no'] = TIMESTAMP;

	if(!empty($_SESSION['USER_ID'])){
		$post['uid'] = $_SESSION['USER_ID'];
		$userinfo = $db -> row_select_one('member', "id={$_SESSION['USER_ID']}");
		if($userinfo['isdealer']==2 and $userinfo['ischeck']==1){
			$post['isshow'] = 1;
		}
		else{
			$post['isshow'] = 0;
		}
	}
	else{
		$post['uid'] = 0;
		$post['isshow'] = 0;
	}

	$db -> row_insert('cars', $post);
	$insertid = $db -> insert_id();
	html_cars($insertid);

	showmsg('您的信息已提交成功！', -1);
} 

$tpl -> display('default/' . $settings['templates'] . '/sell.html');

?>