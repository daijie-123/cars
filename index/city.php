<?php
if (!defined('APP_IN')) exit('Access Denied');

if(isset($_GET['cid'])){
	setMyCookie("city", intval($_GET['cid']), time()+COOKIETIME );
	header("location:".WEB_PATH."?m=search&clear=1");
	exit;
}
//设置城市cookies
if(empty($_COOKIE['city']) or $_COOKIE['city']=="undefined"){
	$ip = getIp();
	$cityname = get_cityname($ip);
	if(empty($cityname) or $cityname=="I"){
		$cityname="总站";
		$cityid = 0;
	}else{
		$citydata = $db->row_select_one('area',"name='".$cityname."'",'id');
		if(!empty($citydata['id'])){
			$cityid = $citydata['id'];
		}
		else{
			$cityid = 0;
		}
	}
	setMyCookie("city", $cityid, time()+COOKIETIME);
}
else{
	$citydata = $db->row_select_one('area',"id='".$_COOKIE['city']."'",'name');
	$cityname = $citydata['name'];
}

echo "document.write(\"<span class='fb'>".$cityname."</span> &nbsp;<span class='gray01'>[切换城市]</span>\");";
echo "$(function() {
		var title = $(document).attr('title');
		$(document).attr('title', '".$cityname."_'+title);
		})";
?>