<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '维权管理';
//允许操作
$ac_arr = array('list' => '维权管理列表', 'update_status' => '修改', 'del' => '删除维权管理', 'bulkdel' => '批量删除');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign('mod_name', $m_name);
$tpl->assign('ac_arr', $ac_arr);
$tpl->assign('ac', $ac);
//页码
$page_g = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
$tpl->assign('page_g', $page_g);

//列表
if ($ac == 'list') {
    $where = '1=1';

    //搜索条件
    if (!empty($_GET['keywords'])) {
        $keywords = $_GET['keywords'];
        $where .= " AND ask LIKE '%{$keywords}%' or reply LIKE '%{$keywords}%'";
    }

    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'member_safeguard', $where, '*', '20', 'id desc');
    $list = $Page->get_data();
    $page = $Page->page;
    foreach ($list as $key => $value) {
        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
        $user = $db->row_select_one('member', 'id =' . $value['user_id'], 'id,username');
        $list[$key]['username'] = $user['username'];
        if ($list[$key]['status'] == 1) {
            $list[$key]['show_status'] = '待处理';
        } elseif ($list[$key]['status'] == 2) {
            $list[$key]['show_status'] = '处理中';
        } elseif ($list[$key]['status'] == 3) {
            $list[$key]['show_status'] = '完成';
        }
    }

    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign('safeguard_list', $list);
    $tpl->assign('button_basic', $button_basic);
    $tpl->assign('button_select', $button_select);
    $tpl->assign('page', $page);
    $tpl->display('admin/member_safeguard.html');
    exit;
}
//单条删除
elseif ($ac == 'del') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID', -1);
    $rs = $db->row_delete('member_safeguard', "id=$id");
}
//批量删除
elseif ($ac == 'bulkdel') {
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('member_safeguard', "id in($str_id)");
}
//修改
elseif ($ac == 'update_status') {
    //添加或修改
    if (submitcheck('a')) {
        if($_POST['status'] == 2){
            $arr_not_empty = array('mediate_company' => '沟通单位不能为空', 'id' => '参数错误');
            can_not_be_empty($arr_not_empty, $_POST);
            $post['status'] = $_POST['status'];
            $post['mediate_company'] = $_POST['mediate_company'];
            $post['start_mediate_time'] = TIMESTAMP;
            $rs = $db->row_update('member_safeguard', $post, "id=" . intval($_POST['id']));
        }elseif($_POST['status'] == 3){
            $arr_not_empty = array('mediate_result' => '沟通单位不能为空', 'id' => '参数错误');
            can_not_be_empty($arr_not_empty, $_POST);
            $post['status'] = $_POST['status'];
            $post['mediate_result'] = $_POST['mediate_result'];
            $post['pics'] = json_encode(['/upload/image/20180505/20180505092714_24054.jpg', '/upload/image/20180505/20180505092715_10464.jpg']);
            $post['end_mediate_time'] = TIMESTAMP;
            $rs = $db->row_update('member_safeguard', $post, "id=" . intval($_POST['id']));
        }

    }
    //转向添加或修改页面
    else {
        if (empty($_GET['id'])) showmsg('非法操作!', -1);
        $data = $db->row_select_one('member_safeguard', "id=" . intval($_GET['id']));
        $user = $db->row_select_one('member', 'id =' . $data['user_id'], 'id,username');
        $data['username'] = $user['username'];
        $data['addtime'] = date('Y-m-d H:i:s', $data['addtime']);
        $data['pics'] = json_decode($data['pics'], true);
        $tpl->assign('ask', $data);
        $tpl->assign('ac', $ac);
        $tpl->display('admin/member_safeguard_edit.html');
        exit;
    }
}
//默认操作
else {
    showmsg('非法操作', -1);
}

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE . "?m=$m&a=list&page=" . $page_g);
