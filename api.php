<?php
include('api.common.inc.php');
// include('index/page.php');

$m = isset($_REQUEST['m']) && trim($_REQUEST['m']) ? trim($_REQUEST['m']) : 'index';

if (!file_exists('api/'.$m.'.php')) splash('', 100);

include('api/'.$m.'.php');