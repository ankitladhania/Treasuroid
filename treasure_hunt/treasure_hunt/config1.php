<?php
$user_name = "root";
	$pass_word = "";
	$database = "treasure_hunt";
	$server = "127.0.0.1";

	$db_handle = mysql_connect($server, $user_name, $pass_word);
	$db_found = mysql_select_db($database, $db_handle);
?>