<?php
require_once "global_class.php";

class shop extends GlobalClass{

	public function __construct($db) {
		parent:: __construct("weapon", $db);
	}
	
	public function getShop($table_name,$lvl){
		$table_name = $this->config->db_prefix.$table_name;
		$query = "SELECT * FROM $table_name WHERE `requiredlvl` <= $lvl ORDER BY `requiredlvl` DESC";
		$result_set = $this->query($query);
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		return $data;
	}
}
?>