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
$m_name = '分类管理';

//允许操作
$ac_arr = array('list'=>'分类列表','add'=>'添加分类','edit'=>'编辑分类','del'=>'删除分类','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';


//列表
if ($ac == 'list')
{
    include(INC_DIR.'page.class.php');
    $friendlink = new page($db->tb_prefix.'friendlink_sorts','1=1','*','20','orderid');
    $list = $friendlink->get_data();
    $button_basic = $friendlink->button_basic();
    $button_select = $friendlink->button_select();
	$tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/flinksort_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $s_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('friendlink_sorts',"s_id=$s_id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('friendlink_sorts',"s_id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('friendlink_sorts',array('orderid'=>$_POST['orderid'][$v]),"s_id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('ac'))
    {	
        $arr_not_empty = array('s_name'=>'分类名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('s_name','orderid','orderid');
        $post['orderid'] = !empty($post['orderid']) ? intval($post['orderid']) : 0;
        if ($ac == 'add')
        {
            $rs = $db->row_insert('friendlink_sorts',$post);
        }
        else
		{ 	
			$rs = $db->row_update('friendlink_sorts',$post,"s_id=".intval($_POST['s_id']));
		}
    }
    //转向添加或修改页面
    else 
    {
		if (empty($_GET['id']))
		{
            $data = array('s_id'=>'','s_name'=>'','orderid'=>'');
        }
        else
		{
            $data = $db->row_select_one('friendlink_sorts',"s_id=".intval($_GET['id']));
        }
		$tpl->assign( 'sort', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_flinksort.html' );
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