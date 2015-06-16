<?php
require_once "modules_class.php";
require_once "arena_class.php";

class arenaContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
		$this->arena = new arena($db);
		$this->enemy = $this->arena->getRandomEnemy($this->user["minLvl"],$this->user["maxLvl"]);
		$this->enemySettings = $this->db->getAllOnField("user_settings", "id", $this->enemy["id"], "", ""); 
		session_start();
		if(!$_SESSION["botLvl"]) $_SESSION["botLvl"] = 1;
		if(!$_SESSION["botId"]) $this->bot = $this->arena->getRandomBot($_SESSION["botLvl"]);
		else $this->bot = $this->db->getAllOnField("arena_bots", "id", $_SESSION["botId"], "", "");
	}
	
	protected function getCenter() {
			$sr["characteristics"] = $this->getCharacteristics();
			$sr["characteristicsBot"] = $this->getCharacteristicsBot();
			$sr["nick"] = $this->enemy["login"];
			$sr["avatar"] = $this->enemy["avatar"];
			$sr["nameBot"] = $this->bot["login"];
				$sr["lvl"] = $this->enemy["lvl"];
			if($this->enemy){
				$sr["avatar"] = $this->enemy["avatar"];
				$sr["id"] = $this->enemy["id"];
			}
			else{
				$sr["avatar"] = "nobody_Here";
				$sr["id"] = 0;
			}
			$sr["lvlBot"] = $this->bot["lvl"];
			$sr["minLvl"] = $this->user["minLvl"];
			$sr["maxLvl"] = $this->user["maxLvl"];
			$sr["messAttacker"] = $this->enemySettings["messAttacker"];
			if($this->enemySettings["messAttacker"] == "")
				$sr["messAttacker"] = "Сообщение нападающему.";
			$sr["botStatistic"] = $this->getBotStatistic();
			$sr["userStatistic"] = $this->getuserStatistic();
			$sr["currentLvlBot"] = $_SESSION["botLvl"];
			$sr["nextLvlBot"] = $_SESSION["botLvl"] + 1;
			if(!$_SESSION["bot_avatar"]) $_SESSION["bot_avatar"] = rand(1,4);
			$sr["avatarBot"] = $this->bot["avatar"].$_SESSION["bot_avatar"];
			$sr["avatarBotRand"] = $_SESSION["bot_avatar"];
			$sr["idBot"] = $_SESSION["botId"];
			$sr["textBot"] = $this->bot["text"];
			$timeToAttack = $this->user["timerAttack"];
			$allSeconds = $timeToAttack - time();
			$minutes = substr($allSeconds/60,0,2);
			if($allSeconds < 600) $minutes = substr($allSeconds/60,0,1);
			$seconds = $allSeconds - ($minutes * 60);
			if(time() > $timeToAttack){
				$minutes = 0;
				$seconds = 0;
			}
			$sr["timerMin"] = $minutes;
			$sr["timerSec"] = $seconds;
		return $this->getReplaceTemplate($sr, "arena");
	}
	
	private function getuserStatistic(){
		$statistic = unserialize($this->user["userStatistic"]);
		$sr["fights"] = $statistic["wins"] + $statistic["lose"] + $statistic["draw"];
		$sr["wins"] = $statistic["wins"];
		$sr["lose"] = $statistic["lose"];
		$sr["draw"] = $statistic["draw"];
		$sr["stoleGold"] = $statistic["goldStolen"];
		$sr["lostGold"] = $statistic["goldLost"];
		$text = $this->getReplaceTemplate($sr, "statistic");
		return $text;
	}
	
	private function getBotStatistic(){
		$botStatistic = unserialize($this->user["arenaBot".$_SESSION["botLvl"]]);
		$allBotStatistic = unserialize($this->user["allBots"]);
		for($i = 0; $i < count($botStatistic); $i++){
			if($botStatistic[$i]["id"] == $_SESSION["botId"]){
				$thisBotStatistic = $botStatistic[$i];
				break;
			}
		}
		$sr["fightsBot"] = $allBotStatistic["wins"] + $allBotStatistic["lose"] + $allBotStatistic["draw"];
		$sr["winsBot"] = $allBotStatistic["wins"];
		$sr["loseBot"] = $allBotStatistic["lose"];
		$sr["drawBot"] = $allBotStatistic["draw"];
		$sr["stoleGoldBot"] = $allBotStatistic["stoleGold"];
		$sr["lostGoldBot"] = $allBotStatistic["lostGold"];
		
		$sr["fightsForThis"] = $thisBotStatistic["wins"] + $thisBotStatistic["lose"] + $thisBotStatistic["draw"];
		$sr["winsForThis"] = $thisBotStatistic["wins"];
		$sr["loseForThis"] = $thisBotStatistic["lose"];
		$sr["drawForThis"] = $thisBotStatistic["draw"];
		$sr["stoleGoldFromThis"] = $thisBotStatistic["stoleGold"];
		$sr["lostGoldFromThis"] = $thisBotStatistic["lostGold"];
		$text = $this->getReplaceTemplate($sr, "BotStatistic");
		return $text;
	}
	
	private function getCharacteristicsBot(){
		if(!$_SESSION["botId"]){
			$_SESSION["botId"] = $this->bot["id"];
			$chars = unserialize($this->bot["characteristics"]);
			$strengh = round((rand($chars["strenghMin"], $chars["strenghMax"]) * $this->user["Strengh"] / 10) + rand($this->user["Strengh"]/100, $this->user["Strengh"]/75), 0);
			$defence = round((rand($chars["defenceMin"], $chars["defenceMax"]) * $this->user["Defence"] / 10) + rand($this->user["Defence"]/100, $this->user["Defence"]/75), 0);
			$agility = round((rand($chars["agilityMin"], $chars["agilityMax"]) * $this->user["Agility"] / 10) + rand($this->user["Agility"]/100, $this->user["Agility"]/75), 0);
			$physique = round((rand($chars["physiqueMin"], $chars["physiqueMax"]) * $this->user["Physique"] / 10) + rand($this->user["Physique"]/100, $this->user["Physique"]/75), 0);
			$mastery = round((rand($chars["masteryMin"], $chars["masteryMax"]) * $this->user["Mastery"] / 10) + rand($this->user["Mastery"]/100, $this->user["Mastery"]/75), 0);
			$sr["strengh"] = $strengh;
			$sr["defence"] = $defence;
			$sr["agility"] = $agility;
			$sr["physique"] = $physique;
			$sr["mastery"] = $mastery;
			$_SESSION["bot_strengh"] = $strengh;
			$_SESSION["bot_defence"] = $defence;
			$_SESSION["bot_agility"] = $agility;
			$_SESSION["bot_physique"] = $physique;
			$_SESSION["bot_mastery"] = $mastery;
			$sr["power"] =  round($strengh * 2 + $defence * 1.7 + $agility * 1.6 + $physique * 1.85 + $mastery * 1.9 , 0);
			$massiv = array ($strengh, $defence, $agility, $physique, $mastery);
			$sr["percentStrengh"] = $this->getImage($massiv,0);
			$sr["percentDefence"] = $this->getImage($massiv,1);
			$sr["percentAgility"] = $this->getImage($massiv,2);
			$sr["percentPhysique"] = $this->getImage($massiv,3);
			$sr["percentMastery"] = $this->getImage($massiv,4);
			$text .= $this->getReplaceTemplate($sr, "characteristics");
			return $text;
		}
		else{
			$sr["strengh"] = $_SESSION["bot_strengh"];
			$sr["defence"] = $_SESSION["bot_defence"];
			$sr["agility"] = $_SESSION["bot_agility"];
			$sr["physique"] = $_SESSION["bot_physique"];
			$sr["mastery"] = $_SESSION["bot_mastery"];
			$sr["power"] =  round($sr["strengh"] * 2 + $sr["defence"] * 1.7 + $sr["agility"] * 1.6 + $sr["physique"] * 1.85 + $sr["mastery"] * 1.9 , 0);
			$massiv = array ($sr["strengh"], $sr["defence"], $sr["agility"], $sr["physique"], $sr["mastery"]);
			$sr["percentStrengh"] = $this->getImage($massiv,0);
			$sr["percentDefence"] = $this->getImage($massiv,1);
			$sr["percentAgility"] = $this->getImage($massiv,2);
			$sr["percentPhysique"] = $this->getImage($massiv,3);
			$sr["percentMastery"] = $this->getImage($massiv,4);
			$text .= $this->getReplaceTemplate($sr, "characteristics");
			return $text;
		}
		
	}
	
	private function getCharacteristics(){
		//$chars = array("strenghMin" => 4, "strenghMax" => 9 , "lose" => 0 , "defenceMin" => 6, "agilityMin" => 7, "agilityMax" => 11, "physiqueMin" => 3, "physiqueMax" => 6, "masteryMin" => 6, "masteryMax" => 8);
		//$this->db->setFieldOnID("arena_bots", 3, "characteristics", serialize($chars));
		if(!$this->enemy){
			return "Никого не нашлось";
		}
		$sr["strengh"] = $this->enemy["Strengh"];
		$sr["defence"] = $this->enemy["Defence"];
		$sr["agility"] = $this->enemy["Agility"];
		$sr["physique"] = $this->enemy["Physique"];
		$sr["mastery"] = $this->enemy["Mastery"];
		
		$massiv = array ($this->enemy["Strengh"], $this->enemy["Defence"], $this->enemy["Agility"], $this->enemy["Physique"], $this->enemy["Mastery"]);
		$sr["percentStrengh"] = $this->getImage($massiv,0);
		$sr["percentDefence"] = $this->getImage($massiv,1);
		$sr["percentAgility"] = $this->getImage($massiv,2);
		$sr["percentPhysique"] = $this->getImage($massiv,3);
		$sr["percentMastery"] = $this->getImage($massiv,4);
		$sr["power"] = $this->enemy["power"];
		$text .= $this->getReplaceTemplate($sr, "characteristics");
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