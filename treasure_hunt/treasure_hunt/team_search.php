<?php
require_once('constant.php');
function quote_smart($str) {

   $str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);

}
$term=quote_smart($_POST['searchit']);
if(strlen($term)<=2)
	echo "";
else{
	$query = mysqli_query($db_connection,"select team_name_lower from main_table where team_name_lower like '{$term}%'");
	$string = '';

	if (mysqli_num_rows($query))
	{
			$row = mysqli_fetch_assoc($query);
			if($row['team_name_lower']!="")
			{
				echo '<div class="disclaimer">Team Name Already Exist</div>';
			}
			else
			{
				echo "";
			}
		
	}
}



?>