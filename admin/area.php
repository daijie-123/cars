<?php
/*
 本软件版权归作者所有,在投入使用之前注意获取许可
 作者：北京市普艾斯科技有限公司
 项目：simcms_锐车1.0高级版
 电话：010-58480317
 Q  Q：228971357
 网址：http://www.simcms.net
 simcms.net保留全部权力，受相关法律和国际		  		
 公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
*/
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$mod_name = '省市管理';
//允许操作
$ac_arr = array('list'=>'地区列表','add'=>'添加地区','edit'=>'编辑地区','recom'=>'推荐地区','del'=>'删除地区','bulkdel'=>'批量删除','bulksort'=>'更新排序');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $mod_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );


//列表
if ($ac == 'list')
{
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'area','parentid=-1','*','20','orderid');
    $list = $Page->get_data();
	foreach($list as $key => $value ){
		$citylist = $db -> row_select('area', "parentid=".$value['id'], 'id,name,isrecom,orderid', 0, 'orderid asc');
		$list[$key]['city'] = $citylist;
	}
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'arealist', $list );
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );
    $tpl->display( 'admin/area_list.html' );
    exit;
}
// 推荐
elseif ($ac == 'recom') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
	$isrecom  = intval($_GET['isrecom']);
	$rs = $db -> row_update('area', array('isrecom' => $isrecom ), "id=" . $id);
} 
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('area',"id=$id or parentid=$id ");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('area',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('area',"id in($str_id) or parentid in($str_id)");
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array('name'=>'名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('id','name','orderid','parentid');
		$post['id'] = intval($post['id']);
        if ($ac == 'add')
        {
			$post['orderid'] = 0;
            $rs = $db->row_insert('area',$post);
        }
        else
        {
            $rs = $db->row_update('area',$post,"id=".intval($_POST['id']));
        }
    }
    //转向添加或修改页面
    else
    {
        if (empty($_GET['id'])) {
			$data = array('id'=>'','name'=>'','orderid'=>'');
			if(empty($_GET['parentid'])){
				$data['parentid'] = -1;
			}
			else{
				$data['parentid'] = $_GET['parentid'];
			}
		}
		else{
			$data = $db->row_select_one('area',"id=".intval($_GET['id']));
		}
        $tpl->assign( 'area', $data );
        $tpl->display( 'admin/add_area.html' );
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