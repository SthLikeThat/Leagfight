<?php
require_once $_SERVER['DOCUMENT_ROOT']."/lib/modules_class.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/database_class.php";

class memPuzz_Content extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
		$this->db = new DataBase();
	}
	
	protected function getCenter() {
		$sr["table"] = $this->getTable($this->data["id"]);
		$sr["menu"] = $this->getMenuGame($this->data["id"]);
		$sr["id"] = "memoryPuzzle";
		return $this->getReplaceTemplate($sr, "memoryPuzzle");
	}
	
	private function getTable($id){
		$newArray = array();
		$array = $this->db->getElementOnID("memoryPuzzle", $id);
		$cells = count(unserialize($array["row0"]));
		for($i = 0; $i < $cells; $i++){
			$text .= "<tr id='tr_$i'>";
			$temp = unserialize($array["row$i"]);
			$newArray = array_merge($newArray,$temp);
			for($j = 0; $j < $cells; $j++){
				$text .= "<td id='{$i}_{$j}' class='{$temp[$j]["ready"]}'></td>";
			}
			$text .= "</tr>";
		}
	return $text;
	}
	
	private function getMenuGame($id){
		$text .= " <input type='checkbox' id='trailOn' >Включить следы<input type='button' value='Дай шанс' onclick='giveChanceMemory($id)' style='float:left;'/>";
		return $text;
	}
}
?>