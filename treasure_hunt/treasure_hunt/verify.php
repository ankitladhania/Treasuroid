<?php
require_once('session.php');
require_once('constant.php');
function quote_smart($str) {

   $str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);

}
$flag=0;
if(isset($_POST['verify']))
{
	$id=$_SESSION['id'];
	$ver_code=quote_smart($_POST['ver_code']);
	$stmt = $db_connection->prepare('SELECT ver_id FROM main_table WHERE user_id = ? AND ver_code=?');
	$stmt->bind_param('ii',$id,$ver_code);
	if (!$stmt->execute()) {
        $errmsg= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $flag=1;
    }
	else
	{
		$result = $stmt->get_result();
		$stmt->close();
		if ($result->num_rows>0) 
		{
			$stmt = $db_connection->prepare('UPDATE `main_table` SET `ver_id`=1 WHERE user_id=? AND ver_code=?');
			$stmt->bind_param('ii',$_SESSION['id'],$ver_code);
			if (!$stmt->execute()) {
        		$errmsg= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        		$flag=1;
    		}
    		else
    		{
    			$errmsg="Verified Successfully";
    			$flag=1;
    			echo '<script type="text/javascript">location.href ="game.php";</script>';
    		}
		}
		else
		{
			$errmsg="No Such verification Exist";
			$flag=1;
		}
	}	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Untitled Document</title>
<script type="text/javascript">
function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}
</script>
<style type="text/css">
div.head
{
	padding-top: 5%;
	text-align: center;
}
#submit
{
	float: none;
	margin: 2% auto;
}
form input[type=text]
{
	font-size: 20px;
	border: solid 0px #fff;
	margin: 2% auto;
}
</style>
</head>
<body>
	<div id="main" >
	<div id="content" class="clearfix">
		<div class="head">
		Please Wait for Your Verification Code.<br/>It will be sent to you as soon as possible.
		<br/>
		<br/><br/><br/>
		If you Got it.Then Verify Below.
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<input type="text" name="ver_code" /><br/>
			<input type="submit" id="submit" value="Verify" name="verify" />
		</form>
		<?php if($flag==1) echo "<br/>".$errmsg; ?>
		</div>
	</div>
	</div>
</body>
</html>