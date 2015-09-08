<?php
	mb_internal_encoding("UTF-8");
	require_once "../lib/database_class.php";
	$db = new DataBase();
	
	require_once "townContent_class.php";
	$content = new townContent($db);
	echo $content->getContent();
?>