<?php
if (!defined('APP_IN')) exit('Access Denied');

$tpl -> assign('menustate', 9);

$brand_search = arr_brand_with_search();
$select_brand_search = select_make('', $brand_search, '请选择品牌');
$tpl->assign( 'selectbrandsearch', $select_brand_search );


//推荐新闻
$comnewslist = get_comnews(53 , 10);
$tpl->assign( 'comnewslist', $comnewslist );

if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
	$_COOKIE['city'] = 0;
}

$where = "isshow=1";

if($_COOKIE['city']!=0){
	$where .= " and cid = ".$_COOKIE['city'];
}

if (isset($_GET['clear']) and $_GET['clear'] == 1) {
	setMyCookie("area", '', time() - COOKIETIME);
	setMyCookie("keywords", '', time() - COOKIETIME);
	setMyCookie("brand", '', time() - COOKIETIME);
	setMyCookie("subbrand", '', time() - COOKIETIME);
	setMyCookie("price", '', time() - COOKIETIME);
	setMyCookie("model", '', time() - COOKIETIME);
	setMyCookie("show", '', time() - COOKIETIME);
	setMyCookie("order", '', time() - COOKIETIME);
} 

$arr_p = array('1' => '100元以下', '2' => '100-200元', '3' => '200-300元', '4' => '300-400元', '5' => '400-500元', '6' => '500元以上');
$tpl -> assign('arr_price', $arr_p);
$arr_b = arr_brand_recom();
$tpl -> assign('arr_brand', $arr_b);
$arr_c = arr_rentmodel();
$tpl -> assign('arr_city', $arr_c);


if (isset($_GET['c'])) {
	$arr_c = explode("_", trim($_GET['c'])); 
	// 价格
	if ($arr_c['0'] == "p") {
		if (isset($arr_c[1])) {
			setMyCookie("price", intval($arr_c[1]), time() + COOKIETIME);
		} 
		if (isset($_COOKIE['price']) and $_COOKIE['price'] == 0) {
			setMyCookie("price", '', time() - COOKIETIME);
		} 
	} 
	// 车型
	elseif ($arr_c['0'] == "m") {
		if (isset($arr_c[1])) {
			setMyCookie("model", intval($arr_c[1]), time() + COOKIETIME);
		} 
		if (isset($_COOKIE['model']) and $_COOKIE['model'] == 0) {
			setMyCookie("model", '', time() - COOKIETIME);
		} 
	} 
	// 品牌
	elseif ($arr_c['0'] == "b") {
		if (isset($arr_c[1])) {
			setMyCookie("brand", intval($arr_c[1]), time() + COOKIETIME);
			if(!empty($_GET['sb'])){
				setMyCookie("subbrand", intval($_GET['sb']), time() + COOKIETIME);
			}
		} 
		if (isset($_COOKIE['brand']) and $_COOKIE['brand'] == 0) {
			setMyCookie("brand", '', time() - COOKIETIME);
			setMyCookie("subbrand", '', time() - COOKIETIME);
		}
	} 
	// 城市
	elseif ($arr_c['0'] == "c") {
		if (isset($arr_c[1])) {
			setMyCookie("area", intval($arr_c[1]), time() + COOKIETIME);
		} 
		if (isset($_COOKIE['area']) and $_COOKIE['area'] == 0) {
			setMyCookie("area", '', time() - COOKIETIME);
		} 
	} 
	// 车源来源
	elseif ($arr_c['0'] == "f") {
		if (isset($arr_c[1])) {
			setMyCookie("carfrom", intval($arr_c[1]), time() + COOKIETIME);
		} 
		if (isset($_COOKIE['carfrom']) and $_COOKIE['carfrom'] == 0) {
			setMyCookie("carfrom", '', time() - COOKIETIME);
		} 
	} 
} 

if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and  $_GET['sb'] == 0) {
	setMyCookie("subbrand", '', time() - COOKIETIME);
} 

if (isset($_COOKIE['brand']) and $_COOKIE['brand']<>0) {
	$where .= " and p_brand = ".$_COOKIE['brand'];
} 

if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand'] <> 0) {
	$subbrand = $db -> row_select_one('brand','b_id='.$_COOKIE['subbrand'],'b_name');
	$tpl -> assign('subrandname', $subbrand['b_name']);
	$where .= " and p_subbrand = " . $_COOKIE['subbrand'];
} 

if (isset($_COOKIE['price']) and $_COOKIE['price']<>0) {
	switch ($_COOKIE['price']) {
		case 1:
			$where .= " and dayprice <= 100";
			break;
		case 2:
			$where .= " and dayprice > 100 and dayprice <= 200";
			break;
		case 3:
			$where .= " and dayprice > 200 and dayprice <= 300";
			break;
		case 4:
			$where .= " and dayprice > 300 and dayprice <= 400";
			break;
		case 5:
			$where .= " and dayprice > 400 and dayprice <= 500";
			break;
		case 6:
			$where .= " and dayprice > 500";
			break;
		default:
			$where .= "";
	} 
} 

if (isset($_COOKIE['model']) and $_COOKIE['model']<>0) {
	$where .= " and p_model = ".$_COOKIE['model'];
} 

if (isset($_COOKIE['area']) and $_COOKIE['area']<>0) {
	$where .= " and cid = ".$_COOKIE['area'];
} 

// 关键字
if (isset($_GET['k']) and $_GET['k'] != "" and $_GET['keywords'] != "请输入要搜索的关键词,如:宝马") {
	setMyCookie("keywords", $_GET['k'], time() + COOKIETIME);
} elseif (isset($_GET['k']) and $_GET['k'] == "") {
	setMyCookie("keywords", '', time() - COOKIETIME);
} 

if (!empty($_COOKIE['keywords'])) {
	$where .= " AND (`p_allname` like '%" . $_COOKIE['keywords'] . "%' or `p_keyword` like '%" . $_COOKIE['keywords'] . "%')";
} 

//来源
if (isset($_COOKIE['carfrom']) and $_COOKIE['carfrom']<>0) {
	switch ($_COOKIE['carfrom']) {
		case 1:
			$where .= " and uid in( select id from " . $db_config['TB_PREFIX'] . "member where isdealer=2)";
			break;
		case 2:
			$where .= " and (uid in( select id from " . $db_config['TB_PREFIX'] . "member where isdealer=1) or uid=0)";
			break;
		default:
			$where .= "";
	} 
} 

// 排序
if (isset($_GET['order'])) {
	setMyCookie("order", $_GET['order'], time() + COOKIETIME);
} else {
	setMyCookie("order", 1, time() + COOKIETIME);
} 
$orderby = "";
if (!empty($_COOKIE['order'])) {
	switch ($_COOKIE['order']) {
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
		default:
			$orderby = "listtime desc";
	} 
} 

// 显示方式
if (isset($_GET['showtype'])) {
	setMyCookie("showtype", $_GET['showtype'], time() + COOKIETIME);
} else {
	setMyCookie("showtype", 'list', time() + COOKIETIME);
} 

include(INC_DIR . 'Page.class.php');
$Page = new Page($db -> tb_prefix . 'rentcars', $where, '*', '24', $orderby);
$listnum = $Page -> total_num;
$list = $Page -> get_data();
foreach($list as $key => $value) {
	if(!empty($value['p_mainpic'])){
		$pic = explode(".", $value['p_mainpic']);
		$list[$key]['p_mainpic'] = $pic[0]."_small".".".$pic[1];
	}
	$list[$key]['p_shortname'] = _substr($value['p_allname'], 0, 26);
	$list[$key]['listtime'] = date('Y-m-d', $value['listtime']);
	$list[$key]['p_details'] = _substr($value['p_details'], 0, 80);
	$list[$key]['dayprice'] = intval($value['dayprice']) == 0 ? "面谈" : "￥" . $value['dayprice']."元/天";
	
	$list[$key]['monthprice'] = intval($value['monthprice']) == 0 ? "面谈" : "￥" . $value['monthprice']."元/月";
	$list[$key]['p_url'] = HTML_DIR . "rentcars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";

	//商家信息
	if(!empty($value['uid'])){ 
		$user = $db -> row_select_one('member', 'id=' . $value['uid'],'ischeck');
		$list[$key]['isshop'] = $user['ischeck'];
	}
	
	//地区
	if(!empty($value['cid'])){
		$area = $db -> row_select_one('area','id = '.$value['cid']);
		$list[$key]['area'] = $area['name'];
	}
} 
$button_basic = $Page -> button_basic();
$button_select = $Page -> button_select();
$tpl -> assign('allnum', $listnum);
$tpl -> assign('button_basic', $button_basic);
$tpl -> assign('button_select', $button_select);
$tpl -> assign('select_model', $select_model);
$tpl -> assign('rentcarslist', $list);

$tpl -> display('default/' . $settings['templates'] . '/rentcarlist.html');

?>