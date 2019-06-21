<?php
if (!defined('APP_IN')) exit('Access Denied');
$ac_post_arr = [];
$ac_get_arr = ['index' => '首页', 'car_list' => '投诉列表', 'safeguard_details' => '维权详情'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'index') {
    $filmstrip = get_filmstrip(4);
    foreach($filmstrip as &$v){
        $v['pic'] = upload_url_modify($v['pic']);
    }
    splash([
        'filmstrip' => $filmstrip,
    ]);

} elseif ($ac == "car_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 2;
    $where = "issell=0 and isshow=1 and issprecom=1";
    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'cars', $where, 'p_id,p_mainpic,p_allname,p_kilometre,p_year,p_month,isrecom,p_addtime,p_price,p_newprice', $page_size, 'listtime desc,p_id desc');
    $list = $Page->get_data();
    $total_num = $Page->total_num;
    $current_page = $Page->page;
    $total_page = $Page->total_page;
    foreach ($list as &$value) {
        // 是否新上
        $value['new_arrival'] = ($value['p_addtime'] > (TIMESTAMP - (24 * 60 * 60))) ? 1 : 0;

        $value['p_mainpic'] = upload_url_modify($value['p_mainpic'], 's');
    }

    splash([
        'data_list' => $list,
        'total_num' => $total_num,
        'current_page' => $current_page,
        'total_page' => $total_page,
    ]);
}
