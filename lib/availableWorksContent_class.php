<?php
require_once "modules_class.php";
require_once "database_class.php";

class availableWorks extends Modules {
	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	protected function getCenter() {
		$sr["works"] = $this->getWorks();
		return $this->getReplaceTemplate($sr, "allWorks");
	}
	
	private function getWorks(){
		$works = $this->db->getAll("available_works", "", "");
		for($i = 0; $i < count($works); $i++){
			if($this->user[$works[$i]["type_require"]] >= $works[$i]["number_require"]){
				$sr["title"] = $works[$i]["title"];
				$sr["description"] = $works[$i]["description"];
				$sr["gold"] = $works[$i]["gold"];
				$sr["another"] = $works[$i]["another"];
				$sr["type_require"] = $works[$i]["type_require"];
				$sr["number_require"] = $works[$i]["number_require"];
				$sr["id"] = $works[$i]["id"];
				$text .= $this->getReplaceTemplate($sr, "work_block");
			}
		}
		return $text;
	}
}
?>