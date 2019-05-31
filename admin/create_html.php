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
header("Content-type: text/html; charset=utf-8");
if (!defined('APP_IN')) exit('Access Denied');
// 当前模块
$m_name = '生成静态';
// 允许操作
$ac_arr = array('index' => '更新首页HTML', 'cars' => '更新车源页HTML', 'rentcars' => '更新租车HTML',  'news' => '更新新闻页HTML', 'page' => '更新单页HTML', 'qiugou' => '更新求购页html');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

$array_page = arr_page();

// 更新首页
if ($ac == 'index') {
	html_index();
	htmlshowmsg('更新首页成功');
	exit;
} 
// 更新车源
elseif ($ac == 'cars') {
	$catids = isset($_REQUEST['catids']) ? $_REQUEST['catids'] : 0;
	if($catids!=0){
		$catidstr = implode(',',$catids);
		$where = "p_brand in (".$catidstr.")";
	}
	else{
		$where = "1=1";
	}
	if (isset($_GET['op']) and !empty($_GET['op'])){
		if($_GET['op']==1){
			$list = $db -> row_select('cars', $where, 'p_id', 0, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新车源页完成！');
				exit;
			}
			$carslist = $db -> row_select('cars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_cars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条车源成功', ADMIN_PAGE.'?m=create_html&a=cars&op=1&carskey='.$carskey.'&catids='.$catids);
		}
		elseif($_GET['op']==2){
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('cars', $where, 'p_id', $carsnum, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新车源页完成！');
				exit;
			}
			$carslist = $db -> row_select('cars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_cars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条车源成功', ADMIN_PAGE.'?m=create_html&a=cars&op=2&carskey='.$carskey.'&carsnum='.$carsnum.'&catids='.$catids);
		}
		elseif($_GET['op']==3){
			$startdate = isset($_REQUEST['startdate']) ? $_REQUEST['startdate'] : '0-0-0';
			$enddate = isset($_REQUEST['enddate']) ? $_REQUEST['enddate'] : '0-0-0';
			$starttimearr = explode('-',$startdate);
			$starttime = mktime(0,0,0,$starttimearr[1],$starttimearr[2],$starttimearr[0]);
			$endtimearr = explode('-',$enddate);
			$endtime = mktime(0,0,0,$endtimearr[1],$endtimearr[2],$endtimearr[0]);	
			if(!empty($starttime) and !empty($endtime)){
				$where .= " and p_addtime > ".$starttime." and p_addtime < ".$endtime;
			}
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('cars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新车源页完成！');
				exit;
			}
			$carslist = $db -> row_select('cars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_cars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条车源成功', ADMIN_PAGE.'?m=create_html&a=cars&op=3&carskey='.$carskey.'&startdate='.$startdate.'&enddate='.$enddate.'&catids='.$catids);
		}
		elseif($_GET['op']==4){
			$startid = isset($_REQUEST['startid']) ? intval($_REQUEST['startid']) : 0;
			$endid = isset($_REQUEST['endid']) ? intval($_REQUEST['endid']) : 0;
			$where .= " and p_id > ".$startid." and p_id < ".$endid;
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('cars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新车源页完成！');
				exit;
			}
			$carslist = $db -> row_select('cars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_cars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条车源成功', ADMIN_PAGE.'?m=create_html&a=cars&op=4&carskey='.$carskey.'&startid='.$startid.'&endid='.$endid.'&catids='.$catids);
		}
		exit;
	}
	else{
		$select_category = arr_brand(-1);
		$starttimevalue = date("Y-m-d",time()-3600*24*5);
		$endtimevalue = date("Y-m-d",time());
		$tpl -> assign('starttimevalue', $starttimevalue);
		$tpl -> assign('endtimevalue', $endtimevalue);
		$tpl -> assign('selectcategory', $select_category);
		$tpl -> display("admin/cars_html.html");
	}
} 
// 更新租车
elseif ($ac == 'rentcars') {
	$catids = isset($_REQUEST['catids']) ? $_REQUEST['catids'] : 0;
	if($catids!=0){
		$catidstr = implode(',',$catids);
		$where = "p_brand in (".$catidstr.")";
	}
	else{
		$where = "1=1";
	}
	if (isset($_GET['op']) and !empty($_GET['op'])){
		if($_GET['op']==1){
			$list = $db -> row_select('rentcars', $where, 'p_id', 0, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新租车页完成！');
				exit;
			}
			$carslist = $db -> row_select('rentcars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_rentcars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=rentcars&op=1&carskey='.$carskey.'&catids='.$catids);
		}
		elseif($_GET['op']==2){
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('rentcars', $where, 'p_id', $carsnum, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新租车页完成！');
				exit;
			}
			$carslist = $db -> row_select('rentcars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_rentcars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=rentcars&op=2&carskey='.$carskey.'&carsnum='.$carsnum.'&catids='.$catids);
		}
		elseif($_GET['op']==3){
			$startdate = isset($_REQUEST['startdate']) ? $_REQUEST['startdate'] : '0-0-0';
			$enddate = isset($_REQUEST['enddate']) ? $_REQUEST['enddate'] : '0-0-0';
			$starttimearr = explode('-',$startdate);
			$starttime = mktime(0,0,0,$starttimearr[1],$starttimearr[2],$starttimearr[0]);
			$endtimearr = explode('-',$enddate);
			$endtime = mktime(0,0,0,$endtimearr[1],$endtimearr[2],$endtimearr[0]);	
			if(!empty($starttime) and !empty($endtime)){
				$where .= " and p_addtime > ".$starttime." and p_addtime < ".$endtime;
			}
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('rentcars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新租车页完成！');
				exit;
			}
			$carslist = $db -> row_select('rentcars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_rentcars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=rentcars&op=3&carskey='.$carskey.'&startdate='.$startdate.'&enddate='.$enddate.'&catids='.$catids);
		}
		elseif($_GET['op']==4){
			$startid = isset($_REQUEST['startid']) ? intval($_REQUEST['startid']) : 0;
			$endid = isset($_REQUEST['endid']) ? intval($_REQUEST['endid']) : 0;
			$where .= " and p_id > ".$startid." and p_id < ".$endid;
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('rentcars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新租车页完成！');
				exit;
			}
			$carslist = $db -> row_select('rentcars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_rentcars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=rentcars&op=4&carskey='.$carskey.'&startid='.$startid.'&endid='.$endid.'&catids='.$catids);
		}
		exit;
	}
	else{
		$select_category = arr_brand(-1);
		$starttimevalue = date("Y-m-d",time()-3600*24*5);
		$endtimevalue = date("Y-m-d",time());
		$tpl -> assign('starttimevalue', $starttimevalue);
		$tpl -> assign('endtimevalue', $endtimevalue);
		$tpl -> assign('selectcategory', $select_category);
		$tpl -> display("admin/rentcars_html.html");
	}
} 
// 更新求购
elseif ($ac == 'qiugou') {
	$catids = isset($_REQUEST['catids']) ? $_REQUEST['catids'] : 0;
	if($catids!=0){
		$catidstr = implode(',',$catids);
		$where = "p_brand in (".$catidstr.")";
	}
	else{
		$where = "1=1";
	}
	if (isset($_GET['op']) and !empty($_GET['op'])){
		if($_GET['op']==1){
			$list = $db -> row_select('buycars', $where, 'p_id', 0, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新求购页完成！');
				exit;
			}
			$carslist = $db -> row_select('buycars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_qiugoucars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=qiugou&op=1&carskey='.$carskey.'&catids='.$catids);
		}
		elseif($_GET['op']==2){
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('buycars', $where, 'p_id', $carsnum, 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新求购页完成！');
				exit;
			}
			$carslist = $db -> row_select('buycars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_qiugoucars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=qiugou&op=2&carskey='.$carskey.'&carsnum='.$carsnum.'&catids='.$catids);
		}
		elseif($_GET['op']==3){
			$startdate = isset($_REQUEST['startdate']) ? $_REQUEST['startdate'] : '0-0-0';
			$enddate = isset($_REQUEST['enddate']) ? $_REQUEST['enddate'] : '0-0-0';
			$starttimearr = explode('-',$startdate);
			$starttime = mktime(0,0,0,$starttimearr[1],$starttimearr[2],$starttimearr[0]);
			$endtimearr = explode('-',$enddate);
			$endtime = mktime(0,0,0,$endtimearr[1],$endtimearr[2],$endtimearr[0]);	
			if(!empty($starttime) and !empty($endtime)){
				$where .= " and p_addtime > ".$starttime." and p_addtime < ".$endtime;
			}
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('buycars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新求购页完成！');
				exit;
			}
			$carslist = $db -> row_select('buycars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_qiugoucars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=qiugou&op=3&carskey='.$carskey.'&startdate='.$startdate.'&enddate='.$enddate.'&catids='.$catids);
		}
		elseif($_GET['op']==4){
			$startid = isset($_REQUEST['startid']) ? intval($_REQUEST['startid']) : 0;
			$endid = isset($_REQUEST['endid']) ? intval($_REQUEST['endid']) : 0;
			$where .= " and p_id > ".$startid." and p_id < ".$endid;
			$carsnum = isset($_REQUEST['carsnum']) ? intval($_REQUEST['carsnum']) : 20;
			$list = $db -> row_select('buycars', $where, 'p_id', '', 'p_id');
			$carscounts = count($list);
			$carskey = isset($_GET['carskey']) ? intval($_GET['carskey']) : 0;
			if($carskey>=$carscounts){
				htmlshowmsg('更新求购页完成！');
				exit;
			}
			$carslist = $db -> row_select('buycars', $where, 'p_id', $carskey.',20', 'p_id');
			foreach($carslist as $key => $value){
				html_qiugoucars($value['p_id']);
			}
			$carskey = $carskey + 20;
			showmsg2('更新'.($carskey-20)."到".$carskey.'条信息成功', ADMIN_PAGE.'?m=create_html&a=qiugou&op=4&carskey='.$carskey.'&startid='.$startid.'&endid='.$endid.'&catids='.$catids);
		}
		exit;
	}
	else{
		$select_category = arr_brand(-1);
		$starttimevalue = date("Y-m-d",time()-3600*24*5);
		$endtimevalue = date("Y-m-d",time());
		$tpl -> assign('starttimevalue', $starttimevalue);
		$tpl -> assign('endtimevalue', $endtimevalue);
		$tpl -> assign('selectcategory', $select_category);
		$tpl -> display("admin/qiugou_html.html");
	}
} 
// 更新新闻
elseif ($ac == 'news') {
	$catids = isset($_REQUEST['catids']) ? $_REQUEST['catids'] : 0;
	if($catids!=0){
		$catidstr = implode(',',$catids);
		$where = "catid in (".$catidstr.")";
	}
	else{
		$where = "1=1";
	}
	if (isset($_GET['op']) and !empty($_GET['op'])){
		if($_GET['op']==1){
			$list = $db -> row_select('news', $where, 'n_id', 0, 'n_id');
			$newscounts = count($list);
			$newskey = isset($_GET['newskey']) ? intval($_GET['newskey']) : 0;
			if($newskey>=$newscounts){
				htmlshowmsg('更新新闻页完成！');
				exit;
			}
			$newslist = $db -> row_select('news', $where, 'n_id', $newskey.',20', 'n_id');
			foreach($newslist as $key => $value){
				html_news($value['n_id']);
			}
			$newskey = $newskey + 20;
			showmsg2('更新'.($newskey-20)."到".$newskey.'条新闻成功', ADMIN_PAGE.'?m=create_html&a=news&op=1&newskey='.$newskey.'&catids='.$catids);
		}
		elseif($_GET['op']==2){
			$newsnum = isset($_REQUEST['newsnum']) ? intval($_REQUEST['newsnum']) : 20;
			$list = $db -> row_select('news', $where, 'n_id', $newsnum, 'n_id');
			$newscounts = count($list);
			$newskey = isset($_GET['newskey']) ? intval($_GET['newskey']) : 0;
			if($newskey>=$newscounts){
				htmlshowmsg('更新新闻页完成！');
				exit;
			}
			$newslist = $db -> row_select('news', $where, 'n_id', $newskey.',20', 'n_id');
			foreach($newslist as $key => $value){
				html_news($value['n_id']);
			}
			$newskey = $newskey + 20;
			showmsg2('更新'.($newskey-20)."到".$newskey.'条新闻成功', ADMIN_PAGE.'?m=create_html&a=news&op=2&newskey='.$newskey.'&newsnum='.$newsnum.'&catids='.$catids);
		}
		elseif($_GET['op']==3){
			$startdate = isset($_REQUEST['startdate']) ? $_REQUEST['startdate'] : '0-0-0';
			$enddate = isset($_REQUEST['enddate']) ? $_REQUEST['enddate'] : '0-0-0';
			$starttimearr = explode('-',$startdate);
			$starttime = mktime(0,0,0,$starttimearr[1],$starttimearr[2],$starttimearr[0]);
			$endtimearr = explode('-',$enddate);
			$endtime = mktime(0,0,0,$endtimearr[1],$endtimearr[2],$endtimearr[0]);
			if(!empty($starttime) and !empty($endtime)){
				$where .= " and n_addtime > ".$starttime." and n_addtime < ".$endtime;
			}
			$newsnum = isset($_REQUEST['newsnum']) ? intval($_REQUEST['newsnum']) : 20;
			$list = $db -> row_select('news', $where, 'n_id', '', 'n_id');
			$newscounts = count($list);
			$newskey = isset($_GET['newskey']) ? intval($_GET['newskey']) : 0;
			if($newskey>=$newscounts){
				htmlshowmsg('更新新闻页完成！');
				exit;
			}
			$newslist = $db -> row_select('news', $where, 'n_id', $newskey.',20', 'n_id');
			foreach($newslist as $key => $value){
				html_news($value['n_id']);
			}
			$newskey = $newskey + 20;
			showmsg2('更新'.($newskey-20)."到".$newskey.'条新闻成功', ADMIN_PAGE.'?m=create_html&a=news&op=3&newskey='.$newskey.'&startdate='.$startdate.'&enddate='.$enddate.'&catids='.$catids);
		}
		elseif($_GET['op']==4){
			$startid = isset($_REQUEST['startid']) ? intval($_REQUEST['startid']) : 0;
			$endid = isset($_REQUEST['endid']) ? intval($_REQUEST['endid']) : 0;
			$where .= " and n_id > ".$startid." and n_id < ".$endid;
			$newsnum = isset($_REQUEST['newsnum']) ? intval($_REQUEST['newsnum']) : 20;
			$list = $db -> row_select('news', $where, 'n_id', '', 'n_id');
			$newscounts = count($list);
			$newskey = isset($_GET['newskey']) ? intval($_GET['newskey']) : 0;
			if($newskey>=$newscounts){
				htmlshowmsg('更新新闻页完成！');
				exit;
			}
			$newslist = $db -> row_select('news', $where, 'n_id', $newskey.',20', 'n_id');
			foreach($newslist as $key => $value){
				html_news($value['n_id']);
			}
			$newskey = $newskey + 20;
			showmsg2('更新'.($newskey-20)."到".$newskey.'条新闻成功', ADMIN_PAGE.'?m=create_html&a=news&op=4&newskey='.$newskey.'&startid='.$startid.'&endid='.$endid.'&catids='.$catids);
		}
		exit;
	}
	else{
		$select_category = select_category('news_category', '', 'style="height:200px;" multiple="multiple" id="catids" name="catids[]"', '', '');
		$starttimevalue = date("Y-m-d",time()-3600*24*5);
		$endtimevalue = date("Y-m-d",time());
		$tpl -> assign('starttimevalue', $starttimevalue);
		$tpl -> assign('endtimevalue', $endtimevalue);
		$tpl -> assign('selectcategory', $select_category);
		$tpl -> display("admin/news_html.html");
	}
} 
// 更新单页
elseif ($ac == 'page') {
	$list = $db -> row_select('page', '1=1', '*', 0, 'orderid');
	foreach($list as $key => $value) {
		html_page($value['p_id']);
	} 
	htmlshowmsg('更新单页成功');
	exit;
} 
?>