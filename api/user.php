<?php
if (!defined('APP_IN')) exit('Access Denied');

include ('page.php');
// include(INC_DIR . 'html.func.php');
if (!is_user_login()) splash('', 110);

$array_brand_with_index = arr_brand_with_index();
$array_brand = arr_brand(-1);
$array_model = arr_model();
$array_year = arr_year();
$array_color = arr_color();
$array_gas = arr_gas();
$array_transmission = arr_transmission();

// 个人信息
$userinfo = $db -> row_select_one('member', "id={$_SESSION['USER_ID']}");
$userinfo['regtime'] = date("Y/m/d", $userinfo['regtime']);

// // 商铺统计
// $usercarcounts[0] = $db -> row_count('cars', 'uid=' . $_SESSION['USER_ID']);
// $usercarcounts[1] = $db -> row_count('cars', 'uid=' . $_SESSION['USER_ID'] . ' and issell=1');
// $usercarcounts[2] = $db -> row_count('cars', 'uid=' . $_SESSION['USER_ID'] . ' and issell=0');

// $userrentcarcounts[0] = $db -> row_count('rentcars', 'uid=' . $_SESSION['USER_ID']);
// $userrentcarcounts[1] = $db -> row_count('rentcars', 'uid=' . $_SESSION['USER_ID'] . ' and issell=1');
// $userrentcarcounts[2] = $db -> row_count('rentcars', 'uid=' . $_SESSION['USER_ID'] . ' and issell=0');

// 允许操作
if($userinfo['isdealer']==1){
	$ac_arr = array('index' => '欢迎登陆', 'logout' => '退出登录', 'upinfo' => '编辑个人信息', 'uppwd' => '修改密码','addlogo'=>'修改头像', 'addcar' => '添加车源', 'editcar' => '编辑车源', 'delcar' => '删除车源', 'refresh' => '刷新车源', 'sellcar' => '改变买卖状态', 'carlist' => '车源列表','rentcarlist' => '租车信息列表', 'addrentcar' => '添加租车信息', 'editrentcar' => '编辑租车信息', 'delrentcar' => '删除租车信息', 'refreshrentcar' => '刷新租车信息');
}
else{
	$ac_arr = array('index' => '欢迎登陆', 'logout' => '退出登录', 'upinfo' => '编辑个人信息', 'uppwd' => '修改密码','addlogo'=>'修改头像', 'addcar' => '添加车源', 'editcar' => '编辑车源', 'delcar' => '删除车源', 'refresh' => '刷新车源', 'sellcar' => '改变买卖状态','rentcarlist' => '租车信息列表', 'addrentcar' => '添加租车信息', 'editrentcar' => '编辑租车信息', 'delrentcar' => '删除租车信息', 'refreshrentcar' => '刷新租车信息', 'carlist' => '车源列表','editshop' => '店铺设置', 'asklist' => '问答列表', 'replyask' => '回复问答', 'delask' => '删除问答', 'newslist' => '促销信息列表', 'addnews' => '添加促销信息', 'editnews' => '编辑促销信息', 'delnews' => '删除促销信息', 'dealerlist' => '销售代表列表', 'adddealer' => '添加销售代表', 'editdealer' => '编辑销售代表', 'deldealer' => '删除销售代表','subscribelist'=>'预约管理','subscribelist'=>'预约管理','delsubscribe'=>'删除预约','inquirylist'=>'询价管理','delinquiry'=>'删除询价');
}

$ac_post_arr = ['updateMobilePhone' => '修改手机号', 'save_safeguard' => '提交维权'];
$ac_get_arr = [];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);
if($ac == ''){

}
elseif($ac == 'save_safeguard'){
    $arr_not_empty = [
        'complain_company' => '请填写投诉单位',
        'complain_cause' => '请填写投诉原因',
        'subbrand_id' => '请选择车型',
        'kilometre' => '请填写行驶里程',
        'self_assessment' => '请选择车况',
        'username' => '请填写姓名',
        'mobile' => '请填写电话',
    ];
    api_can_not_be_empty($arr_not_empty, $_POST);
    $post = post('complain_company', 'complain_cause', 'brand_id', 'subbrand_id', 'subsubbrand_id', 'kilometre', 'self_assessment', 'username', 'mobile', 'pics');
    if(count($post['pics']) > 6){
        splash('', 100, '图片最多可上传6张');
    }
    if (!preg_match('/^1\d{10}$/', $post['mobile'])) splash('', 100, '手机号码格式不正确');
    $post['car_allname'] = brand_full_name($post['subbrand_id']);
    $post['user_id'] = $userinfo['id'];
    $post['status'] = 1;
    $post['pics'] = $post['pics'];
    $post['self_assessment'] = htmlspecialchars($post['self_assessment']);
    $post['addtime'] = TIMESTAMP;
    $rs = $db->row_insert('member_safeguard', $post);
    if (!$rs) {
        splash('', 1, '提交维权失败');
	}
	else{
        // $insertid = $db->insert_id();
        splash();
	}
}
// 修改手机号
elseif ($ac == 'updateMobilePhone') {
    $encrypted_data = $_POST['encryptedData'];
    $iv = $_POST['iv'];

    $decrypt_data = openssl_decrypt(
        base64_decode($encrypted_data),
        'AES-128-CBC',
        base64_decode($_SESSION['WEIXIN_USER_DATA']['sessionkey']),
        OPENSSL_RAW_DATA,
        base64_decode($iv)
    );
    $phoneNumber = json_decode($decrypt_data, true);

    if (!preg_match('/^1\d{10}$/', $phoneNumber['phoneNumber'])) splash('', 100, '手机号格式不正确');
    $post_data = ['mobilephone' => $phoneNumber['phoneNumber']];
    $rs = $db->row_update('member', $post_data, "id={$_SESSION['USER_ID']}");
    if($rs){
        splash($post_data, 0, '手机号修改成功');
    }else{
        splash('', 1);
    }
}
// 退出登录
elseif (is_user_login() && $ac == 'logout') {
	session_unset();
	session_destroy();
	showmsg($ac_arr[$ac] . ('成功'), "/");
}
// 修改密码
elseif ($ac == 'uppwd') {
	if (submitcheck('a')) {
		$arr_not_empty = array('oldpassword' => '原始密码不可为空', 'password' => '请填写新密码', 'repassword' => '请再次输入新密码');
		can_not_be_empty($arr_not_empty, $_POST);
		$_POST['password'] = trim($_POST['password']);
		$_POST['repassword'] = trim($_POST['repassword']);
		if ($_POST['password'] != $_POST['repassword']) showmsg('两次密码输入不一致', -1);
		$rs = $db -> row_select_one('member', "id='{$_SESSION['USER_ID']}'");
		if (!$rs || $rs['password'] != md5($_POST['oldpassword'])) showmsg('原密码输入错误', -1);
		$rs = $db -> row_update('member', array('password' => md5(trim($_POST['password']))), "id={$_SESSION['USER_ID']}");
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=uppwd");
	} else {
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 修改信息
elseif ($ac == 'upinfo') {
	if (submitcheck('a')) {
		$arr_not_empty = array('email' => '邮箱地址不能为空');
		can_not_be_empty($arr_not_empty, $_POST);
		if (!is_email($_POST['email'])) showmsg('错误的邮箱格式', -1);
		if (!preg_match('/^1\d{10}$/', $_POST['mobilephone'])) showmsg('错误的手机格式', -1);
		$post = post('email', 'mobilephone');
		$post['mobilephone'] = trim($_POST['mobilephone']);
		$post['nicname'] = htmlspecialchars($_POST['nicname']);
		$rs = $db -> row_update('member', $post, "id={$_SESSION['USER_ID']}");
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=upinfo");
	} else {
		$tpl -> assign('user', $userinfo);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 修改店铺资料
elseif ($ac == 'editshop') {
	if (submitcheck('a')) {
		$arr_not_empty = array('company' => '公司名称不能为空', 'nicname' => '联系人不能为空', 'mobilephone' => '手机号不能为空', 'address' => '公司地址不能为空');
		can_not_be_empty($arr_not_empty, $_POST);
		if (!preg_match('/^1\d{10}$/', $_POST['mobilephone'])) showmsg('错误的手机格式', -1);
		$post = post('company', 'nicname', 'mobilephone', 'tel', 'address', 'shopdetail', 'shoptype');
		$post['company'] = htmlspecialchars($post['company']);
		$post['nicname'] = htmlspecialchars($post['nicname']);
		$post['address'] = htmlspecialchars($post['address']);
		$post['shopdetail'] = htmlspecialchars($post['shopdetail']);
		$post['shoptype'] = intval($post['shoptype']);
		$post['checkshop'] = 1;
		$rs = $db -> row_update('member', $post, "id={$_SESSION['USER_ID']}");
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=editshop");
	} else {
		$array_dealer_category = arr_dealer_category();
		$select_dealer_category = select_make($userinfo['shoptype'],$array_dealer_category,"请选择公司类型");
		$tpl -> assign('select_dealer_category', $select_dealer_category);
		$tpl -> assign('user', $userinfo);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 修改头像
elseif ($ac == 'addlogo') {
	if (submitcheck('a')) {
		$rs = $db -> row_update('member', array('logo' => trim($_POST['logo'])), "id={$_SESSION['USER_ID']}");
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=editshop");
	} else {
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 车源列表
elseif ($ac == 'carlist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	if(!empty($_GET['keywords'])) {
		$keywords = $_GET['keywords'];
		$where .= " and (name like '%{$keywords}%' or mobilephone like '%{$keywords}%')";
	}
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'cars', $where, '*', '50', 'issell asc,listtime desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['listtime'] = date('Y-m-d H:i:s', $value['listtime']);
		$list[$key]['p_addtime'] = date('Y-m-d H:i:s', $value['p_addtime']);
		if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $array_model[$value['p_model']];
		$list[$key]['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$pageid = $Page -> page;
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('carslist', $list);
	$tpl -> assign('currpage', $pageid);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 添加或修改车源
elseif ($ac == 'addcar' || $ac == 'editcar') {
	if ($userinfo['isdealer'] == 2 and $userinfo['ischeck']!=1) {
		showmsg('您的公司信息暂未通过审核，暂不能发布信息！', "/?m=user&a=carlist");
	}
	if ($ac == 'addcar' and $userinfo['isdealer'] == 1 and $settings['islimit']==1 and !empty($settings['limitcount'])) {
		$usercarcounts = $db -> row_count('cars', 'uid=' . $_SESSION['USER_ID']);
		if ($usercarcounts >= $settings['limitcount']) {
			showmsg('超出限制发布条数', "/?m=user&a=carlist");
		}
	}

	if (submitcheck('a')) {
		foreach (array('p_details') as $v) {
			if (!is_array($_POST[$v])) {
				$_POST[$v] = htmlspecialchars($_POST[$v]);
			}
		}
		$post = post('p_no', 'p_brand', 'p_subbrand','p_subsubbrand','p_model', 'p_allname', 'p_price','p_newprice','p_tax','p_save', 'p_color', 'p_country', 'p_transmission', 'p_year', 'p_month', 'p_details', 'p_model', 'p_hits', 'p_gas', 'p_kilometre', 'p_addtime', 'listtime', 'issell', 'isshow', 'isrecom', 'issprecom', 'ishot','aid', 'cid','p_emission');

		if ($settings['version'] == 3) {
			$post['aid'] = intval($_POST['aid']);
			$post['cid'] = intval($_POST['cid']);
		} else {
			$post['aid'] = 0;
			$post['cid'] = 0;
		}
		$post['p_brand'] = intval($post['p_brand']);
		$post['p_subbrand'] = intval($post['p_subbrand']);
		$post['p_subsubbrand'] = intval($post['p_subsubbrand']);
		$post['p_model'] = intval($post['p_model']);

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

		$post['p_details'] = strip_tags($post['p_details']);

		if (empty($_POST['p_year'])) $post['p_year'] = 0;
		if (empty($_POST['p_month'])) $post['p_month'] = 0;
		if (empty($post['isrecom'])) $post['isrecom'] = 0;
		if (empty($post['issprecom'])) $post['issprecom'] = 0;
		if (empty($post['ishot'])) $post['ishot'] = 0;
		if (empty($post['p_kilometre'])) {
			$post['p_kilometre'] = 0;
		}

		if ($userinfo['isdealer'] == 2 and $userinfo['ischeck'] == 1) {
			$post['isshow'] = 1;
		} else {
			$post['isshow'] = 0;
		}

		if (isset($_POST['p_pics'])) {
			$post['p_pics'] = implode("|", $_POST['p_pics']);
			if (isset($_POST['p_mainpic'])) {
				$post['p_mainpic'] = $_POST['p_mainpic'];
			} else {
				$post['p_mainpic'] = $_POST['p_pics'][0];
			}
		} else {
			$post['p_pics'] = "";
		}

		$post['uid'] = $_SESSION['USER_ID'];
		$paralist = $db -> row_select('selfdefine', "isshow=1",' id,type_name,type_value,c_name',0,'orderid');
		if ($ac == 'addcar') {
			$post['p_hits'] = 0;
			$post['p_addtime'] = time();
			$post['listtime'] = time();
			$post['p_no'] = time();
			$post['issell'] = 0;

			$rs = $db -> row_insert('cars', $post);
			$insertid = $db -> insert_id();

			$post = array();
			//添加自定义参数值
			foreach($paralist as $key => $value){
				$post['c_id']=$value['id'];
				$post['p_id']=$insertid;
				$c_value='para'.$value['id'];
				if($paralist[$key]['type_name']=='checkbox'){
					$checkpara = implode("|",$_POST[$c_value]);
					$post['c_value'] = $checkpara;
				}
				else
				{
					$post['c_value'] = $_POST[$c_value];
				}
				$r = $db -> row_insert('selfdefine_value', $post);
			}
			html_cars($insertid);
		} else {
			$rs = $db -> row_update('cars', $post, "p_id=" . intval($_POST['id']));
			//修改改自定义参数值
			$post = post('p_id','c_value');
			foreach($paralist as $key => $value){
				$post['p_id']=intval($_POST['id']);
				$c_value='para'.$value['id'];
				if($paralist[$key]['type_name']=='checkbox'){
					$checkpara = implode("|",$_POST[$c_value]);
					$post['c_value'] = $checkpara;
				}
				else
				{
					$post['c_value'] = $_POST[$c_value];
				}

				$selfvalue= $db -> row_select_one('selfdefine_value', "p_id=" . intval($_POST['id']).' and c_id='.$paralist[$key]['id'],'c_id,p_id');
				if(empty($selfvalue['c_id'])){
					$post['c_id']=$value['id'];
					$r = $db -> row_insert('selfdefine_value', $post);
				}else{
					$rs = $db -> row_update('selfdefine_value', $post, "p_id=" . intval($_POST['id']).' and c_id='.$value['id']);
				}
			}
			html_cars(intval($_POST['id']));
		}
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=carlist");
	}
	// 转向添加或修改页面
	else {
		$configure_list = array();
		if (empty($_GET['id'])) {
			$data = array('p_brand' => '', 'p_subbrand' => '','p_subsubbrand' => '', 'p_allname' => '', 'p_keyword' => '', 'p_price' => '','p_newprice' => '','p_tax' => '','p_save' => '', 'p_pics' => '', 'p_color' => '', 'p_country' => '', 'p_transmission' => '', 'p_year' => '', 'p_month' => '', 'p_details' => '', 'p_model' => '', 'p_hits' => '', 'p_state' => 1, 'p_gas' => '', 'p_kilometre' => '', 'p_addtime' => '', 'listtime' => '', 'issell' => '', 'isshow' => '', 'cid' => '', 'aid' => '','p_emission'=>'');
		} else {
			$data = $db -> row_select_one('cars', "p_id=" . intval($_GET['id']));
			if (!empty($data['p_pics'])) {
				$pic_list = explode('|', $data['p_pics']);
				$piclist = array();
				foreach($pic_list as $key => $value) {
					$piclist[$key]['pic'] = $value;
					$arr_picid = explode("/", $value);
					$arr_length = count($arr_picid);
					$arr_picids = explode(".", $arr_picid[$arr_length-1]);
					$piclist[$key]['picid'] = $arr_picids[0];
				}
				$tpl -> assign('pic_list', $piclist);
			}
		}
		$array_city = arr_city($userinfo['aid']);
		if ($ac == 'addcar') {
			$select_province = select_make($userinfo['aid'], $commoncache['provincelist'], "请选择省份");
			$select_city = select_make($userinfo['cid'], $array_city, "请选择城市");
		} else {
			$select_province = select_make($data['aid'], $commoncache['provincelist'], "请选择省份");
			$select_city = select_make($data['cid'], $array_city, "请选择城市");
		}

		$tpl -> assign('selectprovince', $select_province);
		$tpl -> assign('selectcity', $select_city);
		$pstate_get = isset($_GET['pstate']) ? $_GET['pstate'] : "";
		$page_get = isset($_GET['page']) ? $_GET['page'] : "";
		$select_brand = select_make($data['p_brand'], $commoncache['markbrandlist'], '请选择品牌');
		$select_subbrand = select_subbrand(intval($data['p_subbrand']));
		$select_subsubbrand = select_subbrand(intval($data['p_subsubbrand']));
		$select_model = select_make($data['p_model'], $array_model, '');
		$select_year = select_make($data['p_year'], $array_year, '请选择年份');
		$select_color = select_make($data['p_color'], $array_color, '请选择颜色');
		$select_month = select_make($data['p_month'], array('01' => '01月', '02' => '02月', '03' => '03月', '04' => '04月', '05' => '05月', '06' => '06月', '07' => '07月', '08' => '08月', '09' => '09月', '10' => '10月', '11' => '11月', '12' => '12月'), '请选择月份', '');
		$select_gas = select_make($data['p_gas'], $array_gas, '车辆属性');
		$select_transmission = select_make($data['p_transmission'], $array_transmission, '请选择变速箱');
		$select_country = select_make($data['p_country'], array('国产' => '国产', '进口' => '进口'), '请选择');

		//显示参数
		$paralist = $db -> row_select('selfdefine', "isshow=1",' id,type_name,type_value,c_name',0,'orderid');
		foreach($paralist as $key => $value){
			if(!empty($data['p_id'])){
				$para_value = $db -> row_select_one('selfdefine_value', "p_id=" . $data['p_id'].' and c_id='.$value['id']);
				if($value['type_name']=='select'){
					$arr_para = arr_selfdefine($value['type_value']);
					$para = select_make($para_value['c_value'], $arr_para, '请选择');
					$paralist[$key]['select'] = $para;
				}elseif($value['type_name']=='checkbox'){
					$check_para = explode("|",$value['type_value']);
					$checkvalue = explode("|",$para_value['c_value']);
					$checkbox_str = "";
					foreach($check_para as $k => $v){
						if(in_array($v,$checkvalue)){
							$check = "checked";
						}
						else{
							$check = "";
						}
						$checkbox_str.= "<input type='checkbox' name='para".$value['id']."[]' value='".$v."' ".$check."> ".$v."&nbsp;&nbsp;";
					}
					$tpl->assign('checkbox_str',$checkbox_str);
				}
				else{
					$paralist[$key]['c_value']=$para_value['c_value'];
				}
			}
			else{
				if($value['type_name']=='select'){
					$arr_para = arr_selfdefine($value['type_value']);
					$para = select_make(-1, $arr_para, '请选择');
					$paralist[$key]['select'] = $para;
				}elseif($value['type_name']=='checkbox'){
					$check_para = explode("|",$value['type_value']);
					foreach($check_para as $k => $v){
						$list[$check_para[$k]]=0;
					}
					$tpl->assign('list',$list);
				}
			}
		}
	    $tpl->assign('paralist',$paralist);
		$tpl -> assign('cars', $data);
		$tpl -> assign('select_brand', $select_brand);
		$tpl -> assign('select_subbrand', $select_subbrand);
		$tpl -> assign('select_subsubbrand', $select_subsubbrand);
		$tpl -> assign('select_model', $select_model);
		$tpl -> assign('select_year', $select_year);
		$tpl -> assign('select_color', $select_color);
		$tpl -> assign('select_month', $select_month);
		$tpl -> assign('select_gas', $select_gas);
		$tpl -> assign('select_transmission', $select_transmission);
		$tpl -> assign('select_country', $select_country);
		$tpl -> assign('pstate', $pstate_get);
		$tpl -> assign('sessionid', session_id());
		$tpl -> assign('page', $page_get);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 刷新车源
elseif ($ac == 'refresh') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('cars', "p_id=" . $id);
	if ($data['uid'] != $_SESSION['USER_ID']) {
		showmsg('非法操作', -1);
		exit;
	}
	$listtime = time();
	$rs = $db -> row_update('cars', array('listtime' => $listtime), "p_id=" . $id);
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=carlist");
}
// 改变买卖状态
elseif ($ac == 'sellcar') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('cars', "p_id=" . $id);
	if ($data['uid'] != $_SESSION['USER_ID']) {
		showmsg('非法操作', -1);
		exit;
	}
	$issell = intval($_GET['sell']);
	$rs = $db -> row_update('cars', array('issell' => $issell), "p_id=" . $id);
	html_cars($id);
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=carlist");
}
// 单条删除
elseif ($ac == 'delcar') {
	$p_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('cars', "p_id=" . $p_id);
	if ($data['uid'] != $_SESSION['USER_ID']) {
		showmsg('非法操作', -1);
		exit;
	}
	if (!empty($data['p_pics'])) {
		$listpic = explode("|", $data['p_pics']);
		foreach($pic_list as $key => $value) {
			$value = substr($value,1);
			$smallpic = str_replace(".", "_small.", $value);
			if(file_exists($value)){
				unlink($value);
			}
			if(file_exists($smallpic)){
				unlink($smallpic);
			}
		}
	}
	$rs = $db -> row_delete('cars', "p_id=$p_id");
	showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),"/?m=user&a=carlist");
}
// 预约列表
elseif ($ac == 'subscribelist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	if(!empty($_GET['keywords'])) {
		$keywords = $_GET['keywords'];
		$where .= " and (name like '%{$keywords}%' or mobilephone like '%{$keywords}%')";
	}
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'subscribe', $where, '*', '20', 'id desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['ordertime'] = date('Y-m-d', $value['ordertime']);
		$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		$car = $db -> row_select_one('cars', "p_id=" . $value['pid']);
		$car['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $car['p_addtime']) . "/" . $car['p_id'] . ".html";
		$list[$key]['p_allname'] = $car['p_allname'];
		$list[$key]['p_url'] = $car['p_url'];
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('subscribelist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 单条删除
elseif ($ac == 'delsubscribe') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('subscribe', "id=$id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=subscribelist");
}
// 询价列表
elseif ($ac == 'inquirylist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	if(!empty($_GET['keywords'])) {
		$keywords = $_GET['keywords'];
		$where .= " and (name like '%{$keywords}%' or mobilephone like '%{$keywords}%')";
	}
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'inquiry', $where, '*', '20', 'id desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
		$car = $db -> row_select_one('cars', "p_id=" . $value['pid'],'p_id,p_allname,p_addtime');
		$car['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $car['p_addtime']) . "/" . $car['p_id'] . ".html";
		$list[$key]['p_allname'] = $car['p_allname'];
		$list[$key]['p_url'] = $car['p_url'];
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('inquirylist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 单条删除
elseif ($ac == 'delinquiry') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('inquiry', "id=$id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=inquirylist");
}
// 租车信息列表
elseif ($ac == 'rentcarlist') {
	$where = 'uid=' . $_SESSION['USER_ID'];

	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'rentcars', $where, '*', '50', 'listtime desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['listtime'] = date('Y-m-d H:i:s', $value['listtime']);
		$list[$key]['p_addtime'] = date('Y-m-d H:i:s', $value['p_addtime']);
		$list[$key]['p_allname'] = _substr($value['p_allname'], 0, 16);
		if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['rentmodellist'][$value['p_model']];
		$list[$key]['p_url'] = HTML_DIR . "rentcars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$pageid = $Page -> page;
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('rentcarslist', $list);
	$tpl -> assign('currpage', $pageid);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 添加或修改租车信息
elseif ($ac == 'addrentcar' || $ac == 'editrentcar') {
	if ($userinfo['isdealer'] == 2 and $userinfo['ischeck']!=1) {
		showmsg('您的公司信息暂未通过审核，暂不能发布信息！', "/?m=user&a=carlist");
	}
	if ($ac == 'addrentcar' and $userinfo['isdealer'] == 1 and $settings['rentlimit']==1 and !empty($settings['rentcount'])) {
		$usercarcounts = $db -> row_count('rentcars', 'uid=' . $_SESSION['USER_ID']);
		if ($usercarcounts >= $settings['rentcount']) {
			showmsg('超出限制发布条数', "/?m=user&a=rentcarlist");
		}
	}
	if (submitcheck('a')) {
		foreach (array('p_details') as $v) {
			if (!is_array($_POST[$v])) {
				$_POST[$v] = htmlspecialchars($_POST[$v]);
			}
		}
		$post = post('p_title', 'p_brand', 'p_subbrand','p_subsubbrand', 'p_model', 'p_allname', 'dayprice', 'monthprice', 'p_details', 'p_model', 'p_addtime', 'listtime', 'isshow', 'cid', 'aid');
		if ($settings['version'] == 3) {
			$post['aid'] = intval($_POST['aid']);
			$post['cid'] = intval($_POST['cid']);
		} else {
			$post['aid'] = 0;
			$post['cid'] = 0;
		}

		$post['p_brand'] = intval($post['p_brand']);
		$post['p_subbrand'] = intval($post['p_subbrand']);
		$post['p_subsubbrand'] = intval($post['p_subsubbrand']);
		$post['p_model'] = intval($post['p_model']);
		$post['p_allname'] = "";
		if(!empty($post['p_subbrand'])){
			$post['p_allname'] .= arr_brandname($post['p_subbrand']);
		}
		if(!empty($post['p_subsubbrand'])){
			$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
		}

		$post['isrecom'] = 0;
		$post['ishot'] = 0;

		$post['isshow'] = 1;

		if (isset($_POST['p_pics'])) {
			$post['p_pics'] = implode("|", $_POST['p_pics']);
			if (isset($_POST['p_mainpic'])) {
				$post['p_mainpic'] = $_POST['p_mainpic'];
			} else {
				$post['p_mainpic'] = $_POST['p_pics'][0];
			}
		} else {
			$post['p_pics'] = "";
		}

		$post['uid'] = $_SESSION['USER_ID'];

		if ($userinfo['isdealer'] == 2 and $userinfo['ischeck'] == 1) {
			$post['isshow'] = 1;
		} else {
			$post['isshow'] = 0;
		}

		if ($ac == 'addrentcar') {
			$post['p_hits'] = 0;
			$post['p_addtime'] = time();
			$post['listtime'] = time();


			$rs = $db -> row_insert('rentcars', $post);
			$insertid = $db -> insert_id();
			html_rentcars($insertid);
		} else {
			$rs = $db -> row_update('rentcars', $post, "p_id=" . intval($_POST['id']));
			html_rentcars(intval($_POST['id']));
		}
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=rentcarlist");
	}
	// 转向添加或修改页面
	else {
		$configure_list = array();
		if (empty($_GET['id'])) {
			$data = array('p_title' => '', 'p_brand' => '', 'p_subbrand' => '', 'p_subsubbrand' => '','p_allname' => '', 'p_keyword' => '', 'dayprice' => '', 'monthprice' => '', 'p_pics' => '', 'p_details' => '', 'p_model' => '', 'p_addtime' => '', 'listtime' => '', 'isshow' => '', 'cid' => '', 'aid' => '');
		} else {
			$data = $db -> row_select_one('rentcars', "p_id=" . intval($_GET['id']));
			if (!empty($data['p_pics'])) {
				$pic_list = explode('|', $data['p_pics']);
				$piclist = array();
				foreach($pic_list as $key => $value) {
					$piclist[$key]['pic'] = $value;
					$arr_picid = explode("/", $value);
					$arr_length = count($arr_picid);
					$arr_picids = explode(".", $arr_picid[$arr_length-1]);
					$piclist[$key]['picid'] = $arr_picids[0];
				}
				$tpl -> assign('pic_list', $piclist);
			}
		}

		$array_city = arr_city($userinfo['aid']);
		if ($ac == 'addrentcar') {
			$select_province = select_make($userinfo['aid'], $commoncache['provincelist'], "请选择省份");
			$select_city = select_make($userinfo['cid'], $array_city, "请选择城市");
		} else {
			$select_province = select_make($data['aid'], $commoncache['provincelist'], "请选择省份");
			$select_city = select_make($data['cid'], $array_city, "请选择城市");
		}

		$select_rentmodel = select_make($data['p_model'], $commoncache['rentmodellist'], '请选择车型');

		$tpl -> assign('selectprovince', $select_province);
		$tpl -> assign('selectcity', $select_city);

		// 品牌选择
		$select_brand = select_make($data['p_brand'], $commoncache['markbrandlist'], '请选择品牌');
		$select_subbrand = select_subbrand(intval($data['p_subbrand']));
		$select_subsubbrand = select_subbrand(intval($data['p_subsubbrand']));

		$page_get = isset($_GET['page']) ? $_GET['page'] : "";
		$select_rentmodel = select_make($data['p_model'], $commoncache['rentmodellist'], '请选择车型');

		$tpl -> assign('cars', $data);
		$tpl -> assign('select_brand', $select_brand);
		$tpl -> assign('select_subbrand', $select_subbrand);
		$tpl -> assign('select_subsubbrand', $select_subsubbrand);
		$tpl -> assign('select_rentmodel', $select_rentmodel);
		$tpl -> assign('sessionid', session_id());
		$tpl -> assign('page', $page_get);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 刷新租车信息
elseif ($ac == 'refreshrentcar') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('rentcars', "p_id=" . $id);
	if ($data['uid'] != $_SESSION['USER_ID']) {
		showmsg('非法操作', -1);
		exit;
	}
	$listtime = time();
	$rs = $db -> row_update('rentcars', array('listtime' => $listtime), "p_id=" . $id);
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=rentcarlist");
}
// 删除租车信息
elseif ($ac == 'delrentcar') {
	$p_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$data = $db -> row_select_one('rentcars', "p_id=" . $p_id);
	if ($data['uid'] != $_SESSION['USER_ID']) {
		showmsg('非法操作', -1);
		exit;
	}
	if (!empty($data['p_pics'])) {
		$listpic = implode("|", $data['p_pics']);
		foreach($pic_list as $key => $value) {
			$value = substr($value,1);
			$smallpic = str_replace(".", "_small.", $value);
			if(file_exists($value)){
				unlink($value);
			}
			if(file_exists($smallpic)){
				unlink($smallpic);
			}
		}
	}
	$rs = $db -> row_delete('rentcars', "p_id=$p_id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=rentcarlist");
}
// 问答列表
elseif ($ac == 'asklist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'member_feedback', $where, '*', '20', 'id desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['asktime'] = date('Y-m-d H:i:s', $value['asktime']);
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('asklist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 添加
elseif ($ac == 'replyask') {
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array('reply' => '回复不能为空');
		can_not_be_empty($arr_not_empty, $_POST);
		$post['reply'] = $_POST['reply'];
		$post['replytime'] = time();
		$rs = $db -> row_update('member_feedback', $post, "id=" . intval($_POST['id']));
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=asklist");
	}
	// 转向添加或修改页面
	else {
		if (empty($_GET['id'])) $data = array('reply' => '', 'asktime' => '', 'ask' => '');
		else $data = $db -> row_select_one('member_feedback', "id=" . intval($_GET['id']));
		$data['asktime'] = date('Y-m-d H:i:s', $data['asktime']);
		$data['replytime'] = date('Y-m-d H:i:s', $data['replytime']);
		$tpl -> assign('ask', $data);
		$tpl -> assign('ac', $ac);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 单条删除
elseif ($ac == 'delask') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('member_feedback', "id=$id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=asklist");
}
// 促销信息列表
elseif ($ac == 'newslist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'member_news', $where, '*', '20', 'n_id desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['n_date'] = date('Ym', $value['n_addtime']);
		$list[$key]['addtime'] = date('Y-m-d H:i:s', $value['n_addtime']);
		$list[$key]['n_typename'] = $value['n_type'] == 1?"<span class='red'>推荐</span>":"";
	}
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('newslist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 添加
elseif ($ac == 'addnews' || $ac == 'editnews') {
	if ($userinfo['isdealer'] == 2 and $userinfo['ischeck']!=1) {
		showmsg('您的公司信息暂未通过审核，暂不能发布信息！', "/?m=user&a=carlist");
	}
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array('n_title' => '信息标题不可为空');
		can_not_be_empty($arr_not_empty, $_POST);
		foreach (array('n_info') as $v) {
			$_POST[$v] = htmlspecialchars($_POST[$v]);
		}
		$post = post('n_title', 'n_info');

		if ($ac == 'addnews') {
			$post['uid'] = $_SESSION['USER_ID'];
			$post['n_addtime'] = time();
			$post['n_hits'] = rand(19, 99);
			$rs = $db -> row_insert('member_news', $post);
		} else {
			$rs = $db -> row_update('member_news', $post, "n_id=" . intval($_POST['id']));
		}
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=newslist");
	}
	// 转向添加或修改页面
	else {
		if (empty($_GET['id'])) $data = array('n_id' => '', 'n_title' => '', 'n_info' => '', 'catid' => '');
		else $data = $db -> row_select_one('member_news', "n_id=" . intval($_GET['id']));
		$tpl -> assign('news', $data);
		$tpl -> assign('ac', $ac);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 单条删除
elseif ($ac == 'delnews') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('member_news', "n_id=$id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=newslist");
}
// 销售代表列表
elseif ($ac == 'dealerlist') {
	$where = 'uid=' . $_SESSION['USER_ID'];
	include(INC_DIR . 'Page.class.php');
	$Page = new Page($db -> tb_prefix . 'member_dealer', $where, '*', '20', 'id desc');
	$list = $Page -> get_data();
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();
	$tpl -> assign('dealerlist', $list);
	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> display('default/' . $settings['templates'] . '/user.html');
	exit;
}
// 添加
elseif ($ac == 'adddealer' || $ac == 'editdealer') {
	if ($userinfo['isdealer'] == 2 and $userinfo['ischeck']!=1) {
		showmsg('您的公司信息暂未通过审核，暂不能发布信息！', "/?m=user&a=carlist");
	}
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array('name' => '姓名不可为空');
		can_not_be_empty($arr_not_empty, $_POST);
		$post = post('name', 'tel', 'pic');

		if ($ac == 'adddealer') {
			$post['uid'] = $_SESSION['USER_ID'];
			$rs = $db -> row_insert('member_dealer', $post);
		} else {
			$rs = $db -> row_update('member_dealer', $post, "id=" . intval($_POST['id']));
		}
		showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=dealerlist");
	}
	// 转向添加或修改页面
	else {
		if (empty($_GET['id'])) $data = array('id' => '', 'name' => '', 'tel' => '', 'pic' => '');
		else $data = $db -> row_select_one('member_dealer', "id=" . intval($_GET['id']));
		$tpl -> assign('dealer', $data);
		$tpl -> assign('ac', $ac);
		$tpl -> display('default/' . $settings['templates'] . '/user.html');
		exit;
	}
}
// 单条删除
elseif ($ac == 'delnews') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = $db -> row_delete('member_dealer', "id=$id");
	showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), "/?m=user&a=dealerlist");
} else {
	showmsg('非法操作', -1);
}