<?php
$con = mysqli_connect(
	$conf['databaseHost'], 
	$conf['databaseUsername'], 
	$conf['databasePassword']
	);

if (!$con) {
	die(mysqli_connect_error());
}

//echo 'Success...' . mysqli_get_host_info($con) . "<br />";

?>