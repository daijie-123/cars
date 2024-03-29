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
// GPC过滤
function _addslashes($value) {
	if (is_array($value)) {
		foreach ($value as $key => $val) {
			$value[$key] = _addslashes($val);
		}
		return $value;
	}
	return addslashes($value);
}
// 去掉addslashes加的\
function _stripslashes($value) {
	if (is_array($value)) {
		foreach ($value as $k => $v) {
			$value[$k] = _stripslashes($v);
		}
		return $value;
	}
	return stripslashes($value);
}
// 过滤函数
function _filter($value) {
	if (is_array($value)) {
		foreach ($value as $key => $val) {
			$value[$key] = _filter($val);
		}
		return $value;
	}
	return str_replace(array('..\\', '../', './', '.\\'), '', trim($value));
}

function RpLine($str) {
	$str = str_replace("\r", "\\r", $str);
	$str = str_replace("\n", "\\n", $str);
	return $str;
}
// 返回模板地址
function tpl($tpl_name = '', $index_file = FILE) {
	global $mod;
	if (empty($tpl_name)) $tpl_name = $mod;
	return TPL_DIR . $index_file . '/' . $tpl_name . TPL_SUFFIX;
}
// 检查是否为合法post提交
function submitcheck($var) {
	if (empty($_POST[$var]) || $_SERVER['REQUEST_METHOD'] != 'POST') return false;
	if ((!empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))
		return true;
	else alert('错误的请求', -1);
}

function showmsg($msg, $url = -1, $is_frame = 0, $time = 2) {
	$addslashes = $is_frame ? '\\' : '';
	$parent = (empty($msg) && $is_frame) ? 'parent.' : '';
	if ($url == '-1') {
		$url = "javascript:history.go(-1);";
		$func = "history.go(-1)";
	} elseif ($url == 1) {
		$url = "javascript:location.href=location.href;";
		$func = "{$parent}window.location.href=location.href;";
	} else {
		$url = str_replace(array("\n", "\r"), '', $url);
		$func = "{$parent}window.location.href=\'$url\';";
	}
	if (empty($msg)) {
		$func = str_replace('\\', '', $func);
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
        <script language='javascript'>$func</script>";
		exit;
	}
	$str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <meta http-equiv=\"cache-control\" content=\"no-cache\">
    <title>提示信息</title>
    <style type=\"text/css\">
    <!--
    * {margin:0;padding:0;}
    body {text-align:center;font-family:Arial, Helvetica, sans-serif,\"宋体\";margin:0;paddig:0;}
    p {font-size: 12px;line-height:150%;background-color:#fff;padding:8px;}
    h1 {height:36px;line-height:36px;text-align:left;padding-left:15px;font-size:14px;font-weight:bold;color:#333;background:#efefef;}
    .noticebox{margin:0 auto;margin-top:80px;width:420px;padding:0;background:#fff;}
	.box_border {border:1px solid #ccc;background:#fff;}
    a:link {color: #0000FF;text-decoration: none;}
    a:visited {text-decoration: none;color: #003399;}
    a:hover {text-decoration: none;color: #0066FF;}
    a:active {text-decoration: none;color: #0066FF;}
	.msg{padding:20px 0;font-size:14px;}
	.msgp{padding:50px 0;}
	.notice{font-szie:12px;background:#efefef;color:#0068a6;}
    -->
    </style>
    </head>
    <body>
	<div class=\"noticebox\">
    <div class=\"box_border\">
    <h1>提示信息</h1>
    <p class=\"msgp\"><span class=\"msg\"> {$msg}</span> </p>
    <p class=\"notice\"><a href=\"{$url}\">如果{$time}秒后您的浏览器没有自动跳转，请点击这里</a></p>
	<script language=\"javascript\">setTimeout(\"{$func}\",{$time}*1000);<{$addslashes}/script>
    </div>
	</div>
    </body>";
	$str = str_replace(array("\r", "\n"), '', $str);
	if ($is_frame) {
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <script language='javascript'>parent.document.write('$str');</script>";
	} else echo $str;
	exit;
}

function showmsg2($msg, $url = -1, $is_frame = 0, $time = 2) {
	$addslashes = $is_frame ? '\\' : '';
	$parent = (empty($msg) && $is_frame) ? 'parent.' : '';
	if ($url == '-1') {
		$url = "javascript:history.go(-1);";
		$func = "history.go(-1)";
	} elseif ($url == 1) {
		$url = "javascript:location.href=location.href;";
		$func = "{$parent}window.location.href=location.href;";
	} else {
		$url = str_replace(array("\n", "\r"), '', $url);
		$func = "{$parent}window.location.href=\'$url\';";
	}
	if (empty($msg)) {
		$func = str_replace('\\', '', $func);
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
        <script language='javascript'>$func</script>";
		exit;
	}
	$str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <meta http-equiv=\"cache-control\" content=\"no-cache\">
    <title>提示信息</title>
    <style type=\"text/css\">
    <!--
    * {margin:0;padding:0;}
    body {text-align:center;font-family:Arial, Helvetica, sans-serif,\"宋体\";margin:0;paddig:0;}
    p {font-size: 12px;line-height:150%;background-color:#fff;padding:8px;}
    h1 {height:36px;line-height:36px;text-align:left;padding-left:15px;font-size:14px;font-weight:bold;color:#333;background:#efefef;}
    .noticebox{margin:0 auto;margin-top:80px;width:420px;padding:0;background:#fff;}
	.box_border {border:1px solid #ccc;background:#fff;}
    a:link {color: #0000FF;text-decoration: none;}
    a:visited {text-decoration: none;color: #003399;}
    a:hover {text-decoration: none;color: #0066FF;}
    a:active {text-decoration: none;color: #0066FF;}
	.msg{padding:20px 0;font-size:14px;}
	.msgp{padding:50px 0;}
	.notice{font-szie:12px;background:#efefef;color:#0068a6;}
    -->
    </style>
    </head>
    <body>
	<div class=\"noticebox\">
    <div class=\"box_border\">
    <h1>提示信息</h1>
    <p class=\"msgp\"><span class=\"msg\"> {$msg}</span> </p>
    <p class=\"notice\"><a href=\"{$url}\">如果{$time}秒后您的浏览器没有自动跳转，请点击这里</a></p>
	<script language=\"javascript\">setTimeout(\"{$func}\",{$time}*100);<{$addslashes}/script>
    </div>
	</div>
    </body>";
	$str = str_replace(array("\r", "\n"), '', $str);
	if ($is_frame) {
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <script language='javascript'>parent.document.write('$str');</script>";
	} else echo $str;
	exit;
}

function htmlshowmsg($msg) {
	$str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <meta http-equiv=\"cache-control\" content=\"no-cache\">
    <title>提示信息</title>
    <style type=\"text/css\">
    <!--
   * {margin:0;padding:0;}
    body {text-align:center;font-family:Arial, Helvetica, sans-serif,\"宋体\";margin:0;paddig:0;}
    p {font-size: 12px;line-height:150%;background-color:#fff;padding:8px;}
    h1 {height:36px;line-height:36px;text-align:left;padding-left:15px;font-size:14px;font-weight:bold;color:#333;background:#efefef;}
    .noticebox{margin:0 auto;margin-top:80px;width:420px;padding:0;background:#fff;}
	.box_border {border:1px solid #ccc;background:#fff;}
    a:link {color: #0000FF;text-decoration: none;}
    a:visited {text-decoration: none;color: #003399;}
    a:hover {text-decoration: none;color: #0066FF;}
    a:active {text-decoration: none;color: #0066FF;}
	.msg{padding:20px 0;font-size:14px;}
	.msgp{padding:50px 0;}
	.notice{font-szie:12px;background:#e4ecf7;color:#0068a6;}
    -->
    </style>
    </head>
    <body>
	<div class=\"noticebox\">
    <div class=\"box_border\">
    <h1>提示信息</h1>
    <p class=\"msgp\"><span class=\"msg\"> {$msg}</span> </p>
    </div>
	</div>
    </body>";
	$str = str_replace(array("\r", "\n"), '', $str);
	echo $str;
}
function mshowmsg($msg) {
	global $tpl;
	$tpl -> assign('msg', $msg);
	$tpl -> display('m/notice.html');
	exit;
}
// 使用JS弹出消息框
function alert1($msg, $url = '', $window = 'window', $display = 1) {
	$str = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	$str .= "<script language='javascript'>";
	if ($msg != '') $str .= "alert('$msg');";
	if ($url == '') $str .= '';
	elseif (is_numeric($url) && $url <= 0) $str .= "history.go($url);";
	elseif (is_numeric($url) && $url == 1) $str .= "{$window}.location.href=location.href";
	else $str .= "{$window}.location.href='$url';";
	$str .= '</script>';
	if (!$display) return $str;
	exit($str);
}
// 消息提示框
function redirect($msg, $url = '', $window = 'window', $display = 1) {
	$str = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	$str .= "<script language='javascript'>";
	if ($msg != '') $str .= "alert('$msg');";
	if ($url == '') $str .= '';
	elseif (is_numeric($url) && $url <= 0) $str .= "history.go($url);";
	elseif (is_numeric($url) && $url == 1) $str .= "{$window}.location.href=location.href";
	else $str .= "{$window}.location.href='$url';";
	$str .= '</script>';
	if (!$display) return $str;
	exit($str);
}
// 字符串截取
function _substr($str, $start = 0, $length, $charset = "utf-8", $suffix = '') {
	$string = substr($str, $start, $length);
	$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $string, $match);
	$slice = join('', array_slice($match[0], 0, $length));
	return strlen($str) > strlen($slice) ? $slice . $suffix : $slice;
}
// 获取post过来的值
function post() {
	$args = func_get_args();
	$value = array();
	while (list(, $key) = each ($args)) {
		if (isset($_POST[$key])) $value[$key] = $_POST[$key];
	}
	if (count($args) === 1) return empty($value) ? '' : array_shift($value);
	return $value;
}
// 获取get过来的值
function get() {
	$args = func_get_args();
	$value = array();
	while (list(, $key) = each ($args)) {
		if (isset($_GET[$key])) $value[$key] = $_GET[$key];
	}
	if (count($args) === 1) return empty($value) ? '' : array_shift($value);
	return $value;
}
// 不能为空
function can_not_be_empty($arr_not_empty, $arr_value) {
	foreach ($arr_not_empty as $k => $v) {
		if (empty($arr_value[$k])) showmsg($v, -1);
	}
}
// 获取客户端IP
function get_client_ip() {
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else $ip = "unknown";
	if (!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) return 'unknown';
	return($ip);
}
// 返回ID字符串
function return_str_id($id) {
	$str_id = '';
	if (is_array($id)) {
		foreach ($id as $v) {
			$str_id .= intval($v) . ',';
		}
		$str_id = substr($str_id, 0, -1);
	} else $str_id = $id;
	return $str_id;
}
// 创建多级目录
function createFolder($dir){
    return is_dir($dir) or (createFolder(dirname($dir)) and mkdir($dir,0777));
}
// 删除目录（包括目录下所有子目录和文件）
function _rmdir($path) {
	if (is_array($path)) {
		$arr = array_map('_rmdir', $path);
		if (in_array(false, $arr)) return false;
	} elseif (is_string($path)) {
		if (is_file($path)) return unlink($path);
		elseif (is_dir($path)) {
			if (!$op = opendir($path)) return false;
			if (substr($path, -1) != '/') $path .= '/';
			while (($file = readdir($op)) !== false) {
				if (!in_array($file, array('.', '..'))) _rmdir($path . $file);
			}
			closedir($op);
			rmdir($path);
		}
	} else return false;
	return true;
}
// 构造select
function select_make($id, $arr, $default_str = '', $default_val = '') {
	$option = $default_str ? "<option value='$default_val'>$default_str</option>\r\n" : '';
	foreach ($arr as $k => $v) {
		$selected = '';
		if ($k == $id) $selected = 'selected';
		$option .= "<option value='{$k}' $selected>{$v}</option>\r\n";
	}
	return $option;
}
// 占用空间大小格式化
function byte_format($size, $unit = 'B', $dec = 2) {
	$arr_unit = array("B", "KB", "MB", "GB", "TB", "PB");
	$arr_rev_unit = array_flip($arr_unit);
	if (!isset($arr_rev_unit[$unit])) return round($size, $dec) . ' ' . $unit;
	$pos = $arr_rev_unit[$unit];
	while ($size >= 1024) {
		$size /= 1024;
		$pos++;
	} while ($size < 1) {
		$size *= 1024;
		$pos--;
	}
	return round($size, $dec) . ' ' . $arr_unit[$pos];
}
// 是否邮件地址
function is_email($email) {
	return preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/', $email);
}

// 获取字符串首字母
function getinitial($s0)
{
	$s0 = trim($s0);
	$firstchar_ord=ord(strtoupper($s0{0}));
	if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return strtoupper($s0{0});
	$s = iconv('utf-8', 'gbk', $s0);
	$asc=ord($s{0})*256+ord($s{1})-65536;
	if($asc>=-20319 and $asc<=-20284)return "A";
	if($asc>=-20283 and $asc<=-19776)return "B";
	if($asc>=-19775 and $asc<=-19219)return "C";
	if($asc>=-19218 and $asc<=-18711)return "D";
	if($asc>=-18710 and $asc<=-18527)return "E";
	if($asc>=-18526 and $asc<=-18240)return "F";
	if($asc>=-18239 and $asc<=-17923)return "G";
	if($asc>=-17922 and $asc<=-17418)return "H";
	if($asc>=-17417 and $asc<=-16475)return "J";
	if($asc>=-16474 and $asc<=-16213)return "K";
	if($asc>=-16212 and $asc<=-15641)return "L";
	if($asc>=-15640 and $asc<=-15166)return "M";
	if($asc>=-15165 and $asc<=-14923)return "N";
	if($asc>=-14922 and $asc<=-14915)return "O";
	if($asc>=-14914 and $asc<=-14631)return "P";
	if($asc>=-14630 and $asc<=-14150)return "Q";
	if($asc>=-14149 and $asc<=-14091)return "R";
	if($asc>=-14090 and $asc<=-13319)return "S";
	if($asc>=-13318 and $asc<=-12839)return "T";
	if($asc>=-12838 and $asc<=-12557)return "W";
	if($asc>=-12556 and $asc<=-11848)return "X";
	if($asc>=-11847 and $asc<=-11056)return "Y";
	if($asc>=-11055 and $asc<=-10247)return "Z";
	return 'Others';
}

/**
 * 生成静态分页函数
 */
function getpagelist($action, $pagecount, $page, $result_num, $page_size) {
	$pagelist = $pagecountlist = "";
	$pagelist .= "<div class=\"page_list\">";
	if ($pagecount > 1) {
		$start = (ceil($page / 10)-1) * 10;
		$end = ceil($page / 10) * 10 + 1;
		if ($start <= 0) $start = 1;
		if ($end >= $pagecount) $end = $pagecount;
		for($i = $start;$i <= $end;$i++) {
			if ($page == $i)
				$pagecountlist .= "<span class=\"xspace-current\">" . $i . "</span>";
			else
				$pagecountlist .= "<a href=" . $action . "_" . sprintf("%02d", $i) . ".html>" . $i . "</a>";
		}
	} else {
		$pagecountlist .= "<span class=\"xspace-current\">1</span>";
	}
	$pagelist .= "Page：";
	if ($page > 1) {
		$pagelist .= "<a href=" . $action . "_" . sprintf("%02d", ($page-1)) . ".html class='prepage'></a>";
	}

	$pagelist .= $pagecountlist . "";

	if ($page < $pagecount) {
		$pagelist .= "<a href=" . $action . "_" . sprintf("%02d", ($page + 1)) . ".html class='nextpage'></a>";
	}
	$pagelist .= "</div>";
	return $pagelist;
}
// PHP COOKIE设置函数立即生效，支持数组
function setMyCookie($var, $value = '', $time = 0, $path = '', $domain = '') {
	$_COOKIE[$var] = $value;
	if (is_array($value)) {
		foreach($value as $k => $v) {
			setcookie($var . '[' . $k . ']', $v, $time, $path, $domain);
		}
	} else {
		setcookie($var, $value, $time, $path, $domain);
	}
}

// 获取访客ip
function getFirstIpFromList($ip) {
	$p = strpos($ip, ',');
	if ($p !== false) {
		return (substr($ip, 0, $p));
	} else {
		return ($ip);
	}
}
function getIp() {
	if (isset($_SERVER['HTTP_CLIENT_IP']) && ($ip = getFirstIpFromList($_SERVER['HTTP_CLIENT_IP'])) && strpos($ip, "unknown") === false && getHost($ip) != $ip) {
		return $ip;
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($ip) && !empty($ip) && strpos($ip, "unknown") === false && getHost($ip) != $ip) {
		return $ip;
	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && strlen(getFirstIpFromList($_SERVER['HTTP_CLIENT_IP'])) != 0) {
		return getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']);
	} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen (getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR'])) != 0) {
		return getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']);
	} else {
		return getFirstIpFromList($_SERVER['REMOTE_ADDR']);
	}
}
function get_cityname($ip) {
	$arr_ip = convertIp($ip);
	$cityname = $arr_ip['city'];
	if($cityname=="unkown") $cityname="";
	return $cityname;
}

function convertIp($ip, $url = 'data/ipdata.dat') {
	if (!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
		return '';
	}

	if ($fd = @fopen($url, 'rb')) {
		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if ($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if ($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;

		$BeginNum = 0;
		$EndNum = $ipAllNum;

		while ($ip1num > $ipNum || $ip2num < $ipNum) {
			$Middle = intval(($EndNum + $BeginNum) / 2);

			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if (strlen($ipData1) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip1num = implode('', unpack('L', $ipData1));
			if ($ip1num < 0) $ip1num += pow(2, 32);

			if ($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}

			$DataSeek = fread($fd, 3);
			if (strlen($DataSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if (strlen($ipData2) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if ($ip2num < 0) $ip2num += pow(2, 32);

			if ($ip2num < $ipNum) {
				if ($Middle == $BeginNum) {
					fclose($fd);
					return 'Unknown';
				}
				$BeginNum = $Middle;
			}
		}

		$ipFlag = fread($fd, 1);
		if ($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if (strlen($ipSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}

		if ($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if (strlen($AddrSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipFlag = fread($fd, 1);
			if ($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if (strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			} while (($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;

			$AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
			fseek($fd, $AddrSeek);

			while (($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;
		} else {
			fseek($fd, -1, SEEK_CUR);
			while (($char = fread($fd, 1)) != chr(0))
			$ipAddr1 .= $char;

			$ipFlag = fread($fd, 1);
			if ($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if (strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			} while (($char = fread($fd, 1)) != chr(0))
			$ipAddr2 .= $char;
		}
		fclose($fd);

		if (preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1,$ipAddr2";
		$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
		$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
		if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
			$ipaddr = '';
		}
		if (count(explode(',', $ipaddr)) > 0)
			$ipaddrArr = explode(',', $ipaddr);

		$Ripaddr[country] = $ipaddrArr[0] == '' ? 'unknown' : $ipaddrArr[0];
		$Ripaddr[province] = $ipaddrArr[1] == '' ? 'unknown' : $ipaddrArr[1];
		$Ripaddr[city] = $ipaddrArr[2] == '' ? 'unknown' : $ipaddrArr[2];
		$Ripaddr[address] = $ipaddrArr[3] == '' ? 'unknown' : $ipaddrArr[3];
		return $Ripaddr;
	} else {
		$datadir = $url . './ipdata/';
		$ip_detail = explode('.', $ip);
		if (file_exists($datadir . $ip_detail[0] . '.txt')) {
			$ip_fdata = @fopen($datadir . $ip_detail[0] . '.txt', 'r');
		} else {
			if (!($ip_fdata = @fopen($datadir . '0.txt', 'r'))) {
				return 'Invalid IP data file';
			}
		}
		for($i = 0; $i <= 3; $i++) {
			$ip_detail[$i] = sprintf('%03d', $ip_detail[$i]);
		}
		$ip = join('.', $ip_detail);
		do {
			$ip_data = fgets($ip_fdata, 200);
			$ip_data_detail = explode('|', $ip_data);
			if ($ip >= $ip_data_detail[0] && $ip <= $ip_data_detail[1]) {
				fclose($ip_fdata);
				return $ip_data_detail[2] . $ip_data_detail[3];
			}
		} while (!feof($ip_fdata));
		fclose($ip_fdata);
		return '';
	}
}

//判断是否为真实图片
function isImage($filename){
    $types = '|.gif|.jpeg|.png|.bmp|';//定义检查的图片类型
    if(file_exists($filename)){
        $info = getimagesize($filename);
        $ext = image_type_to_extension($info['2']);
        return stripos($types,$ext);
    }else{
        return false;
    }
}

function splash($data = '', $code = 0, $msg = '')
{
    $code_msg = [
        0 => [
            'desc' => '成功',
        ],
        1 => [
            'desc' => '未知错误',
        ],
        2 => [
            'desc' => '服务暂不可用',
        ],
        3 => [
            'desc' => '未知的方法',
        ],
        4 => [
            'desc' => '接口调用次数已达到设定的上限',
        ],
        5 => [
            'desc' => '请求来自未经授权的IP地址',
        ],
        6 => [
            'desc' => '无权限访问该用户数据',
        ],
        7 => [
            'desc' => '来自该refer的请求无访问权限',
        ],
        8 => [
            'desc' => '逻辑错误',
        ],
        100 => [
            'desc' => '请求参数无效',
        ],
        104 => [
            'desc' => '无效签名',
        ],
        110 => [
            'desc' => '无效的access token',
        ],
        801 => [
            'desc' => '无效的操作方法',
        ],
    ];
    header('Content-Type: application/json;charset=UTF-8');

    echo json_encode([
        'code' => $code,
        'msg' => $msg ?: $code_msg[$code]['desc'],
        'data' => $data,
    ]);

    exit;
}

function api_session_start()
{
    ini_set('session.gc_maxlifetime', 7 * 24 * 60 * 60 ); // 设置为和“session.cookie_lifetime”一样的时间；(个人理解：设定session有效期)
    // ini_set("session.cookie_lifetime","60"); //这个代表SessionID在客户端Cookie储存的时间，默认是0，代表浏览器一关闭
    // jj($_SERVER);
    $_COOKIE[API_SESSION_NAME] = $_SERVER['HTTP_' . strtoupper(API_SESSION_NAME)];
    session_name(API_SESSION_NAME);
    session_start();
}

function clear_all_fzz_cache(){
    $fzz = new fzz_cache;
    $fzz->clear_all();
    if( !($fzz->_isset( "common_cache" )) ){
		$fzz->set("common_cache", display_common_cache(), CACHETIME);
	}
}

function http_client($url, $params, $method = 'GET', $header = array(), $multi = false){
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header
    );

    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            splash('', 1, '不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) splash('', 1, '请求发生错误：' . $error);
    return  $data;
}

// 检查是否为post提交
function postCheck() {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') return false;
	// if ((!empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))
    return true;
}
// 检查是否为get提交
function getCheck() {
	if ($_SERVER['REQUEST_METHOD'] != 'GET') return false;
	// if ((!empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))
    return true;
}

// 不能为空
function api_can_not_be_empty($arr_not_empty, $arr_value) {
	foreach ($arr_not_empty as $k => $v) {
		if (!isset($arr_value[$k]) || ($arr_value[$k] == '')) splash('', 100, $v);
	}
}

function jj($a) {
    header('Content-Type: text/html;charset=utf-8');
    if(is_array($a)){
        echo json_encode($a);
    }elseif(is_null($a)){
        echo date('Y-m-d H:i:s');
    }else{
        echo $a;
    }
	exit;
}

function request_method_check($ac, $ac_get_arr = [], $ac_post_arr = []){
    // 当前操作
    if(empty($ac)) splash('', 801);
    if(postCheck() && !isset($ac_post_arr[$ac])){
        splash('', 801);
    }else if(getCheck() && !isset($ac_get_arr[$ac])){
        splash('', 801);
    }
}

function is_https() {
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return true;
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

function upload_url_modify($path, $size = 'l')
{
    if(substr($path, 0, 1) !== '/'){
        $path = '/' . $path;
    }

    if($size == 'l'){
        return WEB_DOMAIN . $path;
    }elseif($size == 's'){
        $pic = explode(".", $path);
        return WEB_DOMAIN . $pic[0] . "_small" . "." . $pic[1];
    }
    return $path;
}

function brand_url_modify($path)
{
    if(!$path){
        return '';
    }
    if(substr($path, 0, 1) !== '/'){
        $path = '/' . $path;
    }
    return upload_url_modify('/upload/brand' . $path);
}