<?php
require_once "global_class.php";

class arena extends GlobalClass{

	public function __construct($db) {
		parent:: __construct("users", $db);
	}
	
	public function getRandomEnemy($minlvl, $maxlvl){
		$userid = $_SESSION["id"];
		$query = "SELECT * FROM `smgys_users` WHERE `lvl` >= $minlvl AND `lvl` <= $maxlvl AND `id` != $userid";
		$result_set = $this->query($query);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		if(!$data)
			return false;
		$rand = rand(0,count($data)-1);
		return $data[$rand];
	}
	
	public function getRandomBot($lvl){
		$table_name = $this->config->db_prefix."arena_bots";
		$query = "SELECT * FROM $table_name WHERE `lvl` = $lvl";
		$result_set = $this->query($query);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		$rand = rand(0,count($data)-1);
		return $data[$rand];
	}
	
	public function getUser(){
		return $this->getAllOnField("email", $_SESSION["email"], "", "");
	}
}
?>