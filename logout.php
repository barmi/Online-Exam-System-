<?php 
session_start();
if(isset($_SESSION['email']))
{
	session_destroy();
}
include_once('util.php');
writeLog('logout', "로그아웃");
//die('logout');
$ref= @$_GET['q'];
header("location:$ref");
?>