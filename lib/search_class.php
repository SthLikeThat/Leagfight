<?php
require_once "global_class.php";

class search extends GlobalClass{

	public function __construct($db) {
		parent:: __construct("users", $db);
	}
	
	public function getSearchClans($value, $sort, $descOrNot){
		if($value == "") $value = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
		if($descOrNot == "up")$descOrNot = "DESC";
		else $descOrNot = "";
			$table_name = $this->config->db_prefix."clans";
			$query = "SELECT * FROM `$table_name` WHERE `name` LIKE '%$value%' ORDER BY `$sort` $descOrNot LIMIT 50";
				$result_set = $this->query($query);
					if (!$result_set) return false;
					$i = 0;
					while ($row = $result_set->fetch_assoc()){
						$data[$i] = $row;
						$i++;
					}
					$result_set->close();
					return $data;
	}
	
	public function getSearchClients($value, $sort, $descOrNot){
		if($value == "") $value = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";
		if($descOrNot == "up")$descOrNot = "DESC";
		else $descOrNot = "";
			$table_name = $this->config->db_prefix."users";
			$query = "SELECT * FROM `$table_name` WHERE `login` LIKE '%$value%' ORDER BY `$sort` $descOrNot LIMIT 50";
				$result_set = $this->query($query);
					if (!$result_set) return false;
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