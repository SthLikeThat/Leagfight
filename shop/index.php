<?php
	mb_internal_encoding("UTF-8");
	require_once "../lib/database_class.php";
	$db = new DataBase();
	
	require_once "shopContent_class.php";
	$content = new shopContent($db);
	echo $content->getContent();
?>