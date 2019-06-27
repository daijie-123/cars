<?php
if (!defined('APP_IN')) exit('Access Denied');
$arr_not_empty = [
    'id' => '车辆id不能为空',
];
api_can_not_be_empty($arr_not_empty, $_GET);
$p_id = $_GET['id'];
$data = $db->row_select_one('cars', "issell=0 and isshow=1 and p_id=" . $p_id, 'p_id,p_mainpic,p_pics,p_brand,p_subbrand,p_subsubbrand,p_allname,p_kilometre,p_year,p_month,isrecom,p_addtime,p_price,p_newprice,p_details,p_model,uid,p_color,p_gas,p_transmission,p_emission,p_country,p_hits');
if (!$data) {
    splash('', 100);
}
$db->row_update('cars', ['p_hits' => $data['p_hits'] + 1], "p_id=" . $p_id);

$data['p_details'] = htmlspecialchars_decode($data['p_details']);

if (!empty($data['p_model'])) {
    $category = $db->row_select_one('model', 's_id=' . $data['p_model']);
    $data['modelname'] = $category['s_name'];
}

if (!empty($data['p_mainpic'])) {
    $data['p_mainpic'] = upload_url_modify($data['p_mainpic']);
}

if (!empty($data['p_pics'])) {
    $piclist = explode('|', $data['p_pics']);
    foreach ($piclist as &$v) {
        $v = upload_url_modify($v);
    }
    $data['p_pics'] = $piclist;
}

//显示自定义参数
$paralist = $db->row_select('selfdefine', "isshow=1", 'id,type_name,type_value,c_name');
foreach ($paralist as $key => $value) {
    $para_value = $db->row_select_one('selfdefine_value', "p_id=" . $data['p_id'] . ' and c_id=' . $value['id']);
    if ($value['type_name'] == 'checkbox') {
        $checkvalue = str_replace("|", '', $para_value['c_value']);
        $paralist[$key]['c_value'] = $checkvalue;
    } else {
        $paralist[$key]['c_value'] = $para_value['c_value'];
    }
    $paralist[$key]['c_value'] = $paralist[$key]['c_value'] ?: '未知';

}
$data['para_list'] = $paralist;
//商家信息
if (!empty($data['uid'])) {
    $shop = $db->row_select_one('member', 'id=' . $data['uid'], 'id,company,logo,address,mobilephone,shoptype,lat,lon,shop_score');

    if (!$shop['lat'] || !$shop['lon']) {
        $shop['lat'] = 0;
        $shop['lon'] = 0;
    }
    $shop['on_sale_count'] = $db->row_count('cars', 'uid=' . $data['uid'] . ' and issell=0 and isshow=1');
    $shop['logo'] = upload_url_modify($shop['logo']);
    $data['shop'] = $shop;
}
// 为你推荐同品牌二手车
$recomm_list = $db->row_select('cars', "issell=0 and isshow=1 and p_brand=" . $data['p_brand'] . " and p_id!=" . $p_id, 'p_id,p_mainpic,p_allname,p_price,p_newprice', 4, 'listtime desc');
foreach ($recomm_list as &$value) {

    if (!empty($value['p_mainpic'])) {
        $value['p_mainpic'] = upload_url_modify($value['p_mainpic'], 's');
    }

    $value['collect_count'] = $db->row_count('member_collect',"type='car' and data_id={$p_id}");
}
$data['recomm_list'] = $recomm_list;

// 是否新上
$data['new_arrival'] = ($data['p_addtime'] > (TIMESTAMP - (24 * 60 * 60))) ? 1 : 0;
$data['is_collect'] = is_user_login() ? ($db->row_count('member_collect',"type='car' and data_id={$p_id} and user_id={$_SESSION['USER_ID']}")) : 0;
splash($data);