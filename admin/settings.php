<?php
if (!defined('APP_IN')) exit('Access Denied');
// 当前模块
$m_name = '系统设置';
// 允许操作
$ac_arr = array('web' => '系统基本设置', 'contact' => '联系方式设置', 'car' => '车源相关设置', 'miniprogram' => '小程序配置', 'car_service' => '车管服务配置');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $m_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

if (submitcheck('a')) {
	$post = post('sitename', 'title', 'keywords', 'description', 'copyright', 'icp', 'address', 'postcode', 'fax', 'tel', 'email','htmldir','water','isdstimg','imgwidth','imgheight','thumbwidth','thumbheight','gas','transmission','color','year','issell','waterpic','logo','islimit','limitcount','position','contactman', 'miniprogram_app_id', 'miniprogram_app_secret', 'car_trade_contract', 'vehicle_evaluation_tel', 'query_traffic_violations_tel', 'accident_rescue_tel');

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
    clear_all_fzz_cache();
	showmsg("更新" . $ac_arr[$ac] . "成功", ADMIN_PAGE."?m=$m&a=$ac");
}

$data = settings();

$tpl -> assign('setting', $data);

$tpl -> display('admin/add_setting.html');