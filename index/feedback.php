<?php
if (!defined('APP_IN')) exit('Access Denied');

$tpl -> assign('menustate', 10);

if (submitcheck('action')) {

	$arr_not_empty = array('f_username' => '请填写姓名', 'f_tel' => '请填写手机号', 'f_detail' => '请填写留言');

	can_not_be_empty($arr_not_empty, $_POST); 

	$post = post('f_username','f_tel','f_detail');
	$post['f_username'] = trim($post['f_username']);
	$post['f_tel'] = trim($post['f_tel']);
	$post['f_detail'] = htmlspecialchars(trim($post['f_detail']));

	$post['f_addtime'] = TIMESTAMP;

	$db -> row_insert('feedback', $post);

	showmsg('您的留言已提交成功！', -1);
}
?>