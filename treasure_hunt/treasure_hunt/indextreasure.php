<?PHP
session_start();
include ('constant.php');
 $user_name = "";
$pword = "";
$errorMessage ="";
//==========================================
//	ESCAPE DANGEROUS SQL CHARACTERS
//==========================================
function quote_smart($str) {

   $str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);

}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$user_name = $_POST['uname'];
	$pword = $_POST['pass'];
	$user_name = htmlspecialchars($user_name);
	$pword = htmlspecialchars($pword);

	//==========================================
	//	CONNECT TO THE LOCAL DATABASE
	//==========================================
	if ($db_connection) {
		$user_name = quote_smart($user_name);
		$pword = quote_smart($pword);
		//$pword.=$user_name;
		$pword.="ankitinISM";
		$pword=md5($pword);
		$stmt = $db_connection->prepare('SELECT * FROM main_table WHERE username = ? AND password=?');
		$stmt->bind_param('is',$user_name,$pword);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		{
			$result = $stmt->get_result();
			$db_field = $result->fetch_assoc();
			$stmt->close();
		}
		//====================================================
		//	CHECK TO SEE IF THE $result VARIABLE IS TRUE
		//====================================================

			if ($result->num_rows>0) {
				if ($db_field) {
					$_SESSION['id']=$db_field['user_id'];
					$_SESSION['question']=$db_field['question_no'];
					$_SESSION['clue']=$db_field['no_of_clues'];
					$_SESSION['logintime']=$db_field['log_time'];
					$_SESSION['username']=$user_name;
					$_SESSION['login'] = "1";
					$_SESSION['SESS_NOTIFICATION']=0;
					$_SESSION['SESS_ERROR']=0;
					$_SESSION['SESS_MESSAGE']="";
					$_SESSION['verid']=$db_field['ver_id'];
					$_SESSION['point']=$db_field['user_point'];
					$_SESSION['team_name']=$db_field['team_name'];
					if($_SESSION['verid']==1)
						header('Location:game.php');
					else
					{
						var_dump($_SESSION);
						header("Location:verify.php");
					}
				}
			else {
				session_start();
				$_SESSION['error'] = "either the Number or Password is wrong";
				$_SESSION['SESS_ERROR']=1;
				//header ("Location: index.php");
			}	
		}
		else {
			$_SESSION['SESS_ERROR']=1;
			$errorMessage = "either the Number or Password is wrong";
		}

	
	}

	else {
		$_SESSION['SESS_ERROR']=1;
		$errorMessage = "Error logging on2";
	}  
      

}
if(isset($errorMessage))
$_SESSION['error']=$errorMessage;
mysqli_close($db_connection);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Login</title>
<style type="text/css">
#content table td
{
	padding: 10px 0px 0px 20px;
}
.wrong
{
	text-align: center;
	margin: 2% 20%;
	background-color: #fff;
	box-shadow: 0 0 10px rgba(0,0,0,0.5);
	color: #f91b1b;
}
</style>
</head>
<body>
<div id="main" >
<div id="content" class="clearfix">
	<div class="header">Login</div>
	<?php if(isset($_SESSION['SESS_ERROR'])) echo '<div class="wrong">'.$errorMessage.'</div>'; ?>
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
		<table>
			<tr>
				<td><label for="uname">Username:</label></td>
				<td>+91<input type="text" id="uname" name="uname"/></td>
			</tr>
			<tr>
				<td><label for="pass">Password:</label></td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="pass" name="pass"/></td>
			</tr>
		</table>
		<input type="submit" value="Submit" id="submit" name="submit"/>
	</form>
</div>
</div>
</body>
</html>