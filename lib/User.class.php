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
		*	新建user表
		*/
		public static function createUserTable() {
			$sqlQuery = sprintf("CREATE TABLE user (
								ID int NOT NULL AUTO_INCREMENT,
								PRIMARY KEY(ID),
								username char(63),
								password char(255),
								type int default 0,
								point int default 0
								)");
			if(mysql_query($sqlQuery, $GLOBALS['sql'])) {
				echo "table created";
			} else {
				echo mysql_error();
			};
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
		private function getById($uid){
			$this -> $uid = $uid;
			// 查询SQL获得
			$this -> $password = $password;
		}
		/*
		*	更新用户
		*/
		private function update(){
			
		}
	}
?>