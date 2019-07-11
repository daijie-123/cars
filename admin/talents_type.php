<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '人才类型管理';
//允许操作
$ac_arr = array('list'=>'人才类型列表','add'=>'添加人才类型','edit'=>'编辑人才类型','del'=>'删除人才类型','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );


//列表
if ($ac == 'list')
{
    $list = $db->row_select('talents_type','1=1','*',0,'orderid asc');
	$tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/talents_type_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('talents_type',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('talents_type',"id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('talents_type',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array('name'=>'人才类型名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('name','pic');
        if ($ac == 'add')
        {
            $rs = $db->row_insert('talents_type',$post);
        }
        else
		{
			$rs = $db->row_update('talents_type', $post, "id=".intval($_POST['id']));
		}
    }
    //转向添加或修改页面
    else
    {
		if (empty($_GET['id'])) $data = array('id'=>'','name'=>'','pic'=>'','orderid'=>'');
        else $data = $db->row_select_one('talents_type',"id=".intval($_GET['id']));
		$tpl->assign( 'sort', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_talents_type.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list");
