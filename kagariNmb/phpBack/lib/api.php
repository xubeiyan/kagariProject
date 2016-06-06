<?php
/**
* api列表
*/
class API {
	/**
	* 获取饼干, 需要主动调用？
	*/
	public static function getCookie() {
		$return['request'] = 'getCookie';
		$return['response']['timestamp'] = self::timestamp();
		
		global $conf, $con;		
		$ip = $_SERVER['REMOTE_ADDR'];
		$table = $conf['databaseName'] . '.' . $conf['databaseTableName']['user'];
		$sql = 'SELECT * FROM ' . $table . ' WHERE ip_address="' . $ip . '"';
		// 查询访问ip是否在数据库中
		$result = mysqli_query($con, $sql);
		if (!empty($row = mysqli_fetch_assoc($result))) {
			$return['response']['ip'] = $row['ip_address'];
			$return['response']['username'] = $row['user_name'];
			echo json_encode($return);
			exit();
		} else {
			// 未在数据库中
			$maxsql = 'SELECT max(user_id) FROM ' . $table;
			$result = mysqli_query($con, $maxsql);
			// 计算user_id
			if (empty($row = mysqli_fetch_assoc($result))) {
				$id = 1;
			} else {
				$id = $row['user_id'] + 1;
			}
			$username = self::randomString($id);
			$sql = 'INSERT INTO ' . $table . '(ip_address, user_name, block_time, last_post_id) VALUES ("' . $ip . '", "' . $username . '", 0, 0)';
			if (mysqli_query($con, $sql)) {
				$return['response']['ip'] = $ip;
				$return['response']['username'] = $username;
				echo json_encode($return);
				exit();
			} else {
				die(mysqli_error($con));
			}
		}
	}
	
	/**
	* 获取板块列表
	*/
	public static function getAreaLists() {
		$return['request'] = 'getCookie';
		$return['response']['timestamp'] = self::timestamp();
		
		echo json_encode($return);
		exit();
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
	
	/**
	* 获得某个数字对应的用户名
	*/
	private static function randomString($num) {
		$numberList = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
		$smallList = ['cf', 'bb', 'ac', 'nm', 'wc'];
		$capitalList = ['NSSC', 'BDZD', 'ZHQG', 'YHGJ', 'MDZZ'];
	
		$first = $numberList[($num / (count($capitalList) * count($smallList))) % count($numberList)];
		$second = $smallList[($num / count($capitalList)) % count($smallList)];
		$third = $capitalList[$num % count($capitalList)];
		return $first . $second . $third;
	}
	
	/**
	* 生成一个当前时间戳
	*/
	private static function timestamp() {
		return date("Y-m-d h:i:s");
	}
}
?>