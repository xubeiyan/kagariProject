<?php
	include('lib/Database.class.php');
	/*
	*	视频类
	*
	*/
	class Video {
		private $vid;			//视频VID
		private $upid;			//上传人ID
		private $playTime;		//播放次数
		private $point;			//评分
		private $type;			//视频分区
		private $title;			//视频标题
		private $description;	//视频简介
		
		public function __construct() {
			
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
		*	根据vid获取视频
		*/
		public function getVideoByVid($vid) {
			Database::connectDatabase();
			Database::useDatabase();
			$sql = Database::$sql;
			$sqlQuery = sprintf("SELECT * FROM video WHERE id=%d", $vid);
			$result = mysqli_query($sql, $sqlQuery);
			$row = mysqli_fetch_array($result);
			//print_r($row);
			$this -> vid = $vid;
			$this -> title = $row['title'];
			$this -> description = $row['description'];
		}
		/*
		*	更新视频信息
		*/
		public function updateVideoInfo() {
			$sqlQuery = sprintf("UPDATE video
								SET title=%s, description=%s", $this -> $title, $this -> $description);
			$sqlQuery = mysql_real_escape_string($sqlQuery);
			mysqli_query($con, $sqlQuery);
		}

	}
?>