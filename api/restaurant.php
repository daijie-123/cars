<?php
if (!defined('APP_IN')) exit('Access Denied');
// if (!is_user_login()) splash('', 110);

$ac_post_arr = ['restaurant_apply' => '活动报名'];
$ac_get_arr = ['index' => '特色青岛', 'restaurant_list' => '美食店列表', 'restaurant_category_list' => '美食店分类列表'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == 'index') {
    $filmstrip = get_filmstrip(9);
    foreach($filmstrip as &$v){
        $v['pic'] = upload_url_modify($v['pic']);
    }

    $restaurant_category_list = $db->row_select('restaurant_category','isrecom=1', '*', 0, 'orderid desc,id desc');
    foreach($restaurant_category_list as &$v){
        $v['logo'] = upload_url_modify($v['logo']);
        $v['restaurant_quantity'] = 25;
    }

    splash([
        'filmstrip' => $filmstrip,
        'recom_category' => $restaurant_category_list,
    ]);

} elseif ($ac == "restaurant_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;
    $where = "1=1";
    include(INC_DIR . 'Page.class.php');

    if (isset($_GET['isrecom']) and $_GET['isrecom'] != '') {
        $where .= " and isrecom = {$_GET['isrecom']} ";
    }
    // 店铺分类
    if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
        $restaurant_id = $db->row_select('restaurant_category_associate', "category_id={$_GET['category_id']}", 'restaurant_id', 0);
        if($restaurant_id){
            $restaurant_id = array_column($restaurant_id, 'restaurant_id');
            $restaurant_id = implode(',', $restaurant_id);
            $where .= " and id in ($restaurant_id) ";
        }
    }
    // 人均消费
    if (isset($_GET['consume_type']) and $_GET['consume_type'] != '') {
        $consume_type = $_GET['consume_type'];
        if($consume_type == 1){
            $where .= " and consume <= 50 ";
        }else
        // 低价优选
        if($consume_type == 2){
            $where .= " and consume > 50 and consume <= 100";
        }else
        // 低价优选
        if($consume_type == 3){
            $where .= " and consume > 100 and consume <= 300";
        }else
        // 低价优选
        if($consume_type == 3){
            $where .= " and consume > 300";
        }
    }
    // 距离排序
    if($order_type == 1){
        if($_GET['lat'] && $_GET['lon']){
            $lat = $_GET['lat'];
            $lon = $_GET['lon'];
            $order_by = "ROUND(
                6378.138 * 2 * ASIN(
                  SQRT(
                    POW(SIN(({$lat} * PI() / 180- lat * PI() / 180) / 2), 2) + COS({$lat} * PI() / 180) * COS(lat * PI() / 180) * POW(SIN(({$lon} * PI() / 180- lon * PI() / 180) / 2), 2)
                  )
                ) * 1000
              ) ASC,id DESC";
        }else{
            $order_by = 'id DESC';
        }
    }else
    // 好评优选
    if($order_type == 2){
        $order_by = 'score DESC';
    }else
    // 低价优选
    if($order_type == 3){
        $order_by = 'consume ASC';
    }else
    // 低价优选
    if($order_type == 4){
        $order_by = 'consume DESC';
    }else{
        $order_by = 'id DESC';
    }

    $Page = new Page($db->tb_prefix . 'restaurant', $where, '*', $page_size, $order_by);
    $list = $Page->get_data();
    foreach ($list as &$restaurant) {
        if ($restaurant['logo']) {
            $restaurant['logo'] = upload_url_modify($restaurant['logo']);
        }
        $restaurant['score'] = 5;
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
} elseif ($ac == "restaurant_details"){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('restaurant', "id={$_GET['id']}","id,title,sub_title,start_time,end_time,apply_start_time,apply_end_time,sponsor,address,detail,pics,apply_maximum");
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
    $data['apply_mum'] = $db->row_count('restaurant_user',"restaurant_id='{$_GET['id']}'");
    // 是否收藏
    $data['is_collect'] = $db->row_count('member_collect',"type='restaurant' and data_id={$_GET['id']} and user_id={$_SESSION['USER_ID']}");
    // 是否报名
    $data['is_apply'] = $db->row_count('restaurant_user',"restaurant_id={$_GET['id']} and user_id={$_SESSION['USER_ID']}");

    splash($data);
} elseif ($ac == "restaurant_category_list"){
    $restaurant_category_list = $db->row_select('restaurant_category','1=1', '*', 0, 'orderid desc,id desc');
    foreach($restaurant_category_list as &$v){
        $v['logo'] = upload_url_modify($v['logo']);
        $v['restaurant_quantity'] = 25;
    }
    splash([
        'category_list' => $restaurant_category_list,
    ]);
}
