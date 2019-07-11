<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '人才管理';
//允许操作
$ac_arr = array('list'=>'人才列表','add'=>'添加人才','edit'=>'编辑人才','del'=>'删除人才','bulkdel'=>'批量删除','html'=>'生成静态','bulkhtml'=>'批量生成静态');
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

    if (!empty($_GET['keywords']))
    {
        $keywords = $_GET['keywords'];
        $where .= " AND `name` LIKE '%{$keywords}%'";
    }

	// $select_category = select_category('news_category',$catid, 'name="catid" id="catid"', '-全部分类-', $catid);

    include(INC_DIR.'Page.class.php');
    $Page = new Page($db->tb_prefix . 'talents', $where, '*', '20', 'id desc');
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
	$tpl->assign( 'newslist', $list );
	$tpl->assign( 'button_basic', $button_basic );
	$tpl->assign( 'button_select', $button_select );
	$tpl->assign( 'page', $page );
    $tpl->display( 'admin/talents_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('talents',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('talents',"id in($str_id)");
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
        $arr_not_empty = [
            'name'=>'姓名不可为空',
            'user_id' => '请选择所属公司',
            'type_id' => '请选择人才类型',
            'tel' => '请输入电话',
            'experience' => '请输入从业时间',
            'cert_no' => '请输入证件编号',
            'logo' => '请上传头像',
        ];
        can_not_be_empty($arr_not_empty, $_POST);

        $post = post('name','user_id','type_id','tel','experience','cert_no','logo');

        if ($ac == 'add')
        {
			$post['addtime'] = TIMESTAMP;
            $rs = $db->row_insert('talents', $post);
        }
        else
		{
			$rs = $db->row_update('talents', $post, "id=" . intval($_POST['id']));
		}

    }
    //转向添加或修改页面
    else
    {
		if (empty($_GET['id'])) $data = array('id'=>'','n_title'=>'','n_info'=>'','n_author'=>'','n_keywords'=>'','n_pic'=>'','n_url'=>'','catid'=>'','isrecom'=>'');

        else $data = $db->row_select_one('talents',"id=".intval($_GET['id']));

        $dealer_list = $db->row_select('member', "isdealer=2", 'id,company', 0, 'id desc');
        $select_dealer = select_make($data['user_id'], get_array($dealer_list, 'id', 'company') ,"请选择公司");

        $talents_type = $db->row_select('talents_type', "1=1", 'id,name', 0, 'id desc');
        $select_talents_type = select_make($data['type_id'], get_array($talents_type, 'id', 'name'), "请选择人才类型");

		$tpl->assign( 'select_dealer', $select_dealer );
		$tpl->assign( 'select_talents_type', $select_talents_type );
		$tpl->assign( 'talents', $data );
		$tpl->assign( 'ac', $ac );
        $tpl->display( 'admin/add_talents.html' );
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