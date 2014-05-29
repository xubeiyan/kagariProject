<?php
	/*
	*	��Ƶ��
	*
	*/
	class Video {
		private $vid;			//��ƵVID
		private $upid;			//�ϴ���ID
		private $playTime;		//���Ŵ���
		private $point;			//����
		private $type;			//��Ƶ����
		private $title;			//��Ƶ����
		private $description;	//��Ƶ���
		
		public function __construct($title, $description) {
			$this -> $title = $title;
			$this -> $description = $description;
		}
		
		/*
		*	������Ƶ��
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
		*	��ȡ˽������
		*/
		function __get($property_name){
			if(isset($this -> $property_name)) {
				return $this -> $property_name;
			} else {
				return NULL;
			}
		}
		
		/*
		*	����˽������
		*/
		function __set($property_name, $value){
			$this -> $property_name = $value;
		}
		
		/*
		*	����vid��ȡ��Ƶ
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
		*	������Ƶ��Ϣ
		*/
		public function updateVideoInfo() {
			$sqlQuery = sprintf("UPDATE video
								SET title=%s, description=%s", $this -> $title, $this -> $description);
			$sqlQuery = mysql_real_escape_string($sqlQuery);
			mysql_query($sqlQuery, $con);
		}

	}
?>