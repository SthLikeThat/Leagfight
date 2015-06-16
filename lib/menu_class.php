<?php
require_once "global_class.php";

class Menu extends GlobalClass {

	public function __construct($db) {
		parent:: __construct("menu", $db);
	}
}
?>