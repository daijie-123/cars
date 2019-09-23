<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$mod_name = '美食店管理';
//允许操作
$ac_arr = array('list'=>'美食店列表','add'=>'添加美食店','edit'=>'编辑美食店','del'=>'删除美食店','bulkdel'=>'批量删除');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $mod_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g', $page_g );

$array_dealer_category = arr_dealer_category();

//列表
if ($ac == 'list')
{
	$where = '1=1';
    //搜索条件
    if (!empty($_GET['keywords']))
    {
        $keywords = $_GET['keywords'];
        $where .= " AND name LIKE '%{$keywords}%'";
    }
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'restaurant', $where, '*', '50', 'id desc');
    $list = $Page->get_data();
	$page = $Page -> page;
	foreach($list as &$value){
        // $list[$key]['regtime'] = date('Y-m-d H:i:s',$value['regtime']);
        $category = $db->row_select_one('restaurant_category',"id={$value['category_id']}", 'name');
        $value['category'] = $category['name'];
	}
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'userlist', $list );
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/restaurant_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('restaurant',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('restaurant',"id in($str_id)");
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        if ($ac == 'add')
        {
			// $arr_not_empty = array('username'=>'美食店名不可为空','password'=>'密码不能为空');
			can_not_be_empty($arr_not_empty, $_POST);

            $post = post('name','logo','opening_time','closing_time','address', 'lat', 'lon', 'tel', 'isrecom', 'consume');

			$post['addtime'] = TIMESTAMP;
            $rs = $db->row_insert('restaurant', $post);
            $insertid = $db->insert_id();

        }
        else
        {
			// $arr_not_empty = array('username'=>'美食店名不可为空');
            can_not_be_empty($arr_not_empty, $_POST);

            $post = post('name','logo','opening_time','closing_time','address', 'lat', 'lon', 'tel', 'isrecom', 'consume');

            $rs = $db->row_update('restaurant', $post, "id=" . intval($_POST['id']));
            $insertid = intval($_POST['id']);
        }
        $db->row_delete('restaurant_category_associate',"restaurant_id=$insertid");
        foreach($_POST['category_id'] as $category_id){
            $db->row_insert('restaurant_category_associate', ['restaurant_id' => $insertid, 'category_id' => $category_id]);
        }

    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])){
            $data = array('username'=>'','password'=>'','email'=>'','mobilephone '=>'','aid'=>'','cid'=>'');  $is_select_category = [];
        }else{
            $data = $db->row_select_one('restaurant',"id=".intval($_GET['id']));
            $is_select_category = $db->row_select('restaurant_category_associate', "restaurant_id={$_GET['id']}", 'category_id', 0);
            $is_select_category = array_column($is_select_category, 'category_id');
        }
        $select_category = $db->row_select('restaurant_category','1=1','*',0,'orderid asc');
        // $select_category = get_array($select_category, 'id', 'name');
        // $select_category = select_make($data['category_id'], $select_category, '请选择类型');
        // jj($is_select_category);
        $tpl->assign('is_select_category', $is_select_category);
        $tpl->assign('select_category', $select_category);

        if(!$data['lat'] || !$data['lon']){
            $data['lat'] = 0;
            $data['lon'] = 0;
        }
        $tpl->assign( 'restaurant', $data );
        $tpl->display( 'admin/add_restaurant.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作', -1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);