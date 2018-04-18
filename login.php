<?php
	session_start();
	if(isset($_SESSION["email"]))
	{
		session_destroy();
	}

	include_once 'dbConnection.php';
	$ref=@$_GET['q'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$localip = $_POST['localip'];

	include_once 'util.php';
	if ($localip == "")
	{
		writeLog("login", "($email)login실패-localip없음");
		header("location:$ref?w=브라우저에 문제가 있습니다. 반드시 크롬으로 접속하시기 바랍니다.");
	}
	else
	{
		$email = stripslashes($email);
		$email = addslashes($email);
		$password = stripslashes($password); 
		$password = addslashes($password);
		//$password=md5($password); 
		$result = mysqli_query($con,"SELECT name,init FROM user WHERE email = '$email' and password = '$password'") or die('Error');
		$count=mysqli_num_rows($result);
		if ($count==1)
		{
			$init = "N";
			while($row = mysqli_fetch_array($result)) {
				$name = $row['name'];
				$init = $row['init'];
			}
			$_SESSION["name"] = $name;
			$_SESSION["email"] = $email;
			$_SESSION["localip"] = $localip;
			//$_SESSION["eid"] = "5ac87d6b9a784"; // test용
			
			$config = array();
			$q = mysqli_query($con, "SELECT * FROM `config`");
			while ($row = mysqli_fetch_array($q))
			{
				$config[$row['ckey']] = $row['cvalue'];
			}
			$_SESSION["config"] = $config;

			//print_r($_SESSION);
			//print_r($_SESSION['config']['eid']);
			//die("config");
			if ($init == "Y")
			{
				writeLog("login", "login성공-init");
				header("location:account.php?q=2");
			}
			else
			{
				writeLog("login", "login성공");
				header("location:account.php?q=1");
			}
		}
		else
		{
			writeLog("login", "($email)login실패-정보없음");
			header("location:$ref?w=Wrong Username or Password");
		}
	}
?>