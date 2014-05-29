<?php
include "lib/database.php";
include "lib/Video.class.php";
include "lib/User.class.php";


if (isset($_GET['inittable'])) {
	if ($_GET['inittable'] == 'video') {
		Video::createVideoTable();
	} else if ($_GET['inittable'] == 'user') {
		User::createUserTable();
	} else {
		echo 'nothing to do';
	}
}


?>