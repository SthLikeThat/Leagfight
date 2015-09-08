<?php
require_once "database_class.php";

class inventoryFunctions extends DataBase{

    private $db;
	private $user;
    private $inventory;
    private $potions;
    private $inventories_database;

    public function __construct() {
        parent::__construct();
        $this->db = $this;
        session_start();
		
        $this->account = $this->db->select("accounts", array("*"), "`id_account` =". $_SESSION["id_account"], "id_account")[0];
		$this->user_information = $this->db->select("user_information", array("*"), "`id_user` =". $_SESSION["id_account"], "id_user")[0];
		$this->equipment = $this->db->select("user_equipment", array("*"), "`id_user` =". $_SESSION["id_account"], "id_user")[0];
		
        $this->inventory = $this->db->getAllOnField("user_inventory", "id_user", $_SESSION["id_account"], "id_user", "");
		$this->potions = $this->db->getAllOnField("user_potions", "id_user", $_SESSION["id_account"], "id_user", "");
		$this->inventories_database = $this->db->ancillary->getAllInventory($this->inventory, $this->potions);
    }

    public function query($query){
        if (!$result = $this->mysqli->query($query)) {
            return $query." Ошибка: ".$this->mysqli->error;
        }
        return $result;
    }
	
	private function getPercent($current, $max){
			$procent = 100/$max;
			$length = $procent * $current;
			$length = round($length, 0);
			return $length;
	}
	
	final protected function getAuthUser() {
		session_start();
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
	
	public function toggle($hash){
		if(!is_string($hash)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[1]");
			die(json_encode($resultData));
		}
		//  Пример ответа $resultData = array("result" => true, "item" => "armor", "type" => "change", "error" => 0);
		$resultData = array();
		$result = 0;
		$thing = false;
		
		$this->db->mysqli->autocommit(false);
		if($inventory_item["id"] < 1000){
			//Ищем пришедшую вещь в инвентаре
			foreach($this->inventory as $tempthing){
					$tempthing = json_decode($tempthing);
					if(is_object($tempthing)){
						$tempthing = (array) $tempthing;
						//Находима текущую вещь
						if($tempthing["hash"] == $hash){
							$thing = $tempthing;
						}
						//Находим все вещи для изменения статистики
						if($tempthing["hash"] == $this->equipment["primaryWeapon"])
							$damageInformation["primaryWeapon"] = $tempthing;
						if($tempthing["hash"] == $this->equipment["secondaryWeapon"])
							$damageInformation["secondaryWeapon"] = $tempthing;
						if($tempthing["hash"] == $this->equipment["helmet"])
							$damageInformation["helmet"] = $tempthing;
						if($tempthing["hash"] == $this->equipment["armor"])
							$damageInformation["armor"] = $tempthing;
						if($tempthing["hash"] == $this->equipment["bracers"])
							$damageInformation["bracers"] = $tempthing;
						if($tempthing["hash"] == $this->equipment["leggings"])
							$damageInformation["leggings"] = $tempthing;
					}
					
			}
			if(!$thing){
				$resultData = array("result" => false, "error" => "Нет такой вещи в инвентаре");
				die(json_encode($resultData));
			}
			//Находим о ней информацию в базе
			foreach($this->inventories_database["inventory"] as $inventory_item){
				if($inventory_item["id"] == $thing["id"])
					break;
			}
			
			//Определяем тип вещи(Для оружия нужно уточнение в какой оно руке)
			if($inventory_item["thing"] == 2)
				$resultData["item"] = "armor";
			if($inventory_item["thing"] == 3)
				$resultData["item"] = "helmet";
			if($inventory_item["thing"] == 4)
				$resultData["item"] = "leggings";
			if($inventory_item["thing"] == 5)
				$resultData["item"] = "bracers";
			if($inventory_item["thing"] == 6)
				$resultData["item"] = "secondaryWeapon";
			
			//Снимаем если хеш совпадает
			if($this->equipment["primaryWeapon"] == $hash){
				$resultData["item"] = "primaryWeapon";
				$resultData["type"] = "off";
			}
			if($this->equipment["secondaryWeapon"] == $hash){
				$resultData["item"] = "secondaryWeapon";
				$resultData["type"] = "off";
			}
			if($this->equipment["helmet"] == $hash || $this->equipment["armor"] == $hash || $this->equipment["bracers"] == $hash || $this->equipment["leggings"] == $hash)
				$resultData["type"] = "off";
			
			//Нет, надо не снять, а нечто другое
			if(!$resultData["type"]){
				//Может надеть?
				if($inventory_item["thing"] == 1){
					if($this->equipment["secondaryWeapon"] == "0")
						$resultData["item"] = "secondaryWeapon";
					else
						$resultData["item"] = "primaryWeapon";
				}
				if($this->equipment[$resultData["item"]] == "0"){
					$resultData["type"] = "on";
				}
				//Значит надо заменить вещь
				if(!$resultData["type"]){
					$resultData["type"] = "change";
				}
			}
			
			//Что делать опредились, теперь надо это делать
			if($resultData["type"] == "change" || $resultData["type"] == "on"){
				if($inventory_item["required_lvl"] <= $this->user_information["lvl"]){
					$this->db->update("user_equipment", array($resultData["item"] => $hash), "`id_user` = " . $this->account["id_account"]);
					$damageInformation[$resultData["item"]] = $thing;
				}
				else{
					$resultData["error"] = "Ваш уровень слишком низок для этой вещи!";
					$resultData["result"] = false;
					die(json_encode($resultData));
				}
			}
			if($resultData["type"] == "off"){
				$this->db->update("user_equipment", array($resultData["item"] => 0), "`id_user` = " . $this->account["id_account"]);
				unset($damageInformation[$resultData["item"]]);
			}
		}
		$result = $this->db->mysqli->commit();
		
		if($result)
			$resultData["result"] = true;
		else{
			$resultData["result"] = false;
			$resultData["error"] = "Возникла серверная ошибка[3]";
		}
		
		//Вытягиваем новую статистику урона
		if($resultData["result"]){
			foreach($this->inventories_database["inventory"] as $inventory_db_item){ //Для каждой вещи в базе
				foreach($damageInformation as $key => $info_item){ 
					if($inventory_db_item["id"] == $info_item["id"]){ //Находим такую же вещь в надетом
						$modificator = 1;
						
						//Модификаторы для оружия
						if($info_item["id"] < 500){
							$damage[0] = $inventory_db_item["damage"];
							$crit[0] = $inventory_db_item["crit"];
							for($i = 1; $i <= 5; $i++){
								$modificator += 0.05;
								$damage[$i] = round($damage[0] * $modificator, 2);
								$crit[$i] = round($crit[0] * $modificator,2);
							}
							$inventory_db_item["crit"] = $crit[$info_item["crit"]];
							$inventory_db_item["damage"] = $damage[$info_item["damage"]];
						}
						
						//Модификаторы для брони
						if($info_item["id"] > 500 && $info_item["id"] < 1000){
							$armor[0] = (float) $inventory_db_item["armor"];
							for($i = 1; $i <= 5; $i++){
								$modificator += 0.05;
								$armor[$i] = round($armor[0] * $modificator, 2);
							}
							$inventory_db_item["armor"] = $armor[$info_item["armor"]];
						}
						
						$info[$key] = $inventory_db_item;
					}
				}
			}
			$resultData["statistic"] = $this->db->ancillary->getDamageInformation($this->user_information, $info, true);
		}
		
		echo json_encode($resultData);
	}
	
	public function delete_thing($hash, $name){
		if(!is_string($hash) && !is_null($hash) || !is_string($name) && !is_null($name)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[1]");
			die(json_encode($resultData));
		}
		// Если пришел hash - удаляем из инвентаря, name - из зелий
		// Пример ответа $resultData = array("result" => true, item => "helmet", "error" => 0);
		$resultData = array();
		$result = 0;
		
		$this->db->mysqli->autocommit(false);
		if(!is_null($hash)){
			//Ищем пришедшую вещь в инвентаре
			foreach($this->inventory as $slot => $tempthing){
				if($tempthing != "0" and $tempthing != "999"){
					$tempthing = (array) json_decode($tempthing);
					//Находима текущую вещь
					if($tempthing["hash"] == $hash){
						$slot_in_inventory = $slot;
						break;
					}
				}
			}
		}
		else{
			foreach($this->potions as $slot => $tempthing){
				if($tempthing != "0" and $tempthing != "999"){
					$tempthing = (array) json_decode($tempthing);
					//Находима текущуе зелье
					if($tempthing["image"] == $name){
						$slot_in_inventory = $slot;
						break;
					}
				}
			}
		}
		//Если пришла вещь, то надо проверить её наличие в экипировке
		if(!is_null($hash)){
			$equipment = array("primaryWeapon" => $this->user["primaryWeapon"], "secondaryWeapon" => $this->user["secondaryWeapon"], 
			"helmet" => $this->user["helmet"], "armor" => $this->user["armor"], 
			"bracers" => $this->user["bracers"], "leggings" => $this->user["leggings"]);
			
			foreach($equipment as $item => $equip_hash){
				if($equip_hash == $hash){
					$resultData["item"] = $item;
					break;
				}	
			}
		}
		//Удаляем
		if($slot_in_inventory){
			if(!is_null($hash))
				$this->db->update("user_inventory", array($slot_in_inventory => 0), "`id` = {$this->user["id"]}");
			else
				$this->db->update("user_inventory_potions", array($slot_in_inventory => 0), "`id` = {$this->user["id"]}");
		}
		if($resultData["item"]){
			$this->db->update("users", array($resultData["item"] => 0), "`id` = {$this->user["id"]}");
		}
		$result = $this->db->mysqli->commit();
		
		if($result){
			$resultData["result"] = true;
		}
		else{
			$resultData["result"] = false;
			$resultData["error"] = "Возникла серверная ошибка[4]";
		}
		
		echo json_encode($resultData);
	}

	public function use_thing($name){
		if(!is_string($name)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[1]");
			die(json_encode($resultData));
		}
		
		//Ищем есть ли она у юзера в инвентаре
		foreach($this->potions as $slot => $tempthing){
			if($tempthing != "0" and $tempthing != "999"){
				$tempthing = (array) json_decode($tempthing);
				//Находима текущее зелье
				if($tempthing["image"] == $name){
					$slot_in_inventory = $slot;
					break;
				}
			}
		}
		if(!$slot_in_inventory){
			$resultData = array("result" => false, "error" => "Нет такой вещи в инвентаре");
			die(json_encode($resultData));
		}
		
		//Ищем её в базе
		foreach($this->inventories_database["potions"] as $potion){
			if($potion["id"] == $tempthing["id"]){
				$potion_in_db = $potion;
				break;
			}
			
		}
		
		if(!$potion_in_db){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[2]");
			die(json_encode($resultData));
		}
		
		$resultData["effect"] = $potion_in_db["typeEffect"];
		
		$this->db->mysqli->autocommit(false);
        if($potion_in_db["typeEffect"] == "healPercent"){
			
			if($this->user["maxHp"] == $this->user["currentHp"]){
				$resultData = array("result" => false, "error" => "У вас и так полно здоровья");
				die(json_encode($resultData));
			}
			
            $regenHp = round(($this->user["maxHp"] * $potion_in_db["valueEffect"])/100, 0);
            $newHp = $this->user["currentHp"] + $regenHp;

            if($newHp > $this->user["maxHp"])
				$newHp = $this->user["maxHp"];
            $this->db->setField("users", "currentHp", $newHp, "id", $this->user["id"]);
            $tempthing["count"]--;
			$resultData["valueEffect"] = $this->getPercent($newHp, $this->user["maxHp"]);
			$resultData["numberHp"] = $newHp . " / " . $this->user["maxHp"];
			
            if( $tempthing["count"] > 0){
				$this->db->setField("user_inventory_potions", $slot_in_inventory, json_encode($tempthing), "id", $this->user["id"]);
				$resultData["to_do"] = "count";
			}
			else{
				$this->db->setField("user_inventory_potions", $slot_in_inventory, 0 , "id", $this->user["id"]);
				$resultData["to_do"] = "delete";
			}	
        }
		$result = $this->db->mysqli->commit();
		
		if($result){
			$resultData["result"] = true;
		}
		else{
			$resultData["result"] = false;
			$resultData["error"] = "Возникла серверная ошибка[3]";
		}
		echo json_encode($resultData);
	}

    public function upSmith($type, $slot, $damageLvl, $critLvl, $armorLvl){
        $invItem = unserialize($this->inventory["slot$slot"]);
        if($type == "weapon"){
            if($damageLvl > $invItem["damage"] or $critLvl > $invItem["crit"]){
                $shopItem = $this->db->getElementOnID("weapon", $invItem["id"]);

                //цена и урон с критом
                $priceCrit[0] = round($shopItem["price"] /10, 0);
                $priceDamage[0] = round($shopItem["price"] /10, 0);
                $damage[0] = $shopItem["damage"];
                $crit[0] = $shopItem["crit"];
                $modificator = 1;
                for($i = 1; $i <=5; $i++){
                    $priceCrit[$i] = round($priceCrit[$i - 1] * 1.5, 0);
                    $priceDamage[$i] = round($priceDamage[$i - 1] * 1.75, 0);
                    $modificator += 0.05;
                    $damage[$i] = round($damage[0] * $modificator,2);
                    $crit[$i] = round($crit[0] * $modificator,2);
                }

                $totalPrice = 0;
                if($damageLvl > $invItem["damage"] and $damageLvl <=5 and $damageLvl > 0){
                    for($i = $invItem["damage"]; $i <= $damageLvl; $i++)
                        $totalPrice += $priceDamage[$i];
                    $invItem["damage"] = $damageLvl;
                    $changes["damageLvl"] = $damageLvl;
                    $changes["damage"] = "{$damage[$damageLvl]}";
                }
                if($critLvl > $invItem["crit"] and $critLvl <=5 and $critLvl > 0){
                    for($i = $invItem["crit"]; $i <= $critLvl; $i++)
                        $totalPrice += $priceCrit[$i];
                    $invItem["crit"] = $critLvl;
                    $changes["critLvl"] = $critLvl;
                    $changes["crit"] = "{$crit[$critLvl]}";
                }
                if($this->user["Another"] < $totalPrice)
                    die("Недостаточно жемчуга. ");
                else{
                    $this->mysqli->autocommit(FALSE);
                    $this->db->setFieldOnID("user_inventory", $this->user["id"], "slot$slot", serialize($invItem));
                    $this->db->setFieldOnID("user_resources", $this->user["id"], "Another", $this->user["Another"] - $totalPrice);
                    $statistic = unserialize($this->user["shopStatistic"]);
                    $statistic["spentAnother"] += $totalPrice;
                    $this->db->setFieldOnID("user_statistic", $this->user["id"], "shopStatistic", serialize($statistic));
                    $result = $this->mysqli->commit();
                    if($result){
                        $changes = json_encode($changes);
                        die("OK".$changes."".$invItem["hash"]);
                    }
                    else die("Что-то пошло не так.");
                }
            }
        }
        if($type == "armor"){
            if($armorLvl > $invItem["armor"] and $armorLvl <=5 and $armorLvl > 0){
                $shopItem = $this->db->getElementOnID("armor", $invItem["id"]);
                $priceArmor[0] = round($shopItem["price"] /10, 0);
                $totalPrice = 0;
                $armor[0] = $shopItem["armor"];
                $modificator = 1;
                for($i = 1; $i <=5; $i++){
                    $priceArmor[$i] = round($priceArmor[$i - 1] * 1.9, 0);
                    $modificator += 0.05;
                    $armor[$i] = round($armor[0] * $modificator,2);
                }
                if($armorLvl > $invItem["armor"] and $armorLvl <=5 and $armorLvl > 0){
                    for($i = $invItem["armor"]; $i <= $armorLvl; $i++)
                        $totalPrice += $priceArmor[$i];
                    $invItem["armor"] = $armorLvl;
                    $changes["armorLvl"] = $armorLvl;
                    $changes["armor"] = "{$armor[$armorLvl]}";
                }
                if($this->user["Another"] < $totalPrice)
                    die("Недостаточно жемчуга. ");
                else{
                    $this->db->setFieldOnID("user_inventory", $this->user["id"], "slot$slot", serialize($invItem));
                    $this->db->setFieldOnID("user_resources", $this->user["id"], "Another", $this->user["Another"] - $totalPrice);
                    $changes = json_encode($changes);
                    die("OK".$changes."".$invItem["hash"]);
                }
            }
        }
    }
	
}
$inventoryFunctions = new inventoryFunctions();

switch ($_POST["WhatIMustDo"]) {
	case "toggle_thing":
		$inventoryFunctions->toggle($_POST["hash"]);
		break;
    case "delete_thing":
        $inventoryFunctions->delete_thing($_POST["hash"], $_POST["name"]);
        break;
	case "use_thing":
        $inventoryFunctions->use_thing($_POST["name"]);
        break;
    case "getMenuSmith":
        $inventoryFunctions->getMenuSmith($_POST["slot"]);
        break;
    case "upSmith":
        $inventoryFunctions->upSmith($_POST["type"], $_POST["slot"], $_POST["damageLvl"], $_POST["critLvl"], $_POST["armorLvl"]);
        break;
}
?>