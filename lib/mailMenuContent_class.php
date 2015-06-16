<?php
require_once "modules_class.php";

class mailMenuContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	protected function getCenter() {
		$sr["menuItem"] = $this->getMailMenu();
		return $this->getReplaceTemplate($sr, "mail");
	}
	
	protected function getMailMenu(){
		$menu = $this->mailMenu->getAll();
		for ($i=0;$i<count($menu);$i++){
			$sr["image"] = $menu[$i]["image"];
			$sr["title"] = $menu[$i]["title"];
			$sr["link"] = $menu[$i]["link"];
			$text .= $this->getReplaceTemplate($sr, "mailMenuItem");
		}
		return $text;
	}
}
?>