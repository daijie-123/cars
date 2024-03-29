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

//当前模块
$mod_name = '用户管理';
//允许操作
$ac_arr = array('list'=>'用户列表','add'=>'添加用户','edit'=>'编辑用户','del'=>'删除用户','bulkdel'=>'批量删除','mail'=>'发送邮件');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $mod_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//验证用户名
if (!empty($_POST['param']) and $_POST['name']=="username")
{
	$data = $db->row_count('member',"username='".$_POST['param']."' and id<>".intval($_GET['id']));
    if($data==0){
		echo '{"info":"用户名验证成功！","status":"y"}';
	}
	else{
		echo '{"info":"用户名已存在！","status":"n"}';
	}
	exit;
}

//验证邮箱地址
if (!empty($_POST['param']) and $_POST['name']=="email")
{
	$data = $db->row_count('member',"email='".$_POST['param']."' and id<>".intval($_GET['id']));
    if($data==0){
		echo '{"info":"邮箱验证成功！","status":"y"}';
	}
	else{
		echo '{"info":"邮箱地址已存在！","status":"n"}';
	}
	exit;
}

//验证手机号
if (!empty($_POST['param']) and $_POST['name']=="mobilephone")
{
	$data = $db->row_count('member',"mobilephone='".$_POST['param']."' and id<>".intval($_GET['id']));
    if($data==0){
		echo '{"info":"手机号验证成功！","status":"y"}';
	}
	else{
		echo '{"info":"手机号已存在！","status":"n"}';
	}
	exit;
}

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g', $page_g );

$array_isdealer = array('1' => '个人','2' => '商家');

$array_dealer_category = arr_dealer_category();

//列表
if ($ac == 'list')
{
	$where = 'isdealer=1';
    //搜索条件
    if (!empty($_GET['keywords']))
    {
        $keywords = $_GET['keywords'];
        $where .= " AND (username LIKE '%{$keywords}%' or company LIKE '%{$keywords}%')";
    }

	$isdealer = isset($_GET['isdealer'])? intval($_GET['isdealer']):'';
	$select_isdealer = select_make($isdealer, $array_isdealer, '会员类型');

	if($isdealer==2){
		 $where .= " and isdealer=2";
	}elseif($isdealer==1){
		 $where .= " and isdealer=1";
	}

    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'member',$where, '*', '50', 'id desc');
    $list = $Page->get_data();
	$page = $Page -> page;
	foreach($list as $key => $value){
		$list[$key]['regtime'] = date('Y-m-d H:i:s',$value['regtime']);
		if($settings['version']==3){
			if($value['aid']!=0){
				$list[$key]['province']=$commoncache['provincelist'][$value['aid']];
			}
			if($value['cid']!=0){
				$list[$key]['city']=$commoncache['citylist'][$value['cid']];
			}
		}
	}
	$tpl -> assign('select_isdealer', $select_isdealer);
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'userlist', $list );
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/member_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('member',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('member',"id in($str_id)");
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        if ($ac == 'add')
        {
			$arr_not_empty = array('username'=>'用户名不可为空','password'=>'密码不能为空');
			can_not_be_empty($arr_not_empty,$_POST);
			if($_POST['isdealer']==1){
				$post = post('username','mobilephone','email','nicname','isdealer');
			}
			else{
				$post = post('username','mobilephone','email','nicname','company','address','tel','logo','shoptype','isdealer','isrecom','ishot','ischeck','checknotice','shopdetail', 'lat', 'lon', 'shop_score');
			}
			if ($settings['version'] == 3) {
				$post['aid'] = intval($_POST['aid']);
				$post['cid'] = intval($_POST['cid']);
			} else {
				$post['aid'] = 0;
				$post['cid'] = 0;
			}
			$post['isdealer'] = intval($post['isdealer']);
			$post['shoptype'] = intval($post['shoptype']);
			if(isset($_POST['isrecom'])){
				$post['isrecom'] = 1;
			}
			else{
				$post['isrecom'] = 0;
			}

			if(isset($_POST['ishot'])){
				$post['ishot'] = 1;
			}
			else{
				$post['ishot'] = 0;
			}

			$post['regtime'] = time();
            $rs = $db->row_insert('member',$post);
        }
        else
        {
			$arr_not_empty = array('username'=>'用户名不可为空');
			can_not_be_empty($arr_not_empty,$_POST);
			if($_POST['isdealer']==1){
				$post = post('username','mobilephone','email','nicname','isdealer');
			}
			else{
				$post = post('username','mobilephone','email','nicname','company','address','tel','logo','shoptype','isdealer','isrecom','ishot','ischeck','checknotice','shopdetail', 'lat', 'lon', 'shop_score');
			}

			if($settings['version'] == 3) {
				$post['aid'] = intval($_POST['aid']);
				$post['cid'] = intval($_POST['cid']);
			} else {
				$post['aid'] = 0;
				$post['cid'] = 0;
			}

			$post['isdealer'] = intval($post['isdealer']);
			if(isset($_POST['isrecom'])){
				$post['isrecom'] = 1;
			}
			else{
				$post['isrecom'] = 0;
			}

			if(isset($_POST['ishot'])){
				$post['ishot'] = 1;
			}
			else{
				$post['ishot'] = 0;
			}
			$post['username'] = trim($post['username']);
			if(!empty($_POST['password'])){
				$post['password'] = md5($_POST['password']);
			}
			$post['mobilephone'] = trim($post['mobilephone']);
			$post['email'] = trim($post['email']);
			$post['nicname'] = trim($post['nicname']);
            $rs = $db->row_update('member',$post,"id=".intval($_POST['id']));
        }
    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])) $data = array('username'=>'','password'=>'','email'=>'','mobilephone '=>'','aid'=>'','cid'=>'');
        else $data = $db->row_select_one('member',"id=".intval($_GET['id']));

        if(!$data['lat'] || !$data['lon']){
            $data['lat'] = 0;
            $data['lon'] = 0;
        }

		$select_province = select_make($data['aid'],$commoncache['provincelist'],"请选择省份");
		$select_city = select_make($data['cid'],$commoncache['citylist'],"请选择城市");
		$tpl->assign( 'selectprovince', $select_province );
		$tpl->assign( 'selectcity', $select_city );
		$select_isdealer = select_make($data['isdealer'], $array_isdealer, '会员类型');
		$select_dealer_category = select_make($data['shoptype'],$array_dealer_category,"请选择公司类型");
		$tpl -> assign('selectisdealer', $select_isdealer);
		$tpl -> assign('select_dealer_category', $select_dealer_category);
        $tpl->assign( 'user', $data );
		$tpl->assign( 'sessionid', session_id() );
        $tpl->display( 'admin/add_member.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);
?>