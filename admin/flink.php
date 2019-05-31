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
$m_name = '友情链接管理';
//允许操作
$ac_arr = array('list'=>'链接列表','add'=>'添加链接','edit'=>'编辑链接','del'=>'删除链接', 'show' => '显示链接','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//$array_flinksort = arr_flinksort();

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g', $page_g );

//列表
if ($ac == 'list')
{
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'friendlink','1=1','*','20','l_orderid');
    $list = $Page->get_data();
	$page = $Page -> page;
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'flinklist', $list );
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/flink_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $l_id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('friendlink',"l_id=$l_id");
}
// 显示车源
elseif ($ac == 'show') {
	$lstate = intval($_GET['l_state']);
	$rs = $db -> row_update('friendlink', array('isshow' => $lstate), "l_id=" . intval($_GET['id']));
} 
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('friendlink',array('l_orderid'=>$_POST['orderid'][$v]),"l_id=".intval($v));
    }
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('friendlink',"l_id in($str_id)");
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array('l_name'=>'网站名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('s_id','l_url','l_name','l_note');
    
        if ($ac == 'add')
        {
			$post['l_orderid'] = 0;
			$post['isshow'] = 1;
            $rs = $db->row_insert('friendlink',$post);
        }
        else
        {
            $rs = $db->row_update('friendlink',$post,"l_id=".intval($_POST['l_id']));
        }
    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])) $data = array('l_url'=>'','l_name'=>'','l_note'=>'','isshow'=>'','l_orderid'=>'');
        else $data = $db->row_select_one('friendlink',"l_id=".intval($_GET['id']));
        $tpl->assign( 'link', $data );
        $tpl->display( 'admin/add_flink.html' );
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