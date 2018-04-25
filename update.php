<?php
include_once 'dbConnection.php';
session_start();
$email=$_SESSION['email'];

include_once 'util.php';

//delete feedback
if(isset($_SESSION['key']))
{
	if(@$_GET['fdid'] && $_SESSION['key']=='sunny7785068889')
	{
		$id=@$_GET['fdid'];
		$result = mysqli_query($con,"DELETE FROM feedback WHERE id='$id' ") or die('Error:'.__LINE__);
		header("location:dash.php?q=3");
	}
}

//delete user
if(isset($_SESSION['key']))
{
	if(@$_GET['demail'] && $_SESSION['key']=='sunny7785068889') 
	{
		$demail=@$_GET['demail'];
		$r1 = mysqli_query($con,"DELETE FROM rank WHERE email='$demail' ") or die('Error:'.__LINE__);
		$r2 = mysqli_query($con,"DELETE FROM history WHERE email='$demail' ") or die('Error:'.__LINE__);
		$result = mysqli_query($con,"DELETE FROM user WHERE email='$demail' ") or die('Error');
		header("location:dash.php?q=1");
	}
}
//remove quiz
if(isset($_SESSION['key']))
{
	if(@$_GET['q']== 'rmquiz' && $_SESSION['key']=='sunny7785068889')
	{
		$eid=@$_GET['eid'];
		$result = mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' ") or die('Error');
		while($row = mysqli_fetch_array($result))
		{
			$qid = $row['qid'];
			$r1 = mysqli_query($con,"DELETE FROM options WHERE qid='$qid'") or die('Error');
			$r2 = mysqli_query($con,"DELETE FROM answer WHERE qid='$qid' ") or die('Error');
		}
		$r3 = mysqli_query($con,"DELETE FROM questions WHERE eid='$eid' ") or die('Error');
		$r4 = mysqli_query($con,"DELETE FROM quiz WHERE eid='$eid' ") or die('Error');
		$r4 = mysqli_query($con,"DELETE FROM history WHERE eid='$eid' ") or die('Error');

		header("location:dash.php?q=5");
	}
}

//add quiz
if(isset($_SESSION['key']))
{
	if(@$_GET['q']== 'addquiz' && $_SESSION['key']=='sunny7785068889')
	{
		$name = $_POST['name'];
		$name= ucwords(strtolower($name));
		$total = $_POST['total'];
		$sahi = $_POST['right'];
		$wrong = $_POST['wrong'];
		$time = $_POST['time'];
		$tag = $_POST['tag'];
		$desc = $_POST['desc'];
		$id=uniqid();
		$q3=mysqli_query($con,"INSERT INTO quiz VALUES  ('$id','$name' , '$sahi' , '$wrong','$total','$time' ,'$desc','$tag', NOW())");

		header("location:dash.php?q=4&step=2&eid=$id&n=$total");
	}
}

//add question
if(isset($_SESSION['key']))
{
	if(@$_GET['q']== 'addqns' && $_SESSION['key']=='sunny7785068889')
	{
		$n=@$_GET['n'];
		$eid=@$_GET['eid'];
		$ch=@$_GET['ch'];

		for($i=1;$i<=$n;$i++)
		{
			$qid=uniqid();
			$qns=$_POST['qns'.$i];
			$q3=mysqli_query($con,"INSERT INTO questions (`eid`, `qid`, `qns`, `choice`, `sn`) VALUES  ('$eid','$qid','$qns' , '$ch' , '$i')");
			$oaid=uniqid();
			$obid=uniqid();
			$ocid=uniqid();
			$odid=uniqid();
			$a=$_POST[$i.'1'];
			$b=$_POST[$i.'2'];
			$c=$_POST[$i.'3'];
			$d=$_POST[$i.'4'];
			$qa=mysqli_query($con,"INSERT INTO options (`qid`, `option`, `optionid`) VALUES  ('$qid','$a','$oaid')") or die('Error61');
			$qb=mysqli_query($con,"INSERT INTO options (`qid`, `option`, `optionid`) VALUES  ('$qid','$b','$obid')") or die('Error62');
			$qc=mysqli_query($con,"INSERT INTO options (`qid`, `option`, `optionid`) VALUES  ('$qid','$c','$ocid')") or die('Error63');
			$qd=mysqli_query($con,"INSERT INTO options (`qid`, `option`, `optionid`) VALUES  ('$qid','$d','$odid')") or die('Error64');
			$e=$_POST['ans'.$i];
			switch($e)
			{
			case 'a':
				$ansid=$oaid;
				break;
			case 'b':
				$ansid=$obid;
				break;
			case 'c':
				$ansid=$ocid;
				break;
			case 'd':
				$ansid=$odid;
				break;
			default:
				$ansid=$oaid;
			}

			$qans=mysqli_query($con,"INSERT INTO answer VALUES  ('$qid','$ansid')");

		}
		header("location:dash.php?q=0");
	}
}

//quiz start
if(@$_GET['q']== 'quiz' && @$_GET['step']== 2) 
{
	$eid=@$_GET['eid'];
	$sn=@$_GET['n'];
	$total=@$_GET['t'];
	$ans=$_POST['ans'];
	$qid=@$_GET['qid'];
	if ( is_in_examtime() )
	{
		$atext = mysqli_real_escape_string($con, $_POST['atext']);
		$query = "INSERT INTO `useranswer` (`email`, `qid`, `atext`) VALUES('$email','$qid', '$atext') ON DUPLICATE KEY UPDATE atext='$atext', mtime=NOW()";
		//die($query);
		$q = mysqli_query($con, $query)or die('Error:'.__LINE__);
		
		writeLog("update", "문제 저장-$sn-$qid-$atext");
		header("location:account.php?q=1");
	}
	else
	{
		writeLog("update", "문제 저장 실패-$sn-$qid-".$_SESSION['config']['exam.begin']."~".$_SESSION['config']['exam.end']);
		echo "문서 저장 실패 : 시험 시간을 확인하세요. $sn-$qid-".$_SESSION['config']['exam.begin']."~".$_SESSION['config']['exam.end'];
	}
	
}

//restart quiz
if(@$_GET['q']== 'quizre' && @$_GET['step']== 25 ) 
{
	$eid=@$_GET['eid'];
	$n=@$_GET['n'];
	$t=@$_GET['t'];
	$q=mysqli_query($con,"SELECT score FROM history WHERE eid='$eid' AND email='$email'" )or die('Error156');
	while($row=mysqli_fetch_array($q) )
	{
	$s=$row['score'];
	}
	$q=mysqli_query($con,"DELETE FROM `history` WHERE eid='$eid' AND email='$email' " )or die('Error184');
	$q=mysqli_query($con,"SELECT * FROM rank WHERE email='$email'" )or die('Error161');
	while($row=mysqli_fetch_array($q) )
	{
	$sun=$row['score'];
	}
	$sun=$sun-$s;
	$q=mysqli_query($con,"UPDATE `rank` SET `score`=$sun ,time=NOW() WHERE email= '$email'")or die('Error174');
	header("location:account.php?q=quiz&step=2&eid=$eid&n=1&t=$t");
}

if( @$_GET['q'] == 2 ) 
{
	writeLog('update', "비밀번호변경");
	$password = $_POST['password'];
	$q=mysqli_query($con,"UPDATE `user` SET `password`='$password', `init`='N' WHERE email='$email' " )or die('Error:'.__LINE__);
	session_destroy();
	header("location:index.php");
}
?>



