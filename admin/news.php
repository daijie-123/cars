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
$m_name = '信息管理';
//允许操作
$ac_arr = array('list'=>'信息列表','add'=>'添加信息','edit'=>'编辑信息','del'=>'删除信息','bulkdel'=>'批量删除','html'=>'生成静态','bulkhtml'=>'批量生成静态');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );

//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$tpl->assign( 'page_g', $page_g );


//列表
if ($ac == 'list')
{	
	$where = '1=1';
    //搜索条件
	$newssort = '';
    if (!empty($_GET['catid']))
    {
        $catid = intval($_GET['catid']);
        $where .= " AND catid = $catid";
    }
	else{
		$catid = "";
	}
    if (!empty($_GET['keywords']))
    {
        $keywords = $_GET['keywords'];
        $where .= " AND n_title LIKE '%{$keywords}%'";
    }
	
	$select_category = select_category('news_category',$catid, 'name="catid" id="catid"', '-全部分类-', $catid);

    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix.'news',$where,'*','20','n_id desc');
    $list = $Page->get_data();
	$page = $Page -> page;
	foreach($list as $key => $value){
		$arr_news_category = array_category('news_category');
		if(!empty($value['catid'])){
			$list[$key]['n_category'] = $arr_news_category[$value['catid']];
		}
		$list[$key]['n_date'] = date('Ym',$value['n_addtime']);
		$list[$key]['n_addtime'] = date('Y-m-d H:i:s',$value['n_addtime']);
		$list[$key]['n_typename'] = $value['n_type']==1?"<span class='red'>推荐</span>":"";
	}
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
	$tpl->assign( 'selectcategory', $select_category );
	$tpl->assign( 'newslist', $list );
	$tpl->assign( 'button_basic', $button_basic );
	$tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/news_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('news',"n_id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('news',"n_id in($str_id)");
}
//单条生成静态
elseif ($ac == 'html')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = html_news($id);
}
//批量生成静态
elseif ($ac == 'bulkhtml')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
	foreach($_POST['bulkid'] as $key => $value) {
		$rs = html_news($value);
	} 
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {	
        $arr_not_empty = array('n_title'=>'信息标题不可为空');
        can_not_be_empty($arr_not_empty,$_POST);
        foreach (array('n_info') as $v)
        {
            $_POST[$v] = htmlspecialchars($_POST[$v]);
        }
        $post = post('n_title','n_info','n_author','n_keywords','catid','isrecom','n_hits','n_pic');

        $post['catid'] = intval($post['catid']);

		if(isset($post['isrecom'])){
			$post['isrecom'] = 1;
		}
		else{
			$post['isrecom'] = 0;
		}
		
		if(empty($post['n_hits'])){
			$post['n_hits'] = 0;
		}

		//信息分类
        $rs_category = $db->row_select_one('news_category',"catid=".intval($post['catid']));
        if (!$rs_category) showmsg('错语的分类',-1);
        $post['catid'] = intval($rs_category['catid']);

        if ($ac == 'add')
        {
			$post['n_addtime'] = time();
            $rs = $db->row_insert('news',$post);
			$insertid = $db -> insert_id();
			html_news(intval($insertid));
        }
        else
		{ 	
			$rs = $db->row_update('news',$post,"n_id=".intval($_POST['id']));
			html_news(intval($_POST['id']));
		}

    }
    //转向添加或修改页面
    else 
    {
		if (empty($_GET['id'])) $data = array('n_id'=>'','n_title'=>'','n_info'=>'','n_author'=>'','n_keywords'=>'','n_pic'=>'','n_url'=>'','catid'=>'','isrecom'=>'');
		
        else $data = $db->row_select_one('news',"n_id=".intval($_GET['id']));
		
		$select_category = select_category('news_category',$data['catid'], 'name="catid" id="catid" datatype="n" nullmsg="请选择分类！"', '-全部分类-', $data['catid']);
		
		$tpl->assign( 'selectcategory', $select_category );
		$tpl->assign( 'news', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_news.html' );
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