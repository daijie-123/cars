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

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//列表
if ($ac == 'list')
{
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'page_sorts','1=1','*','20','orderid');
    $list = $Page->get_data();
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
	$tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/pagesort_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $s_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('page_sorts',"s_id=$s_id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('page_sorts',"s_id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('page_sorts',array('orderid'=>$_POST['orderid'][$v]),"s_id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {	
        $arr_not_empty = array('s_name'=>'分类名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('s_name','s_dir','orderid','orderid');
		if(!empty($post['s_dir']) and !is_dir($post['s_dir'])) createFolder($post['s_dir']);
        $post['orderid'] = !empty($post['orderid']) ? intval($post['orderid']) : 0;
        if ($ac == 'add')
        {
            $rs = $db->row_insert('page_sorts',$post);
        }
        else
		{ 	
			$rs = $db->row_update('page_sorts',$post,"s_id=".intval($_POST['s_id']));
		}
    }
    //转向添加或修改页面
    else 
    {
		if (empty($_GET['id']))
		{
            $data = array('s_id'=>'','s_name'=>'','s_dir'=>'','orderid'=>'');
        }
        else
		{
            $data = $db->row_select_one('page_sorts',"s_id=".intval($_GET['id']));
        }
		$tpl->assign( 'sort', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_pagesort.html' );
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