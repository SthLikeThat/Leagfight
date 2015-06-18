<?php
require_once "database_class.php";
require_once "auth.php";

class allFunctions extends DataBase{
	
	private $db;
	private $user;
	
	public function __construct() {
		parent::__construct();
		$this->db = $this;
		$this->auth = new Auth($this->db);
		session_start();
		$this->user = $this->db->selectFromTables(array("users", "user_resources", "user_settings", "user_statistic"), "id", $_SESSION["id"]);
		$this->inventory = $this->db->getAllOnField("user_inventory", "id", $this->user["id"], "", "");
		$this->inventoryPotions = $this->db->getAllOnField("user_inventory_potions", "id", $this->user["id"], "", ""); 
	}
	
	public function query($query){
		if (!$result = $this->mysqli->query($query)) {
			return $query." Ошибка: ".$this->mysqli->error;
		}
		return $result;
	}
	
	public function sendMessage($title, $textMessage, $idAddressee){
		if($this->valid->validID($idAddressee) and $this->db->existsID("users", $idAddressee) and $this->valid->check_sql($title) and $this->valid->check_sql($textMessage)){		
			if(!$this->valid->validString($title,0,25) or !$this->valid->validString($textMessage,1,255)) exit;
			
			$title = $this->valid->secureText($title);
			$textMessage = $this->valid->secureText($textMessage);
			$user = $this->db->getFieldsBetter( "users", "id", $idAddressee, array("avatar", "login"), "=");
			$user = $user[0];
			$date = date("Y").date("m").date("d").date("H").date("i").date("s");
			$time = date("H").":".date("i").":".date("s");
			$beautifulDate = date("d").".".date("m").".".date("y");
			if($this->user["id"] < $idAddressee) $idDialog = $this->user["id"] ."|". $idAddressee;
			else  $idDialog = $idAddressee ."|". $this->user["id"];
			$myMail = array("idDialog"=>$idDialog, "idUser"=>$idAddressee, "idSender" => $this->user["id"], "textMessage"=>$textMessage, "time"=>$time, 
			"date"=>$date, "beautifulDate"=>$beautifulDate, "type"=>1, "title"=>$title, "status" => 1, "extra" => serialize($user));
			$this->db->insert("mail", $myMail);
			die("OK");
		}
		else exit;
	}
	
	public function pump($strengh, $defence, $agility, $physique, $mastery, $pump, $total){
		if($pump != 'yes' and $pump != 'no') exit;
		if($total != ""){
			if($total != "strengh" and $total != "defence" and $total != "agility" and $total != "physique" and $total != "mastery") exit;
		}
		if($this->valid->isNoNegativeInteger($strengh) and $this->valid->isNoNegativeInteger($defence) and $this->valid->isNoNegativeInteger($agility) and $this->valid->isNoNegativeInteger($physique) and $this->valid->isNoNegativeInteger($mastery)){
			$bonus = 1;
			$discount = 1;
            $totalPrice = 0;
			$user = $this->user;
			for($i=1;$i<=$user["lvl"];$i++) $discount = $discount - ($discount * 0.02);
			$discount = round(1 - $discount, 3);
			$lastBonus = 1;
			if($total == "strengh") $totalStrengh = true;
			if($total == "defence") $totalDefence = true;
			if($total == "agility") $totalAgility = true;
			if($total == "physique") $totalPhysique = true;
			if($total == "mastery") $totalMastery = true;
			
			for($counter = 0;$counter<2;$counter++){
				//Сила
				if($totalStrengh == false){
					if($total == "strengh") $strengh = 999;
							for($b = 0; $b < $user["Strengh"]; $b++){
								$lastBonus *= 1.03;
							}
							$price = 15 * $lastBonus;
							$lastBonus = 1;
						for($i = 0; $i < $strengh; $i++){
							for($a = 0; $a < $i; $a++) $bonus *= 1.03;
							$newPrice = $price * $bonus;
							$newPrice = round($newPrice - ($newPrice * $discount),0);
							if($totalPrice + $newPrice > $this->user["Gold"] and $total == "strengh"){
								$char = $i;
								$strengh = $i;
								break;
							}
							$totalPrice += $newPrice;
							$bonus = 1;
						}
						$totalStrengh = true;
				}
				//Защита
				if($totalDefence == false){
					if($total == "defence") $defence = 999;
						for($b=0;$b<$user["Defence"];$b++){
							$lastBonus *= 1.03;
						}
						$price = 10 * $lastBonus;
						$lastBonus = 1;
					for($i=0;$i<$defence;$i++){
						for($a=0;$a<$i;$a++) $bonus *=1.03;
						$newPrice = $price * $bonus;
						$$newPrice = round($newPrice - ($newPrice * $discount),0);
						if($totalPrice + $newPrice > $this->user["Gold"] and $total == "defence"){
							$char = $i;
							$defence = $i;
							break;
						}
						$totalPrice += $newPrice;
						$bonus = 1;
					}
					$totalDefence = true;
				}
				//Ловкость
				if($totalAgility == false){
						for($b=0;$b<$user["Agility"];$b++){
							$lastBonus *= 1.03;
						}
						$price = 8 * $lastBonus;
						$lastBonus = 1;
					if($total == "agility") $agility = 999;
					for($i=0;$i<$agility;$i++){
						for($a=0;$a<$i;$a++) $bonus *=1.03;
						$newPrice = $price * $bonus;
						$newPrice = round($newPrice - ($newPrice * $discount),0);
						if($totalPrice + $newPrice > $this->user["Gold"] and $total == "agility"){
							$char = $i;
							$agility = $i;
							break;
						}
						$totalPrice += $newPrice;
						$bonus = 1;
					}
					$totalAgility = true;
				}
				//Телосложение
				if($totalPhysique == false){
						for($b=0;$b<$user["Physique"];$b++){
							$lastBonus *= 1.03;
						}
						$price = 12 * $lastBonus;
						$lastBonus = 1;
					if($total == "physique") $physique = 999;
					for($i=0;$i<$physique;$i++){
						for($a=0;$a<$i;$a++) $bonus *=1.03;
						$newPrice = $price * $bonus;
						$newPrice = round($newPrice - ($newPrice * $discount),0);
						if($totalPrice + $newPrice > $this->user["Gold"] and $total == "physique"){
							$char = $i;
							$physique = $i;
							break;
						}
						$totalPrice += $newPrice;
						$bonus = 1;
					}
					$totalPhysique = true;
				}
				//Мастерство
				if($totalMastery == false){
						for($b=0;$b<$user["Mastery"];$b++){
							$lastBonus *= 1.03;
						}
						$price = 13 * $lastBonus;
						$lastBonus = 1;
					if($total == "mastery") $mastery = 999;
					for($i=0;$i<$mastery;$i++){
						for($a=0;$a<$i;$a++) $bonus *=1.03;
						$newPrice = $price * $bonus;
						$newPrice = round($newPrice - ($newPrice * $discount),0);
						if($totalPrice + $newPrice > $this->user["Gold"] and $total == "mastery"){
							$char = $i;
							$mastery = $i;
							break;
						}
						$totalPrice += $newPrice;
						$bonus = 1;
					}
					$totalMastery = true;
				}
				if($total == "strengh") $totalStrengh = false;
				if($total == "defence") $totalDefence = false;
				if($total == "agility") $totalAgility = false;
				if($total == "physique") $totalPhysique = false;
				if($total == "mastery") $totalMastery = false;
				$bonusHp  = 1;
				for($i=0;$i<$user["Physique"] + $physique;$i++)
					$bonusHp *= 1.005;
				$maxHp = round(($user["Physique"] + $physique) * 100 * $bonusHp,0);
			}
			$totalPrice = round($totalPrice,0);
			echo $totalPrice."&".$char;
			
			if($pump == "yes" and $totalPrice <= $this->user["Gold"]){
				$text = "";
				$power = round($user["Strengh"] * 2 + $user["Defence"] * 1.7 + $user["Agility"] * 1.6 + $user["Physique"] * 1.85 + $user["Mastery"] * 1.9, 0);
				if($totlaPrice != 0) $this->db->setField("user_resources", "Gold", $this->user["Gold"] - $totalPrice, "id", $user["id"]);
				$newstr = $user["Strengh"] + $strengh;
				$newdef = $user["Defence"] + $defence;
				$newagl = $user["Agility"] + $agility;
				$newphys = $user["Physique"] + $physique;
				$newmas = $user["Mastery"] + $mastery;
				
				if($strengh != 0) $text .= "`Strengh` = '".$newstr."', ";
				if($defence != 0) $text .= "`Defence` = '".$newdef."', ";
				if($agility != 0) $text .= "`Agility` = '".$newagl."', ";
				if($physique != 0){
					$text .= "`Physique` = '".$newphys."', ";
					$text .= "`maxHp` = '".$maxHp."', ";
				}
				if($mastery != 0) $text .= "`Mastery` = '".$newmas."', ";
				if($text != ""){
					$text .= "`power` = ".$power;
					$table_name = $this->config->db_prefix."users";
					$query = "UPDATE $table_name SET $text WHERE id =".$user["id"];
					$this->query($query);
				}
			}
		}
	}
	
	public function changeBorderSearch($minLvl, $maxLvl){
		if($this->valid->isNoNegativeInteger($minLvl) and $this->valid->isNoNegativeInteger($maxLvl)){
				if($minLvl <= $this->user["lvl"] and $minLvl >= $this->user["lvl"] - 3 and $maxLvl >= $this->user["lvl"] and $maxLvl <= $this->user["lvl"] + 3){
					$this->mysqli->autocommit(FALSE);
					$this->db->setFieldOnID("user_settings", $this->user["id"], "minLvl", $minLvl);
					$this->db->setFieldOnID("user_settings", $this->user["id"], "maxLvl", $maxLvl);
					$result = $this->mysqli->commit();
					if($result) 
						die("OK");
					else die("Что-то пошло не так");
				}
				else{
					echo "Введены неправильные границы поиска.";
					exit;
				}
		}
		else{
			echo "Введено не число.";
			exit;
		}
	}
	
	private function checkUpLvl($id){
		$user = $this->db->getAllOnField("users", "id", $id, "", "");
		//Если полученный опыт не влезает апнуть лвл
		if($user["currentExp"] >= $user["needExp"]){
			$newExp = $user["currentExp"] - $user["needExp"];
			$newLvl = $user["lvl"] + 1;
			$newNeedExp = round($user["needExp"] * 1.25,0);
			$table_name = $this->config->db_prefix."users";
			$query = "UPDATE $table_name SET `currentExp` = '$newExp', `needExp` = '$newNeedExp', `lvl` = $newLvl WHERE id = ".$user["id"];
			$this->query($query);
			
		//Изменить границы поиска противника на арене, елси там стоит минимальный лвл
		$winnerSettings = $this->db->getAllOnField("user_settings", "id", $user["id"], "", "");
		if($winnerSettings["minLvl"] < $user["lvl"] - 3)
			$this->db->setField("user_settings", "minLvl", $user["lvl"] - 3, "id", $user["id"]);
		}
	}
	
	public function putOff($slot){
		echo memory_get_usage()/1024 ." - начало"; 
		//Снятие через надетые вещи
		if(is_string($slot)){
			if($slot == "helmet" or $slot == "armor" or $slot == "bracers" or $slot == "leggings" or $slot == "primaryWeapon" or $slot == "secondaryWeapon"){
				$this->db->setFieldOnID("users", $this->user["id"], $slot, 0);
				exit;
			}
		}
		if($slot > count($this->inventory)) exit;
		//Снятие через инвентарь
		$invItem = unserialize($this->inventory["slot$slot"]);
		if($this->user["armor"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "armor", "0");
		if($this->user["helmet"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "helmet", "0");
		if($this->user["leggings"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "leggings", "0");
		if($this->user["bracers"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "bracers", "0");
		
		if($this->user["secondaryWeapon"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "secondaryWeapon", "0");
		if($this->user["primaryWeapon"] == $invItem["hash"])	$this->db->putThingOn($this->user["id"], "primaryWeapon", "0");
	}
	
	public function wantDelete($slot, $type){
		if(!$this->valid->check_sql($slot)) exit;
		if($type == 1){
			$invItem = unserialize($this->inventory["slot$slot"]);
			if($invItem["id"] < 500) $table_name = "weapon";
			if($invItem["id"] > 500 and $invItem["id"] < 1000) $table_name = "armor";
			$item = $this->db->getElementOnID($table_name, $invItem["id"]);
			$sr["text"] = "Вы действительно хотите удалить ".$item["name"]." ?";
		}
		if($type == 2){
			for($i = 1; $i <= 5; $i++){
				if($this->inventoryPotions["slot$i"] == $slot)
					break;
			}
			$item = $this->db->getAllOnField("something", "image", $slot, "", "");
			$sr["text"] = "Вы действительно хотите удалить ".$item["title"]." ?";
		}
		$sr["onclick"] = "shureDelete('$slot', $type)";
		$sr["textDelete"] = "Удалить";
		$text = $this->getReplaceTemplate($sr, "deleteAlert");
		echo $text;
	}
	
	public function deleteThis($slot, $type){
		if($type == 1){
			$invItem = unserialize($this->inventory["slot$slot"]);
			$this->db->setFieldOnID("user_inventory", $this->user["id"], "slot$slot", 0);
			if($this->user["helmet"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "helmet", 0);
			if($this->user["armor"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "armor", 0);
			if($this->user["leggings"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "leggings", 0);
			if($this->user["bracers"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "bracers", 0);
			if($this->user["primaryWeapon"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "primaryWeapon", 0);
			if($this->user["secondaryWeapon"] == $invItem["hash"])
				$this->db->setFieldOnID("users", $this->user["id"], "secondaryWeapon", 0);
		}
		if($type == 2){
			for($i = 1; $i <= 5; $i++){
				if($this->inventoryPotions["slot$i"] == $slot)
					break;
			}
			$this->db->setFieldOnID("user_inventory_potions", $this->user["id"], "slot$i", 0);
			$this->db->setFieldOnID("user_inventory_potions", $this->user["id"], "slot$i"."_count", 0);
		}
	}
	
	public function put($slot){
		$invItem = unserialize($this->inventory["slot$slot"]);
		if($invItem["id"] < 500){
			$weaponInformation = $this->db->getAllOnField("weapon", "id", $invItem["id"], "", "");
			if($this->user["lvl"] >= $weaponInformation["requiredlvl"]){
				$weapon = $this->db->getElementOnID("weapon",$invItem["id"]);
				
				$primaryWeapon = $this->db->getThingByHash($this->user["primaryWeapon"], $this->inventory);
				if($primaryWeapon["type"] == 2){
					$this->db->putThingOn($this->user["id"], "primaryWeapon", $invItem["hash"]);
					die("OK");
				}
								
				if($this->user["primaryWeapon"] == "0" ){
					$this->db->putThingOn($this->user["id"], "primaryWeapon", $invItem["hash"]);
					if($weapon["type"] == 2)
						$this->db->putThingOn($this->user["id"], "secondaryWeapon", 0);
					die("OK");
				}
				if($this->user["secondaryWeapon"] == "0" ){
					$this->db->putThingOn($this->user["id"], "secondaryWeapon", $invItem["hash"]);
				}
				else{
					$this->db->putThingOn($this->user["id"], "primaryWeapon", $invItem["hash"]);
				}
				
				die("OK");
			}
			else die("Нужен ".$weaponInformation["requiredlvl"]." уровень!");
		}
		
		if($invItem["id"] > 500 and $invItem["id"] < 1000){
			$armorInformation = $this->db->getAllOnField("armor", "id", $invItem["id"], "", "");
			if($this->user["lvl"] >= $armorInformation["requiredlvl"]){
			
						if($armorInformation["thing"] == 2){
							$this->db->putThingOn($this->user["id"], "armor", $invItem["hash"]);
							die("OK");
						}
						
						if($armorInformation["thing"] == 3){
							$this->db->putThingOn($this->user["id"], "helmet", $invItem["hash"]);
							die("OK");
						}
						
						if($armorInformation["thing"] == 4){
							$this->db->putThingOn($this->user["id"], "leggings", $invItem["hash"]);
							die("OK");
						}
						
						if($armorInformation["thing"] == 5){
							$this->db->putThingOn($this->user["id"], "bracers", $invItem["hash"]);
							die("OK");
						}
						
						if($armorInformation["thing"] == 6){
							$weapon = $this->db->getThingByHash($this->user["primaryWeapon"], $this->inventory);
							if($weapon["type"] == 2)
								die("Нельзя надеть щит с двуручкой. ");
							$this->db->putThingOn($this->user["id"], "secondaryWeapon", $invItem["hash"]);
							die("OK");
						}
				}
				else die("Нужен ".$armorInformation["requiredlvl"]." уровень!");
			}
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
			if($weapon["bonusstr"]) $stats .= "<tr><td> Сила </td><td> {$weapon['bonusstr']} </td></tr>";
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
	
	public function show($slot, $invItem, $inStorage){
		if($invItem == 0)
			$invItem = unserialize($this->inventory["slot$slot"]);
		if($invItem == null)
				return false;
		if($invItem["id"] < 500){
			$weapon = $this->db->getElementOnID("weapon", $invItem["id"]);
			if($weapon["type"] == 1){ $type="one"; $typeName="Одноручное";}
			if($weapon["type"] == 2){ $type="two"; $typeName="Двуручное";}
			if($weapon["type"] == 3){ $type="staff"; $typeName="Древковое";}
			if($weapon["typedamage"] == 1){ $typedamage="piercing"; $typedamageName="Колющее";}
			if($weapon["typedamage"] == 2){ $typedamage="cutting"; $typedamageName="Режущее";}
			if($weapon["typedamage"] == 3){ $typedamage="maces"; $typedamageName="Дробящее";}
			$damage[0] = $weapon["damage"];
			$crit[0] = $weapon["crit"];
			$modificator = 1;
			for($i = 1; $i <=5; $i++){
				$modificator += 0.05;
				$damage[$i] = round($damage[0] * $modificator,2);
				$crit[$i] = round($crit[0] * $modificator,2);
			}
			$sr["typeName"] = $typeName;
			$sr["type"] = $type;
			$sr["typedamage"] = $typedamage;
			$sr["damageLvl"] = $invItem["damage"];
			$sr["critLvl"] = $invItem["crit"];
			$sr["typedamageName"] = $typedamageName;
			$sr["requiredlvl"] = $weapon["requiredlvl"];
			$sr["damage"] = $damage[$invItem["damage"]];
			$sr["crit"] = $crit[$invItem["crit"]];
			
			$text = $this->getReplaceTemplate($sr, "weaponView");
			
			if($weapon["bonusstr"]) $text .= "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />".$weapon["bonusstr"]."</div>";
			if($weapon["bonusdef"]) $text .= "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>".$weapon["bonusdef"]."</div>";
			if($weapon["bonusag"]) $text .= "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>".$weapon["bonusag"]."</div>";
			if($weapon["bonusph"]) $text .= "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>".$weapon["bonusph"]."</div>";
			if($weapon["bonusms"]) $text .= "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>".$weapon["bonusms"]."</div>";
			
			if(!$inStorage)
				echo $text;
			else{
				if($weapon["bonusstr"])  $sr["strengh"] = $weapon["bonusstr"];
				if($weapon["bonusdef"]) $sr["defence"] = $weapon["bonusdef"];
				if($weapon["bonusag"]) $sr["agility"] = $weapon["bonusag"];
				if($weapon["bonusph"]) $sr["physique"] = $weapon["bonusph"];
				if($weapon["bonusms"]) $sr["mastery"] = $weapon["bonusms"];
				$sr["id"] = $invItem["id"];
				echo json_encode($sr);
			}
		}
		
		if($invItem["id"] > 500 and $invItem["id"] < 1000){
			$armor = $this->db->getElementOnID("armor", $invItem["id"]);
			if($armor["typeDefence"] == 1){ $type="light"; $typeName="Лёгкая";}
			if($armor["typeDefence"] == 2){ $type="medium"; $typeName="Средняя";}
			if($armor["typeDefence"] == 3){ $type="heavy"; $typeName="Тяжелая";}
			
			$defence[0] = $armor["defence"];
			$modificator = 1;
			for($i = 1; $i <=5; $i++){
				$modificator += 0.05;
				$defence[$i] = round($defence[0] * $modificator,2);
			}
			$sr["type"] = $type;
			$sr["typeName"] = $typeName;
			$sr["requiredlvl"] = $armor["requiredlvl"];
			$sr["armor"] = $defence[$invItem["armor"]];
			$sr["armorLvl"] = $invItem["armor"];
			$text = $this->getReplaceTemplate($sr, "armorView");
			
			if($armor["bonusstr"]) $text .= "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />".$armor["bonusstr"]."</div>";
			if($armor["bonusdef"]) $text .= "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>".$armor["bonusdef"]."</div>";
			if($armor["bonusag"]) $text .= "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>".$armor["bonusag"]."</div>";
			if($armor["bonusph"]) $text .= "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>".$armor["bonusph"]."</div>";
			if($armor["bonusms"]) $text .= "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>".$armor["bonusms"]."</div>";
			
			if(!$inStorage)
				echo $text;
			else{
				if($armor["bonusstr"])  $sr["strengh"] = $armor["bonusstr"];
				if($armor["bonusdef"]) $sr["defence"] = $armor["bonusdef"];
				if($armor["bonusag"]) $sr["agility"] = $armor["bonusag"];
				if($armor["bonusph"]) $sr["physique"] = $armor["bonusph"];
				if($armor["bonusms"]) $sr["mastery"] = $armor["bonusms"];
				$sr["id"] = $invItem["id"];
				echo json_encode($sr);
			}
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
	
	public function setFieldInventory($id, $iden) {
		$inventory = $this->db->getAllOnField("user_inventory", "id", $id, "", "");
		$field = false;
		foreach ($inventory as $key => $value){
			if($value == "0"){
				$field = $key;
				break;
			}
		}
		if(!$field)
			return false;
		else
			return $this->db->update("user_inventory", array($field=>$iden), "`id` = '".$id."'");
	}
	
	public function setFieldInvPotion($id){
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
	
	public function loadAllMessages($idSender){
		if(!$this->valid->validID($idSender) or !$this->db->existsID("users", $idSender))	exit;
			$idUser = $this->user["id"];
			$data = $this->getAllMail($idUser, $idSender);
			$allMessages = "<ul>";
			for($i = 0; $i < count($data); $i++){
				$messageTime = $data[$i]["time"];
				$date = date("Y").date("m").date("d");
				if($date > substr($data[$i]["date"],0,8))
				$messageTime = $data[$i]["beautifulDate"];
				$sr["time"] = $messageTime;
				
				$extra = unserialize($data[$i]["extra"]);
				$senderLogin = $extra["login"];
				$allMessages .= "<li><div id='nickSender'>".$senderLogin."</div><div class='timeMore'>".$messageTime."</div><div class='titleMore'>".$data[$i]["title"]."</div><br/><div class='messageMore'>".$data[$i]["textMessage"]."</div></li><hr>";
				if($i == count($data)-1){
					$allMessages = substr($allMessages, 0, -4);
				}
			}
			$allMessages .= "</ul>";
			echo $allMessages;
	}
	
	public function getAllMail($idUser,$idSender){
		if($idUser < $idSender)
			$idDialog = $idUser."|".$idSender;
		if($idUser > $idSender)
			$idDialog = $idSender."|".$idUser;
		$table_name = $this->config->db_prefix."mail";
		$query = "SELECT * FROM $table_name WHERE idDialog = '$idDialog'";
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
	
	public function changeSettings($messAttacker, $description){
		if($this->valid->check_sql($messAttacker) and $this->valid->check_sql($description) and $this->valid->validString($messAttacker,0,255) and $this->valid->validString($description,0,255)){
			$messAttacker = $this->valid->secureText($messAttacker);
			$description = $this->valid->secureText($description);
			$this->db->setField("user_settings", "messAttacker", $messAttacker, "id", $this->user["id"]);
			$this->db->setField("user_settings", "description", $description, "id", $this->user["id"]);
			exit;
		}
	}
	
	public function upHouse($name){
		$house = $this->db->getAll("house_items", "", "");
		$workShop = $this->db->getAll("clan_workshop", "", "");
		$exist = false;
		for($i = 0; $i < count($house); $i++){
			if($house[$i]["name"] == $name){
				$type = "house";
				$exist = true;
				break;
			}
		}
		for($i = 0; $i < count($workShop); $i++){
			if($workShop[$i]["name"] == $name){
				$type = "workShop";
				$exist = true;
				break;
			}
		}
		if(!$exist) exit;
		if($type == "house"){
			$userHouse = $this->db->getAllOnField("user_house", "id", $this->user["id"], "", "");
			$houseItems = $this->db->getAllOnField("house_items", "name", $name, "", "");
			$price 	= $userHouse["$name"] * $houseItems["startPrice"] * 1.5;
			if($this->user["Gold"] > $price and $userHouse[$name] < $houseItems["maxSize"]){
				$this->db->setField("user_house", "$name", $userHouse["$name"] + 1, "id", $this->user["id"]);
				$this->db->setField("user_resources", "Gold", $this->user["Gold"] - $price, "id", $this->user["id"]);
				if($name == "warehouse"){
					$i = $userHouse["$name"] * 2 + 1;
					$this->db->setField("user_inventory", "slot$i", 0, "id", $this->user["id"]);
					$i++;
					$this->db->setField("user_inventory", "slot$i", 0, "id", $this->user["id"]);	
				}
			}
			echo "house";
			exit;
		}
		if($type == "workShop"){
			if($this->admission(3)){
				echo "У вас нет прав на постройку сооружения.";
				exit;
			}
			$workshop = $this->db->getAllOnField("clan_workshop", "name", $name, "", "");
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$clanWorkshop = unserialize($clan["workshop"]);
			$price = $clanWorkshop[$name] * $workshop["startPrice"] * 1.5;
			if($clan["Gold"] > $price and $clanWorkshop[$name] < $workshop["maxSize"]){
				$clanWorkshop[$name] += 1;
				$clanWorkshop = serialize($clanWorkshop);
				$this->db->setField("clans", "workshop", $clanWorkshop, "id", $this->user["clan"]);
				$this->db->setField("clans", "Gold", $clan["Gold"] - $price, "id", $this->user["clan"]);
				echo "workShop";
				exit;
			}
		}
		else{
			header("Location:?view=notfound");
			exit;
		}
		
	}
	
	public function admission($number){
		$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$user_titles = unserialize($clan["user_titles"]);
			$titles = unserialize($clan["titles"]);
			for($i = 0; $i < count($titles); $i++){
				if($titles[$i][0] == $user_titles[$this->user["id"]]){
					if($titles[$i][$number] == 0){
						return true;
					}
					else return false;
				}
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
	
	public function goSleep($values){
		if($this->valid->isNoNegativeInteger($values) and $values >= 1 and $values <= 12 and $this->user["typeJob"] == 0){
			$this->db->setField("users", "typeJob", 1, "id", $this->user["id"]);
			$this->db->setField("users", "jobEnd", time() + $values * 3600, "id", $this->user["id"]);
			$this->db->setField("users", "lastRegen", time() + $values * 3600, "id", $this->user["id"]);
			exit;
		}
	}
	
	public function addAdv($title, $section, $text, $image){
		if($this->valid->validString($title, 1, 25) and $this->valid->validString($text,0,255) and $this->valid->check_sql($title) and $this->valid->check_sql($text) and $this->user["lastAdvertising"] < time()){
			if($section == "buy" or $section == "swap" or $section == "set" or $section == "admin" or $section == "none"){
				$time = $this->user["lastAdvertising"];
				$pos = strpos($time, "&");
				if(substr($time,0,$pos) < time()){
					header("Location:http://zadanie/index.php?view=town&type=addAdvertising");
					exit;
				}
					var_dump($_FILES);
					exit;
					$image = $_FILES['userfile']['tmp_name'];
					$title = $this->valid->secureText($title);
					$text = $this->valid->secureText($text);
					$lastID = $this->db->getLastID("advertisings");
					if($image != "")
						$newName = md5("advertisings".$lastID);
					else $newName = "";
					$mass = array("time"=>time(), "idAuthor"=>$this->user["id"],"title"=>$title, "section"=>$section, "text"=>$text, "image"=>$newName);
					$this->db->insert("advertisings", $mass);
					$this->db->setField("users", "lastAdvertising", time()."&".date('G').":".date('i').":".date('s'), "id", $this->user["id"]);
					$uploaddir = '../images_advertisings/'; 
					$uploadfile = $uploaddir . $newName.".png";
					move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
					header("Location:http://zadanie/index.php?view=town&type=advertising&section=all");
				
			}
		}
	}
	
	public function createClan($name, $tag){
		if($this->valid->validString($name,3,25) and $this->valid->validString($tag,2,7) and $this->valid->check_sql($name) and $this->valid->check_sql($tag)){
			if(!$this->valid->isOnlyLettersAndDigits($name) or !$this->valid->isOnlyLettersAndDigits($tag)){
				echo "Только буквы и цифры.";
				exit;
			}
			else{
				$name = $this->valid->secureText($name);
				$tag = $this->valid->secureText($tag);
				$table_name = $this->config->db_prefix."clans";
				$query = "SELECT * FROM `$table_name` WHERE `name`='$name'";
				$result_set = $this->query($query);
				$num = $result_set->num_rows;
				$i = 0;
				while ($row = $result_set->fetch_assoc()){
					$data[$i] = $row;
					$i++;
				}
				$result_set->close();
				if($num > 0)
					die("Название занято");
				$users = serialize(array($this->user["id"]));
				$gold = serialize(array($this->user["login"] => 0));
				$workshop = serialize(array("barracks"=>1,"table"=>1));
				$userTitles = serialize(array($this->user["id"] => "Полемарх"));
				$mass = array("name"=>$name, "idLeader"=>$this->user["id"],"tag"=>$tag, "users" => $users, "league" => $this->user["league"], "goldOfferings" => $gold, "anotherOfferings" => $gold, "goldDept" => $gold, "anotherDebt" => $gold, "workshop" => $workshop, "user_titles" => $userTitles);
				$OKclan = $this->db->insert("clans", $mass);
				$query = "SELECT * FROM `$table_name` WHERE `name`='$name'";
				$result_set = $this->query($query);
				$num = $result_set->num_rows;
				$i = 0;
				while ($row = $result_set->fetch_assoc()){
					$data[$i] = $row;
					$i++;
				}
				$result_set->close();
				$idClan = $data[0]["id"];
				$OKuser = $this->db->setFieldOnID("users", $this->user["id"], "clan", $idClan);
				if($OKclan and $OKuser) echo "Ok";
			}
		}
	}
	
	public static function changeBorderSearchBot(){
        session_start();
		if($_SESSION["goNextLvl"] == 1 and $_SESSION["botLvl"] < 10){
			$_SESSION["botLvl"]++;
			unset($_SESSION["goNextLvl"]);
			unset($_SESSION["botId"]);
			unset($_SESSION["bot_strengh"]);
			unset($_SESSION["bot_defence"]);
			unset($_SESSION["bot_agility"]);
			unset($_SESSION["bot_physique"]);
			unset($_SESSION["bot_mastery"]);
			echo "OK";
		}
		else die("Сначада надо победить хотя бы раз на этом уровне.");
	}
	
	public function viewAllShop(){
		if($this->user["viewAllShop"] == 0)
			$this->db->setField("user_settings", "viewAllShop",1, "id", $this->user["id"]);
		if($this->user["viewAllShop"] == 1)
			$this->db->setField("user_settings", "viewAllShop",0, "id", $this->user["id"]);
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
	
	public function wantToWork($type){
		if($this->user["typeJob"] != 0) die("!");
		switch ($type) {
		case "river": 
			for($i = 0; $i < 8; $i++){
				$array = array(0,53,107);
				$sr["riverBlocks"] .= "<div id='river_$i' class='riverRaw' style='margin-left:{$array[rand(0,2)]}px;' >
					<div class='riverBlock' id='{$i}_0' onclick='chooseRiverBlock(this.id)'></div>
					<div class='riverBlock' id='{$i}_1' onclick='chooseRiverBlock(this.id)'></div>
					<div class='riverBlock' id='{$i}_2' onclick='chooseRiverBlock(this.id)'></div>
					<div class='riverBlock' id='{$i}_3' onclick='chooseRiverBlock(this.id)'></div>
				</div>";
			}
			$sr["networks"] = $this->user["networks"];
			$sr["maxNetworks"] = 2;
			$content = $this->getReplaceTemplate($sr, "riverMenu");
			break;
		default: 
			$content = "Ой, всё.";
		}
		die($content);
	}
	
	public function goRiver($nets){
		if($this->valid->isNoNegativeInteger($nets) and $nets >= 1 and $nets <= 2 and $this->user["typeJob"] == 0){
			$this->db->setField("users", "typeJob", 2, "id", $this->user["id"]);
			$this->db->setField("users", "jobEnd", time() + 600, "id", $this->user["id"]);
			$this->db->setField("user_resources", "installedNetworks", $nets, "id", $this->user["id"]);
			$this->db->setField("user_resources", "networks", $this->user["networks"] - $nets, "id", $this->user["id"]);
		}
	}
	
	public function buyFishnet($nets){
		if($this->valid->isNoNegativeInteger($nets) and $nets >= 1 and $nets <= 10){
			if($nets * 200 < $this->user["Gold"]){
				$this->db->setField("user_resources", "networks", $this->user["networks"] + $nets, "id", $this->user["id"]);
				$this->db->setField("user_resources", "Gold", $this->user["Gold"] - $nets * 200, "id", $this->user["id"]);
			}
			else die("Не хватает золота.");
		}
	}
	
	public function workIt($id, $number){
		if($this->valid->isNoNegativeInteger($number) and $number >= 1 and $number <= 12){
			$this->db->setField("users", "typeJob", "work_$id", "id", $this->user["id"]);
			$this->db->setField("users", "jobEnd", time() + $number * 60 * 60, "id", $this->user["id"]);
		}
	}
	
	public function sendAuth($email, $password){
		return $this->auth->checkAuth($email, $password);
	}
	
}

if($_REQUEST["WhatIMustDo"] === "changeBorderSearchBot")		
	allFunctions::changeBorderSearchBot();
else{
	$allFunctions = new allFunctions();

	switch ($_REQUEST["WhatIMustDo"]) {
		case "sendMessage":
			$allFunctions->sendMessage($_REQUEST["title"], $_REQUEST["textMessage"], $_REQUEST["idAddressee"]);
			break;
		case "sendChar":
			$allFunctions->pump($_REQUEST["strengh"], $_REQUEST["defence"], $_REQUEST["agility"], $_REQUEST["physique"], $_REQUEST["mastery"], $_REQUEST["pump"], $_REQUEST["total"]);
			break;
		case "putOffThisThing":
			$allFunctions->putOff($_REQUEST["slot"]);
			break;
		case "deleteThis":
			$allFunctions->deleteThis($_REQUEST["slot"], $_REQUEST["type"]);
			break;
		case "wantDelete":
			$allFunctions->wantDelete($_REQUEST["slot"], $_REQUEST["type"]);
			break;
		case "putOnThisThing":
			$allFunctions->put($_REQUEST["slot"]);
			break;
		case "showDetails":
			$allFunctions->show($_REQUEST["iden"], 0, $_REQUEST["inStorage"]);
			break;
		case "showDetailsSmith":
			$allFunctions->showDetailsSmith($_REQUEST["slot"]);
			break;
		case "getMenuSmith":
			$allFunctions->getMenuSmith($_REQUEST["slot"]);
			break;
		case "buyThing":
			$allFunctions->buy($_REQUEST["iden"]);
			break;
		case "loadAllMessages":
			$allFunctions->loadAllMessages($_REQUEST["idSender"]);
			break;
		case "sendMessageEnter":
			$allFunctions->sendMessage("", $_REQUEST["textMessage"], $_REQUEST["idSender"]);
			break;
		case "changeSettings":
			$allFunctions->changeSettings($_REQUEST["messAttacker"], $_REQUEST["description"]);
			break;
		case "upHouse":
			$allFunctions->upHouse($_REQUEST["name"]);
			break;
		case "useIt":
			$allFunctions->useIt($_REQUEST["name"]);
			break;
		case "buyPotion":
			$allFunctions->buyPotion($_REQUEST["iden"]);
			break;
		case "goSleep":
			$allFunctions->goSleep($_REQUEST["values"]);
			break;
		case "addAdv":
			$allFunctions->addAdv($_REQUEST["title"], $_REQUEST["section"], $_REQUEST["text"],$_REQUEST["userfile"]);
			break;
		case "createClan":
			$allFunctions->createClan($_REQUEST["name"], $_REQUEST["tag"]);
			break;
		case "viewAllShop":
			$allFunctions->viewAllShop();
			break;
		case "showDetailsLog":
			$allFunctions->showDetailsLog($_REQUEST["thing"],$_REQUEST["id"]);
			break;
		case "upSmith":
			$allFunctions->upSmith($_REQUEST["type"], $_REQUEST["slot"], $_REQUEST["damageLvl"], $_REQUEST["critLvl"], $_REQUEST["armorLvl"]);
			break;
		case "wantToWork":
			$allFunctions->wantToWork($_REQUEST["type"]);
			break;
		case "goRiver":
			$allFunctions->goRiver($_REQUEST["nets"]);
			break;
		case "buyFishnet":
			$allFunctions->buyFishnet($_REQUEST["nets"]);
			break;
		case "workIt":
			$allFunctions->workIt($_REQUEST["id"], $_REQUEST["number"]);
			break;
		case "sendAuth":
			$allFunctions->sendAuth($_REQUEST["email"], $_REQUEST["password"]);
			break;
	}
}
?>