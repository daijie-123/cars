<?php
if (!defined('APP_IN')) exit('Access Denied');

$tpl -> assign('menustate', 1);

if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
	$_COOKIE['city'] = 0;
}

// 幻灯片
$tpl -> assign('filmlist', get_filmstrip(1));

//推荐品牌
$recombrand = get_brand_recom();

foreach($recombrand as $key => $value){
	$recombrand[$key]['carlist'] = get_carlist($_COOKIE['city'],"p_brand=".$value['b_id']." and issell=0 and p_mainpic!='' and isshow=1", '10', 'listtime desc');
}

$tpl -> assign('recombrandlist', $recombrand);



$carlist = array();

//特荐车源
$carlist['todaycar'] = get_todaycar($_COOKIE['city']);



if($settings['version']==2 or $settings['version']==3){

	//商家推荐二手车
	$carlist['sjcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0 and isshow=1 and uid in( select id from " . $db_config['TB_PREFIX'] . "member where isdealer=2)", '8', 'listtime desc');

	//个人推荐二手车
	$carlist['grcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0  and isshow=1 and (uid in( select id from " . $db_config['TB_PREFIX'] . "member where isdealer=1) or uid=0)", '8', 'listtime desc');
	$tpl -> assign('car_list', $carlist); 

	// 推荐商家
	$tpl -> assign('comdealer', get_comshop($_COOKIE['city']));

	// 热门商家
	$tpl -> assign('hotdealer', get_hotshop($_COOKIE['city'])); 
}
else{
	//个人推荐二手车
	$carlist['grcar'] = get_carlist($_COOKIE['city'],"isrecom=1 and issell=0 and p_mainpic!='' and isshow=1", '8', 'listtime desc');
}

$tpl -> assign('car_list', $carlist); 


// 热门关键词
$tpl -> assign('hotkeywordlist', get_bottomkeywords());

$tpl -> display('default/' . $settings['templates'] . '/index.html');

?>