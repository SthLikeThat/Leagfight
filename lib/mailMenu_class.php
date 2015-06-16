<?php
require_once "global_class.php";

class mailMenu extends GlobalClass{

	public function __construct($db) {
		parent:: __construct("mail_menu", $db);
	}
	
}
?>