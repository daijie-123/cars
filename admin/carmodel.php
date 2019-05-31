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

// 品牌选择
if (!empty($_GET['ajax']) && isset($_GET['bid'])) {
	header('Content-Type:text/plain; charset=utf-8');
	$brandlist = "<option value='' selected>请选择子品牌</option>";
	$list = $db -> row_select('brand', "b_parent='" . $_GET['bid'] . "'");
	if($list) {
		foreach($list as $key => $value) {
			$brandlist .= "<option value=" . $value['b_id'] . ">" . $value['b_name'] . "</option>";
		} 
	} 
	echo $brandlist;
	exit;
} 

//当前模块
$m_name = '品牌管理';
//允许操作
$ac_arr = array('list'=>'品牌列表','add'=>'添加车型','edit'=>'编辑车型','del'=>'删除品牌','bulkdel'=>'批量删除','bulksort'=>'更新排序','sign'=>'标记品牌','editcarstyle'=>'编辑款式');
$match_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','All');

//当前操作

$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';
$sel_key = isset($_REQUEST['k']) && in_array($_REQUEST['k'],$match_arr) ? $_REQUEST['k'] : 'All';
$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//列表
if ($ac == 'list')
{
	if(!empty($_GET['p_subbrand'])){
	   $where='b_id='.$_GET['p_subbrand'];
	   $list = $db -> row_select('brand', $where,'b_id,b_name,b_parent');
	   $list01=$db -> row_select_one('brand', 'b_id='.$list[0]['b_parent'],'b_id,b_parent');
	   foreach($list as $key => $value){
			$subbrandlist = $db->row_select('brand','b_parent = '.$value['b_id']);
			foreach($subbrandlist as $subkey => $subvalue){
				$subsubbrandlist = $db->row_select('brand','b_parent = '.$subvalue['b_id']);
				$subbrandlist[$subkey]['subbrands_list']=$subsubbrandlist;
			}
			$list[$key]['brands_list']=$subbrandlist;
		}
		$tpl->assign( 'types', 1 );
		//$tpl -> assign('select_brand', $select_brand);
		$select_brand = select_make($list01['b_parent'], $commoncache['markbrandlist'], '请选择品牌');
		$select_subbrand = select_subbrand(intval($list[0]['b_id']));
		$tpl -> assign('select_brand', $select_brand);
		$tpl -> assign('select_subbrand', $select_subbrand);
	}
	else{
		$where = 'classid = 5';
		include(INC_DIR.'Page.class.php');
		$Page = new Page($db->tb_prefix.'brand',$where,'*',30,'b_id');
		$list = $Page->get_data();
		$page = $Page -> page;
		foreach($list as $key => $value){
			if(!empty($value['b_parent'])){
				$data01 = $db -> row_select_one('brand','b_id='.$value['b_parent'],'b_parent,b_name,b_id');
				$list[$key]['styles']=$data01['b_name'];
				$list[$key]['f_id']=$data01['b_id'];
				if(!empty($data01)){
					$data02 = $db -> row_select_one('brand','b_id='.$data01['b_parent'],'b_parent,b_name,b_id');
					$list[$key]['model']=$data02['b_name'];
				}
				if(!empty($data02)){
					$data03 = $db -> row_select_one('brand','b_id='.$data02['b_parent'],'b_parent,b_name,b_id');
					$list[$key]['brand02']=$data02['b_name'];
				}
				if(!empty($data03)){
					$data04 = $db -> row_select_one('brand','b_id='.$data03['b_parent'],'b_parent,b_name,b_id');
					$list[$key]['brand01']=$data04['b_name'];
				}
			}
			
		}
		$tpl->assign( 'types', 2 );
		$button_basic = $Page->button_basic();
		$button_select = $Page->button_select();
		$tpl->assign( 'button_basic', $button_basic );
		$tpl->assign( 'button_select', $button_select );
		//$tpl -> assign('select_brand', $select_brand);
		$select_subbrand = select_subbrand(0);
		$tpl -> assign('select_brand', $select_brand);
		$tpl -> assign('select_subbrand', $select_subbrand);
	}
	
    $tpl->assign( 'sel_key',	$sel_key );
    $tpl->assign( 'match_arr',	$match_arr );
    $tpl->assign( 'brandlist', $list );
    $tpl->display( 'admin/carmodel_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $s_id = isset($_GET['b_id']) ? intval($_GET['b_id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('brand',"b_id=$b_id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('brand',"b_id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('brand',array('orderid'=>$_POST['orderid'][$v]),"b_id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {	
      //  $arr_not_empty = array('b_name4'=>'款式名称不可为空');
        //can_not_be_empty($arr_not_empty,$_POST);
        if ($ac == 'add')
        {
			if($_POST['isaddmodel']==1){
			//四级品牌
			$post=post('b_name','b_parent','mark','classid');
            $listthree = $db->row_select_one('brand','b_id='.intval($_POST['p_subbrand']));
			$post['b_parent']=$listthree['b_id'];
			$post['mark']=$listthree['mark'];
			$post['b_name']=$_POST['b_name4'];
			$post['classid']=4;
			$rs = $db->row_insert('brand',$post);
			$insertid = $db -> insert_id();
		  // 五级品牌
		   if(!empty($_POST['b_name5'])){
		    $post['b_name']=$_POST['b_name5'];
			$post['classid']=5;
			$post['b_parent']=$insertid;
			$post['mark']=$listthree['mark'];
            $rs = $db->row_insert('brand',$post);
		   }
			}else{
		  // 五级品牌
		  if(!empty($_POST['b_name5'])){
            $listfour = $db->row_select_one('brand','b_id='.intval($_POST['p_subsubbrand']));
		    $post['b_name']=$_POST['b_name5'];
			$post['classid']=5;
			$post['b_parent']=$listfour['b_id'];
			$post['mark']=$listfour['mark'];
            $rs = $db->row_insert('brand',$post);
		  }
			}
        }
        else
		{ 	
			if($_POST['isaddmodel']==1){
			//四级品牌
			$post=post('b_name','b_parent','mark','classid');
            $listthree = $db->row_select_one('brand','b_id='.intval($_POST['p_subbrand']));
			$post['b_parent']=$listthree['b_id'];
			$post['mark']=$listthree['mark'];
			$post['b_name']=$_POST['b_name4'];
			$post['classid']=4;
			$rs = $db->row_insert('brand',$post);
			$insertid = $db -> insert_id();
		  // 五级品牌
		   // $listfour = $db->row_select_one('brand','b_parent='.intval($_POST['p_subbrand']).' and b_name='.'"'.$_POST['b_name4'].'"');
		    $post['b_name']=$_POST['b_name5'];
			$post['classid']=5;
			$post['b_parent']=$insertid;
			$post['mark']=$listthree['mark'];
            $rs = $db->row_update('brand',$post,'b_id='.$_POST['b_id']);
			}else{
		  // 五级品牌
            $listfour = $db->row_select_one('brand','b_id='.intval($_POST['p_subsubbrand']));
		    $post['b_name']=$_POST['b_name5'];
			$post['classid']=5;
			$post['b_parent']=$listfour['b_id'];
			$post['mark']=$listfour['mark'];
            $rs = $db->row_update('brand',$post,'b_id='.$_POST['b_id']);
			}
		}
    }
    //转向添加或修改页面
    else 
    {
		    if (empty($_GET['b_id'])) {
		    $data = array('b_id'=>'','b_parent'=>'','b_name'=>'','mark'=>'');
		    $select_brand = select_make(0, $commoncache['markbrandlist'], '请选择品牌');
		    $select_subbrand = select_subbrand(0);
		    $select_fourbrand ='';
		}
            else {
	        $data5 = $db->row_select_one('brand',"b_id=".intval($_GET['b_id']));
		    $data4 = $db->row_select_one('brand',"b_id=".$data5['b_parent']);
		    $data3 = $db->row_select_one('brand',"b_id=".$data4['b_parent']);
		    $data2 = $db->row_select_one('brand',"b_id=".$data3['b_parent']);
		    $select_brand = select_make($data2['b_parent'], $commoncache['markbrandlist'], '请选择品牌');
		    $select_subbrand = select_subbrand($data3['b_id']);
		    $select_fourbrand = select_make($data4['b_id'], arr_brand($data3['b_id']));
		    $tpl -> assign('subsubsubsublist', $data5);
		}
		    $tpl->assign( 'ac', $ac );
		    $tpl -> assign('select_brand', $select_brand);
		    $tpl -> assign('select_subbrand', $select_subbrand);
		    $tpl -> assign('select_fourbrand', $select_fourbrand);
            $tpl->display( 'admin/add_carmodel.html' );
            exit;	
    }
}
//修改款式
elseif ($ac == 'editcarstyle')
{
    if (submitcheck('a'))
    {	
        $arr_not_empty = array('b_name4'=>'款式名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
		//四级品牌
		$post=post('b_name');
		$post['b_name']=$_POST['b_name4'];
		$rs = $db->row_update('brand',$post,'b_id='.$_POST['b_id']);	        
    }
    //转向修改页面
    else 
    {
            $data4 = $db->row_select_one('brand',"b_id=".$_GET['b_id']);
			$tpl->assign( 'subsubsublist', $data4 );
		    $tpl->assign( 'ac', $ac );
            $tpl->display( 'admin/edit_carstyle.html' );
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