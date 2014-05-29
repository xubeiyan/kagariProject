<?php
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
		
		public function __construct($title, $description) {
			$this -> $title = $title;
			$this -> $description = $description;
		}
		
		/*
		*	创建视频表
		*/
		public static function createVideoTable() {
			$sqlQuery = sprintf("CREATE TABLE video (
								ID int NOT NULL AUTO_INCREMENT,
								PRIMARY KEY(ID),
								upuid int default 0,
								playtime int default 0,
								type int default 0,
								point int default 0,
								title char(255) character set utf8,
								description text character set utf8
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
		public function getVideoByVid() {
			if (isset($this -> $vid)){
				$sqlQuery = sprintf("SELECT * FROM video WHERE id=%d", $this -> $vid);
				$result = mysql_query($sqlQuey, $con);
				$row = mysql_fetch_array($result);
				$this -> $upid = $row['upid'];
				$this -> $playtime = $row['playtime'];
				$this -> $point = $row['point'];
				$this -> $title = $row['title'];
				$this -> $description = $row['description'];
			}
		}
		/*
		*	更新视频信息
		*/
		public function updateVideoInfo() {
			$sqlQuery = sprintf("UPDATE video
								SET title=%s, description=%s", $this -> $title, $this -> $description);
			$sqlQuery = mysql_real_escape_string($sqlQuery);
			mysql_query($sqlQuery, $con);
		}

	}
?>