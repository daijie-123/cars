<?php
if (!defined('APP_IN')) exit('Access Denied');

$tpl -> assign('menustate', 7);

if(!isset($_COOKIE['city']) or empty($_COOKIE['city'])){
	$_COOKIE['city'] = 0;
}

// 幻灯片
$tpl -> assign('filmlist', get_filmstrip(3));

// 新闻
$picnewslist = array();

$picnewslist['1'] = get_picnews(13,12);
$picnewslist['2'] = get_picnews(14,6);

$tpl -> assign('newslist', $picnewslist);

$asklist = get_comnews(15, 3);
$tpl -> assign('asklist', $asklist);

$tpl -> display('default/' . $settings['templates'] . '/market.html');
?>