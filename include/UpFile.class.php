<?php
/**
 * 本软件版权归作者所有,在投入使用之前注意获取许可
作者：北京市普艾斯科技有限公司
 * 项目：simcms_锐车1.0
 * 电话：010-58480317
 * Q  Q: 228971357
 * 网址：http://www.simcms.net
 * simcms.net保留全部权力，受相关法律和国际公约保护，请勿非法修改、转载、散播，或用于其他赢利行为，并请勿删除版权声明。
 */
class UpFile {
	/**
	 * 上传目录
	 * 
	 * @var string 
	 */
	private $upPath = '';
	/**
	 * 允许上传文件的最大值(默认1M)
	 * 
	 * @var int 
	 */
	private $maxSize;
	/**
	 * 默认允许上传的文件的mime类型
	 * 
	 * @var array 
	 */
	private $allowType = array();
	/**
	 * 上传后的文件信息
	 * 
	 * @var array 
	 */
	private $saveInfo = array();
	/**
	 * 上传框名称
	 * 
	 * @var string 
	 */
	private $inputName = '';
	/**
	 * 上传文件信息
	 * 
	 * @var array 
	 */
	private $uploadInfo = array();
	/**
	 * 是否覆盖同名文件（手动名命时有效）
	 * 
	 * @var bool 
	 */
	private $overWrite = false;

	/**
	 * -----------------------------
	 * 构造函数
	 * 
	 * @param string $upPath 上传路径
	 * @param int $maxSize 上传文件最大值(K)
	 * @param array $allowType 允许上传的文件mine类型=>后缀
	 */
	function __construct($upPath = '', $maxSize = 1024, $allowType = '') {
		$this -> upPath = ($upPath == '') ? $this -> defaultUpPath() : $this -> pathFormat($upPath);
		$this -> maxSize = $maxSize * 1024;
		$this -> allowType = ($allowType == '') ? $this -> defaultAllowType() : $allowType;
	} 
	// 默认上传路径
	private function defaultUpPath() {
		return $this -> pathFormat("upLoad/" . date('Y-m') . "/" . date('d') . '/');
	} 
	// 默认允许上传类型
	private function defaultAllowType() {
		// return array('image/jpeg'=>'jpg','image/pjpeg'=>'jpg','image/gif'=>'gif','image/x-png'=>'png',
		// 'image/png'=>'png','application/x-shockwave-flash'=>'swf');
		return array('.gif', '.jpg', '.jpeg', '.png');
	} 
	// 设置上传路径
	public function setUpPath($path) {
		$this -> upPath = $this -> pathFormat($path);
	} 
	// 设置允许文件大小
	public function setMaxSize($maxSize) {
		$this -> maxSize = $maxSize * 1024;
	} 
	// 设置允许上传类型
	public function setAllowType($allowType) {
		$this -> allowType = $allowType;
	} 
	// 单文件上传
	public function upload($inputName, $saveName = '', $overWrite = false) {
		$this -> inputName = $inputName;
		$this -> overWrite = $overWrite;
		$this -> uploadInfo[$this -> inputName] = $_FILES[$this -> inputName];
		$this -> uploadInfo[$this -> inputName]['save_name'] = $saveName;
		$this -> validate();
	} 
	// 验证上传文件
	private function validate() {
		extract($this -> uploadInfo[$this -> inputName]);
		$this -> setSaveInfo('error', '');
		if ($error != 0) {
			if ($error == 1 || $error == 2) $this -> error('大小超出系统允许');
			elseif ($error == 3) $this -> error('只有部分被上传');
			elseif ($error == 4) $this -> error('没有文件上传');
			return false;
		} 
		if (!$this -> chkSize()) {
			$this -> error('为空或大小超出允许 ' . $this -> byteFormat($this -> maxSize));
			return false;
		} 
		if (!$this -> chkType()) {
			$this -> error('类型超出允许，允许类型：' . $this -> getAllowExt());
			return false;
		} 
		if (!is_uploaded_file($this -> getUploadInfo('tmp_name'))) {
			$this -> error('非法上传文件，已删除');
			@unlink($this -> getUploadInfo('tmp_name'));
			return false;
		} 
		$this -> move();
	} 
	// 移动文件
	private function move() {
		if (!is_dir($this -> upPath)) mkdir($this -> upPath, 0755, true);
		$this -> setSaveInfo('saveName', $this -> getSaveName());
		$this -> setSaveInfo('savePath', $this -> upPath . $this -> getSaveInfo('saveName'));
		if (!move_uploaded_file($this -> getUploadInfo('tmp_name'), $this -> getSaveInfo('savePath'))) {
			$this -> error('文件无法移动');
			return false;
		} 
		$this -> setSaveInfo('fileName', $this -> getUploadInfo('name'));
		$this -> setSaveInfo('fileSize', $this -> getUploadInfo('size'));
		$this -> setSaveInfo('fileType', $this -> getUploadInfo('type'));
		return true;
	} 
	// 检查文件大小是否合法
	private function chkSize() {
		$fileSize = $this -> getUploadInfo('size');
		return ($fileSize > 0 && $fileSize < $this -> maxSize) ? true : false;
	} 
	// 检查文件类型是否合法
	private function chkType() {
		return in_array($this -> getExt(), $this -> allowType); 
		// $fileType = $this->getUploadInfo('type');
		// return isset($this->allowType[$fileType]) ? true : false;
	} 
	// 获取文件后缀名
	private function getExt() {
		$extNum = strrpos($this -> getUploadInfo('name'), '.');
		if ($extNum === false) return ;
		return substr($this -> getUploadInfo('name'), $extNum); 
		// return $this->allowType[$this->getUploadInfo('type')];
	} 
	// 获得允许上传文件后缀
	public function getAllowExt() {
		$allowExt = '';
		foreach (array_unique($this -> allowType) as $v) {
			$allowExt .= $v . ',';
		} 
		return substr($allowExt, 0, -1);
	} 
	// 获取当前上传文件信息
	private function getUploadInfo($var) {
		return $this -> uploadInfo[$this -> inputName][$var];
	} 

	private function setSaveInfo($var, $val = '') {
		$this -> saveInfo[$this -> inputName][$var] = $val;
	} 

	public function getSaveInfo($var = '', $inputName = '') {
		if (empty($inputName)) {
			if (empty($var)) return $this -> saveInfo[$this -> inputName];
			return $this -> saveInfo[$this -> inputName][$var];
		} elseif ($inputName == 'all') return $this -> saveInfo;

		if (empty($var)) return $this -> saveInfo[$inputName];
		return $this -> saveInfo[$inputName][$var];
	} 
	// 格式化路径
	private function pathFormat($path) {
		if (substr($path, -1) != '/') $path .= '/';
		return $path;
	} 
	// 格式化文件大小
	private function byteFormat($size, $dec = 2) {
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		} 
		return round($size, $dec) . " " . $a[$pos];
	} 
	// 获得保存文件名
	private function getSaveName() {
		$saveName = $this -> getUploadInfo('save_name');
		if (empty($saveName)) $saveName = $this -> notExistsFileName();
		else {
			$saveName .= $this -> getExt();
			if (!$this -> overWrite) $saveName = $this -> newSaveName($saveName);
		} 
		return $saveName;
	} 
	// 生成一个新的不相同文件名
	private function newSaveName($fileName, $newFileName = '', $num = 1) {
		if (empty($newFileName)) $newFileName = $fileName;
		if (file_exists($this -> upPath . $newFileName)) {
			$offset = strrpos($fileName, '.');
			$newFileName = substr($fileName, 0, $offset) . "($num)." . substr($fileName, $offset + 1);
			return $this -> newSaveName($fileName, $newFileName, $num + 1);
		} 
		return $newFileName;
	} 
	// 生成一个不存在的文件名
	private function notExistsFileName() {
		$saveName = time() . mt_rand(1000, 9999) . '.' . $this -> getExt();
		if (file_exists($this -> upPath . $saveName)) $saveName = $this -> notExistsFileName();
		return $saveName;
	} 
	// 保存错误
	private function error($error) {
		$this -> setSaveInfo('error', "文件名：{$this->getUploadInfo('name')};错误：{$error}");
	} 
}
?>