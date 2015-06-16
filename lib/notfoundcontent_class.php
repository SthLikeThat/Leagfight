<?php
require_once "modules_class.php";

class NotFoundContent extends Modules {
	
	public function __construct($db) {
		parent::__construct($db);
		header("HTTP/1.0 404 Not Found");
	}

	protected function getCenter(){
		return $this->getTemplate("notfound");
	}
	
}
?>