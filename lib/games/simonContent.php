<?php
require_once $_SERVER['DOCUMENT_ROOT']."/lib/modules_class.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/database_class.php";

class simonContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
		$this->db = new DataBase();
	}
	
	protected function getCenter() {
		$sr["table"] = $this->getTable($this->data["buttons"]);
		$sr["menu"] = $this->getMenuGame();
		$sr["id"] = "Simon";
		return $this->getReplaceTemplate($sr, "memoryPuzzle");
	}
	
	private function getTable($buttons){
		$text .= "<tr>";
		for($i = 1; $i <= $buttons; $i++)
			$text .= "<td class='SimonButton' id='simon_$i'>$i</td>";
		$text .= "</tr>";
		return $text;
	}
	
	private function getMenuGame(){
		$text .= "<table><tr ><td><a href='#' onclick='startSimon()'>СТАРТ</a></td>";
		$text .= "<td ></td><td>Уровень</td><td id='lvlSimon'>1</td></tr></table>";
		return $text;
	}
}
?>