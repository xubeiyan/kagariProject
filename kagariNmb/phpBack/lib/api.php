<?php
/**
* api列表
*/
class API {
	/**
	* 获取饼干, 需要主动调用？
	*/
	public static function getCookie() {
		$sql = 'SELECT * FORM ' . $conf['databaseTableName']['user'] . ' WHERE ip_address="' . $_SERVER['REMOTE_ADDR']. '" LIMIT 1';
		if ($result = mysqli_query($con, $sql)) {
			
		}
	}
	
	/**
	* 获取板块列表
	*/
	public static function getAreaLists() {
		
	}
	
	/**
	* 获取板块串
	*/
	public static function getAreaPosts() {
		
	}
	
	/**
	* 获取串内容
	*/
	public static function getPost() {
		
	}
	
	/**
	* 发表新串
	*/
	public static function sendPost() {
		
	}
}
?>