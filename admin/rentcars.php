<?php
/**
 * 本软件版权归作者所有,在投入使用之前注意获取许可
作者：北京市普艾斯科技有限公司
 * 项目：simcms_锐车1.0高级版
电话：010-58480317
 * Q  Q：228971357
 * 网址：http://www.simcms.net
 * simcms.net保留全部权力，受相关法律和国际		  		
 * 公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
 */
header("Content-type:text/html;charset=utf-8");
if (!defined('APP_IN')) exit('Access Denied');

$data = $db -> row_select('settings', "k='version'");
$version = $data[0]['v'];
$tpl -> assign('version', $version);
// 品牌选择
if (!empty($_GET['ajax']) && isset($_GET['bid'])) {
	header('Content-Type:text/plain; charset=utf-8');
	$brandlist = "<option value='' selected>请选择子品牌</option>";
	$list = $db -> row_select('brand', "b_parent='" . $_GET['bid'] . "'");
	if ($list) {
		foreach($list as $key => $value) {
			$brandlist .= "<option value=" . $value['b_id'] . ">" . $value['b_name'] . "</option>";
		} 
	} 
	echo $brandlist;
	exit;
} 
// 删除图片ajax
if (!empty($_GET['ajax']) && isset($_GET['p_id'])) {
	$str = $_GET['p_pic'];
	$sstr = str_replace(".", "_small.", $str);
	$arr_picid = explode("/", $str);
	$arr_length = count($arr_picid);
	$picstr = explode(".", $arr_picid[$arr_length-1]);
	if (!empty($_GET['p_id'])) {
		$picpath = substr($str, 1);
		$spicpath = substr($sstr, 1);
		if (file_exists($picpath)) unlink($picpath);
		if (file_exists($spicpath)) unlink($spicpath);
		$delstr = $str;
		$arr = $db -> row_select_one('cars', "p_id=" . intval($_GET['p_id']));
		if (!empty($arr['p_pics'])) {
			$pic_list = array_flip(explode("|", $arr['p_pics']));
			unset($pic_list[$delstr]);
			$post['p_pics'] = implode("|", array_flip($pic_list));
			$rs = $db -> row_update('cars', $post, "p_id=" . intval($_GET['p_id']));
		} 
	} 
	echo $picstr[0];
	exit;
} 
// 当前模块
$m_name = '车源管理';
// 允许操作
$ac_arr = array('list' => '车源列表', 'add' => '添加车源', 'edit' => '编辑车源', 'del' => '删除车源', 'sell' => '改变买卖状态', 'show' => '显示车源', 'refresh' => '刷新车源', 'html' => '生成静态', 'bulkhtml' => '批量生成静态', 'bulkdel' => '批量删除', 'bulkrefresh' => '批量刷新', 'bulksell' => '批量改变买卖状态');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

$array_rentmodel = arr_rentmodel();

// 列表
if ($ac == 'list') {
	// 总数
	$pagecount = $db -> row_count('rentcars');
	$tpl -> assign('pagecount', $pagecount);
	// 商家
	$dealercount = $db -> row_count('rentcars', " uid in (select id from " . $db -> tb_prefix . "member where isdealer=2 and ischeck=1)");
	$tpl -> assign('dealercount', $dealercount);
	// 个人
	$personcount = $db -> row_count('rentcars', " uid in (select id from " . $db -> tb_prefix . "member where isdealer=1 )");
	$tpl -> assign('personcount', $personcount); 
	// 游客
	$visitor = $db -> row_count('rentcars', " uid =0 ");
	$tpl -> assign('visitor', $visitor);

	// 待审核
	$unaudited = $db -> row_count('rentcars', "isshow=0");
	$tpl -> assign('unaudited', $unaudited);
	$brand_search = arr_brand_with_search();
	$select_brand_search = select_make('', $brand_search, '请选择品牌');
	$tpl -> assign('selectbrandsearch', $select_brand_search);

	$where = '1=1';
	$brand_search = arr_brand_with_search();

	$select_brand_search = select_make('', $brand_search, '请选择品牌');

	$tpl -> assign('selectbrandsearch', $select_brand_search);

	$province_search = arr_province();
	$select_province_search = select_make('', $province_search, '请选择省份');
	$tpl -> assign('selectprovincesearch', $select_province_search);



	if (!empty($_COOKIE['city'])) {
		$where .= " and cid = " . $_COOKIE['city'];
	} 

	if (isset($_GET['clear']) and $_GET['clear'] == 1) {
		setMyCookie("province", '', time() - COOKIETIME);
		setMyCookie("city", '', time() - COOKIETIME);
		setMyCookie("keywords", '', time() - COOKIETIME);
		setMyCookie("brand", '', time() - COOKIETIME);
		setMyCookie("subbrand", '', time() - COOKIETIME);
		setMyCookie("subsubbrand", '', time() - COOKIETIME);
		setMyCookie("price", '', time() - COOKIETIME);
		setMyCookie("model", '', time() - COOKIETIME);
		setMyCookie("show", '', time() - COOKIETIME);
		setMyCookie("order", '', time() - COOKIETIME);
		setMyCookie("usertype", '', time() - COOKIETIME);
		setMyCookie("status", '', time() - COOKIETIME);
		setMyCookie("recom", '', time() - COOKIETIME);
	} 

	$arr_p = array('1' => '100元以下', '2' => '100-200元', '3' => '200-300元', '4' => '300-400元', '5' => '400-500元', '6' => '500元以上');
	$tpl -> assign('arr_price', $arr_p);
	$arr_c = arr_rentmodel();
	$tpl -> assign('arr_city', $arr_c);
	$arr_t = array('1' => '特荐', '2' => '推荐', '3' => '热门');
	$tpl -> assign('arr_recom', $arr_t);
	$arr_u = array('1' => '商家', '2' => '个人');
	$tpl -> assign('arr_usertype', $arr_u);

//品牌
 if (isset($_GET['b'])) { 
			if (!empty($_GET['b'])) {
				setMyCookie("brand", intval($_GET['b']), time() + COOKIETIME);
			}
			if (!empty($_GET['sb'])) {
				setMyCookie("subbrand", intval($_GET['sb']), time() + COOKIETIME);

			} 
	        if (!empty($_GET['sbsb'])) {
				setMyCookie("subsubbrand", intval($_GET['sbsb']), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['subsubbrand']) and $_COOKIE['subsubbrand'] == 0) {
				setMyCookie("brand", '', time() - COOKIETIME);
				setMyCookie("subbrand", '', time() - COOKIETIME);
				setMyCookie("subsubbrand", '', time() - COOKIETIME);
			}
		} 
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

				if (!empty($_GET['sb'])) {
					setMyCookie("subbrand", intval($_GET['sb']), time() + COOKIETIME);
				} 
			} 

			if (isset($_COOKIE['brand']) and $_COOKIE['brand'] == 0) {
				setMyCookie("brand", '', time() - COOKIETIME);

				setMyCookie("subbrand", '', time() - COOKIETIME);
			} 
		}
		//推荐位
		elseif($arr_c['0'] == "t"){
			if (isset($arr_c[1])) {
				setMyCookie("recom", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['recom']) and $_COOKIE['recom'] == 0) {
				setMyCookie("recom", '', time() - COOKIETIME);
			} 
		}
		//会员类型
		elseif($arr_c['0'] == "u"){
			if (isset($arr_c[1])) {
				setMyCookie("usertype", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['usertype']) and $_COOKIE['usertype'] == 0) {
				setMyCookie("usertype", '', time() - COOKIETIME);
			} 
		}
		//状态
		elseif($arr_c['0'] == "s"){
			if (isset($arr_c[1])) {
				setMyCookie("status", intval($arr_c[1]), time() + COOKIETIME);
			} 
			if (isset($_COOKIE['status']) and $_COOKIE['status'] == 0) {
				setMyCookie("status", '', time() - COOKIETIME);
			} 
		}
	} 

	if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and $_GET['sb'] == 0) {
		setMyCookie("subbrand", '', time() - COOKIETIME);
	} 

	// 城市
	if (!empty($_GET['c'])) {
		setMyCookie("province", intval($_GET['c']), time() + COOKIETIME);
	}
	if (!empty($_GET['cy'])) {
		setMyCookie("city", intval($_GET['cy']), time() + COOKIETIME);
	} 
	if (isset($_COOKIE['city']) and $_COOKIE['city'] == 0) {
		setMyCookie("province", '', time() - COOKIETIME);
		setMyCookie("city", '', time() - COOKIETIME);
	} 
	
	if (isset($_COOKIE['subbrand']) and isset($_GET['sb']) and  $_GET['sb'] == 0) {
	setMyCookie("subbrand", '', time() - COOKIETIME);
} 
if (isset($_COOKIE['subsubbrand']) and isset($_GET['sbsb']) and  $_GET['sbsb'] == 0) {
	setMyCookie("subsubbrand", '', time() - COOKIETIME);

} 
if (isset($_COOKIE['city']) and isset($_GET['cy']) and  $_GET['cy'] == 0) {
	setMyCookie("city", '', time() - COOKIETIME);
} 
	if (isset($_COOKIE['province']) and $_COOKIE['province'] <> 0) {
		$where .= " and aid = " . $_COOKIE['province'];
	} 

	if (isset($_COOKIE['city']) and $_COOKIE['city'] <> 0) {
		$where .= " and cid = " . $_COOKIE['city'];
	} 

	if (isset($_COOKIE['brand']) and $_COOKIE['brand'] <> 0) {
		$where .= " and p_brand = " . $_COOKIE['brand'];
	} 

	if (isset($_COOKIE['subbrand']) and $_COOKIE['subbrand'] <> 0) {
		$subbrand = $db -> row_select_one('brand', 'b_id=' . $_COOKIE['subbrand'], 'b_name');
		$tpl -> assign('subrandname', $subbrand['b_name']);
		$where .= " and p_subbrand = " . $_COOKIE['subbrand'];
	} 

if (isset($_COOKIE['subsubbrand']) and $_COOKIE['subsubbrand'] <> 0) {
		$subsubbrand = $db -> row_select_one('brand', 'b_id=' . $_COOKIE['subsubbrand'], 'b_name');
		$tpl -> assign('subsubrandname', $subsubbrand['b_name']);
		$where .= " and p_subsubbrand = " . $_COOKIE['subsubbrand'];
	} 
	
	if (isset($_COOKIE['price']) and $_COOKIE['price'] <> 0) {
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

	if (isset($_COOKIE['model']) and $_COOKIE['model'] <> 0) {
		$where .= " and p_model = " . $_COOKIE['model'];
	} 

	if (isset($_COOKIE['usertype']) and $_COOKIE['usertype'] <> 0) {
		switch($_COOKIE['usertype']) {
			case 1:
				$where .= " and uid in (select id from " . $db -> tb_prefix . "member where isdealer=2 and ischeck=1)";
				break;
			case 2:
				$where .= " and uid in (select id from " . $db -> tb_prefix . "member where isdealer=1)";
				break;
			case 3:
				$where .= " and uid=0";
				break;
		} 
	}
	
	if (isset($_COOKIE['recom']) and $_COOKIE['recom'] <> 0) {
		switch ($_COOKIE['recom']) {
			case 1:
				$where .= " and issprecom = 1 ";
				break;
			case 2:
				$where .= " and isrecom = 1 ";
				break;
			case 3:
				$where .= " and ishot = 1 ";
				break;
		} 
	}

	// 关键字
	if (isset($_GET['keywords']) and $_GET['keywords'] != "" and $_GET['keywords'] != "请输入要搜索的关键词,如:宝马") {
		setMyCookie("keywords", $_GET['keywords'], time() + COOKIETIME);
	} elseif (isset($_GET['keywords']) and $_GET['keywords'] == "") {
		setMyCookie("keywords", '', time() - COOKIETIME);
	} 

	if (!empty($_COOKIE['keywords'])) {
		$where .= " AND (`p_allname` like '%" . $_COOKIE['keywords'] . "%' or `p_keyword` like '%" . $_COOKIE['keywords'] . "%')";
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
	$Page = new Page($db -> tb_prefix . 'rentcars', $where, '*', '50', 'listtime desc');
	$list = $Page -> get_data();
	foreach($list as $key => $value) {
		$list[$key]['listtime'] = date('Y-m-d H:i:s', $value['listtime']);
		$list[$key]['p_addtime'] = date('Y-m-d H:i:s', $value['p_addtime']);
		if (!empty($value['p_model'])) $list[$key]['p_modelname'] = $array_rentmodel[$value['p_model']];
		$list[$key]['p_url'] = HTML_DIR . "rentcars/" . date('Y/m/d', $value['p_addtime']) . "/" . $value['p_id'] . ".html";
		if (!empty($value['uid'])) {
			$user = $db -> row_select_one('member', 'id=' . $value['uid'], 'username');
			$list[$key]['username'] = $user['username'];
			$list[$key]['province'] = ($commoncache['provincelist'][$value['aid']]);
			$list[$key]['city'] = ($commoncache['citylist'][$value['cid']]);
		} else {
			$list[$key]['username'] = "管理员";
		} 
		// 地区
		if (!empty($value['cid'])) {
			$area = $db -> row_select_one('area', 'id = ' . $value['cid']);
			$list[$key]['area'] = $area['name'];
		} 
	} 
	$button_basic = $Page -> button_basic();
	$button_select = $Page -> button_select();

	$tpl -> assign('button_basic', $button_basic);
	$tpl -> assign('button_select', $button_select);
	$tpl -> assign('rentcarslist', $list);

	$tpl -> display('admin/rentcars_list.html');
	exit;
} 
// 单条删除
elseif ($ac == 'del') {
	$p_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$car = $db -> row_select_one('rentcars', "p_id=$p_id");
	if (!empty($car['p_pics'])) {
		$pic_list = explode('|', $car['p_pics']);
		foreach($pic_list as $key => $value) {
			$value = substr($value,1);
			$smallpic = str_replace(".", "_small.", $value);
			if(file_exists($value)){
				unlink($value);
			}
			if(file_exists($smallpic)){
				unlink($smallpic);
			}
		} 
	} 
	$rs = $db -> row_delete('rentcars', "p_id=$p_id");
} 
// 批量删除
elseif ($ac == 'bulkdel') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	foreach($_POST['bulkid'] as $key => $value) {
		$car = $db -> row_select_one('rentcars', "p_id=" . $value);
		if (!empty($car['p_pics'])) {
			$pic_list = explode('|', $car['p_pics']);
			foreach($pic_list as $key => $value) {
				$value = substr($value,1);
				$smallpic = str_replace(".", "_small.", $value);
				if(file_exists($value)){
					unlink($value);
				}
				if(file_exists($smallpic)){
					unlink($smallpic);
				}
			} 
		} 
	} 
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_delete('rentcars', "p_id in($str_id)");
} 
// 批量刷新
elseif ($ac == 'bulkrefresh') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$listtime = time();
	$rs = $db -> row_update('rentcars', array('listtime' => $listtime), "p_id in($str_id)");
} 
// 批量买卖状态
elseif ($ac == 'bulksell') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	$str_id = return_str_id($_POST['bulkid']);
	$rs = $db -> row_update('rentcars', array('issell' => '1'), "p_id in($str_id)");
	foreach($_POST['bulkid'] as $key => $value) {
		html_rentcars($value);
	} 
} 
// 改变买卖状态
elseif ($ac == 'sell') {
	$rs = $db -> row_update('rentcars', array('issell' => '1'), "p_id=" . intval($_GET['id']));
	html_rentcars(intval($_GET['id']));
} 
// 显示车源
elseif ($ac == 'show') {
	$ptate = intval($_GET['p_state']);
	$rs = $db -> row_update('rentcars', array('isshow' => $ptate), "p_id=" . intval($_GET['id']));
	html_rentcars(intval($_GET['id']));
} 
// 刷新车源
elseif ($ac == 'refresh') {
	$listtime = time();
	$rs = $db -> row_update('rentcars', array('listtime' => $listtime), "p_id=" . intval($_GET['id']));
} 
// 添加
elseif ($ac == 'add' || $ac == 'edit') {
	// 添加或修改
	if (submitcheck('a')) {
		$arr_not_empty = array('p_model' => '车型不可为空');

		can_not_be_empty($arr_not_empty, $_POST);

		foreach (array('p_details') as $v) {
			if (!is_array($_POST[$v])) {
				$_POST[$v] = htmlspecialchars($_POST[$v]);
			} 
		} 
		$post = post('p_title', 'p_brand', 'p_subbrand', 'p_subsubbrand', 'p_name', 'p_allname', 'dayprice', 'monthprice', 'p_details', 'p_model', 'p_hits', 'p_addtime', 'listtime', 'isrent', 'isshow', 'isrecom', 'ishot', 'p_username', 'p_tel');

		if ($settings['version'] == 3) {
			$post['aid'] = intval($_POST['aid']);
			$post['cid'] = intval($_POST['cid']);
		} else {
			$post['aid'] = 0;
			$post['cid'] = 0;
		} 

		$post['p_brand'] = intval($post['p_brand']);
		$post['p_subbrand'] = intval($post['p_subbrand']);
		$post['p_subsubbrand'] = intval($post['p_subsubbrand']);
		$post['p_model'] = intval($post['p_model']);

		$post['p_allname'] = "";
		if(!empty($post['p_subbrand'])){
			$bname = $commoncache['brandlist'][$post['p_brand']];
			$subbname = arr_brandname($post['p_subbrand']);
			$compareword = strstr($subbname,$bname);
			if(!empty($compareword)){
				$post['p_allname'] .= arr_brandname($post['p_subbrand']);
			}
			else{
				$post['p_allname'] .= $bname ." ".arr_brandname($post['p_subbrand']);
			}
		}
		if(!empty($post['p_subsubbrand'])){
			$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
		}
		if (empty($post['isrecom'])) $post['isrecom'] = 0;
		if (empty($post['ishot'])) $post['ishot'] = 0;
 
		if (!empty($post['p_username'])) $post['p_username'] = trim($_POST['p_username']);
		if (!empty($post['p_tel'])) $post['p_tel'] = trim($_POST['p_tel']);

		if (isset($_POST['p_pics'])) {
			$post['p_pics'] = implode("|", $_POST['p_pics']);
			if (isset($_POST['p_mainpic'])) {
				$post['p_mainpic'] = $_POST['p_mainpic'];
			} else {
				$post['p_mainpic'] = $_POST['p_pics'][0];
			} 
		} else {
			$post['p_pics'] = "";
		} 

		if ($ac == 'add') {
			$post['p_hits'] = 0;
			$post['p_addtime'] = time();
			$post['listtime'] = time();
			$post['isshow'] = 1;
			$post['issell'] = 0;
			$rs = $db -> row_insert('rentcars', $post);
			$insertid = $db -> insert_id();
			html_rentcars($insertid);
		} else {
			$rs = $db -> row_update('rentcars', $post, "p_id=" . intval($_POST['id']));
			html_rentcars(intval($_POST['id']));
		} 
	} 
	// 转向添加或修改页面
	else {
		if (empty($_GET['id'])) {
			$data = array('p_title' => '', 'p_brand' => '', 'p_subbrand' => '', 'p_name' => '', 'p_allname' => '', 'p_keyword' => '', 'dayprice' => '', 'monthprice' => '', 'p_pics' => '', 'p_details' => '', 'p_model' => '', 'p_hits' => '', 'p_addtime' => '', 'listtime' => '', 'isrent' => '', 'isshow' => '');
		    if($settings['version']==3){
			$select_province = select_make('', $commoncache['provincelist'], "请选择省份");
			$select_city = select_make('', $commoncache['citylist'], "请选择城市"); 
		}
		
		} else {
			$data = $db -> row_select_one('rentcars', "p_id=" . intval($_GET['id']));
			if($settings['version']==3){
			$select_province = select_make($data['aid'], $commoncache['provincelist'], "请选择省份");
			$select_city = select_make($data['cid'], $commoncache['citylist'], "请选择城市");
		}
			if (!empty($data['p_pics'])) {
				$pic_list = explode('|', $data['p_pics']);
				$piclist = array();
				foreach($pic_list as $key => $value) {
					$piclist[$key]['pic'] = $value;
					$arr_picid = explode("/", $value);
					$arr_length = count($arr_picid);
					$arr_picids = explode(".", $arr_picid[$arr_length-1]);
					$piclist[$key]['picid'] = $arr_picids[0];
				} 
				$tpl -> assign('pic_list', $piclist);
			} 
		} 
		
		if($settings['version']==3){
             $tpl -> assign('selectprovince', $select_province);
			 $tpl -> assign('selectcity', $select_city); 
		}
		$pstate_get = isset($_GET['pstate']) ? $_GET['pstate'] : "";
		$page_get = isset($_GET['page']) ? $_GET['page'] : "";
		$select_brand = select_make($data['p_brand'], $commoncache['markbrandlist'], '请选择品牌');
		$select_subbrand = select_subbrand(intval($data['p_subbrand']));
		$select_subsubbrand = select_subbrand(intval($data['p_subsubbrand']));
		$select_rentmodel = select_make($data['p_model'], $array_rentmodel, '请选择车型');

		$tpl -> assign('rentcars', $data);
		$tpl -> assign('select_brand', $select_brand);
		$tpl -> assign('select_subbrand', $select_subbrand);
		$tpl -> assign('select_subsubbrand', $select_subsubbrand);
		$tpl -> assign('select_rentmodel', $select_rentmodel);
		$tpl -> assign('sessionid', session_id());
		$tpl -> display('admin/add_rentcars.html');
		exit;
	} 
} 
// 生成静态
elseif ($ac == 'html') {
	$p_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
	$rs = html_rentcars($p_id);
} 
// 批量生成静态
elseif ($ac == 'bulkhtml') {
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
	foreach($_POST['bulkid'] as $value) {
		$rs = html_rentcars($value);
	} 
} 
// 默认操作
else {
	showmsg('非法操作', -1);
} 
showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE . "?m=$m&a=list");

?>