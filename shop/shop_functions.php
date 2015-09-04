<?php
require_once "../lib/database_class.php";

class shopFunctions extends DataBase{

    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = $this;
        session_start();
        $this->account = $this->db->select("accounts", array("*"), "`id_account` =". $_SESSION["id_account"], "id_account")[0];
		$this->user_information = $this->db->select("user_information", array("lvl"), "`id_user` =". $_SESSION["id_account"], "id_user")[0];
    }

    public function query($query){
        if (!$result = $this->mysqli->query($query)) {
            return $query." Ошибка: ".$this->mysqli->error;
        }
        return $result;
    }
	
	final protected function getAuthUser() {
		if(!$_SESSION["id_account"]){
            return false;
		}
		if($_SESSION["hash"] === $this->account["user_hash"]){
			return true;
		}
		else{
            return false;
		}
	}
	
	public function get_things($thing, $ajax = false){
		$thing = (int) $thing;
		if($thing == 1)
			$table_name = $this->config->db_prefix."shop_weapon";
		if($thing > 1 && $thing < 7)
			$table_name = $this->config->db_prefix."shop_armor";
		if($thing == 7)
			$table_name = $this->config->db_prefix."shop_something";
		$lvl = $this->user_information["lvl"] + 3;
		$query = "SELECT * FROM `{$table_name}` WHERE `required_lvl` <= {$lvl} && `thing` = {$thing} ORDER BY `required_lvl` DESC";
		$result_set = $this->query($query);
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		
		$count = count($data);
		for($i = 0; $i < $count; $i++){
			if($thing == 1)
				$text .= $this->getPageWeapon($data[$i]);
			if($thing > 1 && $thing < 7)
				$text .= $this->getPageArmor($data[$i]);
			if($thing == 7)
				$text .= $this->getPageSmth($data[$i]);
		}
		
		if(!$ajax)
			return $text;
		else
			echo $text;
	}
	
	private function getPageWeapon($weapon){
		if($weapon["type"] == 1) $weapon["type_word"]="Одноручное";
		if($weapon["type"] == 2) $weapon["type_word"]="Двуручное";
		if($weapon["type"] == 3) $weapon["type_word"]="Древковое";
		if($weapon["type_damage"] == 1) $weapon["type_damage_word"]="Колющее";
		if($weapon["type_damage"] == 2) $weapon["type_damage_word"]="Режущее";
		if($weapon["type_damage"] == 3) $weapon["type_damage_word"]="Дробящее";
		$text = $this->getReplaceTemplate($weapon, "weapon");
		return $text;
	}
	
	private function getPageArmor($armor){
		if($armor["type_defence"] == 1) $armor["type"]="Лёгкая";
		if($armor["type_defence"] == 2) $armor["type"]="Средняя";
		if($armor["type_defence"] == 3) $armor["type"]="Тяжелая";
		$text = $this->getReplaceTemplate($armor, "armor");
		return $text;
	}
	
	private function getPageSmth($something){
		if($something["type_effect"] == "healPercent")
			$something["description"] = "Исцеляет {$something["value_effect"]}% от максимального здоровья";
		$text .= $this->getReplaceTemplate($something, "food");
		return $text;
	}
	
	public function buy($id){
		$hash = $this->generateCode();
		$id = (int) $id;
		$freeSlot = false;
		$resultData = array();
		//  Пример ответа $resultData = array("result" => true, "money" => "1233", "resource" => "gold", "error" => 0);
		
		
		
		
		//Проверяем есть ли вещь с подобным хешем и есть ли вообще свободный слот
		if($id < 1000){
			$this->inventory = $this->db->getAllOnField("user_inventory", "id_user", $_SESSION["id_account"], "id_user", "");
			$count = count($this->inventory);
			for($i = 1; $i < $count; $i++){
				if($this->inventory["slot$i"] != "0" and $this->inventory["slot$i"] != "999"){
					$invItem = (array) json_decode($this->inventory["slot$i"]);
					if($invItem["hash"] == $hash){
						$i = 1;
						$hash = $this->generateCode();
					}
				}
				if($this->inventory["slot$i"] == "0" && !$freeSlot)
					$freeSlot = "slot$i";
			}
		}
		else{
			$this->potions = $this->db->getAllOnField("user_potions", "id_user", $_SESSION["id_account"], "id_user", "");
			$count = count($this->potions);
			for($i = 1; $i < $count; $i++){
				if($this->potions["potion$i"] != "0" and $this->potions["potion$i"] != "999"){
					$invItem = (array) json_decode($this->potions["potion$i"]);
					//Добавляем еще одно зелье, если уже есть такое же
					if($invItem["id"] == $id){
						$potion_in_inventory = true;
						$freeSlot = "potion$i";
						$newInvItem = $invItem;
						$newInvItem["count"]++;
						break;
					}
				}
				if($this->inventory["potion$i"] == "0" && !$freeSlot)
					$freeSlot = "potion$i";
			}
		}
		if(!$freeSlot && !$potion_in_inventory){
			$resultData = array("result" => false, "error" => "Нет свободного места в инвентаре");
			die(json_encode($resultData));
		}
		
		if($id < 500){
			$table_name = "shop_weapon";
			$newInvItem = array("hash" => $hash, "id" => $id, "crit" => 0, "damage" => 0);
		}
		if($id > 500 && $id < 1000){
			$table_name = "shop_armor";
			$newInvItem = array("hash" => $hash, "id"=> $id, "armor" => 0);
		}
		if( $id > 1000){
			$table_name = "shop_something";
			if(!$potion_in_inventory)
				$newInvItem = array( "id"=> $id, "image" => false, "count" => 1);
		}
		$thing = $this->db->getAllOnField($table_name, "id", $id, "", "");
		
		if( $id > 1000 && !$potion_in_inventory){
			$newInvItem["image"] = $thing["name"];
			if($thing["valuta"] == "coinBlack")
				$valuta = "Gold";
		}
		else
			$valuta = "Gold";
		
		//Проверяем на уровень
		if($thing["required_lvl"] > $this->user_information["lvl"] + 3){
			$resultData = array("result" => false, "error" => "Ваш уровень слишком мал для этой вещи");
			die(json_encode($resultData));
		}
		
		//Проверяем на наличие золота
		$this->resources = $this->db->select("user_resources", array("*"), "`id_user` =". $_SESSION["id_account"], "id_user")[0];
		
		
		if($this->resources[$valuta] > $thing["price"]){
			$statistic_array = array("Gold" => 0, "Another" => 0, "equipment" => 0, "potions" => 0);
			$this->statistic = $this->db->select("user_statistic", array("shop_statistic"), "`id_user` =". $_SESSION["id_account"], "id_user")[0];
			
			//Создаём новую статистику, если её еще нет
			if(is_null($this->statistic["shop_statistic"])){
				$new_statistic = $statistic_array;
				$new_statistic[$valuta] += $thing["price"];
				if($id < 1000)
					$new_statistic["equipment"]++;
				else
					$new_statistic["potions"]++;
			}
			else{
				$new_statistic = json_decode($this->statistic["shop_statistic"]);
				if(is_null($new_statistic)){
					$resultData = array("result" => false, "error" => "Возникла серверная ошибка[4]");
					die(json_encode($resultData));
				}
				$new_statistic = (array) $new_statistic;
				$new_statistic[$valuta] += $thing["price"];
				if($id < 1000)
					$new_statistic["equipment"]++;
				else
					$new_statistic["potions"]++;
			}
			
			$this->mysqli->autocommit(false);
			$this->db->update("user_statistic", array("shop_statistic" => json_encode($new_statistic)), "`id_user` =" . $this->account["id_account"]);
			
			$newResource = $this->resources[$valuta] - $thing["price"];
			if($id < 1000)
				$this->db->update("user_inventory", array( $freeSlot => json_encode($newInvItem)), "`id_user` =" . $this->account["id_account"]);
			else
				$this->db->update("user_potions", array( $freeSlot => json_encode($newInvItem)), "`id_user` =" . $this->account["id_account"]);
			$this->db->update("user_resources", array( $valuta => $newResource), "`id_user` =" . $this->account["id_account"]);
			$result = $this->mysqli->commit();
			if($result){
				$resultData["result"] = true;
				$resultData["money"] = $newResource;
				$resultData["resource"] = mb_strtolower($valuta);
				die(json_encode($resultData));
			}
			else{
				$resultData = array("result" => false, "error" => "Возникла серверная ошибка[3]");
				die(json_encode($resultData));
			}
		}
		else{
			$resultData = array("result" => false, "error" => "У вас не хватает денег для покупки этой вещи");
			die(json_encode($resultData));
		}
    }

    public function buyPotion($id){
        $allPotions = $this->db->getAll("something", "", "");
        $exist = false;
        for($i = 0; $i < count($allPotions); $i++){
            if($allPotions[$i]["image"] == $id){
                $exist = true;
                break;
            }
        }
        if(!$exist) exit;
        $currentgold = $this->user["Gold"];
        $thing = $this->db->getAllOnField("something", "image", $id, "", "");
        if($thing["requiredlvl"] > $this->user["lvl"] + 3) exit;
        $price = $thing["price"];
        if($currentgold > $price){
            $newgold = $currentgold - $price;
            $field = $this->setFieldInvPotion($id);
            if($field){
                $statistic = unserialize($this->user["shopStatistic"]);
                $statistic["spentGold"] += $price;
                $statistic["potions"]++;
                $this->db->setFieldOnID("user_statistic", $this->user["id"], "shopStatistic", serialize($statistic));
                $this->db->setField("user_resources", "Gold", $newgold, "id", $this->user["id"]);
                echo $newgold;
                exit;
            }
            else{
                echo "?";
                exit;
            }
        }
        else{
            echo "!";
            exit;
        }
    }
	
	private function generateCode($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
	
}
$shopFunctions = new shopFunctions();

switch ($_POST["WhatIMustDo"]) {
	case "get_things":
		$shopFunctions->get_things($_POST["thing"], true);
		break;
	case "buy_thing":
        $shopFunctions->buy($_POST["id"]);
        break;
}
?>