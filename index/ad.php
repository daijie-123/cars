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
if(!defined('APP_IN')) exit('Access Denied');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id!=0){
	$adstr = "";
	$data = $db->row_select_one('ad','id='.$id);
	if($data['isshow']==1 and ($data['endtime']-time()>=0)){
		if($data['adtype']==1){
			$adstr = "<a href='".$data['url']."' title='".$data['url_note']."' target='_blank'><img src='".$data['pic']."' alt='".$data['url_note']."' width=".$data['picwidth']." height=".$data['picheight']."/></a>";
		}
		else{
			$adstr = "<a href='".$data['url']."' title='".$data['url_note']."' target='_blank'>".$data['name']."</a>";
		}
	}
	echo "document.write(\"".$adstr."\");";
}
?>