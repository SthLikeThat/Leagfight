<?php
require_once "modules_class.php";
require_once "libraries/dBug.php";

class FrontPageContent extends Modules {
	
	public function __construct($db) {
		parent::__construct($db);
	}
	
	public function getCenter(){
        if(is_null($this->user))
            return false;
		$sr["characteristics"] = $this->getCharacteristics();
		$sr["nick"] = $this->user["login"];
		$sr["avatar"] = $this->user["avatar"];
		$discount = 100;
		for($i = 1; $i <= $this->user["lvl"]; $i++) 
			$discount = $discount -($discount * 0.02);
		$sr["discount"] = round(100 - $discount."%", 3);
		$sr["inventory"] = $this->getInventory();
		$sr["equipment"] = $this->getEquipment();
		$sr["invPotions"] = $this->getPotions();
		return $this->getReplaceTemplate($sr, "center");
	}
	
	protected function getEquipment(){
		for($i = 1; $i < count($this->inventory); $i++){
			$invItem = unserialize($this->inventory["slot$i"]);
			if($invItem["hash"] == $this->user["primaryWeapon"]){
				$sr["primaryWeapon"] = $invItem["id"];
				$sr["slotPrim"] = $i;
				$sr["hashPrim"] = $invItem["hash"];
			}
			if($invItem["hash"] == $this->user["secondaryWeapon"]){
				$sr["secondaryWeapon"] = $invItem["id"];
				$sr["slotSec"] = $i;
				$sr["hashSec"] = $invItem["hash"];
			}
			if($invItem["hash"] == $this->user["armor"]){
				$sr["armor"] = $invItem["id"];
				$sr["slotArmor"] = $i;
				$sr["hashArmor"] = $invItem["hash"];
			}
			if($invItem["hash"] == $this->user["helmet"]){
				$sr["helmet"] = $invItem["id"];
				$sr["slotHelmet"] = $i;
				$sr["hashHelmet"] = $invItem["hash"];
			}
			if($invItem["hash"] == $this->user["bracers"]){
				$sr["bracers"] = $invItem["id"];
				$sr["slotBracers"] = $i;
				$sr["hashBracers"] = $invItem["hash"];
			}
			if($invItem["hash"] == $this->user["leggings"]){
				$sr["leggings"] = $invItem["id"];
				$sr["slotLeggings"] = $i;
				$sr["hashLeggings"] = $invItem["hash"];
			}
		}
		if($this->user["primaryWeapon"] == "0"){
			$sr["primaryWeapon"] = "primaryWeapon";
			$sr["slotPrim"] = 0;
			$sr["hashPrim"] = 0;
		}
		if($this->user["secondaryWeapon"] == "0"){
			$sr["secondaryWeapon"] = "secondaryWeapon";
			$sr["slotSec"] = 0;
			$sr["hashSec"] = 0;
		}
		if($this->user["armor"] == "0"){
			$sr["armor"] = "armor";
			$sr["slotArmor"] = 0;
			$sr["hashArmor"] = 0;
		}
		if($this->user["helmet"] == "0"){
			$sr["helmet"] = "helmet";
			$sr["slotHelmet"] = 0;
			$sr["hashHelmet"] = 0;
		}
		if($this->user["bracers"] == "0"){
			$sr["bracers"] = "bracers";
			$sr["slotBracers"] = 0;
			$sr["hashBracers"] = 0;
		}
		if($this->user["leggings"] == "0"){
			$sr["leggings"] = "leggings";
			$sr["slotLeggings"] = 0;
			$sr["hashLeggings"] = 0;
		}
        //sadfasdfsad
		$text = $this->getReplaceTemplate($sr, "equipment");
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
	
	protected function getCharacteristics(){
		$dir = "lib";
		$strengh = $this->user["Strengh"];
		$defence = $this->user["Defence"];
		$agility = $this->user["Agility"];
		$physique = $this->user["Physique"];
		$mastery = $this->user["Mastery"];
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
		$sr["power"] = $this->user["power"];
		$text = $this->getReplaceTemplate($sr, "characteristics");
		return $text;
	}
	
	protected function getInventory(){
		$count = 0;	
		$house = $this->db->getAllOnField("user_house", "id", $this->user["id"], "", "");
		$countInv = $house["warehouse"] * 2;
		$inventory = $this->inventory;
		
		//Сортировка инвентаря
		$edit = false; 		//Признак того, что распложение вещей изменилось
		for($x = 1; $x < count($inventory); $x++){
			for($i = 1;$i < count($inventory); $i++){
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
			$newInv .= " WHERE `id` = '".$this->user["id"]."'";
			$this->db->query($newInv);
		}
		
		//Сам инвентарь
		for($i = 1; $i < count($inventory); $i++){
			if($inventory["slot$i"] != "0" and $inventory["slot$i"] != "999"){
				$invItem = unserialize($inventory["slot$i"]);
				$sr["show"] = 1;
			}
			if($inventory["slot$i"] == "0" or $inventory["slot$i"] == "999"){
				$invItem["id"] = $inventory["slot$i"];
				$sr["show"] = 0;
			}

			$sr["id"] = $invItem["id"];
			$sr["slot"] = $i;
			if($invItem["hash"] == $this->user["secondaryWeapon"] or $invItem["hash"] == $this->user["primaryWeapon"]){
				$sr["onOff"] = "1";
			}
			
			elseif($invItem["hash"] == $this->user["armor"] or $invItem["hash"] == $this->user["helmet"] or $invItem["hash"] == $this->user["leggings"] 
			or $invItem["hash"] == $this->user["bracers"]){
				$sr["onOff"] = "1";
			}
			else $sr["onOff"] = "0";
			$sr["type"] = 1;
			$sr["hash"] = $invItem["hash"];
			$text .= $this->getReplaceTemplate($sr, "inventoryItem");
		}
		$sd["inventoryItem"] = $text;
		$textInv = $this->getReplaceTemplate($sd, "inventory");
		return $textInv;
	}
	
	private function getPotions(){
		$potions = $this->user;
		//Сортировка инвентаря
		for($i=1;$i<=5;$i++){
			$a = $i + 1;
			if($potions["slot$i"] == 0 and $potions["slot$a"] != 999){
				$temporal = $potions["slot$i"];
				$potions["slot$i"] = $potions["slot$a"];
				$potions["slot$a"] = $temporal;
			}
			if($potions["slot$i"] == 999) break;
		}
		
		//Сам инвентарь
		for($i=1;$i<=5;$i++){
			$sr["number"] = $i;
			$sr["count"] = $potions["slot$i"."_count"];
			if($potions["slot$i"."_count"] == 0) $sr["count"] = "";
			$sr["slot"] = $potions["slot$i"];
			$sr["type"] = 2;
			$text .= $this->getReplaceTemplate($sr, "potions");
		}
		$sd["inventoryItem"] = $text;

		$textInv = $this->getReplaceTemplate($sd, "inventory");
		return $textInv;
	}
	
	private function getImage($massiv, $nomer){
			$max = 0;
			for($i=0;$i<5;$i++){
				if($max<$massiv[$i]) $max = $massiv[$i];
			}
			$procent = 100/$max;
			$dlina = $procent * $massiv[$nomer];
			$dlina = round($dlina, 0);
			return $dlina;
	}
}
//--------------------------------------------------------------------------------------------------------------------------------------------------
function FizzBuzz($triggers = array("Fizz" => "(\$i % 3 == 0)", "Buzz" => "(\$i % 5 == 0)"), $min = 1, $max = 100 ){
		for( $i = $min; $i <= $max; $i++){
			$text = "";
			foreach($triggers as $key => $value){
				eval("\$mda =".$value.";");
				if(is_numeric($text))
					$text = "";
				if($mda)
					$text .= $key;
				elseif($text == "")
					$text = $i;
			}
			$global .= $text.", ";
		}
		echo substr($global,0, -2);
}
?>