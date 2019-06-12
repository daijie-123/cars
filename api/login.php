<?php
if(!defined('APP_IN')) exit('Access Denied');

if(!postCheck()){
    splash('', 801);
}
$code = $_POST['code'];
$encrypted_data = $_POST['encryptedData'];
$iv = $_POST['iv'];

if (!$code || !$encrypted_data || !$iv) {
    splash('', 100);
}

$params = [
    'appid' => $commoncache['settings']['miniprogram_app_id'],
    'secret' => $commoncache['settings']['miniprogram_app_secret'],
    'grant_type' => 'authorization_code',
    'js_code' => $code,
];
$res = http_client('https://api.weixin.qq.com/sns/jscode2session', $params, 'GET');
$res = json_decode($res, true);
if ($res['errcode']) {
    splash('', $res['errcode'], $res['errmsg']);
}
$openid = $res['openid'];
$session_key = $res['session_key'];
/**
 * 3. 解密数据
 * 由于官方的解密方法不兼容 PHP 7.1+ 的版本
 * 这里弃用微信官方的解密方法
 * 采用推荐的 openssl_decrypt 方法（支持 >= 5.3.0 的 PHP）
 * @see http://php.net/manual/zh/function.openssl-decrypt.php
 */

$decrypt_data = openssl_decrypt(
    base64_decode($encrypted_data),
    'AES-128-CBC',
    base64_decode($session_key),
    OPENSSL_RAW_DATA,
    base64_decode($iv)
);
$weixin_user_info = json_decode($decrypt_data, true);
$weixin_user_info['sessionkey'] = $session_key;
if (empty($weixin_user_info['openId'])) {
    splash('', 100);
}

$_SESSION['WEIXIN_USER_DATA'] = $weixin_user_info;

$rs_user = $db->row_select_one('member', "open_id='{$weixin_user_info['openId']}'");
// 如果存在则登录,不存在则注册
if ($rs_user){
    $db->row_update('member', ['last_login_time'=>TIMESTAMP, 'last_login_ip'=>get_client_ip(),  'login_count' => $rs_user['login_count'] + 1], "id={$rs_user['id']}");

    $_SESSION['USER_ID'] = $rs_user['id'];
	$_SESSION['USER_NAME'] = $rs_user['username'];
}else{
	if($settings['version']==3){
		$post['aid'] = 0;
		$post['cid'] = 0;
    }
	$post['username'] = $weixin_user_info['openId'];
	$post['nicname'] = $weixin_user_info['nickName'];
    $post['regtime'] = TIMESTAMP;
	$post['company'] = '';
	$post['isdealer'] = 1;
	$post['ischeck'] = 1;
	$post['open_id'] = $weixin_user_info['openId'];

    $rs = $db->row_insert('member', $post);
	$insertid = $db->insert_id();
    if (!$rs) {
		splash('', 1);
	}else{
		$_SESSION['USER_ID'] = $insertid;
        $_SESSION['USER_NAME'] = $post['username'];
        // $rs_user = $db->row_select_one('member', "id=$insertid");
		$db->row_update('member', ['last_login_time' => TIMESTAMP, 'last_login_ip' => get_client_ip(), 'login_count' => 1], "id=$insertid");
	}
}

$rs_user = $db->row_select_one('member', "id='{$_SESSION['USER_ID']}'");

splash([
    'apitoken' => session_id(),
    'user_id' => $_SESSION['USER_ID'],
    'user_data' => $rs_user,
]);


