<?php
	/*
	*	用户类
	*
	*/
	class User{
		private $username;		// 用户名
		private $password;		// 密码
		private $nickname;		// 昵称
		private $signature;		// 签名
		private $uid;			// 用户ID
		/*
		*	构造函数
		*/
		public function __construct() {
			$username = '';
			$nickname = '';
			$signature = '';
		}
	
		
		/*
		*	获取私有属性
		*/
		public function __get($property_name){
			if(isset($this -> $property_name)) {
				return $this -> $property_name;
			} else {
				return NULL;
			}
		}
		/*
		*	设置私有属性
		*/
		public function __set($property_name, $value){
			$this -> $property_name = $value;
		}
		
		/*
		*	根据uid查询
		*/
		public function getById($uid){
			$sqlQuery = sprintf("SELECT ID, username, nickname, password, type, point FROM user 
								WHERE ID = %d", mysql_escape_string($uid));
			//echo $sqlQuery;
			$result = mysql_query($sqlQuery, $GLOBALS['sql']); 
				
			if ($result = mysql_fetch_array($result)){
				$this -> uid = $result['ID'];
				$this -> username = $result['username'];
				$this -> nickname = $result['nickname'];
				$this -> password = $result['password'];
				$this -> type = $result['type'];
				$this -> point = $result['point'];
			} else {
				return false;
			}
		}
		/*
		*	验证用户是否存在
		*/
		public function validateUserExist($username){
			$sqlQuery = sprintf("SELECT * WHERE username = %s", $username);
			$result = mysql_query($sqlQuery, $GLOBALS['sql']);
			
			if ($result = mysql_fetch_array($result)){
				return true;
			} else {
				return false;
			}
		}
		/*
		*	更新用户
		*/
		private function update(){
			if(validateUserExist($this -> username)){
				$sqlQuery= sprinf("UPDATE user SET (username, nickname, password, type, point) = (%s, %s, %s, %d, %d) WHERE id = %d", $this -> username, $this -> nickname, $this -> password, $this ->type, $this -> point);
				if (!mysql_query($sqlQuery, $GLOBALS['sql'])){
					die("更新用户数据失败……" . mysql_error());
				}
			} else {
				$sqlQuery = sprintf("INSERT INTO user (username, nickname, password, type, point) BALUES (%s, %s, %s, %d, %d)", 
							$this -> username, $this -> nickname, $this -> password, $this ->type, $this -> point);
				if (!mysql_query($sqlQuery, $GLOBALS['sql'])){
					die("插入用户数据失败……" . mysql_error());
				}
			}
			
		}
	}
?>