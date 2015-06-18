<?php
require_once "database_class.php";

class someFunctions extends DataBase{
	
	private $db;
	
	public function __construct() {
		parent::__construct();
		$this->db = $this;
	}
	
	public function query($query){
		if (!$result = $this->mysqli->query($query)) {
			return $query." Ошибка: ".$this->mysqli->error;
		}
		return $result;
	}
	
	public function showDetailsLog($thing, $id){
		$log = $this->db->getAllOnField("logs", "idLog", $id, "idLog", "");
        $pos = strpos($thing, "|");
        $userType = substr($thing, 0, $pos);
        $userThing = substr($thing, $pos + 1);
        $user = unserialize($log[$userType]);
		$item = $user["user"][$userThing];
		if($item["id"] != 0){
			$text = $this->show(0, $item);
			echo $text;
		}
		else echo " ";
	}
	
	public function show($slot, $invItem ){
		if($invItem["id"] < 500){
			$weapon =  $invItem;
			if($weapon["type"] == 1){ $type="one"; $typeName="Одноручное";}
			if($weapon["type"] == 2){ $type="two"; $typeName="Двуручное";}
			if($weapon["type"] == 3){ $type="staff"; $typeName="Древковое";}
			if($weapon["typedamage"] == 1){ $typedamage="piercing"; $typedamageName="Колющее";}
			if($weapon["typedamage"] == 2){ $typedamage="cutting"; $typedamageName="Режущее";}
			if($weapon["typedamage"] == 3){ $typedamage="maces"; $typedamageName="Дробящее";}

			$sr["typeName"] = $typeName;
			$sr["type"] = $type;
			$sr["typedamage"] = $typedamage;
			$sr["damageLvl"] = $invItem["damageLvl"];
			$sr["critLvl"] = $invItem["critLvl"];
			$sr["typedamageName"] = $typedamageName;
			$sr["requiredlvl"] = $weapon["requiredlvl"];
			$sr["damage"] = $invItem["damage"];
			$sr["crit"] = $invItem["crit"];
			
			$text = $this->getReplaceTemplate($sr, "weaponView");
			
			if($weapon["bonusstr"]) $text .= "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />".$weapon["bonusstr"]."</div>";
			if($weapon["bonusdef"]) $text .= "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>".$weapon["bonusdef"]."</div>";
			if($weapon["bonusag"]) $text .= "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>".$weapon["bonusag"]."</div>";
			if($weapon["bonusph"]) $text .= "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>".$weapon["bonusph"]."</div>";
			if($weapon["bonusms"]) $text .= "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>".$weapon["bonusms"]."</div>";
			
			echo $text;
		}
		
		if($invItem["id"] > 500 and $invItem["id"] < 1000){
			$armor = $invItem;
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
			$sr["armor"] = $armor["defence"];
			$sr["armorLvl"] = $invItem["armor"];
			$text = $this->getReplaceTemplate($sr, "armorView");
			
			if($armor["bonusstr"]) $text .= "<div class='detail2 photoDetail' data-title='Сила'><img src='image_char/image/strengh.png' alt='Сила'  height='20' > <br />".$armor["bonusstr"]."</div>";
			if($armor["bonusdef"]) $text .= "<div class='detail2 photoDetail' data-title='Защита'><img src='image_char/image/defence.png' alt='Защита'  height='20' > <br/>".$armor["bonusdef"]."</div>";
			if($armor["bonusag"]) $text .= "<div class='detail2 photoDetail' data-title='Ловкость'><img src='image_char/image/agility.png' alt='Ловкость' height='20' > <br/>".$armor["bonusag"]."</div>";
			if($armor["bonusph"]) $text .= "<div class='detail2 photoDetail' data-title='Телосложение'><img src='image_char/image/physique.png' alt='Телосложение'  height='20' > <br/>".$armor["bonusph"]."</div>";
			if($armor["bonusms"]) $text .= "<div class='detail2 photoDetail' data-title='Мастерство'><img src='image_char/image/mastery.png' alt='Мастерство'  height='20' > <br/>".$armor["bonusms"]."</div>";
			
			echo $text;
		}
	}
}

$someFunctions = new someFunctions();
if($_REQUEST["WhatIMustDo"] === "showDetailsLog")		$someFunctions->showDetailsLog($_REQUEST["thing"],$_REQUEST["id"]);

?>