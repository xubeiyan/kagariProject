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
		function __construct() {
			$username = '';
			$nickname = '';
			$signature = '';
		}
		/*
		*	获取私有属性
		*/
		function __get($property_name){
			if(isset($this -> $property_name)) {
				return $this -> $property_name;
			} else {
				return NULL;
			}
		}
		/*
		*	设置私有属性
		*/
		function __set($property_name, $value){
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
>