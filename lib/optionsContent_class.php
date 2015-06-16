<?php
require_once "modules_class.php";

class optionsContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	protected function getCenter() {
			$sr["messAttacker"] = $this->user["messAttacker"];
			$sr["description"] = $this->user["description"];
		return $this->getReplaceTemplate($sr, "options");
	}
}
?>