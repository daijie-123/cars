<?php
if (!defined('APP_IN')) exit('Access Denied');
$arr_not_empty = [
    'type' => 'type不能为空',
];
api_can_not_be_empty($arr_not_empty, $_GET);
if(!in_array($_GET['type'], [2, 3])) splash('', 100, 'type参数值不合法！');
$page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
$where  = "isdealer=2 and shoptype={$_GET['type']} and ischeck=1";
include(INC_DIR . 'Page.class.php');
$Page = new Page($db->tb_prefix . 'member', $where, 'id,company,logo,address,lat_lng,shop_score,shop_score', $page_size, 'id desc');
$list = $Page->get_data();
foreach ($list as &$shop) {
    if ($shop['lat_lng']) {
        $lat_lng = explode(',', $shop['lat_lng']);
        $shop['lat'] = $lat_lng[0];
        $shop['lng'] = $lat_lng[1];
    } else {
        $shop['lat'] = 0;
        $shop['lng'] = 0;
    }
    $shop['logo'] = upload_url_modify($shop['logo'], 's');
    // $shop['on_sale_count'] = $db->row_count('cars', 'uid=' . $shop['id'] . ' and issell=0 and isshow=1');
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
