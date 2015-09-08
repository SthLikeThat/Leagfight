<?php
require_once "../lib/database_class.php";

class townFunctions{

    private $db;

    public function __construct($db = false, $acc = false, $info = false) {
        if(!$db){
			$this->db = new DataBase();
			session_start();
			$this->account = $this->db->select("accounts", array("*"), "`id_account` =". $_SESSION["id_account"], "id_account", true, 1)[0];
			$this->user_information = $this->db->select("user_information", array("lvl"), "`id_user` =". $_SESSION["id_account"], "id_user", true, 1)[0];
		}
		else{
			$this->db = $db;
			session_start();
			$this->account = $acc;
			$this->user_information = $info;
		}
    }

    public function query($query){
        if (!$result = $this->mysqli->query($query)) {
            return $query." Ошибка: ".$this->mysqli->error;
        }
        return $result;
    }
	
	public function get_town_item($thing){
		$thing = (int) $thing;
		switch ($thing){
			case 1:
				return $this->getHouse();
			case 2:
				return $this->getLeagues();
			case 3:
				return $this->getSmith();
			case 4:
				return $this->getWork();
			case 5:
				return $this->getAdvertising();
			case 6:
				return $this->getBattle();
		}
	}
	
	private function getHouse(){
		$house = $this->db->getAllOnField("user_house", "id_user", $this->account["id_account"], "id_user", "");
		$houseItems = $this->db->getAll("user_house_information", "", ""); 
		
		$text = '<table class="table table-bordered table-striped table-hover " style=" margin-top: 5px;text-align: center;"><thead>
		<tr  style=" font-weight: 700;">
			 <td class="my-color"></td>
			 <td class="my-color">Название</td>
			 <td class="my-color">Значение</td>
			 <td class="my-color">Описание</td>
			 <td class="my-color">Уровень</td>
			 <td class="my-color"> Цена</td>
			 <td class="my-color"> Улучшить </td>
		</tr></thead><tbody>
		';
		foreach($houseItems as $item){
			$item["price"] = $house[$item["name"]] * $item["startPrice"] * 1.5;
			$item["lvl"] = $house[$item["name"]];
			$item["percent"] = $item["value"] * $house[$item["name"]];
			$text .= $this->db->getReplaceTemplate($item, "house");
		}
		$text .= "</tbody></table>";
		return $text;
	}
	
	private function getLeagues(){
		$srAll["contentAdv"] = $text;
		return $this->db->getReplaceTemplate($srAll, "league");
	}
	
	private function getSmith(){
		$sr["inventory"] = $this->getInventory();
		$text .= $this->db->getReplaceTemplate($sr, "smith");
		return $text;
	}
	
	private function getInventory(){
		$inventory = $this->db->getAllOnField("user_inventory", "id_user", $_SESSION["id_account"], "id_user", true, 1);
		$inventory_database = $this->db->ancillary->getAllInventory($inventory, false)["inventory"];
		$count = count($inventory);
		
		for($i = 1; $i < $count; $i++){
			if($inventory["slot$i"] != "0" and $inventory["slot$i"] != "999"){
				$invItem = (array) json_decode($inventory["slot$i"]);
				$info = $this->db->ancillary->getTableInfo($invItem, $inventory_database, true);
				$sr["info"] = $info["html"];
				$sr["price"] = $info["array"]["price"];
				$sr["hash"] = $invItem["hash"];
				$sr["id"] = $invItem["id"];
				$sr["armor"] = $invItem["armor"];
				$sr["damage"] = $invItem["damage"];
				$sr["crit"] = $invItem["crit"];
				if($invItem["id"] < 500)
					$sr["type"] = 1;
				else
					$sr["type"] = 2;
				
				//Добавляем чистые значения урона/крита/брони
				foreach($inventory_database as $inventory_item){
					if($inventory_item["id"] == $invItem["id"])
						break;
				}
				$sr["armor-value"] = $inventory_item["armor"];
				$sr["damage-value"] = $inventory_item["damage"];
				$sr["crit-value"] = $inventory_item["crit"];
				$text .= $this->db->getReplaceTemplate($sr, "inventoryItemSmith");
			}
			if($inventory["slot$i"] == "999"){
				break;
			}
		}
		return $text;
	}
	
	private function getWork(){
		return false;
	}
	
	private function getAdvertising(){
		return false;
	}
	
	private function getBattle(){
		return false;
	}
	
	public function up_house($id){
		$id = (int) $id;
		$resultData = array();
		
		//Информация о вещи в базе
		$house_information = $this->db->select("user_house_information", array("*"), "`id` =  {$id}", "id", true, 1)[0];
		if(is_null($house_information)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[2]");
			die(json_encode($resultData));
		}
		$house = $this->db->getAllOnField("user_house", "id_user", $this->account["id_account"], "id_user", "");
		$name = $house_information["name"];
		$price = $house_information["startPrice"] * $house[$name] * 1.5;
		
		if($house[$name] == $house_information["maxSize"]){
			$resultData = array("result" => false, "error" => "У вас максимальный уровень прокачки");
			die(json_encode($resultData));
		}
		
		$gold = $this->db->select("user_resources", array("Gold"), "`id_user` = {$this->account["id_account"]}", "id_user", true, 1)[0];
		if($gold["Gold"] >= $price){
			$this->db->mysqli->autocommit(false);
			$this->db->update("user_resources", array("Gold" => $gold["Gold"] - $price), "`id_user` = " . $this->account["id_account"]);
			$this->db->update("user_house", array($house_information["name"] => $house[$name] + 1), "`id_user` = " . $this->account["id_account"]);
			$result = $this->db->mysqli->commit();
			
			if($result){
				$resultData["result"] = true;
				$resultData["name"] = $name;
				$resultData["gold"] = $gold["Gold"] - $price;
				$resultData["lvl"] = $house[$name] + 1;
				$resultData["value"] = $house_information["value"] * $resultData["lvl"] . $house_information["prefix"];
				$resultData["price"] = $house_information["startPrice"] * $resultData["lvl"] * 1.5;
				die(json_encode($resultData));
			}
			else{
				$resultData = array("result" => false, "error" => "Возникла серверная ошибка[3]");
				die(json_encode($resultData));
			}
		}
		else{
			$resultData = array("result" => false, "error" => "У вас не хватает золота на прокачку");
			die(json_encode($resultData));
		}
	}
	
}

if($_POST["WhatIMustDo"]){
	$townFunctions = new townFunctions();
	switch ($_POST["WhatIMustDo"]) {
		case "up_house":
			$townFunctions->up_house($_POST["id"]);
			break;
		case "get_town_item":
			echo $townFunctions->get_town_item($_POST["thing"]);
			break;
	}
}
?>