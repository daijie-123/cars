<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '美食分类管理';
//允许操作
$ac_arr = array('list'=>'美食分类列表','add'=>'添加美食分类','edit'=>'编辑美食分类','del'=>'删除美食分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );


//列表
if ($ac == 'list')
{
    $list = $db->row_select('restaurant_category','1=1','*',0,'orderid desc,id desc');
	$tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/restaurant_category_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('restaurant_category',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('restaurant_category',"id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('restaurant_category',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array('name'=>'美食分类名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('name','logo', 'isrecom');
        if ($ac == 'add')
        {
            $rs = $db->row_insert('restaurant_category',$post);
        }
        else
		{
			$rs = $db->row_update('restaurant_category', $post, "id=".intval($_POST['id']));
		}
    }
    //转向添加或修改页面
    else
    {
		if (empty($_GET['id'])) $data = array('id'=>'','name'=>'','pic'=>'','orderid'=>'');
        else $data = $db->row_select_one('restaurant_category',"id=".intval($_GET['id']));
		$tpl->assign( 'sort', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_restaurant_category.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list");
