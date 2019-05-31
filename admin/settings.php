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
// 当前模块
$m_name = '系统设置';
// 允许操作
$ac_arr = array('web' => '系统基本设置', 'contact' => '联系方式设置', 'car' => '车源相关设置');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

if (submitcheck('a')) {
	$post = post('sitename', 'title', 'keywords', 'description', 'copyright', 'icp', 'address', 'postcode', 'fax', 'tel', 'email','htmldir','water','isdstimg','imgwidth','imgheight','thumbwidth','thumbheight','gas','transmission','color','year','issell','waterpic','logo','islimit','limitcount','position','contactman');
	
	if(isset($post['issell'])){
		$post['issell'] = 1;
	}
	else{
		$post['issell'] = 0;
	}
	foreach ($post as $k => $v) {
		if (!in_array($k, array('smtp_port', 'smtp_password'))) {
			$post[$k] = htmlspecialchars($v);
		} elseif ($k == 'smtp_port') $post[$k] = intval($v);
		$rs = $db -> row_update('settings', array('v' => $v), "k='{$k}'");
		if (!$rs) showmsg("更新系统配置 {$k} 失败", -1);
	} 
	showmsg("更新" . $ac_arr[$ac] . "成功", ADMIN_PAGE."?m=$m&a=$ac");
} 

$data = settings();

$tpl -> assign('setting', $data);

$tpl -> display('admin/add_setting.html');
?>