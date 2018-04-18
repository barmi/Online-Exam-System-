<?php
//all the variables defined here are accessible in all the files that include this one
include_once 'db.config';
$con= new mysqli($db_host, $db_user, $db_passwd, $db_dbname)or die("Could not connect to mysql".mysqli_error($con));
$con->query("set names utf8");
?>