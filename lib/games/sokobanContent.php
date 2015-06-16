<?php
require_once $_SERVER['DOCUMENT_ROOT']."/lib/modules_class.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/database_class.php";

class sokobanContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
		$this->db = new DataBase();
	}
	
	protected function getCenter() {
		$sr["table"] = $this->getTable(31);
		$sr["menu"] = $this->getMenuGame();
		$sr["id"] = "Sokoban";
		return $this->getReplaceTemplate($sr, "memoryPuzzle");
	}
	
	protected function getHeader(){
		return false;
	}
	
	private function getTable($max){
		$table = $this->getArray($max);
		for($row = 1; $row <= $max; $row++){
			$text .= "<tr id='row_$row'>";
			for($cell = 1; $cell <= $max; $cell++){
				$temp = $table[$row][$cell];
					foreach($temp as $key => $value){
						if($value)
							$type .= $key." ";
					}
				$text .= "<td id='$row"."_$cell' class='$type'></td>";
				$type = "";
			}
			$text .= "</tr>";
		}
		return $text;
	}
	
	private function getArray($max){
		$result = array();
		$cellArr = array("top"=>0, "right"=>0, "bottom"=>0, "left"=>0 );
		for($row = 1; $row <= $max; $row++){
			for($cell = 1; $cell <= $max; $cell++){
					foreach($cellArr as $key => $value){
						$rand = rand(0,1);
						$result[$row][$cell][$key] = $rand;
						$total += $rand;
						if($total == 3)
							$result[$row][$cell][$key] = 0;
					}
					if($total == 1){
						if($result[$row][$cell]["top"] == 0 and isset($result[$row-1][$cell])){
							$result[$row-1][$cell]["bottom"] = 0;
						}
						if($result[$row][$cell]["left"] == 0 and isset($result[$row][$cell-1])){
							$result[$row][$cell-1]["left"] = 0;
						}
					}
			}
		}
		return $result;
	}
	
	private function getMenuGame(){
		$text .= " ";
		return $text;
	}
}
?>