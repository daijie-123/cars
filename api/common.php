<?php
if (!defined('APP_IN')) exit('Access Denied');

$ac_get_arr = [
    'mark_brand_list' => '带ABC品牌列表',
    'sub_brand_list' => '二级品牌与车系列表',
    'car_model_list' => '车型列表',
    'dealer_list' => '商家列表',
];
$ac_post_arr = [
];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if($ac == 'mark_brand_list'){
    splash($commoncache['markbrandlist']);
}else if($ac == 'sub_brand_list'){
    $arr_not_empty = [
        'brand_id' => '一级品牌不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $list = [];
	$list = $db->row_select('brand', "b_parent='" . $_GET['brand_id'] . "'", 'b_id,b_name,b_parent');
	if($list){
		foreach($list as &$value){
			$value['sub_list'] = $db->row_select('brand', "b_parent='" . $value['b_id'] . "'", 'b_id,b_name,b_parent');
		}
    }
    splash($list);
}else if($ac == 'car_model_list'){
    $arr_not_empty = [
        'brand_id' => '车系不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $list = [];
    $list = $db->row_select('brand',"b_parent='" . $_GET['brand_id'] . "'", 'b_id,b_name,b_parent');
	if($list){
		foreach($list as &$value){
			$value['sub_list'] = $db->row_select('brand',"b_parent='" . $value['b_id'] . "'", 'b_id,b_name,b_parent');
		}
    }
    splash($list);
}else if($ac == 'dealer_list'){
    $where = "isdealer=2 and ischeck=1";

	if($_GET['type']){
		$where .= " and shoptype=" . $_GET['type'];
	}

    if($_GET['isrecom']){
		$where .= " and cid=1";
    }

    if($_GET['ishot']){
		$where .= " and ishot=1";
    }

	$list = $db->row_select('member', $where, 'id,logo,company,mobilephone,shoptype');
    splash($list);
}