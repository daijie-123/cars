<?php
if (!defined('APP_IN')) exit('Access Denied');

if (submitcheck('action'))
{
    //不可为空
    $arr_not_empty = array('p_brand'=>'请选择品牌','p_subbrand'=>'请选择子品牌','p_contact_tel'=>'请输入您的联系电话');
    can_not_be_empty($arr_not_empty,$_POST);


	$post = post('p_brand','p_subbrand','p_subsubbrand','p_year','p_month','p_kilometre','p_contact_tel');

	if(isset($_COOKIE['city'])){
		$post['cid'] = $_COOKIE['city'];
		if($_COOKIE['city']!=0){
			$area = $db->row_select_one('area','id='.$_COOKIE['city']);
			$post['aid'] = $area['parentid'];
		}
	}

    $post['p_brand'] =intval($post['p_brand']);
    $post['p_subbrand'] =intval($post['p_subbrand']);
	$post['p_subsubbrand'] = intval($_POST['p_subsubbrand']);
	$post['p_allname'] = "";

	if(!empty($post['p_subbrand'])){
		$bname = $commoncache['brandlist'][$post['p_brand']];
		$subbname = arr_brandname($post['p_subbrand']);
		$compareword = strstr($subbname,$bname);
		if(!empty($compareword)){
			$post['p_allname'] .= arr_brandname($post['p_subbrand']);
		}
		else{
			$post['p_allname'] .= $bname ." ".arr_brandname($post['p_subbrand']);
		}
	}
	if(!empty($post['p_subsubbrand'])){
		$post['p_allname'] .= " ".arr_brandname($post['p_subsubbrand']);
	}

	$post['p_kilometre'] =trim($post['p_kilometre']);
	$post['p_year'] = intval($post['p_year']);
	$post['p_month'] = intval($post['p_month']);
	$post['p_contact_tel']  = $post['p_contact_tel'];

	$post['p_addtime'] = TIMESTAMP;

    $db->row_insert('assesscars',$post);

	$lastprice = rapidasscars($post['p_subsubbrand'],$post['p_kilometre'],$post['p_year'],$post['p_month']);
	$tpl -> assign('post', $post);
	$tpl -> assign('lastprice', $lastprice);

	$tpl -> display('default/' . $settings['templates'] . '/assessresult.html');
}
else{
	$tpl -> assign('menustate', 4);

	//最新评估列表
	$assesslist = $db->row_select('assesscars','p_price!=0');
	foreach($assesslist as $key => $value){
		$assesslist[$key]['p_assesstime'] = date('Y-m-d H:i:s', $value['p_assesstime']);
		if($settings['version']==3){
			if($value['aid']!=0){
				$assesslist[$key]['province']=$commoncache['provincelist'][$value['aid']];
			}
			if($value['cid']!=0){
				$assesslist[$key]['city']=$commoncache['citylist'][$value['cid']];
			}
		}
	}
	$tpl -> assign('assesslist', $assesslist);
	$tpl -> display('default/' . $settings['templates'] . '/assess.html');
}
?>