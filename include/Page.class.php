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
class Page {
	// 总记录条数
	var $total_num; 
	// 每页显示的条目数
	var $page_size = 10; 
	// 总的页数
	var $total_page; 
	// 当前页数
	var $page = 1; 
	// 查询结果数据
	var $data;
	/**
	 * ------------------------------
	 * 构造函数
------------------------------
	 * 
	 * @param string $tbname 要操作的表名
	 * @param string $where 定位条件
	 * @param string $field 要查询的字段
	 * @param string $pageSize 每页显示数量
	 * @param string $orderBy 排序方式
	 */
	function Page($tbname, $where = '1=1', $field = '*', $page_size = 20, $order_by = '', $group_by = '') {
		!mysql_ping() && exit('mysql can not connect!');

		if (!empty($page_size)) $this -> page_size = $page_size; 
		// 获取总记录条数
		$sql = "SELECT count(*) as row_num FROM $tbname WHERE $where";
		$row_num = mysql_fetch_array(mysql_query($sql));
	    $this -> total_num = $row_num['row_num'];
		//$this -> total_page = ceil($this -> total_num / $page_size); 
		// 当前page
		$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
		$this -> page = ($page < $this -> total_page || $this -> total_page == 0) ? $page : $this -> total_page; 
		// 计算查询的起始值
		$start = ($this -> page - 1) * $page_size; 
		// 查询结果

		if($page_size==0){
			$sql = "SELECT $field FROM $tbname WHERE $where" .  ($group_by ? ' GROUP BY ' . $group_by : '').($order_by ? ' ORDER BY ' . $order_by : '') ;
		}else{
			$this -> total_page = ceil($this -> total_num / $page_size); 
			$sql = "SELECT $field FROM $tbname WHERE $where" . ($group_by ? ' GROUP BY ' . $group_by : '').($order_by ? ' ORDER BY ' . $order_by : '') . " LIMIT $start,$this->page_size";
		}

		$result = mysql_query($sql);
		
		$data = array();
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = $row;
		} 
		$this -> data = $data;
	} 
	/**
	 * ------------------------------
	 * 获得查询数据
	 * ------------------------------
	 * 
	 * @return array 
	 */
	function get_data() {
		return $this -> data;
	} 

	/**
	 * ------------------------------
	 * 获得除了页码部分的URL
	 * ------------------------------
	 * 
	 * @return string 
	 */
	function get_url() {
		$arr_url = parse_url($_SERVER['REQUEST_URI']);
		if (!isset($arr_url['query'])) $arr_url['query'] = '';
		parse_str($arr_url['query'], $arr_get);
		if (isset($arr_get['page'])) unset($arr_get['page']);
		$str_url = '';
		foreach ($arr_get as $k => $v) {
			$str_url .= $k . '=' . $v . '&';
		} 
		return $arr_url['path'] . '?' . substr($str_url, 0, -1) . '&page=';
	} 

	/**
	 * ------------------------------
	 * <select>方式页面跳转框
------------------------------
	 * 
	 * @return string 
	 */
	function button_select() {
		$str = "<select onchange=\"location.href='" . $this -> get_url() . "'+this.value\">";
		for ($i = 1; $i <= $this -> total_page; $i++) {
			$selected = ($this -> page == $i) ? 'selected' : '';
			$str .= "<option value=$i $selected>$i</option>";
		} 
		return $str .= '</select>';
	} 

	/**
	 * ------------------------------
	 * 普通页码条
	 * ------------------------------
	 * 
	 * @param int $totalNum 是否显示总页数，0为不显示（默认显示）
	 * @param int $correntNum 是否显示当前页数，0为不显示（默认显示）
	 * @return string 
	 */
	function button_basic($total_num = 1, $current_page = 1, $first_and_last = 1) {
		$url = $this -> get_url();
		$str = '';
		$str .= ($total_num ? '<span>共' . $this -> total_num . '条</span>' : '');
		$str .= "<span>第" . ($current_page ? $this -> page . '/' . $this -> total_page . '页</span>' : '');
		$str .= ($first_and_last ? ($this -> total_page > 1 ? "<a href='{$url}1'>首页</a>" : '') : '');
		$str .= ($this -> page > 1 ? "<a href='$url" . ($this -> page-1) . "'>上一页</a>" : '');
		$str .= ($this -> page + 1 <= $this -> total_page ? "<a href='$url" . ($this -> page + 1) . "'>下一页</a>" : '');
		$str .= ($first_and_last ? ($total_num > 1 ? "<a href='{$url}{$this->total_page}'>尾页</a>" : "<a href='{$url}{$this->total_page}'>尾页</a>") . "" : '');
		return $str;
	} 
	// ################################伪静态分页#########################################
	/**
	 * ------------------------------
	 * 替换地址
	 * ------------------------------
	 * 
	 * @param int $page 当前页
	 * @param int $url 地址
	 * @return string 
	 */
	function page_replace($page, $url) {
		return str_replace("{page}", $page, $url);
	} 
	/**
	 * ------------------------------
	 * 伪静态分页
------------------------------
	 * 
	 * @param string $url 地址
	 * @return string 
	 */
	function button_basic_html($url, $total_num = 1, $current_page = 1, $first_and_last = 1) {
		$str = "";
		$str .= "<span>" . ($total_num ? '共' . $this -> total_num . '条' : '') . "</span>";
		$str .= "<span>" . ($current_page ? $this -> page . '/' . $this -> total_page . '页' : '') . "</span>";
		$str .= ($first_and_last ? ($this -> total_page > 1 ? "<a href='" . $this -> page_replace(1, $url) . "'>首页</a>" : '<a>首页</a>') . "" : '');
		for ($i = 1; $i <= $this -> total_page; $i++) {
			$selected = ($this -> page == $i) ? 'selected' : '';
			$str .= "<a href='" . $this -> page_replace($i, $url) . "'\" class=$selected>$i</a>";
		} 
		$str .= ($first_and_last ? ($total_num > 1 ? "<a href='" . $this -> page_replace($this -> total_page, $url) . "'>尾页</a>" : "<a href='" . $this -> page_replace($this -> total_page, $url) . "'>尾页</a>") . "" : '');
		return $str;
	} 

	/**
     * ------------------------------
     * 按页码分页
     * ------------------------------
     * @param  string $url 地址
     * @return string
     */
	function button_basic_num($total_num=1,$current_page=1,$first_and_last=1)
    {
		$url = $this->get_url();
        $str = "";
		$str .= "<span>".($total_num ? '共'.$this->total_num.'条' : '')."</span>";
		$str .= ($first_and_last ? ($this->total_page > 1 ? "<a href='".$url."1'>首页</a>" : '<a>首页</a>')."" : '');
		
		if($this->total_page - $this->page >= 10){
			$allpage = $this->page+10;
		}
		else{
			$allpage = $this->total_page;
		}
        for ($i = ($this->page>5?($this->page-5):1); $i <= (($this->page+5)>=$allpage?$allpage:($this->page+5)); $i++)
        {
			$selected = ($this->page == $i) ? 'selected' : '';
            $str .= "<a href='".$url.$i."' class=$selected>$i</a>";
        }
		 $str .= ($first_and_last ? ($total_num > 1 ? "<a href='".$url."$this->total_page'>尾页</a>" : "<a href='".$url.$this->total_page."'>尾页</a>")."" : '');
        return $str;
    }

	function api_page() {
		$listpage = array('currentPage' => $this -> page, 'TotalCount' => $this -> total_num, 'pageSize' => $this -> page_size,);
		return $listpage;
	} 

	/**
	 * ------------------------------
	 * 手机版页码条
	 * ------------------------------
	 * 
	 * @param int $totalNum 是否显示总页数，0为不显示（默认显示）
	 * @param int $correntNum 是否显示当前页数，0为不显示（默认显示）
	 * @return string 
	 */
	function mobilephone_button_basic($total_num = 1, $current_page = 1, $first_and_last = 1) {
		$url = $this -> get_url();
		$str = "";
		$str .= ($this -> page > 1 ? "<a href='$url" . ($this -> page-1) . "'>上一页</a>" : "<a href='javascript:void(0);'>上一页</a>");
		$str .= "<span>" . ($current_page ? $this -> page . '/' . $this -> total_page : '');
		$str .= " <select onchange=\"location.href='" . $this -> get_url() . "'+this.value\" style='border:none'>";
		for ($i = 1; $i <= $this -> total_page; $i++) {
			$selected = ($this -> page == $i) ? 'selected' : '';
			$str .= "<option value=$i $selected>$i</option>";
		} 
		$str .= '</select></span>';
		$str .= ($this -> page + 1 <= $this -> total_page ? "<a href='$url" . ($this -> page + 1) . "'>下一页</a>" : "<a href='javascript:void(0);'>下一页</a>");
		return $str;
	} 
} 
?>