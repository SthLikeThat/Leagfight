<?php
require_once "database_class.php";
require_once "auth.php";

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
		if(!$_SESSION["id_account"]){
            return false;
		}
		$user = $this->db->getFieldsBetter("accounts", "id_account", $_SESSION["id_account"], array("id_account", "user_hash"), "=");
		$user = $user[0];
		if($_SESSION["hash"] === $user["user_hash"]){
			return true;
		}
		else{
            return false;
		}
	}
	
	public function toggle($hash){
		if(!is_string($hash)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка");
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
				if($inventory_item["lvl"] <= $this->user_information["lvl"]){
					$this->db->update("user_equipment", array($resultData["item"] => $hash), "`id_user` = " . $this->account["id_account"]);
					$damageInformation[$resultData["item"]] = $thing;
				}
				else{
					$resultData["error"] = "Ваш уровень слишком низок для этой вещи!";
					$resultData["result"] = false;
					$result = false;
				}
			}
			if($resultData["type"] == "off"){
				$this->db->update("user_equipment", array($resultData["item"] => 0), "`id_user` = " . $this->account["id_account"]);
				unset($damageInformation[$resultData["item"]]);
			}
		}
		
		if(!$resultData["result"])
			$result = $this->db->mysqli->commit();
		
		if($result)
			$resultData["result"] = true;
		else{
			$resultData["result"] = false;
			$resultData["error"] = "Возникла серверная ошибка";
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
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка");
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
			$resultData["error"] = "Возникла серверная ошибка";
		}
		
		echo json_encode($resultData);
	}

	public function use_thing($name){
		if(!is_string($name)){
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[0]");
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
			$resultData = array("result" => false, "error" => "Возникла серверная ошибка[1]");
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
			$resultData["error"] = "Возникла серверная ошибка[2]";
		}
		echo json_encode($resultData);
	}
    
	public function showDetailsSmith($slot){
        $invItem = unserialize($this->inventory["slot$slot"]);
        if($invItem["id"] < 500){
            $weapon = $this->db->getElementOnID("weapon", $invItem["id"]);
            if($weapon["type"] == 1)	$typeName="Одноручное";
            if($weapon["type"] == 2)	$typeName="Двуручное";
            if($weapon["type"] == 3)	$typeName="Древковое";
            if($weapon["typedamage"] == 1)	$typedamageName="Колющее";
            if($weapon["typedamage"] == 2)	$typedamageName="Режущее";
            if($weapon["typedamage"] == 3)	$typedamageName="Дробящее";
            $sr["type"] = $typeName;
            $sr["typedamage"] = $typedamageName;
            $sr["lvl"] = $weapon["requiredlvl"];
            $sr["damage"] = $weapon["damage"];
            $sr["crit"] = $weapon["crit"];
            if($weapon["bonusstr"]) $stats = "<tr><td> Сила </td><td> {$weapon['bonusstr']} </td></tr>";
            if($weapon["bonusdef"]) $stats .= "<tr><td> Защита </td><td> {$weapon['bonusdef']} </td></tr>";
            if($weapon["bonusag"]) $stats .= "<tr><td> Ловкость </td><td> {$weapon['bonusag']} </td></tr>";
            if($weapon["bonusph"]) $stats .= "<tr><td> Телосложение </td><td> {$weapon['bonusph']} </td></tr>";
            if($weapon["bonusms"]) $stats .= "<tr><td> Мастерство </td><td> {$weapon['bonusms']} </td></tr>";
            $sr["stats"] = $stats;
            $text = $this->getReplaceTemplate($sr, "informationSmithWeapon");
            echo $text;
        }
        if($invItem["id"] > 500 and $invItem["id"] < 1000){
            $armor = $this->db->getElementOnID("armor", $invItem["id"]);
            if($armor["thing"] == 2)	$typeThing="Броня";
            if($armor["thing"] == 3)	$typeThing="Шлем";
            if($armor["thing"] == 4)	$typeThing="Поножи";
            if($armor["thing"] == 5)	$typeThing="Наручи";
            if($armor["thing"] == 6)	$typeThing="Щит";
            if($armor["typeDefence"] == 1)	$typeName="Лёгкая";
            if($armor["typeDefence"] == 2)	$typeName="Средняя";
            if($armor["typeDefence"] == 3)	$typeName="Тяжелая";
            $sr["typeThing"] = $typeThing;
            $sr["type"] = $typeName;
            $sr["lvl"] = $armor["requiredlvl"];
            $sr["defence"] = $armor["defence"];
            if($armor["bonusstr"]) $stats .= "<tr><td> Сила </td><td> {$armor['bonusstr']} </td></tr>";
            if($armor["bonusdef"]) $stats .= "<tr><td> Защита </td><td> {$armor['bonusdef']} </td></tr>";
            if($armor["bonusag"]) $stats .= "<tr><td> Ловкость </td><td> {$armor['bonusag']} </td></tr>";
            if($armor["bonusph"]) $stats .= "<tr><td> Телосложение </td><td> {$armor['bonusph']} </td></tr>";
            if($armor["bonusms"]) $stats .= "<tr><td> Мастерство </td><td> {$armor['bonusms']} </td></tr>";
            $sr["stats"] = $stats;
            $text = $this->getReplaceTemplate($sr, "informationSmithArmor");
            echo $text;
        }
    }

    public function getMenuSmith($slot){
        $invItem = unserialize($this->inventory["slot$slot"]);
        if($invItem["id"] < 500){
            $weapon = $this->db->getElementOnID("weapon", $invItem["id"]);
            $modificator = 1;
            for($i = 1; $i <= $invItem['damage']; $i++)
                $modificator += 0.05;
            $damage = round($weapon['damage'] * $modificator, 2);
            $modificator = 1;
            for($i = 1; $i <= $invItem['crit']; $i++)
                $modificator += 0.05;
            $crit = round($weapon['crit'] * $modificator, 2);
            $text = "<div id='putSmith'> <img src=\"images/cloth/{$weapon['id']}.png\" class='inventoryItems'> </div>
				<div id='allPowerUpsSmith'>
				<table class='upSmithTable'>
					<tr><td>Урон</td>
					<td><a href='#' onclick='upCharsWeapon(\"down\", \"damage\",{$weapon['price']},{$weapon['damage']},{$invItem['damage']},{$weapon['crit']},{$invItem['crit']})'> < </a></td>
					<td ><div id='damageLvl'> {$invItem['damage']} </div></td>
					<td><a href='#' onclick='upCharsWeapon(\"up\", \"damage\",{$weapon['price']},{$weapon['damage']},{$invItem['damage']},{$weapon['crit']},{$invItem['crit']})'> > </a></td>
					<td id='weaponDamage'> $damage </td></tr>
					
					<tr><td>Крит</td>
					<td><a href='#' onclick='upCharsWeapon(\"down\", \"crit\",{$weapon['price']},{$weapon['damage']},{$invItem['damage']},{$weapon['crit']},{$invItem['crit']})'> < </a></td>
					<td ><div id='critLvl'> {$invItem['crit']} </div></td>
					<td><a href='#' onclick='upCharsWeapon(\"up\", \"crit\",{$weapon['price']},{$weapon['damage']},{$invItem['damage']},{$weapon['crit']},{$invItem['crit']})'> > </a></td>
					<td id='weaponCrit'> $crit </td></tr>
					<tr><td> Цена </td><td></td><td id='price'>0</td><td></td><td><a href='#' onclick='upWeapon($slot)'>Прокачать</a></td></tr>
				</table>
				</div>";

            echo $text;
        }

        if($invItem["id"] > 500 and $invItem["id"] < 1000){
            $armor = $this->db->getElementOnID("armor", $invItem["id"]);
            $modificator = 1;
            for($i = 1; $i <= $invItem['armor']; $i++)
                $modificator += 0.05;
            $defence = round($armor['defence'] * $modificator, 2);
            $text = "<div id='putSmith'> <img src=\"images/cloth/{$armor['id']}.png\" class='inventoryItems'> </div>
				<div id='allPowerUpsSmith'>
				<table class='upSmithTable'>
					<tr><td>Броня</td>
					<td><a href='#' onclick='upLvlArmor(\"del\",{$armor['defence']},{$armor['price']},{$invItem['armor']})'> < </a></td>
					<td ><div id='armorLvl'> {$invItem['armor']} </div></td>
					<td><a href='#' onclick='upLvlArmor(\"up\",{$armor['defence']},{$armor['price']},{$invItem['armor']})'> > </a></td>
					<td id='armorDefence'> $defence </td></tr>
					<tr><td> Цена </td><td></td><td id='price'>0</td><td></td><td><a href='#' onclick='upArmor($slot)'>Прокачать</a></td></tr>
				</table>
				</div>";
            echo $text;
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

    public function buy($iden){
        if(!$this->valid->validID($iden))
            exit;
        $hash = $this->generateCode();
        for($i = 1; $i < count($this->inventory); $i++){
            if($this->inventory["slot$i"] != "0" and $this->inventory["slot$i"] != "99"){
                $invItem = unserialize($this->inventory["slot$i"]);
                if($invItem["hash"] == $hash){
                    $i = 0;
                    $hash = $this->generateCode();
                }
            }
        }
        if($iden < 500){
            $table_name = "weapon";
            $newInvItem = array("hash"=>$hash, "id"=>$iden, "crit" => 0, "damage"=>0);
        }
        if($iden > 500){
            $table_name = "armor";
            $newInvItem = array("hash"=>$hash, "id"=>$iden, "armor" => 0);
        }
        if(!$this->valid->validID($iden) or !$this->db->existsID($table_name, $iden)) exit;
        $thing = $this->db->getAllOnField($table_name, "id", $iden, "", "");
        if($thing["requiredlvl"] > $this->user["lvl"] + 3)	exit;
        $price = $thing["price"];
        if($this->user["Gold"] > $price){
            $statistic = unserialize($this->user["shopStatistic"]);
            $statistic["spentGold"] += $price;
            $statistic["equipment"]++;
            $this->mysqli->autocommit(FALSE);
            $this->db->setFieldOnID("user_statistic", $this->user["id"], "shopStatistic", serialize($statistic));
            $newgold = $this->user["Gold"] - $price;
            $ready = $this->setFieldInventory($this->user["id"], serialize($newInvItem));
            if(!$ready){
                $this->mysqli->rollback();
                die("Нет места в инвентаре.");
            }
            else{
                $this->db->setField("user_resources", "Gold", $newgold, "id", $this->user["id"]);
                $result = $this->mysqli->commit();
                if($result)
                    die("OK".$newgold);
                else die("Что-то пошло не так");
            }
        }
        else die("Не хватает денег.");
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

    private function setFieldInvPotion($id){
        $inventory = $this->inventoryPotions;
        foreach ($inventory as $key => $value){
            if($value === $id){
                $field = $key;
                break;
            }
        }
        if($field != ""){
            $newCount = $inventory[$field."_count"] + 1;
            if($newCount <= 99){
                $this->db->setFieldOnID("user_inventory_potions", $this->user["id"], $field."_count", $newCount);
                return true;
            }
            else return false;
        }
        else{
            foreach ($inventory as $key => $value){
                if($value === "0"){
                    $field = $key;
                    break;
                }
            }
            $this->db->setFieldOnID("user_inventory_potions", $this->user["id"], $field."_count", 1);
            $this->db->setFieldOnID("user_inventory_potions", $this->user["id"], $field, $id);
            return true;
        }
    }

    public function useIt($name){
        if(!$this->valid->check_sql($name)) exit;
        $exist = false;
        for($i = 1; $i <= 5; $i++){
            if($name == $this->inventoryPotions["slot$i"]){
                $exist = true;
                $slot = $i;
                break;
            }
        }
        if(!$exist)		exit;
        $item = $this->db->getAllOnField("something", "image", $name, "", "");
        if($item["typeEffect"] == 1){
            $regenHp = ($this->user["maxHp"] * $item["valueEffect"])/100;
            $newHp = $this->user["currentHp"] + $regenHp;

            if($newHp > $this->user["maxHp"]) $newHp = $this->user["maxHp"];
            $this->db->setField("users", "currentHp", $newHp, "id", $this->user["id"]);
            $newCount = $this->inventoryPotions["slot$i"."_count"] - 1;
            $this->db->setField("user_inventory_potions", "slot$i"."_count", $newCount, "id", $this->user["id"]);
            if($newCount == 0)
                $this->db->setField("user_inventory_potions", "slot$i", 0 , "id", $this->user["id"]);
        }
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
    case "buyThing":
        $inventoryFunctions->buy($_POST["iden"]);
        break;
    case "useIt":
        $inventoryFunctions->useIt($_POST["name"]);
        break;
    case "buyPotion":
        $inventoryFunctions->buyPotion($_POST["iden"]);
        break;
    case "upSmith":
        $inventoryFunctions->upSmith($_POST["type"], $_POST["slot"], $_POST["damageLvl"], $_POST["critLvl"], $_POST["armorLvl"]);
        break;
}
?>