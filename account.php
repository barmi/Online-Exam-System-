<?php
 include_once 'head.html';
?>

 <!--alert message-->
<?php 
	if(@$_GET['w'])
	{
		echo'<script>alert("'.@$_GET['w'].'");</script>';
	}
?>
<!--alert message end-->
<script>
function validateForm() 
{
	var a = document.forms["form"]["password"].value;
	if (a == null || a == "")
	{
		alert("Password must be filled out");
		return false;
	}
	if (a.length<5 || a.length>25)
	{
		alert("Passwords must be 5 to 25 characters long.");
		return false;
	}
	var b = document.forms["form"]["cpassword"].value;
	if (a!=b)
	{
		alert("Passwords must match.");
		return false;
	}
}
</script>
</head>
<?php
	include_once 'dbConnection.php';
?>
<body>
<?php
	include_once 'title.html';
?>
<div class="col-md-4 col-md-offset-2">

<?php
	session_start();
	if(!(isset($_SESSION['email'])))
	{
		header("location:index.php");
	}
	else
	{
		$name = $_SESSION['name'];
		$email = $_SESSION['email'];

		echo '<span class="pull-right top title1" ><span class="log1"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;Hello,</span> <a href="account.php?q=1" class="log log1">'.$name.'</a>&nbsp;|&nbsp;<a href="logout.php?q=account.php" class="log"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Signout</button></a></span>';
	}
?>
</div>
</div></div>
<div class="bg">

<!--navigation menu-->
<nav class="navbar navbar-default title1">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <!-- a class="navbar-brand" href="#"><b>Netcamp</b></a -->
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li <?php if(@$_GET['q']==1) echo'class="active"'; ?> ><a href="account.php?q=1"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;Home<span class="sr-only">(current)</span></a></li>
        <li <?php if(@$_GET['q']==2) echo'class="active"'; ?>><a href="account.php?q=2"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>&nbsp;Password</a></li>
		<!--
		<li <?php if(@$_GET['q']==3) echo'class="active"'; ?>><a href="account.php?q=3"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>&nbsp;Ranking</a></li></ul>
            <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Enter tag ">
        </div>
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;Search</button>
      </form>
	  -->
      </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav><!--navigation menu closed-->
<div class="container"><!--container start-->
<div class="row">
<div class="col-md-12">

<!--home start-->
<?php 

include_once 'util.php';

if(@$_GET['q']==1) 
{
	if ( is_in_examtime() )
	{
		// 시험리스트 출력을 문제 리스트 출력으로 변경
		$eid = $_SESSION['config']['eid'];
		$email = $_SESSION['email'];
		$result = mysqli_query($con,"SELECT a.qid as qid, a.qtitle as qtitle, a.sn as sn, b.email as ans FROM `questions` a LEFT OUTER JOIN `useranswer` b ON b.qid = a.qid AND b.email='$email' WHERE a.eid='$eid' ORDER BY sn") or die('Error');
		echo  '<div class="panel"><p><b>시 험 명 : '.$_SESSION['config']['exam.title'].'<br />응시시간 : '.$_SESSION['config']['exam.begin'].' ~ '.$_SESSION['config']['exam.end'].'</b></p><table class="table table-striped title1"> <tr><td><b>번호</b></td><td><b>문제명</b></td><td><b>저장여부</b></td><td></td></tr>';
		$c=1;
		while($row = mysqli_fetch_array($result)) 
		{
			$qid = $row['qid'];
			$qtitle = $row['qtitle'];
			$sn = $row['sn'];
			$ans = is_null($row['ans']) ? "X" : "O";
			echo '<tr><td>'.$c++.'</td><td>'.$qtitle.'</td><td>'.$ans.'</td><td><b><a href="account.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'" class="pull-right btn sub1" style="margin:0px;background:#99cc32"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>&nbsp;<span class="title1"><b>Start</b></span></a></b></td></tr>';
		}
		$c=0;
		echo '</table></div>';
		writeLog('account', "목록조회-성공");
	}
	else
	{
		echo '<div class="panel">현재시각에는 시험이 없습니다. 시각 확인 후 다시 시도해 주시기 바랍니다.</div>';
		writeLog('account', "목록조회-실패-".$_SESSION['config']['exam.begin']."~".$_SESSION['config']['exam.end']);
	}
}
?>
<!--<span id="countdown" class="timer"></span>
<script>
	var seconds = 40;
    function secondPassed() {
		var minutes = Math.round((seconds - 30)/60);
		var remainingSeconds = seconds % 60;
		if (remainingSeconds < 10) {
			remainingSeconds = "0" + remainingSeconds; 
		}
		document.getElementById('countdown').innerHTML = minutes + ":" +    remainingSeconds;
		if (seconds == 0) {
			clearInterval(countdownTimer);
			document.getElementById('countdown').innerHTML = "Buzz Buzz";
		} else {    
			seconds--;
		}
    }
	var countdownTimer = setInterval('secondPassed()', 1000);
</script>-->

<!--home closed-->

<!--quiz start-->
<?php
if(@$_GET['q']== 'quiz' && @$_GET['step']== 2) 
{
	$eid = $_SESSION['config']['eid'];
	$email = $_SESSION['email'];

	$eid=@$_GET['eid'];
	$sn=@$_GET['n'];
	$total=@$_GET['t'];
	if ( is_in_examtime() )
	{
		$q=mysqli_query($con,"SELECT * FROM questions WHERE eid='$eid' AND sn='$sn' " );
		echo '<div class="panel" style="margin:5%">';
		$choice = 0;
		while($row=mysqli_fetch_array($q) )
		{
			$qns=nl2br($row['qns']);
			$qid=$row['qid'];
			$choice = $row['choice'];
			$image = $row['image'];
			echo '<b>Question &nbsp;'.$sn.'&nbsp;::<br />'.$qns.'</b><br /><br />';
			if ($image != "")
			{
				if (substr($image, 0, 4) == "http")
				{
					echo '<img src="'.$image.'"><br />';
				}
				else
				{
					echo '<img src="image/'.$image.'"><br />';
				}
			}
			writeLog("acoount", "시험선택-$sn-$qid");
		}
		echo '<form action="update.php?q=quiz&step=2&eid='.$eid.'&n='.$sn.'&t='.$total.'&qid='.$qid.'" method="POST">
		<br />';
		if ($choice == 0)
		{
			$dtext = "";
			$q=mysqli_query($con,"SELECT * FROM useranswer WHERE email='$email' AND qid='$qid'");
			if ($row = mysqli_fetch_array($q) ) $dtext = $row['atext'];
			echo'<textarea name="atext" cols="100" rows="20" >'.$dtext.'</textarea>';
		}
		else
		{
			$q=mysqli_query($con,"SELECT * FROM options WHERE qid='$qid' " );

			while($row=mysqli_fetch_array($q) )
			{
				$option=$row['option'];
				$optionid=$row['optionid'];
				echo'<input type="radio" name="ans" value="'.$optionid.'">'.$option.'<br /><br />';
			}
		}
		echo'<br /><table><tr valign="top"><td><button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;저장</button></form></td><td style="width:10px"></td><td style="valign:bottom"><form action="account.php?q=1" method="POST"><button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;취소</button></form></td></tr></table></div>';
		//header("location:dash.php?q=4&step=2&eid=$id&n=$total");
	}
	else
	{
		echo '<div class="panel">현재시각에는 시험이 없습니다. 시각 확인 후 다시 시도해 주시기 바랍니다.</div>';
		writeLog("acoount", "시험선택실패-$sn-$qid-".$_SESSION['config']['exam.begin']."~".$_SESSION['config']['exam.end']);
	}
}
//result display
if(@$_GET['q']== 'result' && @$_GET['eid']) 
{
$eid=@$_GET['eid'];
$q=mysqli_query($con,"SELECT * FROM history WHERE eid='$eid' AND email='$email' " )or die('Error157');
echo  '<div class="panel">
<center><h1 class="title" style="color:#660033">Result</h1><center><br /><table class="table table-striped title1" style="font-size:20px;font-weight:1000;">';

while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
$w=$row['wrong'];
$r=$row['sahi'];
$qa=$row['level'];
echo '<tr style="color:#66CCFF"><td>Total Questions</td><td>'.$qa.'</td></tr>
      <tr style="color:#99cc32"><td>right Answer&nbsp;<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></td><td>'.$r.'</td></tr> 
	  <tr style="color:red"><td>Wrong Answer&nbsp;<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></td><td>'.$w.'</td></tr>
	  <tr style="color:#66CCFF"><td>Score&nbsp;<span class="glyphicon glyphicon-star" aria-hidden="true"></span></td><td>'.$s.'</td></tr>';
}
$q=mysqli_query($con,"SELECT * FROM rank WHERE  email='$email' " )or die('Error157');
while($row=mysqli_fetch_array($q) )
{
$s=$row['score'];
echo '<tr style="color:#990000"><td>Overall Score&nbsp;<span class="glyphicon glyphicon-stats" aria-hidden="true"></span></td><td>'.$s.'</td></tr>';
}
echo '</table></div>';

}
?>
<!--quiz end-->
<?php
//history start
if(@$_GET['q']== 2) 
{
	writeLog('account', "비밀번호변경화면");
?>
<div class="col-md-4 panel">
<!-- sign in form begins -->  
  <form class="form-horizontal" name="form" action="update.php?q=2" onSubmit="return validateForm()" method="POST">
<fieldset>

<div class="form-group">
  <label class="col-md-12 control-label" for="password"></label>
  <div class="col-md-12">
    <input id="password" name="password" placeholder="Enter your password" class="form-control input-md" type="password">
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-12control-label" for="cpassword"></label>
  <div class="col-md-12">
    <input id="cpassword" name="cpassword" placeholder="Conform Password" class="form-control input-md" type="password">
    
  </div>
</div>
<!-- Button -->
<div class="form-group">
  <label class="col-md-12 control-label" for=""></label>
  <div class="col-md-12"> 
    <input  type="submit" class="sub" value="비밀번호 변경" class="btn btn-primary"/>
  </div>
</div>

</fieldset>
</form></div>

<?php
/*
	$q=mysqli_query($con,"SELECT * FROM history WHERE email='$email' ORDER BY date DESC " )or die('Error197');
	echo  '<div class="panel title">
	<table class="table table-striped title1" >
	<tr style="color:red"><td><b>S.N.</b></td><td><b>Quiz</b></td><td><b>Question Solved</b></td><td><b>Right</b></td><td><b>Wrong<b></td><td><b>Score</b></td>';
	$c=0;
	while($row=mysqli_fetch_array($q) )
	{
		$eid=$row['eid'];
		$s=$row['score'];
		$w=$row['wrong'];
		$r=$row['sahi'];
		$qa=$row['level'];
		$q23=mysqli_query($con,"SELECT title FROM quiz WHERE  eid='$eid' " )or die('Error208');
		while($row=mysqli_fetch_array($q23) )
		{
			$title=$row['title'];
		}
		$c++;
		echo '<tr><td>'.$c.'</td><td>'.$title.'</td><td>'.$qa.'</td><td>'.$r.'</td><td>'.$w.'</td><td>'.$s.'</td></tr>';
	}
	echo'</table></div>';
*/
}

//ranking start
if(@$_GET['q']== 3) 
{
$q=mysqli_query($con,"SELECT * FROM rank  ORDER BY score DESC " )or die('Error223');
echo  '<div class="panel title">
<table class="table table-striped title1" >
<tr style="color:red"><td><b>Rank</b></td><td><b>Name</b></td><td><b>Gender</b></td><td><b>College</b></td><td><b>Score</b></td></tr>';
$c=0;
while($row=mysqli_fetch_array($q) )
{
$e=$row['email'];
$s=$row['score'];
$q12=mysqli_query($con,"SELECT * FROM user WHERE email='$e' " )or die('Error231');
while($row=mysqli_fetch_array($q12) )
{
$name=$row['name'];
$gender=$row['gender'];
$college=$row['college'];
}
$c++;
echo '<tr><td style="color:#99cc32"><b>'.$c.'</b></td><td>'.$name.'</td><td>'.$gender.'</td><td>'.$college.'</td><td>'.$s.'</td><td>';
}
echo '</table></div>';}
?>



</div></div></div></div>
<?php
	include_once 'foot.html'
?>


</body>
</html>
