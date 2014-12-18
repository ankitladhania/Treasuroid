<?php
session_start();
require_once("constant.php");
function quote_smart($str) {

   $str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);

}
$flag=0;
if(isset($_POST['submit']))
{
	$team_name=quote_smart($_POST['team_name']);
	$team_name_lower=strtolower($team_name);
	$username=quote_smart($_POST['username']);
	$password=quote_smart($_POST['password']);
	$mobile2=quote_smart($_POST['mobile2']);
	$mobile3=quote_smart($_POST['mobile3']);
	$mobile4=quote_smart($_POST['mobile4']);
	$college=quote_smart($_POST['college']);
	if($username!=0 && $mobile2!=0 && strlen($team_name)>3)
	{
		$e_o_n_query="SELECT * FROM main_table WHERE username='".$username."' OR mobile2='".$username."' OR mobile3='".$username."' OR mobile4='".$username."' OR ";
		$e_o_n_query.="username='".$mobile2."' OR mobile2='".$mobile2."' OR mobile3='".$mobile2."' OR mobile4='".$mobile2."'";
		if($mobile3!=0)
		{
			$e_o_n_query.=" OR username='".$mobile3."' OR mobile2='".$mobile3."' OR mobile3='".$mobile3."' OR mobile4='".$mobile3."'";
		}
		if($mobile4!=0)
		{
			$e_o_n_query.=" OR username='".$mobile4."' OR mobile2='".$mobile4."' OR mobile3='".$mobile4."' OR mobile4='".$mobile4."'";
		}
		$e_o_n_result=mysqli_query($db_connection,$e_o_n_query);
		if($e_o_n_result===false)
		{
			$flag=1;
			$errorMessage= "Error".mysqli_error($db_connection);
		}
		if(mysqli_num_rows($e_o_n_result)>0)
		{
			$flag=1;
			$errorMessage="Number already exists";
		}
		else
		{
			//check if exists or not
			$ver_code=rand(1000,10000)*rand(1,1000);
			//$password.=$username;
			$password.="ankitinISM";
			$password=md5($password);
			$log_time="CURRENT_TIMESTAMP"; 
			$stmt = $db_connection->prepare('INSERT INTO `main_table`(`ver_code`,`log_time`,`username`, `password`, `mobile2`, `mobile3`, `mobile4`,`college`,`team_name`,`team_name_lower`) VALUES (?,CURRENT_TIMESTAMP,?,?,?,?,?,?,?,?)');
			$stmt->bind_param('sisiiisss',$ver_code, $username,$password,$mobile2,$mobile3,$mobile4,$college,$team_name,$team_name_lower);
			if (!$stmt->execute()) {
    		    $flag=1;
			$errorMessage= "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    		}
			else
			{
				$flag=0;
				$errorMessage= "Success";
				header("Location:success.php");
			}	
			$stmt->close();
		}
	}
	else
	{
		$flag=1;
		$errorMessage= "Error Please check the Fields";
	}
		/*
		$e_o_n_query="SELECT * FROM main_table WHERE username=? OR mobile2=? OR mobile3=? OR mobile4=? OR";
	$e_o_n_query.="username=? OR mobile2=? OR mobile3=? OR mobile4=? OR";
	if($mobile3!=0)
	{
		$e_o_n_query.="username=? OR mobile2=? OR mobile3=? OR mobile4=? OR";
	}
	if($mobile4!=0)
	{
		$e_o_n_query.="username=? OR mobile2=? OR mobile3=? OR mobile4=? ";
	}
	if($mobile3!=0 && $mobile4!=0)
	{
		$stmt = $db_connection->prepare($e_o_n_query);
		$stmt->bind_param("iiiiiiiiiiiiiiii", $username,$username,$username,$username,$mobile2,$mobile2,$mobile2,$mobile2,$mobile3,$mobile3,$mobile3,$mobile3,$mobile4,$mobile4,$mobile4,$mobile4);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		echo "Success";
	}
	else if($mobile3!=0)
	{
		$stmt = $db_connection->prepare($e_o_n_query);
		$stmt->bind_param('iiiiiiiiiiii', $username,$username,$username,$username,$mobile2,$mobile2,$mobile2,$mobile2,$mobile3,$mobile3,$mobile3,$mobile3);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		echo "Success";
	}
	else if($mobile4!=0)
	{
		$stmt = $db_connection->prepare($e_o_n_query);
		$stmt->bind_param('iiiiiiiiiiii', $username,$username,$username,$username,$mobile2,$mobile2,$mobile2,$mobile2,$mobile4,$mobile4,$mobile4,$mobile4);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		echo "Success";
	}
	else
	{
		$stmt = $db_connection->prepare($e_o_n_query);
		$stmt->bind_param("iiiiiiii", $username,$username,$username,$username,$mobile2,$mobile2,$mobile2,$mobile2);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		echo "Success";
	}
	/*
	
	
	*/
	//$result = $stmt->get_result();
	//while ($row = $result->fetch_assoc()) {
	    // do something with $row
	//}
	mysqli_close($db_connection);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Untitled Document</title>
<style type="text/css">

#error{
	margin: 0 20%;
	font-size: 14px;
	background-color: #fff;
	color: #f91b1b;
}
#error ul{
	list-style: none;
}
#error ul li label.error
{
	background-color: #fff;
}
.error
{
	background-color: #FF9999;
}
.team_name_result td
{
	display: none;
}
</style>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script>
$(document).ready(function() {
	var container = $('#error');
 $('#signup').validate({
   
   rules: {
     password: {
        required: true,
        rangelength:[8,16]
     },
     confirm_password: {equalTo:'#password'},
     spam: "required"
   }, //end rules
   messages: {
      password: {
        required: '<b>Please type a Password</b>',
        rangelength: '<b>Password must be between 8 and 16 characters long.</b>'
      },
      confirm_password: {
        equalTo: '<b>The two passwords do not match.</b>'
      }
   },
	errorContainer: container,
		errorLabelContainer: $("ul", container),
		wrapper: 'li'

  }); // end validate 
	var regEx = new RegExp("/[0-9]/");
	
	$("#signup").bind("submit", function(event) {

    if ($("#username,#mobile2").val().length != 10 && !$("#username,#mobile2").val().match(regEx)) 
    {
        $("#error ul").append('<li><b>Please enter a valid Number of 10 digit</b></li>');
        return false;
    	if($('#mobile3').val()!="")
    	{
    		if ($("#mobile3").val().length != 10 && !$("#mobile3").val().match(regEx)) {
    	   	$("#error ul").append('<li><b>Please enter a valid Number of 10 digit</b></li>');
    		return false;
    		}

    	}
    	if($('#mobile4').val()!="")
    	{
    		if ($("#mobile4").val().length != 10 && !$("#mobile4").val().match(regEx)) {
    	   	$("#error ul").append('<li><b>Please enter a valid Number of 10 digit</b></li>');
    		return false;
    		}
    	}
    }
    else
    {
    	return true;
    }
	});
	var search_query=$("#team_name");
	search_query.keyup(function(event){
			event.preventDefault();
			search_ajax_way();
			if($("#team_result").html()=="")
			{
				console.log("1");
				$("#submit").removeAttr("disabled");
				$(".team_name_result td").css('display','none');
			}
			else
			{
				console.log("0");
				$("#submit").attr('disabled','disabled');
			}
			
		});

	search_query.keydown(function(event){
	});
	function search_ajax_way(){
			var search_this=$("#team_name").val();
			if(search_this!='')
			{
				if(search_this.length > 3)
				{
					$(".team_name_result td").css('display','block');
					$.post("team_search.php", {searchit : search_this}, function(data){
					$("#team_result").html(data);
					});

				}
				else
				{
					$(".team_name_result td").css('display','none');
				}
			}
			else
			{
				$(".team_name_result td").css('display','none');
			}
			

		};
}); // end ready
</script>
</head>
<body>
<div id="main" >
<div id="content" class="clearfix">
<div class="header">Register</div>
<div class="disclaimer">*Username should be the mobile number of android enabled phone.</div>
<div class="disclaimer">^Team Name Should have minimum length of 3.</div>

<div class="error" id="error"><ul><?php if($flag==1) echo "<li>".$errorMessage."</li>"; ?></ul></div>
                       <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="signup" method="POST">
							<table>
								<tr>
									<td><label for="team_name" >Team Name ^:</label></td>
									<td><input type="text" value="<?php if(isset($_POST['team_name'])) echo $_POST['team_name']; ?>" id="team_name" name="team_name" class="required" title="<b>Please enter Team Name</b>"></td>
								</tr>
								<tr class="team_name_result">
									<td id="team_result" class=""></td>
								</tr>
								<tr>
									<td><label for="name">Username * :</label></td>
									<td><input type="text" id="name" class="pre_num" name="username" 
                                    value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" class="required" title="<b>Please enter Username</b>"></td>
								</tr>
								
								<tr>
									<td><label for="password">Password:</label></td>
									<td><input type="password" id="password" name="password"></td>
								</tr>
								<tr>
									<td><label for="password">Confirm Password:</label></td>
									<td><input type="password" id="confirm_password" name="confirm_password"></td>
								</tr>
                                <tr>
									<td><label for="mobile2">Mobile 2 :</label></td>
									<td><input type="text" id="mobile2" class="pre_num" name="mobile2" value="<?php if(isset($_POST['mobile2'])) echo $_POST['mobile2']; ?>" class="required" title="<b>Please enter Field Mobile 2</b>"></td>
								</tr>
                                <tr>
									<td><label for="mobile3">Mobile 3 :</label></td>
									<td><input type="text" id="mobile3" class="pre_num" value="<?php if(isset($_POST['mobile3'])) echo $_POST['mobile3']; ?>" name="mobile3"></td>
								</tr>
                                <tr>
									<td><label for="mobile4">Mobile 4 :</label></td>
									<td><input type="text" id="mobile4" class="pre_num" value="<?php if(isset($_POST['mobile4'])) echo $_POST['mobile4']; ?>" name="mobile4"></td>
								</tr>
								<tr>
									<td><label for="mobile4">College:</label></td>
									<td><input type="text" id="college" name="college"></td>
								</tr>
								
							</table>
							<input type="submit" value="Submit" name="submit" id="submit">
						</form>
					</div>
 </div>
</body>
</html>