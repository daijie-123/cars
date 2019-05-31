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
$m_name = '缓存管理';
//允许操作
$ac_arr = array('del'=>'清除缓存');
//当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

if ($ac == 'del')
{
    $fzz = new fzz_cache;
	$fzz->clear_all();
	if( !($fzz->_isset( "common_cache" )) ){
		$fzz->set("common_cache",display_common_cache(),CACHETIME);
	}
}
else
{
    showmsg('非法操作',-1);
}
showmsg($ac_arr[$ac].('成功'),ADMIN_PAGE."?m=main");
?>