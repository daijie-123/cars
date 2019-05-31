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

$tpl->assign( 'weburl', WEB_PATH );


$time = date("Y-m-d H:i:s");
$tpl->assign( 'time', $time);

//退出登陆
if (is_admin_login() && get('a') == 'logout')
{
    session_unset();
    session_destroy();
}

//已登陆转向
if (is_admin_login()) redirect('', ADMIN_PAGE.'?m=main');

//登陆
if (submitcheck('username'))
{
    //不可为空
    $arr_not_empty = array('username'=>'请输入您的授权账号','password'=>'请输入您的进入密码');
    can_not_be_empty($arr_not_empty,$_POST);
    
    //if (trim($_POST['authcode']) != $_SESSION['authcode']) showmsg('验证码不正确',-1);
    $rs_admin = $db->row_select_one('admin',"username='".trim($_POST['username'])."' AND password='".md5(trim($_POST['password']))."'");
    if (!$rs_admin) showmsg('用户不存在或密码错误',-1);
    if (!$rs_admin['status']) showmsg('此账户已被禁用',-1);
    $db->row_update('admin',array('last_login_time'=>TIMESTAMP,'last_login_ip'=>get_client_ip(),'login_count'=>$rs_admin['login_count']+1),"adminid={$rs_admin['adminid']}");
    $_SESSION['ADMIN_UID'] = $rs_admin['adminid'];
    $_SESSION['ADMIN_NAME'] = $rs_admin['username'];
    $_SESSION['ADMIN_TYPE'] = $rs_admin['admin_type'];
    showmsg('登陆成功', ADMIN_PAGE.'?m=main');
}
else{
	//未登陆转到登陆页面
	$tpl->display( 'admin/login.html' );
}
?>