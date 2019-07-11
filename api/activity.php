<?php
if (!defined('APP_IN')) exit('Access Denied');
if (!is_user_login()) splash('', 110);

$ac_post_arr = ['activity_apply' => '活动报名'];
$ac_get_arr = ['index' => '活动页面', 'activity_list' => '活动列表', 'activity_details' => '活动详情'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'index') {
    $arr_not_empty = [
        'type' => 'type类型不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    if(!in_array($_GET['type'], [1, 2])) splash('', 100, 'type参数不合法');
    $type = $_GET['type'] == 1 ? 7 : 8;
    $filmstrip = get_filmstrip($type);
    foreach($filmstrip as &$v){
        $v['pic'] = upload_url_modify($v['pic']);
    }
    splash([
        'filmstrip' => $filmstrip,
    ]);

} elseif ($ac == "activity_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    if(!in_array($_GET['type'], [1, 2])) splash('', 100, 'type参数不合法');

    $where = "type={$_GET['type']}";
    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'activity', $where, 'id,title,sub_title,start_time,end_time,detail,mainpic,apply_maximum,apply_start_time,apply_end_time', $page_size, 'end_time desc,id desc');
    $list = $Page->get_data();
    foreach ($list as &$activity) {
        if ($activity['mainpic']) {
            $activity['mainpic'] = upload_url_modify($activity['mainpic']);
        }
        // 已经申请的人数
        $activity['apply_mum'] = $db->row_count('activity_user',"activity_id='{$activity['id']}'");
    }
    $total_num = $Page->total_num;
    $current_page = $Page->page;
    $total_page = $Page->total_page;
    splash([
        'data_list' => $list,
        'total_num' => $total_num,
        'current_page' => $current_page,
        'total_page' => $total_page,
    ]);
} elseif ($ac == "activity_details"){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('activity', "id={$_GET['id']}","id,title,sub_title,start_time,end_time,apply_start_time,apply_end_time,sponsor,address,detail,pics,apply_maximum");
    if (!$data) {
        splash('', 100);
    }
    // $data['mainpic'] = upload_url_modify($data['mainpic']);

    if ($data['pics']) {
        $data['pics'] = json_decode($data['pics'], true);
        foreach ($data['pics'] as &$value) {
            $value = upload_url_modify($value);
        }
    }

    // 已经申请的人数
    $data['apply_mum'] = $db->row_count('activity_user',"activity_id='{$_GET['id']}'");
    // 是否收藏
    $data['is_collect'] = $db->row_count('member_collect',"type='activity' and data_id={$_GET['id']} and user_id={$_SESSION['USER_ID']}");
    // 是否报名
    $data['is_apply'] = $db->row_count('activity_user',"activity_id={$_GET['id']} and user_id={$_SESSION['USER_ID']}");

    splash($data);
} elseif ($ac == "activity_apply"){
    $arr_not_empty = [
        'activity_id' => '请选择活动',
        'name' => '请填写姓名',
        'mobile' => '请填写手机号',
    ];
    api_can_not_be_empty($arr_not_empty, $_POST);
    $post = post('activity_id', 'name', 'mobile');
    if (!preg_match('/^1\d{10}$/', $post['mobile'])) splash('', 100, '手机号码格式不正确');

    $data = $db->row_select_one('activity', "id={$post['activity_id']}","id,title,sub_title,start_time,end_time,apply_start_time,apply_end_time,sponsor,address,detail,pics,apply_maximum");

    if (!$data) {
        splash('', 100, 'activity_id值不合法');
    }

    if($data['apply_start_time'] > TIMESTAMP){
        splash('', 8, '报名未开始');
    }elseif($data['apply_end_time'] < TIMESTAMP){
        splash('', 8, '报名已结束');
    }

    $count = $db->row_count('activity_user',"activity_id={$post['activity_id']} and user_id={$_SESSION['USER_ID']}");
    if($count){
        splash('', 8, '你已经报过名了');
    }

    $count = $db->row_count('activity_user',"activity_id='" . $post['activity_id'] . "'");

    if($count >= $data['apply_maximum']){
        splash('', 8, '报名人数已满');
    }
    $post['user_id'] = $_SESSION['USER_ID'];
    $post['created_time'] = TIMESTAMP;

    $rs = $db->row_insert('activity_user', $post);
    if (!$rs) {
        splash('', 1, '报名失败');
	}
    // $insertid = $db->insert_id();
    splash('', 0, '报名成功');
}
