<?php
include_once('config/siteconfig.php');
class View {
	// 渲染某个模板
	public static function display($target = '', $replace = array()) {
		if($target != ''){
			$filepath = "templates/". $target .".html";
			if (file_exists($filepath) == FALSE) {
				echo ('找不到templates/'. $target .'.html');
				exit();
			}
			$file = file_get_contents($filepath);
			foreach ($replace as $from => $to) {
				$file = str_replace($from, $to, $file);
			}
			return $file;
		}
	}
	
	// 网页头部
	public static function head($title = '') {
		global $siteconfig;
		if($title == '') {
			$title = '无标题页面';
		}
		$page = '<!DOCTYPE html>' .
				'<html>' .
				'<head>' .
				'<title>' . $title . '</title>'. 
				'<meta http-equiv="content-type" content="text/html; charset=utf-8" />'.
				'<link href="'. $siteconfig['cssfile'] .'" rel="stylesheet" type="text/css"/>';
		$page .= '<body>';
		return $page;
	}
	
	// 网页尾部
	public static function foot() {
		$page = '</body>' .
				'</html>';
		return $page;
	}
}
?>