<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '招聘管理';
//允许操作
$ac_arr = array('list'=>'招聘列表','add'=>'添加招聘','edit'=>'编辑招聘','del'=>'删除招聘','bulkdel'=>'批量删除');
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
    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix . 'dealer_recruitment', '1=1', '*', '20', 'id desc');
    $list = $Page->get_data();
	$page = $Page -> page;
	foreach($list as &$value){
        $type = $db->row_select_one('talents_type', "id={$value['type_id']}", 'name');
        $value['type'] = $type['name'];
        
        $company = $db->row_select_one('member', "id={$value['user_id']}", 'company');
        $value['company'] = $company['company'];
	}
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
	// $tpl->assign( 'selectcategory', $select_category );
	$tpl->assign( 'recruitmentlist', $list );
	$tpl->assign( 'button_basic', $button_basic );
	$tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/recruitment_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('news',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('news',"id in($str_id)");
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
        $arr_not_empty = array('title'=>'招聘标题不可为空', 'user_id' => '请选择商家', 'tel' => '电话不能为空');
        can_not_be_empty($arr_not_empty,$_POST);
        // foreach (array('desc') as $v)
        // {
        //     $_POST[$v] = htmlspecialchars($_POST[$v]);
        // }
        $post = post('title','user_id','n_author','label','experience','education','wage_min','wage_max', 'desc', 'tel', 'type_id');

        if ($ac == 'add')
        {
			$post['addtime'] = TIMESTAMP;
            $rs = $db->row_insert('dealer_recruitment', $post);
			// $insertid = $db -> insert_id();
			// html_news(intval($insertid));
        }
        else
		{
			$rs = $db->row_update('dealer_recruitment', $post, "id=" . intval($_POST['id']));
			// html_news(intval($_POST['id']));
		}

    }
    //转向添加或修改页面
    else
    {
		if (empty($_GET['id'])) $data = [];

        else $data = $db->row_select_one('dealer_recruitment', "id=" . intval($_GET['id']));

        $dealer_list = $db->row_select('member', "isdealer=2", 'id,company', 0, 'id desc');
        $select_dealer = select_make($data['user_id'], get_array($dealer_list, 'id', 'company') ,"请选择公司");
        $select_experience = select_make($data['experience'], [0 => '不限', 1 => '无经验', 2 => '1年以下', 3 => '1-3年', 4 => '3-5年', 5 => '5-10年', 6 => '10年以上'], "请选择工作经验");

        $select_education = select_make($data['education'], [0 => '全部', 1 => '不限', 2 => '初中', 3 => '中技', 4 => '高中', 5 => '中专', 6 => '大专', 7 => '本科', 8 => '硕士', 9 => '博士'], "请选择学历要求");

        $talents_type = $db->row_select('talents_type', "1=1", 'id,name', 0, 'id desc');
        $select_talents_type = select_make($data['type_id'], get_array($talents_type, 'id', 'name'), "请选择人才类型");
		$tpl->assign( 'select_talents_type', $select_talents_type );
		$tpl->assign( 'select_dealer', $select_dealer );
		$tpl->assign( 'select_experience', $select_experience );
		$tpl->assign( 'select_education', $select_education );
		$tpl->assign( 'recruitment', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_recruitment.html' );
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