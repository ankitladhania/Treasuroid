<?php
include('constant.php');
echo '<body bgcolor=#555><h1><font color=#0066cc>RANK-LIST<font></h1>';
$SQL="SELECT DISTINCT user_id FROM question_log_table ORDER BY question_id,log_time ";
$result=mysqli_query($db_connection,$SQL);
while($db_field = mysqli_fetch_assoc($result))
{
	echo $db_field['user_id'];echo '<br>';
}
echo '</body>';
?>