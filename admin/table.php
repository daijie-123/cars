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
$mod_name = '数据库备份管理';
// 允许操作
$ac_arr = array('list' => '数据库列表','viewinfo' => '查看表结构', 'opimize' => '优化表', 'repair' => '修复表', 'bak' => '数据备份');
// 当前操作
$ac = isset($_REQUEST['a']) && isset($ac_arr[$_REQUEST['a']]) ? $_REQUEST['a'] : 'default';

$tpl -> assign('mod_name', $mod_name);
$tpl -> assign('ac_arr', $ac_arr);
$tpl -> assign('ac', $ac);

$bkdir = "backdata";
$cfg_cookie_encode = '6ntCFh9vn3Ctb2mewtQ4FU3UBemxg';

$dirname = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
$tablename = isset($_REQUEST['tablename']) ? $_REQUEST['tablename'] : '';

if($ac == "list"){
	// 获取系统存在的表信息
	$data = $db -> show_tables($db_config['DB_NAME']);
	$tpl -> assign('tablelist', $data);

	$mysql = $db -> get_server_info();
	$tpl -> assign('mysql', $mysql);

	for($i = 0; $i < count($data); $i++) {
		$str1 = substr($data[$i], 7);
		$count[$i] = $db -> row_count($str1);
		$tpl -> assign('count', $count);
	} 
	$tpl -> display('admin/table_list.html');
}
elseif ($ac == "viewinfo") { // 查看表结构
	echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
	if (empty($tablename)) {
		echo "没有指定表名！";
	} else {
		$rs = $db -> viewinfo_table($tablename);
		while ($table_def = mysql_fetch_row($rs))
		for ($i = 1; $i < count($table_def); $i++)
		echo $table_def[$i];
	} 
	echo '</xmp>';
	exit();
} else if ($ac == "opimize") { // 优化表
	echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
	if (empty($tablename)) {
		echo "没有指定表名！";
	} else {
		$rs = $db -> operation_table($tablename, 'OPTIMIZE');
		if ($rs) {
			echo "执行优化表： $tablename  OK！";
		} else {
			echo "执行优化表: $tablename  失败，原因是：" . $db -> GetError();
		} 
	} 
	echo '</xmp>';
	exit();
} else if ($ac == "repair") { // 修复表
	echo "[<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'><u>关闭</u></a>]\r\n<xmp>";
	if (empty($tablename)) {
		echo "没有指定表名！";
	} else {
		$rs = $db -> operation_table($tablename, 'repair');
		if ($rs) {
			echo "修复表： $tablename  OK！";
		} else {
			echo "修复表： $tablename  失败，原因是：" . $db -> GetError();
		} 
	} 
	echo '</xmp>';
	exit();
} else if ($ac == "bak") { // 备份数据库
	if (empty($_POST['bulkid'])) showmsg('没有选中任何项', -1); 
	// 初始化使用到的变量
	foreach($_POST['bulkid'] as $key => $value) {
		$tablearr .= $value . ",";
	} 

	$tablearr = substr($tablearr, 0, -1);
	$tables = explode(',', $tablearr);
	$dh = dir($bkdir);
	while ($filename = $dh -> read()) {
		if (!preg_match("#txt$#", $filename)) {
			continue;
		} 
		$filename = $bkdir . "/$filename";
		if (!is_dir($filename)) {
			unlink($filename);
		} 
	} 
	$dh -> close();
	$mysql_version = $db -> get_server_info();
	foreach($tables as $t) {
		$bkfile = $bkdir . "/$t" . substr(md5(time() . mt_rand(1000, 5000) . $cfg_cookie_encode), 0, 16) . ".txt";
		$fp = fopen($bkfile, "w");
		fwrite($fp, "DROP TABLE IF EXISTS `$t`;\r\n\r\n");
		$row = $db -> row_query("SHOW CREATE TABLE " . $t);
		$a = $db -> row_query("select * from " . $t);
		$intable = "INSERT INTO " . $t . " VALUES(";
		$fsd = $db -> fields_count($t)-1;
		foreach ($a as $key => $value) {
			foreach ($value as $k => $var)
			$intable .= "'" . RpLine(addslashes($var)) . "',";
		} 
		$intable = substr($intable, 0, -1);
		$intable .= ");\r\n"; 
		// 去除AUTO_INCREMENT
		$row[0]['Create Table'] = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "", $row[0]['Create Table']); 
		// 4.1以下版本备份为低版本
		if ($datatype == 4.0 && $mysql_version > 4.0) {
			$eng1 = "#ENGINE=MyISAM[ \r\n\t]{1,}DEFAULT[ \r\n\t]{1,}CHARSET=" . 'utf8' . "#i";
			$tableStruct = preg_replace($eng1, "TYPE=MyISAM", $row[0]['Create Table'] . "\r\n\r\n" . $intable);
		} 
		// 4.1以下版本备份为高版本
		else if ($datatype == 4.1 && $mysql_version < 4.1) {
			$eng1 = "#ENGINE=MyISAM DEFAULT CHARSET={'utf8'}#i";
			echo $tableStruct;
			$tableStruct = preg_replace("TYPE=MyISAM", $eng1, $row[0]['Create Table'] . "\r\n\r\n" . $intable);
		} 
		// 普通备份
		else {
			$tableStruct = $row[0]['Create Table'] . "\r\n\r\n" . $intable;
		} 
		fwrite($fp, '' . $tableStruct . "\r\n\r\n");
	} 
	fclose($fp);
	showmsg($ac_arr[$ac] . ($tableStruct ? '成功' : '失败'), ADMIN_PAGE . "?m=$m&a=list&page=" . $page_g);
}  
?>