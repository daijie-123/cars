<?php
/**
 * @author littlemao <littlemao2007@126.com>
 * Sat Dec 18 00:22:32 CST 2010
 */

if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$mod_name = '模版管理';
//允许操作
$ac_arr = array('list'=>'模版列表','add'=>'添加模版','edit'=>'编辑模版','del'=>'删除模版','bulkdel'=>'批量删除','bulksort'=>'更新排序','start'=>'启用模板');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'list';

$tpl->assign( 'mod_name', $mod_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//列表
if ($ac == 'list')
{	
	$dir = "templates/default/";
	
	$list = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				$filetype = filetype($dir . $file);
				$post['filename'] = $file;
				if($filetype=="dir" and $file!="." and $file!=".."){
					$configflie = "templates/default/".$file."/config.php";
					if(file_exists($configflie)){
						 $config = require($configflie);
						 $list[] = $config;
					}
				}
			} 
			closedir($dh);
		} 
	} 
	
	//print_r($list);
	//启用状态
    $data = $db->row_select('settings',"k='templates'");
	$tpl->assign( 'templates', $data );
	$tpl->assign( 'templates_category_list', $list );
    $tpl->display( 'admin/templates_category_list.html' );
	
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('templates_category',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('templates_category',"id in($str_id)");
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('ac'))
    {	
        $arr_not_empty = array('c_name'=>'模版名称不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        $post = post('c_name','c_url','c_target');
        if ($ac == 'add')
        {
			$post['c_orderid'] = 0;
            $rs = $db->row_insert('templates_category',$post);
        }
        else
		{ 	
			$rs = $db->row_update('templates_category',$post,"id=".intval($_POST['id']));
		}
    }
    //转向添加或修改页面
    else 
    {
		if (empty($_GET['id'])) $data = array('c_name'=>'','c_url'=>'','c_target'=>'');
        else $data = $db->row_select_one('templates_category',"id=".intval($_GET['id']));

		$tpl->assign( 'templates_category', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_templates_category.html' );
        exit;
    }
}
else if($ac=='start'){
	  $catname = $_GET['catname'];
	  $rs = $db -> row_update('settings',array('v'=>$catname),"k='templates'");
      $data = settings();
      $dir = "./templates/default/";
     $array_temdir = array();
     if (is_dir($dir)) {
     if ($dh = opendir($dir)) {
         while (($file = readdir($dh)) !== false) {
             if(is_dir($dir . $file)) {
              $array_temdir[] = $file;
            }
         }
        closedir($dh);
     }
}
$arr_temdir = array_slice($array_temdir, 2);
foreach($arr_temdir as $key => $value){
	$arr_tem[$value] = $value;
}

$select_templates = select_make($data['templates'],$arr_tem);
$tpl -> assign('selecttemplates', $select_templates);

}

//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac].($rs ? '成功' : '失败'),ADMIN_PAGE."?m=$m&a=list&page=".$page_g);
?>