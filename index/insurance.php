<?php
if (!defined('APP_IN')) exit('Access Denied');

$data = $db->row_select('settings',"k='version'");
$version=$data[0]['v'];
$tpl -> assign('version', $version);

$tpl -> assign('menustate', 6);
if($_SESSION[USER_ID])
{
	$tpl -> assign('session', $_SESSION);
	
}

if(submitcheck('tablename'))
{
	$post[loan_amount]=$_POST[loan_amount];
	$post[detail]=$_POST[detail];
	$post[times]=time();
	$post[uid]=$_SESSION[USER_ID];
	$db -> row_insert('loan',$post);
	showmsg('您的信息已提交成功！', -1);
}
else
{	
	$tpl -> display('default/' . $settings['templates'] . '/insurance.html');
}

?>