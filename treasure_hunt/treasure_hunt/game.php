<?php
require_once('session.php');
require_once("constant.php");
function quote_smart($str) {

   $str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);

}
$finish=0;
$wrong=0;
$question="";
if(isset($_SESSION['question']))
{
	$query="SELECT question,answer FROM question_table WHERE question_id=".$_SESSION['question'];
	$query_result=mysqli_query($db_connection,$query);
	if(mysqli_num_rows($query_result)>0)
	{
		$query_result=mysqli_fetch_assoc($query_result);
		$question=$query_result['question'];
		$_SESSION['answer']=$query_result['answer'];
	}
	else
	{
		$finish=1;
	}
}
if(isset($_POST['submit']))
{
	$answer=quote_smart($_POST['answer']);
	$answer=strtolower($answer);
	if($answer==$_SESSION['answer'])
	{
		$question_log_query="INSERT INTO `question_log_table`(`question_id`, `user_id`, `log_time`,`user_point`) VALUES (".$_SESSION['question'].",".$_SESSION['id'].",CURRENT_TIMESTAMP,".$_SESSION['point'].")";
		$question_log_result=mysqli_query($db_connection,$question_log_query);
		if($question_log_result===false)
		{
			echo "Error".mysqli_error($db_connection);
		}
		else
		{
			//echo "Right Answer<br/>";
			$_SESSION['question']+=1;
			$_SESSION['point']+=10;
			$update_main_table=$db_connection->prepare('UPDATE `main_table` SET `question_no`=?,`user_point`=?,`log_time`=CURRENT_TIMESTAMP WHERE user_id=?');
			$update_main_table->bind_param('iii',$_SESSION['question'],$_SESSION['point'],$_SESSION['id']);
			if (!$update_main_table->execute()) {
     		   echo "Execute failed: (" . $update_main_table->errno . ") " . $update_main_table->error;
   			}
			echo '<META HTTP-EQUIV="Refresh" Content="0" URL="game.php">';
		}
	}
	else
	{
		$wrong=1;
	}
	//echo $_SESSION['question'];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Untitled Document</title>
<style type="text/css">
.question
{
	padding: 2% 0;
	text-align: center;
}
form
{
	text-align: center;
}
input[type=text]
{
	width: 80%;
}
input
{
	margin: 2%;
}
button#clue_button
{
	background-color: rgba(0,0,0,1);
	border: solid 0px #fff;
	color: #fff;
	cursor: pointer;
	font-family: Futura, "Trebuchet MS", Arial, sans-serif;
	font-size: 15px;
	padding: 10px;
	box-shadow: 0 0 10px rgba(0,0,0,0.6);
}
#disclaimer
{
	margin: 1% 0 2% 0;
}
#clue
{
	margin: 2% 0 4% 0;
}
.wrong
{
	margin: 0 20%;
	background-color: #fff;
	box-shadow: 0 0 10px rgba(0,0,0,0.5);
	color: #f91b1b;
}
a
{
	text-decoration: none;
	color: #fff;
	background-color: #fff;
	color: #f91b1b;
	padding: 10px;
	box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
.bottom
{
	visibility: hidden;
}
img
{
	width: 300px;
	height: auto;
}
div.blank
{
	padding-top:10px; 
	font-size: 30px;
}
div.level
{
	padding: 5% 10%;
	font-size: 20px;
}
div.team_name
{
	position: absolute;
	right: 10%;
	top:20px;
}
div.point
{
	position: absolute;
	right: 10%;
	top:45px;
}
</style>
<script src="js/jquery.min.js"></script>
<script>
$(document).ready(function(){
	$("#clue_button").click(function(event){
			event.preventDefault();
			search_if_avail();
		});
	function search_if_avail()
		{
			$.post("clue.php",{clue:$('#clue_button').attr('data-id')},function(data){
			$("#clue").html(data);
			$("#clue_button").css('display','none');
			$("#disclaimer").css('display','none');
			$(".wrong").css('display','none');
			});
		};
});
</script>
</head>

<body>
	<div id="main" >
	<div id="content" class="clearfix" style="position:relative">
		<div class="level">Level <?php echo $_SESSION['question']; ?></div>
		<div class="team_name">Hello, Team <?php echo $_SESSION['team_name']; ?></div>
		<div class="point">Your Points: <?php echo $_SESSION['point']; ?></div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<?php if(!$finish)
		{
			$clue_left=5-$_SESSION['clue'];
			$str='<div class="question">'.$question.'</div>';
			$str.='<input type="text" name="answer"/><br/>';
			$str.='<input type="submit" value="submit" name="submit"/>';
			echo $str;
			if($wrong==1) echo '<div class="wrong">Wrong answer</div>';
			$str='<div id="clue"></div>';
			$str.='<div id="disclaimer">Clue Will Cost You Penalty<br/>You Have '.$clue_left.' Clues to exhaust</div>';		
			$str.='<button id="clue_button" data-id="'.$_SESSION['question'].' name="clue">Clue</button>';
			echo $str;
		}
		else
		{
			echo "<div>You Have Completed All Levels</div><br/>";
		}
		?>
		<a href="logout.php">Logout</a>
	</form>
	<div class="clearfix bottom">Hello World</div>
	</div>
	</div>
</body>
</html>