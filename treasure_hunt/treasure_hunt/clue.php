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
if($db_connection)
{
	if($_SESSION['clue']<=5)
	{
		$clue_id=$_POST['clue'];
		$clue_id=quote_smart($clue_id);
		$stmt = $db_connection->prepare('SELECT * FROM clue_table WHERE clue_id=?');
		$stmt->bind_param('i',$clue_id);
		if (!$stmt->execute()) {
    	    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    	}
		else
		{
			$result = $stmt->get_result();
			$db_field = $result->fetch_assoc();
			$stmt->close();
			if($result->num_rows>0)
			{
				if($db_field)
				{
					$e_o_n=$db_connection->prepare('SELECT * FROM `clue_log_table` WHERE clue_id=? AND user_id=?');
					$e_o_n->bind_param('ii',$clue_id,$_SESSION['id']);
					if (!$e_o_n->execute()) {
    	    			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    				}
					else
					{
						$e_o_n_field = $e_o_n->get_result();
						$e_o_n_array=$e_o_n_field->fetch_assoc();
						$e_o_n->close();
					}
					if($e_o_n_field->num_rows == 0)
					{
						$update_clue_log = $db_connection->prepare('INSERT INTO `clue_log_table`(`clue_id`, `user_id`, `log_time`) VALUES (?,?,CURRENT_TIMESTAMP)');
						$update_clue_log->bind_param('ii',$clue_id,$_SESSION['id']);
						if (!$update_clue_log->execute()) {
    	 				   echo "Execute failed: (" . $update_clue_log->errno . ") " . $update_clue_log->error;
   						}
   						$_SESSION['clue']+=1;
   						$_SESSION['point']-=$db_field['clue_point'];
   						$update_main_table=$db_connection->prepare('UPDATE `main_table` SET `no_of_clues`=? WHERE user_id=?');
						$update_main_table->bind_param('ii',$_SESSION['clue'],$_SESSION['id']);
						if (!$update_main_table->execute()) {
    	 				   echo "Execute failed: (" . $update_main_table->errno . ") " . $update_main_table->error;
   						}
   					}
					echo "Clue :   ".$db_field['clue'];
					
				}
			}
			else
				echo "No Clue for Such an Easy Question.";
		}
	}
	else
	{
		echo "No More Clues Can Be Given";
	}
}
?>