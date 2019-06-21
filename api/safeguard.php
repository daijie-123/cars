<?php
if (!defined('APP_IN')) exit('Access Denied');
$ac_post_arr = [];
$ac_get_arr = ['index' => '维权页面', 'safeguard_list' => '投诉列表', 'safeguard_details' => '维权详情'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'index') {
    $filmstrip = get_filmstrip(6);
    foreach($filmstrip as &$v){
        $v['pic'] = upload_url_modify($v['pic']);
    }
    splash([
        'filmstrip' => $filmstrip,
    ]);

} elseif ($ac == "safeguard_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    $where = "1=1";
    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'member_safeguard', $where, 'id,complain_company,car_allname,pics,addtime,status,kilometre,mediate_company', $page_size, 'id desc');
    $list = $Page->get_data();
    foreach ($list as &$safeguard) {
        if ($safeguard['pics']) {
            $safeguard['pics'] = json_decode($safeguard['pics'], true);
            foreach ($safeguard['pics'] as &$value) {
                $value = upload_url_modify($value);
            }
        }
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
} elseif ($ac == "safeguard_details"){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('member_safeguard', "id={$_GET['id']}","id,complain_company,car_allname,pics,addtime,status,kilometre,mediate_company");
    if (!$data) {
        splash('', 100);
    }
    if ($data['pics']) {
        $data['pics'] = json_decode($data['pics'], true);
        foreach ($data['pics'] as &$value) {
            $value = upload_url_modify($value);
        }
    }
    splash($data);
}
