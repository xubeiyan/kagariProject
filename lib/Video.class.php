<?php
	include('lib/Database.class.php');
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
		
		public function __construct() {
			
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
		*	������Ƶ��Ϣ
		*/
		public function updateVideoInfo() {
			$sqlQuery = sprintf("UPDATE video
								SET title=%s, description=%s", $this -> $title, $this -> $description);
			$sqlQuery = mysql_real_escape_string($sqlQuery);
			mysqli_query($con, $sqlQuery);
		}

	}
?>