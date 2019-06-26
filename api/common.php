<?php
if (!defined('APP_IN')) exit('Access Denied');

$ac_get_arr = [
    'mark_brand_list' => '带ABC品牌列表',
    'sub_brand_list' => '二级品牌与车系列表',
    'car_model_list' => '车型列表',
    'dealer_list' => '商家列表',
    'car_service' => '车管服务',
];
$ac_post_arr = [
    'collect' => '收藏'
];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if($ac == 'mark_brand_list'){
    $list = $db->row_select('brand', "b_parent=-1 and classid=1", 'b_id,mark,b_name,b_type,pic', 0, 'mark asc');
    if($list){
		foreach($list as &$value){
			$value['pic'] = brand_url_modify($value['pic']);
		}
    }
    splash($list);
}else if($ac == 'sub_brand_list'){
    $arr_not_empty = [
        'brand_id' => '一级品牌不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
	$list = $db->row_select('brand', "b_parent='" . $_GET['brand_id'] . "'", 'b_id,b_name,b_parent,pic');
	if($list){
		foreach($list as &$value){
            // $value['pic'] = brand_url_modify($value['pic']);

            $value['sub_list'] = $db->row_select('brand', "b_parent='" . $value['b_id'] . "'", 'b_id,b_name,b_parent,b_type,pic');
            // if($value['sub_list']){
            //     foreach($value['sub_list'] as &$v){
            //         $v['pic'] = brand_url_modify($v['pic']);
            //     }
            // }
		}
    }
    splash($list);
}else if($ac == 'car_model_list'){
    $arr_not_empty = [
        'brand_id' => '车系不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $list = [];
    $list = $db->row_select('brand',"b_parent='" . $_GET['brand_id'] . "'", 'b_id,b_name,b_parent,pic');
	if($list){
		foreach($list as &$value){
            // $value['pic'] = brand_url_modify($value['pic']);

            $value['sub_list'] = $db->row_select('brand',"b_parent='" . $value['b_id'] . "'", 'b_id,b_name,b_parent');
            // if($value['sub_list']){
            //     foreach($value['sub_list'] as &$v){
            //         $v['pic'] = brand_url_modify($v['pic']);
            //     }
            // }
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
}else if($ac == 'car_service'){
    $settings  = $commoncache['settings'];
    splash([
        'car_trade_contract' => upload_url_modify($settings['car_trade_contract']),
        'vehicle_evaluation_tel' => $settings['vehicle_evaluation_tel'],
        'query_traffic_violations_tel' => $settings['query_traffic_violations_tel'],
        'accident_rescue_tel' => $settings['accident_rescue_tel'],
    ]);
}else if($ac == 'collect'){
    if (!is_user_login()) splash('', 110);

    $arr_not_empty = [
        'type' => '收藏类型不能为空',
        'data_id' => '数据id不能为空',
        'collect' => '收藏操作不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_POST); 
    if(!in_array($_POST['type'], ['car', 'activity'])) splash('', 100, 'type值不合法');

    $is_collect = $db->row_count('member_collect',"type='{$_POST['type']}' and data_id={$_POST['data_id']} and user_id={$_SESSION['USER_ID']}");

    if($_POST['collect'] == 1){
        if($is_collect){
            splash('', 0, '已收藏成功');
        }else{
            $post = [
                'type' => $_POST['type'],
                'data_id' => $_POST['data_id'],
                'user_id' => $_SESSION['USER_ID'],
                'addtime' => TIMESTAMP,
            ];
            $rs = $db->row_insert('member_collect', $post);
            if($rs){
                splash('', 0, '收藏成功');
            }else{
                splash('', 0, '收藏失败');
            }
        }
    }else{
        if($is_collect){
            $rs = $db -> row_delete('member_collect', "type='{$_POST['type']}' and data_id={$_POST['data_id']} and user_id={$_SESSION['USER_ID']}");
            if($rs){
                splash('', 0, '取消收藏成功');
            }else{
                splash('', 0, '取消收藏失败');
            }
        }else{
            splash('', 0, '已取消收藏');
        }
    }
}