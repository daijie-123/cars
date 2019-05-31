<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：北京市普艾斯科技有限公司
 项目：simcms_锐车1.0
 电话：010-58480317
 Q  Q: 228971357
 网址：http://www.simcms.net
 simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/

if (!defined('APP_IN')) exit('Access Denied');

$tpl -> assign('menustate', 7);

$where = "issell=0 and isshow=1";

if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
	$_COOKIE['city'] = 0;
}

if($_COOKIE['city']!=0){
	$where .= " and cid=".$_COOKIE['city'];
}

$arr_p = array('1' => '3万以下', '2' => '3-5万', '3' => '5-8万', '4' => '8-12万', '5' => '12-18万', '6' => '18-24万', '7' => '24-35万', '8' => '35-50万', '9' => '50-100万', '10' => '100万以上');
$tpl -> assign('arr_price', $arr_p);
$arr_a = array('1' => '1年以内', '2' => '2年以内', '3' => '3年以内', '4' => '4年以内', '5' => '5年以内', '6' => '6年以内', '7' => '6年以上');
$tpl -> assign('arr_age', $arr_a);
$arr_g = array('1' => '1.0L', '2' => '2.0L', '3' => '3.0L', '4' => '4.0L', '5' => '5.0L及以上');
$tpl -> assign('arr_gas', $arr_g);
$arr_b = arr_brand_recom();
$tpl -> assign('arr_brand', $arr_b);

// 车型
if(!empty($_GET['model'])){
	$where .= " and p_model = ".intval($_GET['model']);
} 

//品牌
if(!empty($_GET['brand'])){
	$where .= " and p_brand = ".intval($_GET['brand']);
} 
if(!empty($_GET['subbrand'])){
	$where .= " and p_subbrand = ".intval($_GET['subbrand']);
}
//价格
if (!empty($_GET['price'])) {
	switch ($_GET['price']) {
		case 1:
			$where .= " and p_price > 0 and p_price <= 3";
			break;
		case 2:
			$where .= " and p_price > 3 and p_price <= 5";
			break;
		case 3:
			$where .= " and p_price > 5 and p_price <= 8";
			break;
		case 4:
			$where .= " and p_price > 8 and p_price <= 12";
			break;
		case 5:
			$where .= " and p_price > 12 and p_price <= 18";
			break;
		case 6:
			$where .= " and p_price > 18 and p_price <= 24";
			break;
		case 7:
			$where .= " and p_price > 24 and p_price <= 35";
			break;
		case 8:
			$where .= " and p_price > 35 and p_price <= 50";
			break;
		case 9:
			$where .= " and p_price > 50 and p_price <= 100";
			break;
		case 10:
			$where .= " and p_price > 100";
			break;
		default:
			$where .= "";
	} 
} 
//车龄
if (!empty($_GET['years'])) {
	$compareyear = date("Y") - $_GET['years'];
	switch ($_GET['years']) {
		case 7:
			$where .= " and p_year < ".$compareyear;
			break;
		default:
			$where .= " and p_year >= ".$compareyear;
	} 
}

//行驶里程
if (!empty($_GET['kilometre'])) {
	switch ($_GET['kilometre']) {
		case 1:
			$where .= " and p_kilometre <= 1";
			break;
		case 2:
			$where .= " and p_kilometre >1 and  p_kilometre <=3";
			break;
		case 3:
			$where .= " and p_kilometre >3 and  p_kilometre <=5";
			break;
		case 4:
			$where .= " and p_kilometre >5 and  p_kilometre <=8";
			break;
		case 5:
			$where .= " and p_kilometre >8 and  p_kilometre <=10";
			break;
		case 6:
			$where .= " and p_kilometre > 10";
			break;
	} 
}

// 关键字
if (isset($_GET['k']) and $_GET['k'] != "" and $_GET['k'] != "请输入要搜索的关键词,如:宝马") {
	$where .= " AND (`p_allname` like '%" . $_GET['k'] . "%' or `p_keyword` like '%" . $_GET['k'] . "%')";
} 

// 排序
if (isset($_GET['order'])) {
	setMyCookie("order", $_GET['order'], time() + COOKIETIME);
} else {
	setMyCookie("order", 1, time() + COOKIETIME);
} 
$orderby = "";
if (!empty($_COOKIE['order'])){
	switch ($_COOKIE['order']){
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
			$orderby = "p_year desc,p_month desc";
			break;
		case 8:
			$orderby = "p_year asc,p_month asc";
			break;
		default:
			$orderby = "listtime desc";
	} 
} 
 
include(dirname(dirname(dirname(__FILE__))).'/'.INC_DIR . 'Page.class.php');
$Page = new Page($db -> tb_prefix . 'cars', $where, '*', '24', $orderby);
$listnum = $Page -> total_num;
$list = $Page -> get_data();
foreach($list as $key => $value) {
	if (!empty($value['p_mainpic'])) {
		$pic = explode(".", $value['p_mainpic']);
		$list[$key]['p_mainpic'] = WEB_DOMAIN."/".$pic[0] . "_small" . "." . $pic[1];
	} 
	$list[$key]['p_shortname'] = _substr($value['p_allname'], 0, 26);
	$list[$key]['listtime'] = date('Y-m-d', $value['listtime']);
	$list[$key]['p_details'] = _substr($value['p_details'], 0, 80);
	$list[$key]['p_price'] = intval($value['p_price']) == 0 ? "面议" : "￥" . $value['p_price']."万";
	if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $commoncache['modellist'][$value['p_model']];
	$list[$key]['p_url'] ="index.php?m=cars&id=".$value['p_id'];
} 
$button_basic = $Page -> mobilephone_button_basic();
$tpl -> assign('button_basic', $button_basic);
$tpl -> assign('allnum', $listnum);
$tpl -> assign('carslist', $list);
$tpl -> display('m/search.html');
?>