<?php
include "lib/Database.class.php";
include "lib/Video.class.php";
include "lib/User.class.php";

Database::createDatabase();
Database::useDatabase();
Database::createVideoTable();
Database::createUserTable();

if (isset($_GET['test'])) {
	Database::testData();
}

?>