<?php
if (!defined('APP_IN')) exit('Access Denied');
$page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;

// 总数
$pagecount = $db->row_count('cars');

$where = 'issell=0 and isshow=1';

$arr_p = array('1' => '3万以下', '2' => '3-5万', '3' => '5-10万', '4' => '10-15万', '5' => '15-20万', '6' => '20-30万', '7' => '30-50万', '8' => '50万以上');
// $tpl->assign('arr_price', $arr_p);
$arr_a = array('1' => '1年以内', '2' => '1-3年', '3' => '3-5年', '4' => '5年以上');
// $tpl->assign('arr_age', $arr_a);
// $arr_g = array(1 => '1.0L以下', 2 => '1.1-1.5L', 3 => '1.6-2.0L', 4 => '2.1-3.0L', 5 => '3.0L及以上');
// $tpl->assign('arr_gas', $arr_g);
// $arr_b = arr_brand_recom();
// $tpl->assign('arr_brand', $arr_b);
// $arr_t = array('1' => '特荐', '2' => '推荐', '3' => '热门');
// $tpl->assign('arr_recom', $arr_t);
// $arr_s = array('1' => '未卖', '2' => '已卖');
// $tpl->assign('arr_status', $arr_s);
// $arr_u = array('1' => '商家', '2' => '个人');
// $tpl->assign('arr_usertype', $arr_u);
//$arr_brandname=arr_brandname();
// $tpl -> assign('arr_brandname', $arr_brandname);

//搜索条件
if (isset($_GET['brand']) and $_GET['brand'] <> 0) {
    $where .= " and p_brand = " . $_GET['brand'];
}

if (isset($_GET['subbrand']) and $_GET['subbrand'] <> 0) {
    $where .= " and p_subbrand = " . $_GET['subbrand'];
}

if (isset($_GET['subsubbrand']) and $_GET['subsubbrand'] <> 0) {
    $where .= " and p_subsubbrand = " . $_GET['subsubbrand'];
}

if (isset($_GET['price']) and $_GET['price'] <> 0) {
    switch ($_GET['price']) {
        case 1:
            $where .= " and p_price > 0 and p_price <= 3";
            break;
        case 2:
            $where .= " and p_price > 3 and p_price <= 5";
            break;
        case 3:
            $where .= " and p_price > 5 and p_price <= 10";
            break;
        case 4:
            $where .= " and p_price > 10 and p_price <= 15";
            break;
        case 5:
            $where .= " and p_price > 15 and p_price <= 20";
            break;
        case 6:
            $where .= " and p_price > 20 and p_price <= 30";
            break;
        case 7:
            $where .= " and p_price > 30 and p_price <= 50";
            break;
        case 8:
            $where .= " and p_price > 50";
            break;
        default:
            $where .= "";
    }
}

if (isset($_GET['age']) and $_GET['age'] <> 0) {
    switch ($_GET['age']) {
        case 1:
        // 1年以内
            $where .= " and p_add_license_time >= " . strtotime("-1 year");
            break;
        case 2:
        // 1-3年
            $where .= " and p_add_license_time < " . strtotime("-1 year");
            $where .= " and p_add_license_time >= " . strtotime("-3 year");
            break;
        case 3:
        // 3-5年
            $where .= " and p_add_license_time < " . strtotime("-3 year");
            $where .= " and p_add_license_time >= " . strtotime("-5 year");
            break;
        case 3:
        // 5年以上
            $where .= " and p_add_license_time < " . strtotime("-5 year");
            break;
        default:
            $where .= "";
    }
}

if (isset($_GET['model']) and $_GET['model'] <> 0) {
    $where .= " and p_model = " . $_GET['model'];
}

if (isset($_GET['gas']) and $_GET['gas'] <> 0) {
    switch ($_GET['gas']) {
        case 1:
            $where .= " and p_gas <= 1.0";
            break;
        case 2:
            $where .= " and p_gas >= 1.1 and p_gas <= 1.5";
            break;
        case 3:
            $where .= " and p_gas >= 1.6 and p_gas <= 2.0";
            break;
        case 4:
            $where .= " and p_gas >= 2.1 and p_gas <= 3.0";
            break;
        case 5:
            $where .= " and p_gas >= 3.1";
            break;
        default:
            $where .= "";
    }
}

if (isset($_GET['usertype']) and $_GET['usertype'] <> 0) {
    switch ($_GET['usertype']) {
        case 1:
            $where .= " and uid in (select id from " . $db->tb_prefix . "member where ischeck=1 and isdealer=2)";
            break;
        case 2:
            $where .= " and uid in (select id from " . $db->tb_prefix . "member where isdealer=1)";
            break;
        case 3:
            $where .= " and uid=0";
            break;
    }
}

if (isset($_GET['issprecom']) and $_GET['issprecom'] != '') {
    $where .= " and issprecom = {$_GET['issprecom']} ";
}

if (isset($_GET['isrecom']) and $_GET['isrecom'] != '') {
    $where .= " and isrecom = {$_GET['isrecom']} ";
}

if (isset($_GET['ishot']) and $_GET['ishot'] != '') {
    $where .= " and ishot = {$_GET['ishot']} ";
}

// if (isset($_GET['issell']) and $_GET['issell'] != '') {
//     $where .= " and issell = {$_GET['issell']} ";
// }

if (!empty($_GET['keywords'])) {
    $where .= " AND (`p_allname` like '%" . $_GET['keywords'] . "%' or `p_keyword` like '%" . $_GET['keywords'] . "%' or `p_no` like '%" . $_GET['keywords'] . "%')";
}

$orderby = "";
if (!empty($_GET['order'])) {
    switch ($_GET['order']) {
        case 1:
            $orderby = "listtime desc";
            break;
        case 2:
            $orderby = "listtime asc";
            break;
        case 3:
            $orderby = "p_price asc";
            break;
        case 4:
            $orderby = "p_price desc";
            break;
        case 5:
            $orderby = "p_kilometre asc";
            break;
        case 6:
            $orderby = "p_kilometre desc";
            break;
        case 7:
            $orderby = "p_add_license_time desc";
            break;
        case 8:
            $orderby = "p_add_license_time asc";
            break;
        default:
            $orderby = "listtime desc";
    }
}
include(INC_DIR . 'Page.class.php');
$Page = new Page($db->tb_prefix . 'cars', $where, 'p_id,p_mainpic,p_allname,p_kilometre,p_year,p_month,isrecom,p_addtime,p_price,p_newprice', $page_size, 'listtime desc');
$list = $Page->get_data();
$total_num = $Page->total_num;
$current_page = $Page->page;
$total_page = $Page->total_page;
foreach ($list as &$value) {
    // 是否新上
    $value['new_arrival'] = ($value['p_addtime'] > (TIMESTAMP - (24 * 60 * 60))) ? 1 : 0;
    // $value['listtime'] = date('Y-m-d H:i:s', $value['listtime']);
    // $value['p_addtime'] = date('Y-m-d H:i:s', $value['p_addtime']);
    $value['p_mainpic'] = upload_url_modify($value['p_mainpic'], 's');
    // 是否收藏
    $value['is_collect'] = 0;
    // if (!empty($value['p_model'])) $value['p_modelname'] = $commoncache['modellist'][$value['p_model']];
    // $value['p_url'] = HTML_DIR . "buycars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
}

splash([
    'data_list' => $list,
    'total_num' => $total_num,
    'current_page' => $current_page,
    'total_page' => $total_page,
]);