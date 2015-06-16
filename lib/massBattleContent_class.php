<?php
require_once "modules_class.php";

class massBattle extends Modules {

	protected $data;
	private $coordinates;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->battle = $this->db->getAllOnField("mass_battle", "id", $_GET["id"], "", "");
		if($this->battle["time_end"] < time())
			header("Location: index.php");
	}
	
	protected function getCenter(){
		$sr["field"] = $this->getField();
		$sr["status"] = $this->getStatus();
		$sr["skills"] = $this->getSkills();
		$sr["time"] = $this->getTime();
		$sr["turn"] = $this->getTurn();
		return $this->getReplaceTemplate($sr, "battleField");
	}
	
	private function getTime(){
		$information = unserialize($this->battle["information"]);
		return $information["endRound"] - time();
	}
	
	private function getTurn(){
		$information = unserialize($this->battle["information"]);
		return $information["turn"];
	}
	
	private function getSkills(){
		$alive = unserialize($this->battle["alive"]);
		for($k = 0; $k < count($alive); $k++){
			if($alive[$k]["login"] == $this->user["login"]){
				$type = $alive[$k]["type"];
				$i = 1;
				$sr["image"] = "attack";
				$sr["type"] = "attack";
				$sr["data"] = "Обычная атака <br/> Тип: удар<br/>Влияние: 1 выбранная клетка <br/> Урон: 100%";
				$sr["i"] = $i;
				$text = $this->getReplaceTemplate($sr, "battle_skill_item");
				$i++;
				switch ($type) {
					case "shield": 
						$sr["image"] = "block_shield";
						$sr["type"] = "block_shield";
						$sr["data"] = "Укрыться щитом <br/> Тип: на себя<br/> Получаемый урон: - 50% <br/> Наносимый урон: - 50%";
						$sr["i"] = $i;
						$i++;
						$text .= $this->getReplaceTemplate($sr, "battle_skill_item");
						break;
					case "two": 
						$sr["image"] = "roundhouse_kick";
						$sr["type"] = "roundhouse_kick";
						$sr["data"] = "Круговой удар <br/> Тип: удар <br/> Влияние: Все примыкающие клетки <br/> Урон: 50%";
						$sr["i"] = $i;
						$i++;
						$text .= $this->getReplaceTemplate($sr, "battle_skill_item");
						break;
				}
				break;
			}
		}
		return $text;
	}
	
	private function getStatus(){
		$alive = unserialize($this->battle["alive"]);
		for($i = 0; $i < count($alive); $i++){
			if($alive[$i]["login"] == $this->user["login"]){
				$sr["height"] = round($alive[$i]["currentHp"] / $alive[$i]["maxHp"] * 100);
				$sr["currentHp"] = $alive[$i]["currentHp"];
				$sr["course"] = $alive[$i]["course"];
				$sr["attack"] = $alive[$i]["attack"];
				$sr["coordinates"] = $this->coordinates;
				$sr["damage"] = round($alive[$i]["maxHp"]/10);
				return $this->getReplaceTemplate($sr, "battle_status");
			}
		}
	}
	
	private function getField(){
		$text = "<table id='battleTaible'>";
		$inStock = "false";
		$alive = unserialize($this->battle["alive"]);
		for($i = 1; $i <= 10; $i++){
			$field = unserialize($this->battle["line_$i"]);
			$text .= "<tr>";
			for($j = 1; $j <= count($field); $j++){
				if($field[$j]["user"] !== 0){
					$user = $field[$j]["user"];
					if($user == $this->user["login"]){
						$this->coordinates = $i."_".$j;
					}
					//Находим его тип
					for($k = 0; $k < count($alive); $k++){
						if($alive[$k]["login"] == $user){
							$type = $alive[$k]["type"];
							$damage = round($alive[$k]["maxHp"]/ 10);
							$data = "onmouseover='showInformationBattle(this)' onmouseout='hideInformation(this)' data-info='Хп: {$alive[$k]["currentHp"]} <br/> Урон: {$damage} <br/> Ход: {$alive[$k]["course"]} <br/> Удар: {$alive[$k]["attack"]}'";
							break;
						}
					}
					//проверка лиги пользователя
					if($this->battle["grey"] !== "0"){
						$guild = unserialize($this->battle["grey"]);
						for($q = 0; $q < count($guild); $q++){
							if($guild[$q]["login"] == $user){
								$style = " greyUser";
								if($user == $this->user["login"]){
									$inStock = "true";
									$move = $guild[$q]["move"];
									$attack = $guild[$q]["hit"];
								}
								$user .= "<img src='image_char/image/{$type}.png' height='15'>";
							}
						}
					}
					if($this->battle["black"] !== "0"){
						$guild = unserialize($this->battle["black"]);
						for($q = 0; $q < count($guild); $q++){
							if($guild[$q]["login"] == $user){
								$style = " blackUser";
								if($user == $this->user["login"]){
									$inStock = "true";
									$move = $guild[$q]["move"];
									$attack = $guild[$q]["hit"];
								}
								$user .= "<img src='image_char/image/{$type}.png' height='15'>";
							}
						}
					}
					if($this->battle["white"] !== "0"){
						$guild = unserialize($this->battle["white"]);
						for($q = 0; $q < count($guild); $q++){
							if($guild[$q]["login"] == $user){
								$style = " whiteUser";
								if($user == $this->user["login"]){
									$inStock = "true";
									$move = $guild[$q]["move"];
									$attack = $guild[$q]["hit"];
								}
								$user .= "<img src='image_char/image/{$type}.png' height='15'>";
							}
						}
					}
				}
				else{
					$user = "";
					$style = "";
				}
				$text .= "<td id='{$i}_{$j}'class='{$field[$j]["type"]} $style' $data >$user</td>";
				$data = "";
			}
			$text .= "</tr>";
		}
		$text .= "</table>";
		if($this->user["league"] == 0)
			$league = "grey";
		if($this->user["league"] == 1)
			$league = "black";
		if($this->user["league"] == 2)
			$league = "white";
		$text .= "<div id = 'information' data-inStock='$inStock' data-move='$move' data-attack='$attack' data-league='{$league}' data-turn='{$this->getTurn()}'> </div>";
		return $text;
	}
}
?>