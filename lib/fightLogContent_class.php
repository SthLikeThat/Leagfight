<?php
require_once "modules_class.php";

class fightLogContent extends Modules {

	private $winner;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->idLog = $this->data["id"];
		$this->log = $this->db->getAllOnField("logs","idLog", $this->idLog, "idLog", "");
		if($this->log == 0) header("Location:?view=notfound");
	}
	
	protected function getCenter() {
        $agressor = unserialize($this->log["agressor"]);
        $defender = unserialize($this->log["defender"]);
		$sr["fightLogDamageAgr"] = $this->getLog("Agr");
		$sr["fightLogDamageDef"] = $this->getLog("Def");
		$sr["agressorCharacteristics"] = $this->getCharacteristics("agressor");
		$sr["defenderCharacteristics"] = $this->getCharacteristics("defender");
		$sr["agressorNick"] = $agressor["user"]["login"];
		$sr["idAgressor"] = $agressor["user"]["id"];
		$sr["defenderNick"] = $defender["user"]["login"];
		$sr["idDefender"] = $defender["user"]["id"];
		$sr["agressorAvatar"] = $agressor["user"]["avatar"];
		if($this->log["typeFight"] == "arenaUser")
			$sr["defenderAvatar"] = $defender["user"]["avatar"];
		if($this->log["typeFight"] == "arenaBot")
			$sr["defenderAvatar"] = "arena_bots/".$defender["user"]["avatar"].$this->log["avatar"];
		$sr["prize"] = $this->getPrize();
		$sr["powerAgr"] = $agressor["user"]["power"];
		$sr["powerDef"] = $defender["user"]["power"];
		$sr["onOff"] = "off";
		
		//Вывод экипировки
        $agressor = unserialize($this->log["agressor"]);
        if(is_array($agressor["user"]["primaryWeapon"]))
            $sr["agrPrimaryWeapon"] = $agressor["user"]["primaryWeapon"]["id"];
        else  $sr["agrPrimaryWeapon"] = "primaryWeapon";

        if(is_array($agressor["user"]["secondaryWeapon"]))
            $sr["agrSecondaryWeapon"] = $agressor["user"]["secondaryWeapon"]["id"];
        else $sr["agrSecondaryWeapon"] = "secondaryWeapon";

        if(is_array($agressor["user"]["armor"]))
            $sr["agrArmor"] =  $agressor["user"]["armor"]["id"];
        else $sr["agrArmor"] = "armor";

        if(is_array($agressor["user"]["helmet"]))
            $sr["agrHelmet"] =  $agressor["user"]["helmet"]["id"];
        else  $sr["agrHelmet"] = "helmet";

        if(is_array($agressor["user"]["leggings"]))
            $sr["agrLeggings"] = $agressor["user"]["leggings"]["id"];
        else  $sr["agrLeggings"] = "leggings";

        if(is_array($agressor["user"]["bracers"]))
            $sr["agrBracers"] = $agressor["user"]["bracers"]["id"];
        else  $sr["agrBracers"] = "bracers";
        ////////////////////////////
        $defender = unserialize($this->log["defender"]);
        if(is_array($defender["user"]["primaryWeapon"]))
		    $sr["defPrimaryWeapon"] = $defender["user"]["primaryWeapon"]["id"];
        else  $sr["defPrimaryWeapon"] = "primaryWeapon";

        if(is_array($defender["user"]["secondaryWeapon"]))
		    $sr["defSecondaryWeapon"] = $defender["user"]["secondaryWeapon"]["id"];
        else $sr["defSecondaryWeapon"] = "secondaryWeapon";

        if(is_array($defender["user"]["armor"]))
            $sr["defArmor"] =  $defender["user"]["armor"]["id"];
        else $sr["defArmor"] = "armor";

        if(is_array($defender["user"]["helmet"]))
		    $sr["defHelmet"] =  $defender["user"]["helmet"]["id"];
        else  $sr["defHelmet"] = "helmet";

        if(is_array($defender["user"]["leggings"]))
            $sr["defLeggings"] = $defender["user"]["leggings"]["id"];
        else  $sr["defLeggings"] = "leggings";

        if(is_array($defender["user"]["bracers"]))
		    $sr["defBracers"] = $defender["user"]["bracers"]["id"];
        else  $sr["defBracers"] = "bracers";
		
		if($this->log["winner"] == $this->log["idAgressor"]){
			$sr["agrClass"] = "winner";
			$sr["defClass"] = "loser";
		}
		else{
			$sr["agrClass"] = "loser";
			$sr["defClass"] = "winner";
		}
		$sr["id"] = $this->log["idLog"];
		return $this->getReplaceTemplate($sr, "fightLog");
	}
	
	private function getPrize(){
	if($this->log["winner"] == $this->user["id"])
		$text = "Вы победили: ";
	if($this->log["winner"] == 0)
		$text = "Ничья";
	if($this->user["id"] != $this->log["idAgressor"] and $this->user["id"] != $this->log["idDefender"]){
		$spectacor = $this->db->getAllOnField("users", "id", $this->log["winner"], "", "");
		$text = "Победил ".$spectacor["login"].": ";
	}
	else if($this->log["winner"] != $this->user["id"])
		$text = "Вы проиграли: ";
	$prize = unserialize($this->log["prize"]);
		foreach($prize as $name => $value){
			if($value != 0){
				$text .= '<img src="images/'.$name.'.png" height="20"/> '.$value."&nbsp;&nbsp;";
			}
		}
		return $text;
	}
	
	private function getLog($who){
		//Грузим урон и его тип
        $agressorFullDamage = 0;
        $defenderFullDamage = 0;
        $agressor = unserialize($this->log["agressor"]);
        $defender = unserialize($this->log["defender"]);
		$allAgrDamage = unserialize($this->log["allAgrDamage"]);
		$allDefDamage = unserialize($this->log["allDefDamage"]);
		$typesAgrDamage = unserialize($this->log["typesAgrDamage"]);
		$typesDefDamage = unserialize($this->log["typesDefDamage"]);
		for($i = 0 ; $i < 5 ; $i++ ){
			$sr["agressor_$i"] = $allAgrDamage[$i];
			$sr["defender_$i"] = $allDefDamage[$i];
			$sr["type_agressor_$i"] = $typesAgrDamage[$i];
			$sr["type_defender_$i"] = $typesDefDamage[$i];
			$agressorFullDamage += $allAgrDamage[$i];
			$defenderFullDamage += $allDefDamage[$i];
		}
		$sr["agressorFullDamage"] = $agressorFullDamage;
		$sr["defenderFullDamage"] = $defenderFullDamage;
		

		//грузим оружие бойцов
		$agrWepDamage = $agressor["user"]["primaryWeapon"]["damage"];

		if($agressor["user"]["secondaryWeapon"]["id"] < 500)
			$agrWepDamage += $agressor["user"]["secondaryWeapon"]["damage"];
		$defWepDamage =  $defender["user"]["primaryWeapon"]["damage"];
		if($defender["user"]["secondaryWeapon"]["id"] < 500)
			$defWepDamage += $defender["user"]["secondaryWeapon"]["damage"];
		
		//Облегчаем себе работу со статами
		$agrStrengh = $agressor["user"]["Strengh"];
		$agrDefence = $agressor["user"]["Defence"];
		$agrAgility = $agressor["user"]["Agility"];
		$agrMastery = $agressor["user"]["Mastery"];
		$defStrengh =  $defender["user"]["Strengh"];
		$defDefence = $defender["user"]["Defence"];
		$defAgility = $defender["user"]["Agility"];
		$defMastery =  $defender["user"]["Mastery"];
		
		//Расчитываем статистику
		$agrDamage = $agrWepDamage * $agrStrengh;
			if($agrWepDamage == 0)	
				$agrDamage = 0.1 * $agrStrengh;
		$defDamage =  $defWepDamage * $defStrengh;
			if($defWepDamage == 0)
				$defDamage = 0.1 * $defStrengh;
		
		$agrDodge = round($defStrengh/($defStrengh + $agrAgility) * 100);
		if($agrDodge > 100) $agrDodge = 100;
		$defDodge =  round($agrStrengh/($agrStrengh + $defAgility) * 100);
		if($defDodge > 100) $defDodge = 100;

		$agrArmorGet = $agressor["totalArmor"] * $agrDefence;
		$defArmorGet = $defender["totalArmor"] * $defDefence;
		
		$agrCrit = round(($agrMastery + $defMastery)/($defMastery/15));
		if($agrCrit > 100) $agrCrit = 100;
		$defCrit = round(($defMastery + $agrMastery)/($agrMastery/15));
		if($defCrit > 100) $defCrit = 100;
		
		//Выводим статистику
		$sr["agrDodge"] = $agrDodge;
		$sr["defDodge"] = $defDodge;
		$sr["agrDamage"] = $agrDamage;
		$sr["defDamage"] = $defDamage;
		$sr["agrArmor"] = $agrArmorGet;
		$sr["defArmor"] = $defArmorGet;
		$sr["agrCrit"] = $agrCrit;
		$sr["defCrit"] = $defCrit;
		
		$text = $this->getReplaceTemplate($sr, "fightLogDamage$who");
		return $text;
	}
	
	private function getCharacteristics($user){
		$user = unserialize($this->log[$user]);
		$sr["strengh"] = $user["user"]["Strengh"];
		$sr["defence"] = $user["user"]["Defence"];
		$sr["agility"] = $user["user"]["Agility"];
		$sr["physique"] = $user["user"]["Physique"];
		$sr["mastery"] = $user["user"]["Mastery"];
		
		$massiv = array ($sr["strengh"],$sr["defence"],$sr["agility"], $sr["physique"],$sr["mastery"]);
		$sr["percentStrengh"] = $this->getImage($massiv,0);
		$sr["percentDefence"] = $this->getImage($massiv,1);
		$sr["percentAgility"] = $this->getImage($massiv,2);
		$sr["percentPhysique"] = $this->getImage($massiv,3);
		$sr["percentMastery"] = $this->getImage($massiv,4);
		$text = $this->getReplaceTemplate($sr, "characteristics");
		return $text;
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
?>