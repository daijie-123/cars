<?php
if (!defined('APP_IN')) exit('Access Denied');

if($settings['version']==3){
	// 城市选择
	if (!empty($_COOKIE['city'])) {
		$citydata = $db -> row_select_one('area', "id='" . $_COOKIE['city'] . "'", 'parentid');
		$select_province = select_make($citydata['parentid'], $commoncache['provincelist'], "请选择省份");
		$array_city = arr_city($citydata['parentid']);
		$select_city = select_make($_COOKIE['city'], $array_city, "请选择城市");
	} else {
		$array_city = array();
		$select_province = select_make('', $commoncache['provincelist'], "请选择省份");
		$select_city = select_make('', $array_city, "请选择城市");
	} 
	$tpl -> assign('selectprovince', $select_province);
	$tpl -> assign('selectcity', $select_city);
}

//推荐城市
$recomcitylist = $db -> row_select('area','isrecom=1');
$tpl -> assign('recomcity', $recomcitylist);

//所有城市列表
$list = $db->row_select('area','parentid=-1');
foreach($list as $key => $value){
	$citylist = $db -> row_select('area','parentid='.$value['id']);
	$list[$key]['citylist'] = $citylist;
}

$tpl->assign( 'arealist', $list );
$tpl -> display('default/'.$settings['templates'].'/city.html');
?>