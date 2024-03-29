<?php
if (!defined('APP_IN')) exit('Access Denied');
$ac_post_arr = [];
$ac_get_arr = ['index' => '新车页面', 'shop_list' => '车商列表'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'index') {
    $filmstrip = get_filmstrip(5);
    foreach($filmstrip as &$v){
        $v['pic'] = upload_url_modify($v['pic']);
    }
    splash([
        'filmstrip' => $filmstrip,
    ]);

} elseif ($ac == "shop_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    $where  = "isdealer=2 and shoptype=2 and ischeck=1";
    if (!empty($_REQUEST['keywords'])) {
        $where .= " AND (`nicname` like '%" . $_GET['keywords'] . "%' or `company` like '%" . $_GET['keywords'] . "%' or `shopdetail` like '%" . $_GET['keywords'] . "%')";
    }
    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'member', $where, 'id,company,logo,address,lat,lon,shop_score,shop_score', $page_size, 'id desc');
    $list = $Page->get_data();
    foreach ($list as &$shop) {
        if (!$shop['lat'] || !$shop['lon']) {
            $shop['lat'] = 0;
            $shop['lon'] = 0;
        }
        $shop['logo'] = upload_url_modify($shop['logo']);
        $shop['on_sale_count'] = $db->row_count('cars', 'uid=' . $shop['id'] . ' and issell=0 and isshow=1');
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
}
