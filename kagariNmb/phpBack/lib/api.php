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
	* `area_id`(板块id，必须)  
	* `area_page`(板块页数，默认是1)
	*/
	public static function getAreaPosts($post) {
		$return['request'] = 'getAreaPosts';
		$return['response']['timestamp'] = self::timestamp();
		
		$area_id = is_numeric($post['area_id']) ? $post['area_id'] : 1;
		$area_page = isset($post['area_page']) && is_numeric($post['area_page']) && $post['area_page'] > 0 ? $post['area_page'] : 1;
		
		global $conf, $con;
		$userTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['user'];
		
		// 查询所在area是否存在
		$areaTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['area'];
		$sql = 'SELECT area_id, area_name FROM ' . $areaTable . ' WHERE area_id=' . $area_id;
		$result = mysqli_query($con, $sql);
		// 检查结果是否非空
		if (!empty($row = mysqli_fetch_assoc($result))) {
			$return['response']['area_id'] = intval($row['area_id']);
			$return['response']['area_name'] = $row['area_name'];
		} else {
			$return['response']['error'] = 'Not such area with area_id=' . $area_id;
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		// $postsPerPage为每页post数量，$lastReplyPosts为最多显示多少条post回复
		$postTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['post'];
		$postsPerPage = $conf['postsPerPage'];
		$lastReplyPosts = $conf['lastReplyPosts'];
		
		$return['response']['area_page'] = intval($area_page);
		$return['response']['posts_per_page'] = intval($postsPerPage);
		$return['response']['last_reply_posts'] = intval($lastReplyPosts);
		$return['response']['posts'] = Array();
		
		// 查询所在post表
		$sql = 'SELECT * FROM ' . $postTable . ' WHERE area_id=' . $area_id .' AND reply_post_id=0 LIMIT ' . $postsPerPage . ' OFFSET ' . ($area_page - 1) * $postsPerPage;
		//echo $sql;
		$result = mysqli_query($con, $sql);
		
		$forloop = 0;
		// 返回当前页面的postPerPage数量的主串
		for ($row = mysqli_fetch_assoc($result); !empty($row); $row = mysqli_fetch_assoc($result), $forloop += 1) {
			//print_r($row);
			$sql = 'SELECT user_name FROM ' . $userTable . ' WHERE user_id=' . $row['user_id'];
			$userResult = mysqli_query($con, $sql);
			$userRow = mysqli_fetch_assoc($userResult);
			
			$sql = 'SELECT COUNT(post_id) FROM ' . $postTable . ' WHERE reply_post_id=' . $row['post_id'];
			$replyNumResult = mysqli_query($con, $sql); 
			$replyNum = mysqli_fetch_assoc($replyNumResult);
			//print_r($replyNum);
			
			$postArray['post_id'] = intval($row['post_id']);
			$postArray['post_title'] = $row['post_title'];
			$postArray['post_content'] = $row['post_content'];
			$postArray['post_images'] = $row['post_images'];
			$postArray['user_id'] = intval($row['user_id']);
			$postArray['user_name'] = $userRow['user_name'];
			$postArray['author_name'] = $row['author_name'];
			$postArray['author_email'] = $row['author_email'];
			$postArray['create_time'] = $row['create_time'];
			$postArray['update_time'] = $row['update_time'];
			$postArray['reply_num'] = intval($replyNum['COUNT(post_id)']);
			$postArray['reply_recent_post'] = Array();
			
			// 再次查询reply_post_id=指定值的结果，但结果是降序的，已搞定
			$sql = 'SELECT * FROM ' . $postTable . ' WHERE area_id=' . $area_id . ' AND reply_post_id=' . $row['post_id'] . ' ORDER BY update_time DESC LIMIT ' . $lastReplyPosts;
			$replyResult = mysqli_query($con, $sql);
			//echo $sql;
			// 如果非空则将其写入reply_recent_post
			for ($replyRow = mysqli_fetch_assoc($replyResult); !empty($replyRow); $replyRow = mysqli_fetch_assoc($replyResult)) {
				//print_r($replyRow);
				$sql = 'SELECT user_name FROM ' . $userTable . ' WHERE user_id=' . $replyRow['user_id'];
				$userResult = mysqli_query($con, $sql);
				$userRow = mysqli_fetch_assoc($userResult);
				
				$replyPostArray['post_id'] = intval($replyRow['post_id']);
				$replyPostArray['post_title'] = $replyRow['post_title'];
				$replyPostArray['post_content'] = $replyRow['post_content'];
				$replyPostArray['post_images'] = $replyRow['post_images'];
				$replyPostArray['user_id'] = intval($replyRow['user_id']);
				$replyPostArray['user_name'] = $userRow['user_name'];
				$replyPostArray['author_name'] = $replyRow['author_name'];
				$replyPostArray['author_email'] = $replyRow['author_email'];
				$replyPostArray['create_time'] = $replyRow['create_time'];
				$replyPostArray['update_time'] = $replyRow['update_time'];
				array_push($postArray['reply_recent_post'], $replyPostArray);
				//$postArray['reply_num'] += 1;
			}
			// 倒序reply_recent_post
			$postArray['reply_recent_post'] = array_reverse($postArray['reply_recent_post']);
			array_push($return['response']['posts'], $postArray);
		}
		// 为空则返回
		if (empty($row) && $forloop == 0) {
			$return['response']['error'] = 'No posts in area with area_id=' . $area_id;
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();
	}
	
	/**
	* 获取串内容
	* `post_id`
	* `post_page` (默认为1)
	*/
	public static function getPost($post) {
		$return['request'] = 'getPosts';
		$return['response']['timestamp'] = self::timestamp();
		
		if (!isset($post['post_id']) && !is_numeric($post['post_id']) && $post['post_id'] < 10000) {
			$return['response']['error'] = 'No such posts found';
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		} else {
			$post_id = $post['post_id'];
		}
		
		$post_page = isset($post['post_page']) && is_numeric($post['post_page']) && $post['post_page'] > 0 ? $post['post_page'] : 1;
		
		global $con, $conf;
		
		$userTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['user'];
		$postTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['post'];
		$postsPerPage = $conf['postsPerPage'];
		// 查询所在post表
		$sql = 'SELECT * FROM ' . $postTable . ' WHERE post_id=' . $post_id .' AND reply_post_id=0 LIMIT 1';
		//echo $sql;
		$result = mysqli_query($con, $sql);
		
		// 主贴处理
		if (empty($mainPostRow = mysqli_fetch_assoc($result))) {
			$return['response']['error'] = 'No such posts found with post_id ' . $post_id;
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		// 从里面获取用户名
		$sql = 'SELECT user_name FROM ' . $userTable . ' WHERE user_id=' . $mainPostRow['user_id'];
		$userResult = mysqli_query($con, $sql);
		$userResultRow = mysqli_fetch_assoc($userResult);
		
		$return['response']['post_id'] = intval($post_id);
		$return['response']['post_page'] = intval($post_page);
		$return['response']['postsPerPage'] = intval($postsPerPage);
		$return['response']['post_title'] = $mainPostRow['post_title'];
		$return['response']['post_content'] = $mainPostRow['post_content'];
		$return['response']['post_images'] = $mainPostRow['post_images'];
		$return['response']['user_id'] = $mainPostRow['user_id'];
		$return['response']['user_name'] = $userResultRow['user_name'];
		$return['response']['author_name'] = $mainPostRow['author_name'];
		$return['response']['author_email'] = $mainPostRow['author_email'];
		$return['response']['create_time'] = $mainPostRow['create_time'];
		$return['response']['update_time'] = $mainPostRow['update_time'];
		$return['response']['reply_num'] = 0;
		$return['response']['reply_recent_posts'] = Array();
		
		// 回帖处理
		$sql = 'SELECT * FROM ' . $postTable . ' WHERE reply_post_id=' . $post_id . ' ORDER BY update_time ASC LIMIT ' . $postsPerPage . ' OFFSET ' . ($post_page - 1) * $postsPerPage;
		$replyResult = mysqli_query($con, $sql);
		//echo $sql;
		
		for ($replyRow = mysqli_fetch_assoc($replyResult); !empty($replyRow); $replyRow = mysqli_fetch_assoc($replyResult)) {
			$sql = 'SELECT user_name FROM ' . $userTable . ' WHERE user_id=' . $replyRow['user_id'];
			$userResult = mysqli_query($con, $sql);
			$userResultRow = mysqli_fetch_assoc($userResult);
			
			$replyArray['post_id'] = $replyRow['post_id'];
			$replyArray['user_id'] = $replyRow['user_id'];
			$replyArray['user_name'] = $userResultRow['user_name'];
			$replyArray['author_name'] = $replyRow['author_name'];
			$replyArray['author_email'] = $replyRow['author_email'];
			$replyArray['post_title'] = $replyRow['post_title'];
			$replyArray['post_content'] = $replyRow['post_content'];
			$replyArray['post_images'] = $replyRow['post_images'];
			$replyArray['create_time'] = $replyRow['create_time'];
			$replyArray['update_time'] = $replyRow['update_time'];
			array_push($return['response']['reply_recent_posts'], $replyArray);
			$return['response']['reply_num'] += 1;
		}
		
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();
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
		//echo $sql;
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
		$reply_post_id = isset($post['reply_post_id']) && is_numeric($post['reply_post_id']) ? $post['reply_post_id'] : 0;
		if ($reply_post_id != 0) {
			$sql = 'SELECT reply_post_id FROM ' . $post_table . ' WHERE post_id=' . $reply_post_id;
			$result = mysqli_query($con, $sql);
			// 先检查回帖是否存在
			if (empty($row = mysqli_fetch_assoc($result))) {
				$return['response']['error'] = 'Post not exists';
				echo json_encode($return, JSON_UNESCAPED_UNICODE);
				exit();
			// 再检查回复的帖子是否为主贴
			} else if ($row['reply_post_id'] != 0) {
				$return['response']['error'] = 'Post is reply post';
				echo json_encode($return, JSON_UNESCAPED_UNICODE);
				exit();
			}
		}
		
		// 补全其他字段
		$author_name = $post['author_name'] == '' ? $conf['default_author_name'] : $post['author_name'];
		$author_email = $post['author_email'];
		$post_title = $post['post_title'] == '' ? $conf['default_post_title'] : $post['post_title'];
		if ($post['post_content'] == '') {
			$return['response']['error'] = 'content can not be empty';
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		$post_content = $post['post_content'];
		$post_image = $post['post_image'];
		
		// 发送请求
		$sql = 'INSERT INTO ' . $post_table . 
		'(area_id, user_id, reply_post_id, author_name, author_email, post_title, post_content, post_images, create_time, update_time) VALUES (' . 
		$area_id . ',' . $user_id . ',' . $reply_post_id . ',"' . $author_name . '","' . $author_email . '","' . $post_title . '","' . $post_content . '","' . $post_image . '","' . self::timestamp() . '","' . self::timestamp() . '")';
		// 如果reply_post_id不为0，更新主串update_time
		if ($reply_post_id != 0) {
			$updatesql = 'UPDATE ' . $post_table . ' SET update_time=' . self::timestamp() . ' WHERE post_id=' . $reply_post_id;
			if (!mysqli_query($con, $updatesql)) {
				mysqli_error();
			}
		}
		//echo $sql;
		if (mysqli_query($con, $sql)) {
			$return['response']['status'] = "OK";
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		} else {
			die(mysqli_error($con));
		}
	}
	
	/**
	* 删除串
	*/
	public static function deletePost($post) {
		// 返回目标
		$return['request'] = 'deletePost';
		$return['response']['timestamp'] = self::timestamp();
		
		$post_id = is_numeric($post['post_id']) && $post['post_id'] > 0 ? $post['post_id'] : 0;
		
		global $con, $conf;
		$postTable = $conf['databaseName'] . '.' . $conf['databaseTableName']['user'];
		
		// 查询指定的串
		$sql = 'SELECT reply_post_id FROM ' . $postTable . ' WHERE post_id=' . $post_id;
		$result = mysqli_query($con, $sql);
		
		// 为空返回
		if (empty($row = mysqli_fetch_assoc($result))) {
			$return['response']['error'] = 'to delete post not exists';
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		// 为0表示这是主串，需要删除回复
		if ($row['reply_post_id'] == 0) {
			$sql = 'DELETE FROM ' . $postTable . ' WHERE reply_post_id=' . $post_id;
			$result = mysqli_query($con, $sql);
			print_r($result);
		}
		
		// 删除该记录
		$sql = 'DELETE FROM ' . $postTable . ' WHERE post_id=' . $post_id;
		$result = mysqli_query($con, $sql);
		print_r($result);
		$return['response']['status'] = 'OK';
		echo json_encode($return, JSON_UNESCAPED_UNICODE);
		exit();
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