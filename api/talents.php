<?php
if (!defined('APP_IN')) exit('Access Denied');
$ac_post_arr = [];
$ac_get_arr = ['recruitment_list' => '招聘列表', 'recruitment_details' => '招聘详情', 'talents_list' => '招聘列表', 'talents_details' => '招聘详情', 'talents_type' => '人才类型'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'recruitment_list') {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    $where = '1=1';
    //搜索条件
    if (isset($_GET['wage_min']) and $_GET['wage_min'] <> 0 and isset($_GET['wage_max']) and $_GET['wage_max'] <> 0) {
        $wage_min = $_GET['wage_min'];
        $wage_max = $_GET['wage_max'];

        $where .= " and (($wage_min <= wage_min and $wage_max >= wage_min) or ($wage_min <= wage_max and $wage_max >= wage_max) or ($wage_min >= wage_min and $wage_max <= wage_max))";
    }

    if (isset($_GET['experience']) and $_GET['experience'] <> 0) {
        $where .= " and experience = " . $_GET['experience'];
    }

    if (isset($_GET['education']) and $_GET['education'] <> 0) {
        $where .= " and education = " . $_GET['education'];
    }
    
    if (isset($_GET['type_id']) and $_GET['type_id'] <> 0) {
        $where .= " and type_id = " . $_GET['type_id'];
    }

    if (!empty($_GET['keywords'])) {
        $where .= " AND (`title` like '%" . $_GET['keywords'] . "%')";
    }

    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'dealer_recruitment', $where, 'id,user_id,title,wage_min,wage_max,experience,education,label,addtime', $page_size, 'addtime desc');
    $list = $Page->get_data();
    $total_num = $Page->total_num;
    $current_page = $Page->page;
    $total_page = $Page->total_page;
    foreach ($list as &$value) {
        $value['label'] = explode('|', $value['label']);
        $company = $db->row_select_one('member', "id={$value['user_id']}", 'company');
        $value['company'] = $company['company'];
    }

    splash([
        'data_list' => $list,
        'total_num' => $total_num,
        'current_page' => $current_page,
        'total_page' => $total_page,
    ]);
}else if($ac == 'recruitment_details'){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('dealer_recruitment', "id={$_GET['id']}","id,user_id,title,wage_min,wage_max,experience,education,label,addtime,`desc`,tel");
    if (!$data) {
        splash('', 100);
    }
    $data['label'] = explode('|', $data['label']);
    $data['company'] = $db->row_select_one('member', "id={$data['user_id']}", 'company,lon,lat,address,shopdetail');

    // 是否收藏
    $data['is_collect'] = is_user_login() ? $db->row_count('member_collect',"type='recruitment' and data_id={$_GET['id']} and user_id={$_SESSION['USER_ID']}") : 0;

    splash($data);
}else if ($ac == 'talents_list') {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    $where = '1=1';
    //搜索条件
    if (isset($_GET['experience_min']) and $_GET['experience_min'] <> 0 and isset($_GET['experience_max']) and $_GET['experience_max'] <> 0) {
        $experience_min = $_GET['experience_min'];
        $experience_max = $_GET['experience_max'];

        $where .= " and experience <= $experience_max and experience >= $experience_min";
    }

    if (isset($_GET['type_id']) and $_GET['type_id'] <> 0) {
        $where .= " and type_id = " . $_GET['type_id'];
    }

    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'talents', $where, 'id,logo,name,type_id,experience', $page_size, 'addtime desc');
    $list = $Page->get_data();
    $total_num = $Page->total_num;
    $current_page = $Page->page;
    $total_page = $Page->total_page;
    foreach ($list as &$value) {
        $value['logo'] = upload_url_modify($value['logo']);
        $type = $db->row_select_one('talents_type', "id={$value['type_id']}", 'name');
        $value['type'] = $type['name'];
    }

    splash([
        'data_list' => $list,
        'total_num' => $total_num,
        'current_page' => $current_page,
        'total_page' => $total_page,
    ]);
}else if($ac == 'talents_details'){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('talents', "id={$_GET['id']}", "id,logo,name,type_id,experience,tel,cert_no,user_id");
    if (!$data) {
        splash('', 100);
    }

    $data['logo'] = upload_url_modify($data['logo']);
    $company = $db->row_select_one('member', "id={$data['user_id']}", 'company');
    $data['company'] = $company['company'];
    $type = $db->row_select_one('talents_type', "id={$data['type_id']}", 'name');
    $data['type'] = $type['name'];

    splash($data);
}else if($ac == 'talents_type'){
    $list = $db->row_select('talents_type', "1=1", 'id,name', 0, 'orderid desc');\
    splash(['talents_type' => $list]);
}
