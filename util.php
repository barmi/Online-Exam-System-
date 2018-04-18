<?php

include_once('dbConnection.php');

function writeLog($category, $msg)
{
	$email = $_SESSION['email'];
	$email = stripslashes($email);
	$email = addslashes($email);
	$localip = $_SESSION['localip'];
	$remoteip = $_SERVER['REMOTE_ADDR'];
	
	global $con;
	$q3 = mysqli_query($con,"INSERT INTO `log` (`seq`, `userid`, `category`, `localip`, `remoteip`, `msg`) VALUES (NULL, '$email', '$category', '$localip', '$remoteip', '$msg')");
}

function loadConfig()
{
	global $con;
	$q = mysqli_query($con, "SELECT * FROM `config`");
	$config = array();
	while ($row = mysqli_fetch_array($q))
	{
		$config[$row['ckey']] = $row['cvalue'];
	}
	return $config;
}

function is_in_examtime()
{
	$now = strtotime('now');
	$exam_begin = strtotime($_SESSION['config']['exam.begin']);
	$exam_end   = strtotime($_SESSION['config']['exam.end']);
	$is_in_time = ($now >= $exam_begin && $now <= $exam_end);

	return ($_SESSION['config']['exam.now'] == "Y" && $is_in_time);
}

?>