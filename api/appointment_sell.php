<?php
if (!defined('APP_IN')) exit('Access Denied');
include ('page.php');
// include(INC_DIR . 'html.func.php');
if (!is_user_login()) splash('', 110);
// 个人信息
$userinfo = $db -> row_select_one('member', "id={$_SESSION['USER_ID']}");
$userinfo['regtime'] = date("Y/m/d", $userinfo['regtime']);

// 允许操作
$ac_arr = array('index' => '欢迎登陆', 'logout' => '退出登录', 'upinfo' => '编辑个人信息', 'uppwd' => '修改密码','addlogo'=>'修改头像', 'addcar' => '添加车源', 'editcar' => '编辑车源', 'delcar' => '删除车源', 'refresh' => '刷新车源', 'sellcar' => '改变买卖状态', 'carlist' => '车源列表','rentcarlist' => '租车信息列表', 'addrentcar' => '添加租车信息', 'editrentcar' => '编辑租车信息', 'delrentcar' => '删除租车信息', 'refreshrentcar' => '刷新租车信息');

$ac_post_arr = ['add' => '修改手机号'];
$ac_get_arr = [];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'list') {
}
elseif ($ac == 'add') {
    $arr_not_empty = [
        'dealer_id' => '请选择车行',
        'contact_name' => '联系人不能为空',
        'contact_tel' => '手机号码不能为空',
        'subsubbrand_id' => '请选择车型',
        'car_license' => '车辆牌照不能为空',
        'add_license_time' => '上牌时间不能为空',
        'kilometre' => '行驶里程不能为空',
        'self_assessment' => '车况自评不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_POST);
    $post = post('contact_name', 'contact_tel', 'dealer_id', 'brand_id', 'subbrand_id', 'subsubbrand_id', 'car_license', 'add_license_time', 'kilometre', 'self_assessment');
    $post['car_license'] = trim($post['car_license']);
    $count = $db->row_count('appointment_sell', "car_license='{$post['car_license']}'");
    if($count) splash('', 8, '此牌照已经提交过了。');
    if (!preg_match('/^1\d{10}$/', $post['contact_tel'])) splash('', 100, '手机号码格式不正确');
    $post['allname'] = brand_full_name($post['subsubbrand_id']);
    $post['user_id'] = $userinfo['id'];
    $post['add_license_time'] = strtotime($post['add_license_time']);
    $post['self_assessment'] = htmlspecialchars($post['self_assessment']);
    $post['create_time'] = TIMESTAMP;
    $post['ischeck'] = 0;
    $rs = $db->row_insert('appointment_sell', $post);
    if (!$rs) {
        splash('', 1, '卖车预约失败');
	}
	else{
        // $insertid = $db->insert_id();
        splash('', 0, '卖车预约成功');
	}
}