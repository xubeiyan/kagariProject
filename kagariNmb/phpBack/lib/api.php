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
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
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
				echo json_encode($return, JSON_UNESCAPED_UNICODE);
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
		global $conf, $con;
		$return['request'] = 'getAreaLists';
		$return['response']['timestamp'] = self::timestamp();
		$return['response']['areas'] = Array();
		
		$areaTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['area'];
		$sql = 'SELECT area_id, area_name, parent_area FROM ' . $areaTable;
		$result = mysqli_query($con, $sql);
		// 返回所有的area
		for ($row = mysqli_fetch_assoc($result); !empty($row); $row = mysqli_fetch_assoc($result)) {
			$area['area_id'] = $row['area_id'];
			$area['area_name'] = $row['area_name'];
			$area['parent_area'] = $row['parent_area'] != 0 ? $row['parent_area'] : "";
			array_push($return['response']['areas'], $area);
		}
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
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
	* `user_id`(用户id，必需)
	* `area_id`(区id，必须)
	* `reply_post_id`(回复串id)
	* `author_name`   
	* `author_email`   
	* `post_title`   
	* `post_content`(串内容，必需)    
	* `post_image`
	*/
	public static function sendPost($post) {
		// 返回目标
		$return['request'] = 'sendPost';
		$return['response']['timestamp'] = self::timestamp();
		global $conf, $con;
		$user_table = $conf['databaseName'] . '.' . $conf['databaseTableName']['user'];
		$area_table = $conf['databaseName'] . '.' . $conf['databaseTableName']['area'];
		$post_table = $conf['databaseName'] . '.' . $conf['databaseTableName']['post'];
		
		// 检查user_id是否合法，检查其是否对得上ip
		$user_id = is_numeric($post['user_id']) ? $post['user_id'] : 0;
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = 'SELECT user_id FROM ' . $user_table . ' WHERE ip_address="' . $ip . '" AND user_id=' . $user_id;
		$result = mysqli_query($con, $sql);
		// 未找到则返回错误
		if (empty($row = mysqli_fetch_assoc($result))) {
			$return['response']['error'] = 'Not exists such user';
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		} 
		// 检查区id
		$area_id = is_numeric($post['area_id']) ? $post['area_id'] : 0;
		$sql = 'SELECT area_id FROM ' . $area_table . ' WHERE area_id=' . $area_id;
		$result = mysqli_query($con, $sql);
		if (!$row = mysqli_fetch_assoc($result)) {
			$return['response']['error'] = 'Not exist such area';
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		// 检查reply_post_id，只检查不为空的情况
		$reply_post_id = is_numeric($post['reply_post_id']) ? $post['reply_post_id'] : '';
		if ($reply_post_id != '') {
			$sql = 'SELECT reply_post_id FROM ' . $post_table . ' WHERE reply_post_id=' . $reply_post_id;
			$result = mysqli_query($con, $sql);
			if (!$row = mysqli_fetch_assoc($result)) {
				$return['response']['error'] = 'Not such to-reply post id';
				echo json_encode($return, JSON_UNESCAPED_UNICODE);
				exit();
			}
		}
		
		// 补全其他字段
		$author_name = $post['author_name'] == '' ? $conf['default_author_name'] : $post['author_name'];
		$post_title = $post['post_title'] == '' ? $conf['default_post_title'] : $post['post_title'];
		if ($post['post_content'] == '') {
			$return['response']['error'] = 'content can not be empty';
			echo json_decode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$post_content = $post['post_content'];
		$post_image = $post['post_image'];
		
		// 发送请求
		$sql = 'INSERT INTO ' . $post_table . 
		'(area_id, user_id, reply_post_id, author_name, author_email, post_title, post_content, post_images, create_time, update_time) VALUES (' . 
		$area_id . ',' . $user_id . ',' . $reply_post_id . ',"' . $author_name . '","' . $author_email . '","' . $post_title . '","' . $post_content . '","' . $post_image . '","' . timestamp() . '","' . timestamp() . '"';
		if (mysqli_query($con, $sql)) {
			$return['response']['status'] = $sql;
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		} else {
			die(mysqli_error($con));
		}
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
		return date("Y-m-d H:i:s");
	}
}
?>