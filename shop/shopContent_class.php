<?php
require_once "../lib/modules_class.php";
require_once "shop_functions.php";

class shopContent extends Modules {
	
	private $functions;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->functions = new shopFunctions($db, $this->account, $this->user_information);
	}
	
	protected function getCenter() {
		$sr_shop["weapon"] = $this->functions->get_things(1);
		$sr["menuItem"] = $this->getReplaceTemplate($sr_shop, "shopMenu");
		$sr["content"] = "";
		return $this->getReplaceTemplate($sr, "shop");
	}
	
	protected function getTitle(){
		return "Магазин - LF";
	}
	
	protected function getShortcut_icon(){
		return "shop";
	}
}
?>