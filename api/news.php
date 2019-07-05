<?php
if (!defined('APP_IN')) exit('Access Denied');

$ac_post_arr = [];
$ac_get_arr = ['news_list' => '新闻列表', 'news_details' => '新闻详情'];
// 当前操作
$ac = $_REQUEST['a'];
request_method_check($ac, $ac_get_arr, $ac_post_arr);

if ($ac == "news_list") {
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 20;

    $where = " 1=1";
    include(INC_DIR . 'Page.class.php');
    $Page = new Page($db->tb_prefix . 'news', $where, 'n_id,n_title,n_addtime,n_pic', $page_size, 'n_addtime desc');
    $list = $Page->get_data();
    foreach ($list as &$news) {
        if ($news['n_pic']) {
            $news['n_pic'] = upload_url_modify($news['n_pic']);
        }
    }
    $total_num = $Page->total_num;
    $current_page = $Page->page;
    $total_page = $Page->total_page;
    splash([
        'data_list' => $list,
        'total_num' => $total_num,
        'current_page' => $current_page,
        'total_page' => $total_page,
    ]);
} elseif ($ac == "news_details"){
    $arr_not_empty = [
        'id' => 'id不能为空',
    ];
    api_can_not_be_empty($arr_not_empty, $_GET);
    $data = $db->row_select_one('news', "n_id={$_GET['id']}","n_id,n_title,n_addtime,n_hits,n_author,n_info");
    if (!$data) {
        splash('', 100);
    }
    $data['n_info'] = htmlspecialchars_decode($data['n_info']);
    $pregRule = "/(<[img|IMG].*?src=[\'|\"])(.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))([\'|\"].*?[\/]?>)/";

    $data['n_info'] = preg_replace($pregRule, '${1}' . WEB_DOMAIN . '${2}${3}', $data['n_info']);

    splash($data);
}
