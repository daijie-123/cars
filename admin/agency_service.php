<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '代办服务管理';
//允许操作
$ac_arr = array('list'=>'代办服务列表','add'=>'添加代办服务','edit'=>'编辑代办服务','del'=>'删除代办服务','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//列表
if ($ac == 'list')
{
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'agency_service');
    $list = $Page->get_data();
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'filmlist', $list );
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );
    $tpl->display( 'admin/agency_service_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('agency_service',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('agency_service',"id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('agency_service',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $post = post('logo','name', 'tel', 'address', 'orderid', 'type');
        // if(!empty($_FILES['upload']['name'])){
        //     $newname = time();
		// 	$post['pic'] = upload_pic($newname,1,'common/');
        // }
        if ($ac == 'add')
        {
            $post['addtime'] = TIMESTAMP;
            $rs = $db->row_insert('agency_service',$post);
        }
        else
        {
            $rs = $db->row_update('agency_service', $post, "id=" . intval($_POST['id']));
        }
    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])) $data = [];
        else $data = $db->row_select_one('agency_service',"id=".intval($_GET['id']));
        $tpl->assign( 'filmstrip', $data );
        $tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_agency_service.html' );
        exit;
    }
}
//默认操作
else
{
    showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list");