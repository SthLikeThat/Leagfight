<?php
require_once "global_class.php";

class mail extends GlobalClass{

	public function __construct($db) {
		parent:: __construct("mail", $db);
	}
	
	public function getMail($id){
		$query = "SELECT * FROM `smgys_mail` WHERE `idUser` = $id || `idSender` = $id ORDER BY `date` DESC";
		$result_set = $this->query($query);
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		return $data;
	}
	
	public function getMailUsers($idUser){
		$query = "SELECT `idDialog` FROM `smgys_mail` WHERE `idUser` = $idUser || `idSender` =$idUser ORDER BY `date` DESC";
		$result_set = $this->query($query);
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row["idDialog"];
			$i++;
		}
		$result_set->close();
		if(!$data) return false;
		$data = array_unique($data);
		$data = array_values($data);
		return $data;
	}
}
?>