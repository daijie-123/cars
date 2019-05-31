<?php
if(!defined('APP_IN')) exit('Access Denied');

//登录
if (!empty($_GET['ajax']) && isset($_GET['login']))
{	header('Content-Type:text/plain; charset=utf-8');
	if(!empty($_SESSION['USER_ID']) || !empty($_SESSION['USER_NAME'])){
		$loginstr=$_SESSION['USER_NAME']."<a href='".WEB_PATH."/?m=user' target='_parent'><font color=red>会员中心</font></a> <a href='".WEB_PATH."/?m=user&a=logout' target='_parent'>[退出]</a><span class='line'>|</span><a href='http://www.qzqcw.com/' target='_blank' class='red b'>泉州汽车网</a><span class='line'>|</span><a href='http://www.qzfcw.cn/' target='_blank'>泉州房产网</a><span class='line'>|</span><a href='http://www.xqzrc.com/' target='_blank'>泉州人才网</a>";
	}
	else{
		$loginstr = "<a href='".WEB_PATH."/?m=login' target='_blank'><font color=red>登录</font></a><span class='line'>|</span><a href='".WEB_PATH."/?m=register' target='_blank'>注册</a><span class='line'>|</span><a href='http://www.qzqcw.com/' target='_blank' class='red b'>泉州汽车网</a><span class='line'>|</span><a href='http://www.qzfcw.cn/' target='_blank'>泉州房产网</a><span class='line'>|</span><a href='http://www.xqzrc.com/' target='_blank'>泉州人才网</a>";
	}
	echo $loginstr;
	exit;
}

//品牌选择
if (!empty($_GET['ajax']) && !empty($_GET['bid']))
{	header('Content-Type:text/plain; charset=utf-8');
	$arr_b = explode("_", trim($_GET['bid'])); 
	$brandlist = "<option value='' selected>请选择车系</option>";
	$list = $db->row_select('brand',"b_parent='".$arr_b[1]."'");
	if($list){
		foreach($list as $key => $value){
			$brandlist .= "<optgroup label=".$value['b_name']." style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
			$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
			foreach($sublist as $subkey => $subvalue){
				$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
			}
		}
	}
	echo $brandlist;
	exit;
}

//二级品牌选择
if (!empty($_GET['ajax']) && !empty($_GET['brandid']))
{	header('Content-Type:text/plain; charset=utf-8');
	$brandlist = "<option value='' selected>请选择车系</option>";
	$list = $db->row_select('brand',"b_parent='".$_GET['brandid']."'");
	if($list){
		foreach($list as $key => $value){
			$brandlist .= "<optgroup label=".$value['b_name']." style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
			$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
			foreach($sublist as $subkey => $subvalue){
				$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
			}
		}
	}
	echo $brandlist;
	exit;
}


//三级品牌选择
if (!empty($_GET['ajax']) && !empty($_GET['subbrandid']))

{	header('Content-Type:text/plain; charset=utf-8');
	$brandlist = "<option value='' selected>请选择款式</option>";
	$list = $db->row_select('brand',"b_parent='".$_GET['subbrandid']."'");
	if($list){
		foreach($list as $key => $value){
			$brandlist .= "<optgroup label='".$value['b_name']."' style='font-style: normal; background: none repeat scroll 0% 0% rgb(239, 239, 239); text-align: center;'></optgroup>";
			$sublist = $db->row_select('brand',"b_parent='".$value['b_id']."'");
			foreach($sublist as $subkey => $subvalue){
				$brandlist .= "<option value=".$subvalue['b_id'].">".$subvalue['b_name']."</option>";
			}
		}
	}
	echo $brandlist;
	exit;
}



//四级品牌选择
if (!empty($_GET['ajax']) && !empty($_GET['subsubbrandid']))

{	header('Content-Type:text/plain; charset=utf-8');
	$brandlist = "<option value='' selected>请选择款式</option>";
	$list = $db->row_select('brand',"b_parent='".$_GET['subsubbrandid']."'");
	if($list){
		foreach($list as $key => $value){
				$brandlist .= "<option value=".$value['b_id'].">".$value['b_name']."</option>";
		}
	}
	echo $brandlist;
	exit;
}


//城市选择
if (!empty($_GET['ajax']) && isset($_GET['cityid']))
{	header('Content-Type:text/plain; charset=utf-8');
	$provincelist = "<option value='' selected>请选择城市</option>";
	$list = $db->row_select('area',"parentid='".$_GET['cityid']."'");
	if($list){
		foreach($list as $key => $value){
			$provincelist .= "<option value=".$value['id'].">".$value['name']."</option>";		
		}
	}
	echo $provincelist;
	exit;

}


//城市选择(搜索页面)
if (!empty($_GET['ajax']) && isset($_GET['searchcityid']))
{	header('Content-Type:text/plain; charset=utf-8');
	$citylist = "";
	$list = $db->row_select('area',"parentid='".$_GET['searchcityid']."'");
	if($list){
		foreach($list as $key => $value){
			$citylist .= "<option value='c_".$value['id']."'>".$value['name']."</option>";
		}
	}
	echo $citylist;
	exit;
}
//车源数量
if (!empty($_GET['ajax']) && isset($_GET['carcount']))
{	header('Content-Type:text/plain; charset=utf-8');
	$todaytime = strtotime(date('Y')."-".date('m')."-".date('d')." 00:00:00");
	$count1 = $db->row_count('cars','issell=0');
	$count2 = $db->row_count('cars','issell=0 and p_addtime>'.$todaytime);
	$count3 = $db->row_count('cars','issell=1');
	$carcount = "<p>目前有 <span class='counts'>".$count1."</span> 辆二手车供您选择</p><p>累计出售<span class='counts'><a href='/?m=issell&amp;c=s_2'> ".$count3."</a> </span>辆 今天新增 <span class='counts'>".$count2."</span> 辆</p>";
	echo $carcount;
	exit;
}
//热门车源排行
if(!empty($_GET['ajax']) && isset($_GET['cartype']) && $_GET['cartype']=="hot" ){
	header('Content-Type:text/plain; charset=utf-8');
	$where = "ishot=1 and p_mainpic!='' and isshow=1";
	if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
		$_COOKIE['city'] = 0;
	}
	$list = get_carlist($_COOKIE['city'],$where,'10','listtime desc');
	$str = "";
	foreach($list as $key => $value) {
		$str .= "<div class='hotcarlist'><a href=".$value['p_url']." target='_blank'><img src=".$value['p_mainpic']."></a>
			<p class='mt5'><a href=".$value['p_url']." target='_blank' >".$value['p_shortname']."</a></p>
			<p class='gray01'>".$value['p_year']."年".$value['p_month']."月上牌<span class='orange01 fb fr'>".$value['p_price']."</span></p></div>";
	}
	echo $str;
	exit;
}


		
//最新车源排行
if(!empty($_GET['ajax']) && isset($_GET['cartype']) && $_GET['cartype']=="indexnew" ){
	header('Content-Type:text/plain; charset=utf-8');
	$where = "issell=0 and p_mainpic!='' and isshow=1";
	if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
		$_COOKIE['city'] = 0;
	}
	$list = get_carlist($_COOKIE['city'],$where,'10','listtime desc');
	$str = "";
	foreach($list as $key => $value) {
		$str .= "<li class='bbgspe'> <a href=".$value['p_url']." target='_blank'><img src=".$value['p_mainpic']." alt=".$value['p_allname']."></a>
        <p class='carname mt5'><a href=".$value['p_url']." target='_blank' >".$value['p_allname']."</a></p>
        <p><span class='price'>".$value['p_price']."</span>万</p>
        <p class='gray01 mt5 f12'><span class='fr'>里程：".$value['p_kilometre']."万公里</span>上牌：".$value['p_year']."年".$value['p_month']."月</p>
        </li>";
			

	}
	echo $str;
	exit;
}

//首页热门车源排行
if(!empty($_GET['ajax']) && isset($_GET['cartype']) && $_GET['cartype']=="indexhot" ){
	header('Content-Type:text/plain; charset=utf-8');
	$where = "ishot=1 and p_mainpic!='' and isshow=1";
	if(!empty($_COOKIE['city'])){
		$where .= " and cid = ".$_COOKIE['city'];
	}
	if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
		$_COOKIE['city'] = 0;
	}
	$list = get_carlist($_COOKIE['city'],$where,'10','listtime desc');
	$str = "";
	foreach($list as $key => $value) {
		if($key<3){
			$class = "class='num01'";
		}
		else{
			$class = "class='num02'";
		}
		$str .= "<p class='clearfix'><span class='orange01 fb fr'>".$value['p_price']."</span><span ".$class.">".($key+1)."</span><a href='".$value['p_url']."' target='_blank' class='fl pl10'>".$value['p_shortname']."</a></p>";
	}
	echo $str;
	exit;
}

//预约看车
if(!empty($_GET['ajax']) && isset($_GET['yuyue']) && $_GET['yuyue']==1 ){
	header('Content-Type:text/plain; charset=utf-8');
	if(!empty($_POST['name']) and !empty($_POST['mobilephone']) and !empty($_POST['ordertime']) and !empty($_POST['orderinfo'])){
		$rs = $db->row_insert('subscribe',array('pid'=>intval($_POST['pid']),'uid'=>intval($_POST['uid']),'name'=>trim($_POST['name']),'mobilephone'=>trim($_POST['mobilephone']),'ordertime'=>strtotime(trim($_POST['ordertime'])),'orderinfo'=>trim($_POST['orderinfo']),'addtime'=>time()));
		if($rs){
			$status = 1;
		}
		else{
			$status = 0;
		}
	}
	else{
		$status = 0;
	}
	echo $status;
	exit;
}

//询价
if(!empty($_GET['ajax']) && isset($_GET['xunjia']) && $_GET['xunjia']==1 ){
	header('Content-Type:text/plain; charset=utf-8');
	if(!empty($_POST['name']) and !empty($_POST['mobilephone'])){
		$rs = $db->row_insert('inquiry',array('pid'=>intval($_POST['pid']),'uid'=>intval($_POST['uid']),'name'=>trim($_POST['name']),'mobilephone'=>trim($_POST['mobilephone']),'addtime'=>time()));
		if($rs){
			$status = 1;
		}
		else{
			$status = 0;
		}
	}
	else{
		$status = 0;
	}
	echo $status;
	exit;
}



?>