<?php
if (!defined('APP_IN')) exit('Access Denied');

//当前模块
$m_name = '活动管理';
//允许操作
$ac_arr = array('list'=>'活动列表','add'=>'添加活动','edit'=>'编辑活动','del'=>'删除活动','bulkdel'=>'批量删除','bulksort'=>'更新排序', 'apply_list' => '报名列表', 'apply_del' => '删除报名');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl->assign( 'mod_name', $m_name );
$tpl->assign( 'ac_arr', $ac_arr );
$tpl->assign( 'ac', $ac );
include(INC_DIR.'Page.class.php');
//列表
if ($ac == 'list')
{
    $Page = new Page($db->tb_prefix . 'activity', "1=1", '*' , 20 , 'id desc');
    $list = $Page->get_data();
    foreach($list as &$v){
        $v['type_name'] = $v['type'] == 1 ? '金标公益' : '自驾俱乐部';

        if(($v['start_time'] < TIMESTAMP) && ($v['end_time'] > TIMESTAMP)){
            $v['activity_status'] = '进行中';
        }elseif($v['start_time'] > TIMESTAMP){
            $v['activity_status'] = '未开始';
        }elseif($v['end_time'] < TIMESTAMP){
            $v['activity_status'] = '已结束';
        }

        if(($v['apply_start_time'] < TIMESTAMP) && ($v['apply_end_time'] > TIMESTAMP)){
            $v['apply_status'] = '进行中';
        }elseif($v['apply_start_time'] > TIMESTAMP){
            $v['apply_status'] = '未开始';
        }elseif($v['apply_end_time'] < TIMESTAMP){
            $v['apply_status'] = '已结束';
        }

    }
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );

    $tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/activity_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('activity',"id=$id");
}
//批量删除
elseif ($ac == 'bulkdel')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    $str_id = return_str_id($_POST['bulkid']);
    $rs = $db->row_delete('activity',"id in($str_id)");
}
//批量排序
elseif ($ac == 'bulksort')
{
    if (empty($_POST['bulkid'])) showmsg('没有选中任何项',-1);
    foreach ($_POST['bulkid'] as $k => $v)
    {
        $rs = $db->row_update('activity',array('orderid'=>$_POST['orderid'][$v]),"id=".intval($v));
    }
}
//添加
elseif ($ac == 'add' || $ac == 'edit')
{
    //添加或修改
    if (submitcheck('a'))
    {
        $arr_not_empty = array(
            'type'=>'请选择类型',
            'title'=>'请输入标题',
            'sub_title'=>'请输入副标题',
            'start_time'=>'请选择活动开始时间',
            'end_time'=>'请选择活动结束时间',
            'apply_start_time'=>'请选择报名开始时间',
            'apply_end_time'=>'请选择报名结束时间',
            'apply_maximum'=>'请填写报名最多人数',
            'address'=>'请输入活动地址',
            'detail'=>'请输入活动详情',
            // 'mainpic'=>'请选择封面图',
            'pics'=>'请上传图片',
            'sponsor'=>'请输入主办方',
        );
        can_not_be_empty($arr_not_empty, $_POST);
        $post = post('type','title', 'sub_title', 'start_time', 'end_time', 'apply_start_time', 'apply_end_time', 'apply_maximum', 'address', 'detail', 'pics', 'mainpic', 'sponsor');

        $post['start_time'] = strtotime($post['start_time']);
        $post['end_time'] = strtotime($post['end_time']);
        $post['apply_start_time'] = strtotime($post['apply_start_time']);
        $post['apply_end_time'] = strtotime($post['apply_end_time']);

        $post['pics'] = json_encode($post['pics']);
        $post['created_time'] = TIMESTAMP;
        if ($ac == 'add')
        {
            $rs = $db->row_insert('activity',$post);
        }
        else
		{
            can_not_be_empty(['id' => '参数错误'], $_POST);
			$rs = $db->row_update('activity', $post, "id=" . intval($_POST['id']));
		}
    }
    //转向添加或修改页面
    else
    {
		if (empty($_GET['id'])) $data = array('id'=>'','pics'=>'','orderid'=>'');
        else $data = $db->row_select_one('activity',"id=".intval($_GET['id']));
        // $data['pics'] = json_decode($data['pics'], true);
        if (!empty($data['pics'])) {
            $pic_list = json_decode($data['pics'], true);
            $piclist = array();
            foreach ($pic_list as $key => $value) {
                $piclist[$key]['pic'] = $value;
                $piclist[$key]['showpic'] = WEB_PATH . $value;
                $arr_picid = explode("/", $value);
                $arr_length = count($arr_picid);
                $arr_picids = explode(".", $arr_picid[$arr_length - 1]);
                $piclist[$key]['picid'] = $arr_picids[0];
            }
            // jj($piclist);

            $tpl->assign('pic_list', $piclist);
        }
		$tpl->assign('activity', $data);
		$tpl->assign('ac', $ac);
        $tpl->display('admin/add_activity.html');
        exit;
    }
}
// 报名列表
elseif ($ac == 'apply_list')
{
    $Page = new Page($db->tb_prefix . 'activity_user', "activity_id={$_GET['id']}", '*' , 20 , 'id desc');
    $list = $Page->get_data();
    foreach($list as &$v){
        $v['type_name'] = $v['type'] == 1 ? '金标公益' : '自驾俱乐部';

        if(($v['start_time'] < TIMESTAMP) && ($v['end_time'] > TIMESTAMP)){
            $v['activity_status'] = '进行中';
        }elseif($v['start_time'] > TIMESTAMP){
            $v['activity_status'] = '未开始';
        }elseif($v['end_time'] < TIMESTAMP){
            $v['activity_status'] = '已结束';
        }

        if(($v['apply_start_time'] < TIMESTAMP) && ($v['apply_end_time'] > TIMESTAMP)){
            $v['apply_status'] = '进行中';
        }elseif($v['apply_start_time'] > TIMESTAMP){
            $v['apply_status'] = '未开始';
        }elseif($v['apply_end_time'] < TIMESTAMP){
            $v['apply_status'] = '已结束';
        }

    }
    $button_basic = $Page->button_basic();
    $button_select = $Page->button_select();
    $tpl->assign( 'button_basic', $button_basic );
    $tpl->assign( 'button_select', $button_select );

    $tpl->assign( 'sortlist', $list );
    $tpl->display( 'admin/activity_apply_list.html' );
    exit;
}
//单条删除
elseif ($ac == 'apply_del')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : showmsg('缺少ID',-1);
    $rs = $db->row_delete('activity_user',"id=$id");
}
//默认操作
else
{
    showmsg('非法操作',-1);
}

showmsg($ac_arr[$ac] . ($rs ? '成功' : '失败'), ADMIN_PAGE . "?m=activity&a=list");