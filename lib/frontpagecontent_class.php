<?php
require_once "modules_class.php";

class FrontPageContent extends Modules {
	
	private $damageInformation;
	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	public function getCenter(){
        if(is_null($this->account))
            return false;
		$this->inventory = $this->db->getAllOnField("user_inventory", "id_user", $_SESSION["id_account"], "id_user", "");
		$this->potions = $this->db->getAllOnField("user_potions", "id_user", $_SESSION["id_account"], "id_user", "");
		$this->equipment = $this->db->getAllOnField("user_equipment", "id_user", $_SESSION["id_account"], "id_user", "");
		$inventories = $this->db->ancillary->getAllInventory($this->inventory, $this->potions);
		$this->inventory_database = $inventories["inventory"];
		$this->inventory_potions_database = $inventories["potions"];
		unset($inventories);
		
		$sr["characteristics"] = $this->getCharacteristics();
		$sr["nick"] = htmlspecialchars($this->account["login"]);
		$sr["avatar"] = $this->account["avatar"];

		$sr["equipment"] = $this->getEquipment();
		$sr["inventory"] = $this->getInventory();
		$sr["invPotions"] = $this->getPotions();
		$sr["damageInformation"] = $this->db->ancillary->getDamageInformation($this->user_information, $this->damageInformation);
		
		
		$sr["modales"] = $this->getModales();
		return $this->getReplaceTemplate($sr, "center");
	}
	
	protected function getTitle(){
		return "Персонаж - LF";
	}
	protected function getShortcut_icon(){
		return "profile";
	}
	
	private function getModales(){
		$modal["discount"] = $this->db->ancillary->getDiscount($this->user_information);
		$modal["strengh"] = $this->user_information["Strengh"];
		$modal["defence"] = $this->user_information["Defence"];
		$modal["agility"] = $this->user_information["Agility"];
		$modal["physique"] = $this->user_information["Physique"];
		$modal["mastery"] = $this->user_information["Mastery"];
		
		$modales = $this->getReplaceTemplate($modal, "modal_training");
		$modales .= $this->getReplaceTemplate($modal, "modal_deleting");
		return $modales;
	}
	
	private function getEquipment($array = false){
		$count = count($this->inventory);
		for($i = 1; $i < $count; $i++){
            if($this->inventory["slot$i"] != "0" and $this->inventory["slot$i"] != "999") {
                $invItem = (array) json_decode($this->inventory["slot$i"]);
                if ($invItem["hash"] == $this->equipment["primaryWeapon"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["primaryWeaponInfo"] = $info["html"];
					$this->damageInformation["primaryWeapon"] = $info["array"];
					
                    $sr["primaryWeapon"] = $invItem["id"];
                    $sr["hashPrim"] = $invItem["hash"];
                }
                if ($invItem["hash"] == $this->equipment["secondaryWeapon"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["secondaryWeaponInfo"] = $info["html"];
					$this->damageInformation["secondaryWeapon"] = $info["array"];
					
                    $sr["secondaryWeapon"] = $invItem["id"];
                    $sr["hashSec"] = $invItem["hash"];
                }
                if ($invItem["hash"] == $this->equipment["armor"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["armorInfo"] = $info["html"];
					$this->damageInformation["armor"] = $info["array"];

                    $sr["armor"] = $invItem["id"];
                    $sr["hashArmor"] = $invItem["hash"];
                }
                if ($invItem["hash"] == $this->equipment["helmet"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["helmetInfo"] = $info["html"];
					$this->damageInformation["helmet"] = $info["array"];
					
                    $sr["helmet"] = $invItem["id"];
                    $sr["hashHelmet"] = $invItem["hash"];
                }
                if ($invItem["hash"] == $this->equipment["bracers"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["bracersInfo"] = $info["html"];
					$this->damageInformation["bracers"] = $info["array"];
					
                    $sr["bracers"] = $invItem["id"];
                    $sr["hashBracers"] = $invItem["hash"];
                }
                if ($invItem["hash"] == $this->equipment["leggings"]) {
					$info = $this->getTableInfo($invItem, true);
					$sr["leggingsInfo"] = $info["html"];
					$this->damageInformation["leggings"] = $info["array"];
					
                    $sr["leggings"] = $invItem["id"];
                    $sr["hashLeggings"] = $invItem["hash"];
                }
            }
		}
		if($this->equipment["primaryWeapon"] == "0"){
			$sr["primaryWeapon"] = "primaryWeapon";
			$sr["hashPrim"] = 0;
			$sr["primaryWeaponInfo"] = "";
		}
		if($this->equipment["secondaryWeapon"] == "0"){
			$sr["secondaryWeapon"] = "secondaryWeapon";
			$sr["hashSec"] = 0;
			$sr["secondaryWeaponInfo"] = "";
		}
		if($this->equipment["armor"] == "0"){
			$sr["armor"] = "armor";
			$sr["hashArmor"] = 0;
			$sr["armorInfo"] = "";
		}
		if($this->equipment["helmet"] == "0"){
			$sr["helmet"] = "helmet";
			$sr["hashHelmet"] = 0;
			$sr["helmetInfo"] = "";
		}
		if($this->equipment["bracers"] == "0"){
			$sr["bracers"] = "bracers";
			$sr["hashBracers"] = 0;
			$sr["bracersInfo"] = "";
		}
		if($this->equipment["leggings"] == "0"){
			$sr["leggings"] = "leggings";
			$sr["hashLeggings"] = 0;
			$sr["leggingsInfo"] = "";
		}
		
		$text = $this->getReplaceTemplate($sr, "equipment");
        if(!$array)
		    return $text;
        else
            return $sr;
	}
	
	public function getTableInfo($thing, $return_array = false){
		// $return_array нужна для отображения информации об уроне в getDamageInformation

		foreach($this->inventory_database as $inventory_item){
			if($inventory_item["id"] == $thing["id"])
				break;
		}
        if($inventory_item["id"] < 500){
			
            if($inventory_item["type"] == 1)
				$typeName="Одноручное";
            if($inventory_item["type"] == 2)
				$typeName="Двуручное";
            if($inventory_item["type"] == 3)
				$typeName="Древковое";
            if($inventory_item["type_damage"] == 1)
				$typedamageName="Колющее";
            if($inventory_item["type_damage"] == 2)
				$typedamageName="Режущее";
            if($inventory_item["type_damage"] == 3)
				$typedamageName="Дробящее";
			
            $damage[0] = $inventory_item["damage"];
            $crit[0] = $inventory_item["crit"];
            $modificator = 1;
            for($i = 1; $i <=5; $i++){
                $modificator += 0.05;
                $damage[$i] = round($damage[0] * $modificator,2);
                $crit[$i] = round($crit[0] * $modificator,2);
            }
			
            $sr["name"] = $inventory_item["name"];
            $sr["typeName"] = $typeName;
			$sr["typedamageName"] = $typedamageName;
			$sr["type_damage"] = $inventory_item["type_damage"];
            $sr["damageLvl"] = $thing["damage"];
            $sr["critLvl"] = $thing["crit"];
            $sr["requiredlvl"] = $inventory_item["required_lvl"];
            $sr["damage"] = $damage[$thing["damage"]];
            $sr["crit"] = $crit[$thing["crit"]];

            $text = $this->getReplaceTemplate($sr, "weaponView");
			
            if($inventory_item["bonus_strengh"]){
				$sr["bonus_strengh"] = $inventory_item["bonus_strengh"];
				$text .= " <tr><td class='success'>Сила</td><td class='active'>{$inventory_item["bonus_strengh"]}</td></tr>";
			}
            if($inventory_item["bonus_defence"]){
				$sr["bonus_defence"] = $inventory_item["bonus_defence"];
				$text .= " <tr><td class='success'>Защита</td><td class='active'>{$inventory_item["bonus_defence"]}</td></tr>";
			} 
            if($inventory_item["bonus_agility"]){
				$sr["bonus_agility"] = $inventory_item["bonus_agility"];
				$text .= " <tr><td class='success'>Ловкость</td><td class='active'>{$inventory_item["bonus_agility"]}</td></tr>";
			} 
            if($inventory_item["bonus_physique"]){
				$sr["bonus_physique"] = $inventory_item["bonus_physique"];
				$text .= " <tr><td class='success'>Телосложение</td><td class='active'>{$inventory_item["bonus_physique"]}</td></tr>";
			} 
            if($inventory_item["bonus_mastery"]){
				$sr["bonus_mastery"] = $inventory_item["bonus_mastery"];
				$text .= " <tr><td class='success'>Мастерство</td><td class='active'>{$inventory_item["bonus_mastery"]}</td></tr>";
			} 
			
			$text .= "</tbody></table></div>";
        }

        if($inventory_item["id"] > 500 && $inventory_item["id"] < 1000){

            if($inventory_item["type_defence"] == 1)
				$typeName="Лёгкая";
            if($inventory_item["type_defence"] == 2)
				$typeName="Средняя";
            if($inventory_item["type_defence"] == 3)
				$typeName="Тяжелая";

            $defence[0] = $inventory_item["armor"];
            $modificator = 1;
            for($i = 1; $i <=5; $i++){
                $modificator += 0.05;
                $defence[$i] = round($defence[0] * $modificator,2);
            }
            $sr["type_defence"] = $inventory_item["type_defence"];
            $sr["typeName"] = $typeName;
            $sr["name"] = $inventory_item["name"];
            $sr["requiredlvl"] = $inventory_item["required_lvl"];
            $sr["armor"] = $defence[$thing["armor"]];
            $sr["armorLvl"] = $thing["armor"];
			
            $text = $this->getReplaceTemplate($sr, "armorView");

            if($inventory_item["bonus_strengh"]){
				$sr["bonus_strengh"] = $inventory_item["bonus_strengh"];
				$text .= " <tr><td class='success'>Сила</td><td class='active'>{$inventory_item["bonus_strengh"]}</td></tr>";
			}
            if($inventory_item["bonus_defence"]){
				$sr["bonus_defence"] = $inventory_item["bonus_defence"];
				$text .= " <tr><td class='success'>Защита</td><td class='active'>{$inventory_item["bonus_defence"]}</td></tr>";
			} 
            if($inventory_item["bonus_agility"]){
				$sr["bonus_agility"] = $inventory_item["bonus_agility"];
				$text .= " <tr><td class='success'>Ловкость</td><td class='active'>{$inventory_item["bonus_agility"]}</td></tr>";
			} 
            if($inventory_item["bonus_physique"]){
				$sr["bonus_physique"] = $inventory_item["bonus_physique"];
				$text .= " <tr><td class='success'>Телосложение</td><td class='active'>{$inventory_item["bonus_physique"]}</td></tr>";
			} 
            if($inventory_item["bonus_mastery"]){
				$sr["bonus_mastery"] = $inventory_item["bonus_mastery"];
				$text .= " <tr><td class='success'>Мастерство</td><td class='active'>{$inventory_item["bonus_mastery"]}</td></tr>";
			} 
			
			$text .= "</tbody></table></div>";
        }
		if($return_array)
			return array("html" => $text, "array" => $sr);
		return $text;
    }
	
	private function getTableInfoSth($potion){
		foreach($this->inventory_potions_database as $inventory_item){
			if($inventory_item["id"] == $potion["id"])
				break;
		}
		if($inventory_item["type_effect"] == "healPercent"){
			$sr["effect"] = "Лечебный";
			$sr["description"] = "Исцеляет {$inventory_item["value_effect"]}% от максимального здоровья";
		}
		$sr["name"] = $inventory_item["title"];
		$sr["price"] = $inventory_item["price"];
		$sr["money"] = $inventory_item["valuta"];
		
		$text = $this->db->getReplaceTemplate($sr, "potionInfo");
		return $text;
	}
	
	/*private function dirSmth($dir, $margin){
		$what = opendir($dir);
		$margin += 25;
		while (($f = readdir($what)) != false){
			if($f != "." and $f != ".."){
				$temp = "<li style='margin-left:{$margin}px;'>".$f."</li>";
				if(substr($f, -4) == ".php")
					$temp = "<li style='margin-left:{$margin}px;' id='phpfile'>".$f."</li>";
				if(substr($f, -4) == ".png")
					$temp = "<li style='margin-left:{$margin}px;' id='pngfile'>".$f."</li>";
				if(substr($f, -4) == ".tpl")
					$temp = "<li style='margin-left:{$margin}px;' id='tplfile'>".$f."</li>";
				if(substr($f, -3) == ".js")
					$temp = "<li style='margin-left:{$margin}px;' id='jsfile'>".$f."</li>";
				if(is_dir($dir."/".$f)){
					$temp = "<li style='margin-left:{$margin}px;' id='dir'>".$f."</li>";
					$temp .= $this->dirSmth($dir."/".$f, $margin);
				}
				$mda .= $temp;
			}
		}
		closedir($what);
		return $mda;
	}*/
	
	private function getCharacteristics(){
		$dir = "lib";
		$strengh = $this->user_information["Strengh"];
		$defence = $this->user_information["Defence"];
		$agility = $this->user_information["Agility"];
		$physique = $this->user_information["Physique"];
		$mastery = $this->user_information["Mastery"];
		$massiv = array ($strengh, $defence, $agility, $physique, $mastery);
		$sr["percentStrengh"] = $this->getImage($massiv,0);
		$sr["percentDefence"] = $this->getImage($massiv,1);
		$sr["percentAgility"] = $this->getImage($massiv,2);
		$sr["percentPhysique"] = $this->getImage($massiv,3);
		$sr["percentMastery"] = $this->getImage($massiv,4);
		$sr["strengh"] = $strengh;
		$sr["defence"] = $defence;
		$sr["agility"] = $agility;
		$sr["physique"] = $physique;
		$sr["mastery"] = $mastery;
		$sr["power"] = $this->user_information["power"];
		$text = $this->getReplaceTemplate($sr, "characteristics");
		return $text;
	}
	
	private function getInventory(){
		
		$inventory = $this->inventory;
		
		//Сортировка инвентаря
		$edit = false; 		//Признак того, что распложение вещей изменилось
        $count = count($inventory);
		for($x = 1; $x < $count; $x++){
			for($i = 1;$i < $count; $i++){
				$a = $i + 1;
				if($inventory["slot$i"] == "0" and $inventory["slot$a"] != "999" and $inventory["slot$a"] != "0" and $i < 24){
					$inventory["slot$i"] = $inventory["slot$a"];
					$inventory["slot$a"] = "0";
					$edit = true;
				}
			}
		}
		if($edit){
			$table_name = $this->config->db_prefix."user_inventory";
			$newInv = "UPDATE $table_name SET ";
			for($i=1; $i < count($inventory); $i++){
				$newInv .= "`slot$i` = '".$inventory["slot$i"]."',";
			}
			$newInv = substr($newInv, 0, -1);
			$newInv .= " WHERE `id_user` = '".$this->account["id_account"]."'";
			$this->db->query($newInv);
		}
		
		//Сам инвентарь
		for($i = 1; $i < $count; $i++){
			if($inventory["slot$i"] != "0" and $inventory["slot$i"] != "999"){
				$invItem = (array) json_decode($inventory["slot$i"]);
				$sr["show"] = 1;
				$sr["info"] = $this->getTableInfo($invItem);
				$sr["hash"] = $invItem["hash"];
			}
			if($inventory["slot$i"] == "0" or $inventory["slot$i"] == "999"){
				$invItem["id"] = $inventory["slot$i"];
				$sr["show"] = 0;
				$sr["info"] = "";
				$sr["hash"] = "";
			}

			$sr["id"] = $invItem["id"];
			if($invItem["hash"] == $this->equipment["secondaryWeapon"] or $invItem["hash"] == $this->equipment["primaryWeapon"]){
				$sr["onOff"] = "1";
			}
			
			elseif($invItem["hash"] == $this->equipment["armor"] or $invItem["hash"] == $this->equipment["helmet"] or $invItem["hash"] == $this->equipment["leggings"] 
			or $invItem["hash"] == $this->equipment["bracers"]){
				$sr["onOff"] = "1";
			}
			else
				$sr["onOff"] = "0";
			
			$text .= $this->getReplaceTemplate($sr, "inventoryItem");
		}
		return $text;
	}
	
	private function getPotions(){
		
		//Сортировка инвентаря
		$count = count($this->potions);
		for($j = 1; $j < $count; $j++){
			for($i = 1; $i < $count; $i++){
				$a = $i + 1;
				
				if($this->potions["potion$a"] == 999) 
					break;
				if($this->potions["potion$i"] == "0" && $this->potions["potion$a"] != "0"){
					$temporal = $this->potions["potion$a"];
					$this->potions["potion$a"] = 0;
					$this->potions["potion$i"] = $temporal;
				}
			}
			$i = 1;
		}
		
		//Сам инвентарь
		for($i = 1; $i < $count; $i++){
			if($this->potions["potion$i"] != "0" && $this->potions["potion$i"] != "999"){
				$potion = (array) json_decode($this->potions["potion$i"]);
				$sr["count"] = $potion["count"];
				$sr["image"] = $potion["image"];
				$sr["info"] = $this->getTableInfoSth($potion);
				$sr["show"] = 1;
				$text .= $this->getReplaceTemplate($sr, "potions");
			}
			if($this->potions["potion$i"] == "999"){
				$sr["image"] = 999;
				$sr["count"] = "";
				$sr["info"] = "";
				$sr["show"] = 0;
				$text .= $this->getReplaceTemplate($sr, "potions");
			}
			if($this->potions["potion$i"] == "0"){
				$sr["image"] = 0;
				$sr["count"] = "";
				$sr["info"] = "";
				$sr["show"] = 0;
				$text .= $this->getReplaceTemplate($sr, "potions");
			}
		}
		return $text;
	}
	
	private function getImage($massiv, $nomer){
			$max = 0;
			for($i=0;$i<5;$i++){
				if($max<$massiv[$i]) $max = $massiv[$i];
			}
			$procent = 100/$max;
			$length = $procent * $massiv[$nomer];
			$length = round($length, 0);
			return $length;
	}
}
?>