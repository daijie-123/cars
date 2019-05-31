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
$m_name = '商家类型管理';
//允许操作
$ac_arr = array('list'=>'商家类型列表','add'=>'添加商家类型','edit'=>'编辑商家类型','del'=>'删除商家类型','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );


//列表
if ($ac == 'list')
{
    $list = $db->row_select('dealer_category','1=1','*',0,'orderid asc');
	$tpl->assign( 'categorylist', $list );
    $tpl->display( 'admin/dealer_category_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('dealer_category',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('dealer_category',"id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('dealer_category',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {	
        $arr_not_empty = array('catname'=>'商家类型名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('catname','orderid');
		$post['catname'] = trim($post['catname']);
		$post['orderid'] = intval($post['orderid']);
        if ($ac == 'add')
        {
            $rs = $db->row_insert('dealer_category',$post);
        }
        else
		{ 	
			$rs = $db->row_update('dealer_category',$post,"id=".intval($_POST['id']));
		}
    }
    //转向添加或修改页面
    else 
    {
		if (empty($_GET['id'])) $data = array('id'=>'','catname'=>'','orderid'=>'');
        else $data = $db->row_select_one('dealer_category',"id=".intval($_GET['id']));
		$tpl->assign( 'category', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_dealer_category.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list");
?>