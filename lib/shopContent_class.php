<?php
require_once "modules_class.php";
require_once "shop_class.php";

class shopContent extends Modules {
	
	public function __construct($db) {
		parent::__construct($db);
		$this->shop = new Shop($db);
	}
	
	protected function getCenter() {
		if($this->user["viewAllShop"] == 1)
			$sr["checked"] = "checked";
		else $sr["checked"] = "";
		if($this->data["thing"] == 1){
			$sr["weapon"] = $this->getWeapon();
			$sr["type"] = 1;
			$weaponShop = $this->getReplaceTemplate($sr, "shopWeapon");
			$srSth["content"] = $weaponShop;
		}
		
		if($this->data["thing"] == 2){
			$sr["armor"] = $this->getArmor(2);
			$sr["type"] = 0;
			$armorShop = $this->getReplaceTemplate($sr, "shopArmor");
			$srSth["content"] = $armorShop;
		}
		
		if($this->data["thing"] == 3){
			$sr["armor"] = $this->getArmor(3);
			$sr["type"] = 0;
			$armorShop = $this->getReplaceTemplate($sr, "shopArmor");
			$srSth["content"] = $armorShop;
		}
		
		if($this->data["thing"] == 4){
			$sr["armor"] = $this->getArmor(4);
			$sr["type"] = 0;
			$armorShop = $this->getReplaceTemplate($sr, "shopArmor");
			$srSth["content"] = $armorShop;
		}
		
		if($this->data["thing"] == 5){
			$sr["armor"] = $this->getArmor(5);
			$sr["type"] = 0;
			$armorShop = $this->getReplaceTemplate($sr, "shopArmor");
			$srSth["content"] = $armorShop;
		}
		
		if($this->data["thing"] == 6){
			$sr["armor"] = $this->getArmor(6);
			$sr["type"] = 0;
			$armorShop = $this->getReplaceTemplate($sr, "shopArmor");
			$srSth["content"] = $armorShop;
		}
		if($this->data["thing"] == 7){
			$sr["smth"] = $this->getSmth();
			$smthShop = $this->getReplaceTemplate($sr, "shopSmth");
			$srSth["content"] = $smthShop;
		}
		$srSth["menuItem"] = $this->getFileContent("shopMenu");
		return $this->getReplaceTemplate($srSth, "shop");
	}
	
	protected function getWeapon(){
		$typeSort = $this->data["typeSort"];
		if(!$this->valid->isIntNumber($typeSort))header("Location:?view=notfound");
		if($typeSort != 0 and $typeSort != 1 and $typeSort != 2 and $typeSort != 3) header("Location:?view=notfound");
		$typedamageSort = $this->data["typedamageSort"];
		if(!$this->valid->isIntNumber($typedamageSort))header("Location:?view=notfound");
		if($typedamageSort != 0 and $typedamageSort != 1 and $typedamageSort != 2 and $typedamageSort != 3) header("Location:?view=notfound");
		$lvl = $this->user["lvl"] + 3;
		$weapon = $this->shop->getShop("weapon",$lvl);
		
		$count = count($weapon);
		if($count > 8 and $this->user["viewAllShop"] == 1)
			$count = $count;
		if($count > 8 and $this->user["viewAllShop"] == 0)
			$count = 8;
		
		for ($i = 0; $i < $count; $i++){
			if($typeSort ==0)	{	$typeSort = $weapon[$i]["type"];	$typeFiltr = true;	}
			if($typedamageSort ==0)	{	$typedamageSort = $weapon[$i]["typedamage"];	$typedamageFiltr = true;	}
		
			if($weapon[$i]["requiredlvl"] <= $lvl and $weapon[$i]["type"] == $typeSort and $weapon[$i]["typedamage"] == $typedamageSort){
					$sr = $this->getPageWeapon($weapon,$i);
					$text .= $this->getReplaceTemplate($sr, "weapon");
			}
		
			if($typeFiltr){	$typeSort =0;	$typeFiltr=false;	}
			if($typedamageFiltr){		$typedamageSort =0;		$typedamageFiltr=false;		}
		}
		return $text;
		
	}
	
	protected function getArmor($thing){
			$typeSort = $this->data["typeSort"];
			$lvl = $this->user["lvl"] + 3;
			$armor = $this->shop->getShop("armor",$lvl);
			
			$count = count($armor);
			if($count > 8 and $this->user["viewAllShop"] == 1)
				$count = $count;
			if($count > 8 and $this->user["viewAllShop"] == 0)
				$count = 8;
			
			for ($i=0;$i<count($armor);$i++){
				if($typeSort ==0)	{	$typeSort = $armor[$i]["typeDefence"];	$typeFiltr = true;	}
				if($armor[$i]["requiredlvl"] <= $lvl and $armor[$i]["typeDefence"] == $typeSort and $armor[$i]["thing"] == $thing){
					$sr = $this->getPageArmor($armor,$i);
					$text .= $this->getReplaceTemplate($sr, "armor");
				}
			
				if($typeFiltr){	$typeSort =0;	$typeFiltr=false;	}
			}
		
		return $text;
	}
	
	private function getPageWeapon($weapon,$i){
		if($weapon[$i]["type"] == 1) $type="Одноручное";
		if($weapon[$i]["type"] == 2) $type="Двуручное";
		if($weapon[$i]["type"] == 3) $type="Древковое";
		if($weapon[$i]["typedamage"] == 1) $typedamage="Колющее";
		if($weapon[$i]["typedamage"] == 2) $typedamage="Режущее";
		if($weapon[$i]["typedamage"] == 3) $typedamage="Дробящее";
		
			$sr["name"] = $weapon[$i]["name"];
			$sr["type"] = $type;
			$sr["typedamage"] = $typedamage;
			$sr["requiredlvl"] = $weapon[$i]["requiredlvl"];
			$sr["damage"] = $weapon[$i]["damage"];
			$sr["crit"] = $weapon[$i]["crit"];
			$sr["bonusstr"] = $weapon[$i]["bonusstr"];
			$sr["bonusdef"] = $weapon[$i]["bonusdef"];
			$sr["bonusag"] = $weapon[$i]["bonusag"];
			$sr["bonusph"] = $weapon[$i]["bonusph"];
			$sr["bonusms"] = $weapon[$i]["bonusms"];
			$sr["price"] = $weapon[$i]["price"];
			$sr["id"] = $weapon[$i]["id"];
			return $sr;
	}
	
	private function getPageArmor($armor,$i){
		if($armor[$i]["typeDefence"] == 1) $type="Лёгкая";
		if($armor[$i]["typeDefence"] == 2) $type="Средняя";
		if($armor[$i]["typeDefence"] == 3) $type="Тяжелая";
		
			$sr["name"] = $armor[$i]["name"];
			$sr["type"] = $type;
			$sr["requiredlvl"] = $armor[$i]["requiredlvl"];
			$sr["defence"] = $armor[$i]["defence"];
			$sr["bonusstr"] = $armor[$i]["bonusstr"];
			$sr["bonusdef"] = $armor[$i]["bonusdef"];
			$sr["bonusag"] = $armor[$i]["bonusag"];
			$sr["bonusph"] = $armor[$i]["bonusph"];
			$sr["bonusms"] = $armor[$i]["bonusms"];
			$sr["price"] = $armor[$i]["price"];
			$sr["id"] = $armor[$i]["id"];
			return $sr;
	}
	
	private function getSmth(){
		$lvl = $this->user["lvl"] + 3;
		$food = $this->shop->getShop("something",$lvl);
		for($i=0;$i<count($food);$i++){
			$count++;
			$sr["name"] = $food[$i]["title"];
			$sr["regen"] = $food[$i]["valueEffect"];
			$sr["image"] = $food[$i]["image"];
			$sr["requiredlvl"] = $food[$i]["requiredlvl"];
			$sr["price"] = $food[$i]["price"];
			$sr["valuta"] = $food[$i]["valuta"];
			$sr["id"] = $food[$i]["image"];
			$sr["liclass"] = "on";
			if($count > 8) $sr["liclass"] = "off";
			$text .= $this->getReplaceTemplate($sr, "food");
		}
		return $text;
	}
	
	protected function getShopMenu(){
		$menu = $this->db->getAll("shop", "", "");
		for ($i=0;$i<count($menu);$i++){
			$sr["image"] = $menu[$i]["image"];
			$sr["title"] = $menu[$i]["title"];
			$sr["link"] = $menu[$i]["link"];
			$text .= $this->getReplaceTemplate($sr, "shopMenuItem");
		}
		return $text;
	}
}
?>