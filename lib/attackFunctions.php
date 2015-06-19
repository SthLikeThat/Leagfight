<?php
require_once "checkvalid_class.php";
require_once "database_class.php";

class attackFunctions extends DataBase{
	
	public function __construct() {
		parent::__construct();
		$this->db = $this;
	}

    private function getBot($id){
        $botInfo = $this->db->getAllOnField("arena_bots", "id", $id, "", "");

        $equipment = unserialize($botInfo["equipment"]);
        $botInfo["Strengh"] = $_SESSION["bot_strengh"];
        $botInfo["Defence"] = $_SESSION["bot_defence"];
        $botInfo["Agility"] = $_SESSION["bot_agility"];
        $botInfo["Physique"] = $_SESSION["bot_physique"];
        $botInfo["Mastery"] = $_SESSION["bot_mastery"];
        $botInfo["power"] =  round($botInfo["Strengh"] * 2 + $botInfo["Defence"] * 1.7 + $botInfo["Agility"] * 1.6 + $botInfo["Physique"] * 1.85 + $botInfo["Mastery"] * 1.9, 0);
        $botInfo["lvl"] = $_SESSION["botLvl"];
        $botInfo["id"] = $id;
        if(rand(0,1) and $equipment["primaryWeapon"])	$botInfo["primaryWeapon"]["id"] = $equipment["primaryWeapon"];
        else $botInfo["primaryWeapon"] = 0;
        if(rand(0,1) and $equipment["secondaryWeapon"] and $botInfo["primaryWeapon"]["id"])	$botInfo["secondaryWeapon"]["id"] = $equipment["secondaryWeapon"];
        else $botInfo["secondaryWeapon"] = 0;
        if(rand(0,1) and $equipment["armor"])	$botInfo["armor"]["id"] = $equipment["armor"];
        else $botInfo["armor"] = 0;
        if(rand(0,1) and $equipment["helmet"])	$botInfo["helmet"]["id"] = $equipment["helmet"];
        else $botInfo["helmet"] = 0;
        if(rand(0,1) and $equipment["leggings"])	$botInfo["leggings"]["id"] = $equipment["leggings"];
        else $botInfo["leggings"] = 0;
        if(rand(0,1) and $equipment["bracers"])	$botInfo["bracers"]["id"] = $equipment["bracers"];
        else $botInfo["bracers"] = 0;

        $allArmors = $this->db->ancillary->getAllArmors($botInfo);
        $userEquip = $allArmors[1];
        $allArmors = $allArmors[0];

        $count = count($allArmors);
        for($i = 0; $i <= $count; $i++) {
            if ($allArmors[$i]["thing"] == 2) {
                $botInfo["armor"] = $allArmors[$i];
                $botInfo["armor"]["armorLvl"] = 0;
            }
            if ($allArmors[$i]["thing"] == 3){
                $botInfo["helmet"] = $allArmors[$i];
                $botInfo["helmet"]["armorLvl"] = 0;
            }
            if($allArmors[$i]["thing"] == 4) {
                $botInfo["leggings"] = $allArmors[$i];
                $botInfo["leggings"]["armorLvl"] = 0;
            }
            if($allArmors[$i]["thing"] == 5) {
                $botInfo["bracers"] = $allArmors[$i];
                $botInfo["bracers"]["armorLvl"] = 0;
            }
            if($allArmors[$i]["thing"] == 6) {
                $botInfo["secondaryWeapon"] = $allArmors[$i];
                $botInfo["secondaryWeapon"]["armorLvl"] = 0;
            }
        }
        $botTotalArmor = 0;
        $botArmorTypes = array(0,0,0);
        if(count($userEquip)) {
            foreach ($userEquip as $key => $value) {
                $botTotalArmor += $botInfo[$value]["defence"];
                $botArmorTypes[$botInfo[$value]["typeDefence"]] = 1;
                $botInfo['Strengh'] += $botInfo[$value]["bonusstr"];
                $botInfo['Defence'] += $botInfo[$value]["bonusdef"];
                $botInfo['Agility'] += $botInfo[$value]["bonusag"];
                $botInfo['Physique'] += $botInfo[$value]["bonusph"];
                $botInfo['Mastery'] += $botInfo[$value]["bonusms"];
            }
        }
        if($botInfo["primaryWeapon"] != 0){
            $botInfo["primaryWeapon"] = $this->db->getAllOnField("weapon", "id", $botInfo["primaryWeapon"]["id"], "", "");
        }
        if($botInfo["secondaryWeapon"] != 0 and $botInfo["secondaryWeapon"]["id"] < 500){
            $botInfo["secondaryWeapon"] = $this->db->getAllOnField("weapon", "id",$botInfo["secondaryWeapon"]["id"] , "", "");
        }
        unset($_SESSION["bot_strengh"]);
        unset($_SESSION["bot_defence"]);
        unset($_SESSION["bot_agility"]);
        unset($_SESSION["bot_physique"]);
        unset($_SESSION["bot_mastery"]);
        unset($_SESSION["botId"]);
        $user["user"] = $botInfo;
        $user["totalArmor"] = $botTotalArmor;
        $user["armorTypes"] = $botArmorTypes;
        return $user;
    }
	
	public function attack($type, $avatar, $id){
		if($type == "arenaUser"){
			if(!$this->valid->validID($id) or !$this->db->existsID("users", $id))
				echo "Location:?view=arena";
			$defender = $this->db->ancillary->getInfo($id);
		}
        if($type == "arenaBot"){
            $defender = $this->db->getAllOnField("arena_bots", "id", $_SESSION["botId"], "", "");
        }
        session_start();
		$agressor =  $this->db->ancillary->getInfo($_SESSION["id"]);
		$idUser = $agressor["user"]["id"];

		$time = time();
		if($type == "arenaUser"){
			if($agressor["timerAttack"] < $time and $defender["currentHp"] > 50 and $agressor["lvl"] - $defender["lvl"] <= 3 and $defender["lvl"] - $agressor["lvl"] <= 3){
				if($agressor["league"] != 0){
					if($agressor["league"] == $defender["league"]){
						echo "friend";
						exit;
					}
				}
			}
		}
		if($type == "arenaBot"){
            $defender = $this->getBot($_SESSION["botId"]);
		}
			
			//Высчитываем урон
			$allAgrDamage = array();
			$allDefDamage = array();
			$agrDamageBlocked = array();
			$defDamageBlocked = array();
			for($i = 0; $i < 5; $i++){
				$agressorDmg = $this->getDamage($agressor, $defender);
				$defenderDmg = $this->getDamage($defender, $agressor);
				$allAgrDamage[$i] = $agressorDmg[0];
				$allDefDamage[$i] = $defenderDmg[0];
				$typesAgrDamage[$i] = $agressorDmg[1];
				$typesDefDamage[$i] = $defenderDmg[1];
				if($agressorDmg[1] == "shield")
					$defDamageBlocked[$i] = $agressorDmg[2];
				if($defenderDmg[1] == "shield")
					$agrDamageBlocked[$i] = $defenderDmg[2];
			}
			$totalAgrDamage = 0;
			$totalDefDamage = 0;
			for($i = 0; $i <= count($allAgrDamage); $i++)
				$totalAgrDamage += $allAgrDamage[$i];
			for($i = 0; $i <= count($allDefDamage); $i++)
				$totalDefDamage += $allDefDamage[$i];
			
			//Добавляем конечные результаты в таблицу
			$agrStatistic = $this->db->getAllOnField("user_statistic", "id", $agressor["user"]["id"], "", "");
			if($type == "arenaUser")
				$defStatistic = $this->db->getAllOnField("user_statistic", "id", $defender["user"]["id"], "", "");
			$this->mysqli->autocommit(FALSE);
			if($totalAgrDamage > $totalDefDamage){
				$winner = $agressor["user"]["id"];
                $Iwin = true;
				$prize = $this->getPrize($agressor["user"], $defender["user"], $type, $agrStatistic, $defStatistic, $Iwin, $defender["user"]);
			}
			if($totalAgrDamage < $totalDefDamage){
				$winner = $defender["user"]["id"];
                $Iwin = false;
				$prize = $this->getPrize($defender["user"], $agressor["user"], $type, $defStatistic, $agrStatistic, $Iwin, $defender["user"]);
			}
			if($totalAgrDamage == $totalDefDamage){
				$winner = 0;
                $Iwin = 0;
				$prize = array("gold"=>0);
			}
			$agressorHp = $agressor["user"]["currentHp"] - $totalDefDamage;
			$defenderHp = $defender["user"]["currentHp"] - $totalAgrDamage;
			if($agressorHp <=0) $agressorHp = 1;
			if($defenderHp <=0) $defenderHp = 1;
			
			
			//ХП
			$this->db->setField("users", "currentHp", $agressorHp, "id", $agressor["user"]["id"]);
			if($type == "arenaUser")
				$this->db->setField("users", "currentHp", $defenderHp, "id", $defender["user"]["id"]);
			
			//Статистика
            if($type == "arenaUser")
                $this->setUserStatistic($agressor["user"]["id"], $defender["user"]["id"], $agrStatistic, $defStatistic, $winner,
                    $prize, $totalAgrDamage, $totalDefDamage, $typesAgrDamage, $typesDefDamage, $allAgrDamage, $allDefDamage, $agrDamageBlocked, $defDamageBlocked);
            if($type == "arenaBot")
                $this->setBotStatistic($defender, $Iwin, $agrStatistic, $prize, $agressor["user"]["id"]);
			$prize = serialize($prize);

			$allAgrDamage = serialize($allAgrDamage);
			$allDefDamage = serialize($allDefDamage);
			$typesAgrDamage = serialize($typesAgrDamage);
			$typesDefDamage = serialize($typesDefDamage);
            $agressorInfo = $agressor["user"];
            $defenderInfo= $defender["user"];
            $agressor = serialize($agressor);
            $defender = serialize($defender);

        $table_name = $this->config->db_prefix."logs";
			$query = "INSERT INTO `$table_name` (idAgressor, idDefender,  typeFight, Time, winner, prize, allAgrDamage,
            allDefDamage, typesAgrDamage, typesDefDamage,avatar, agressor, defender)

			VALUES ($idUser, $id, '$type', $time, $winner, '$prize', '$allAgrDamage', '$allDefDamage',
			'$typesAgrDamage', '$typesDefDamage','$avatar', '$agressor', '$defender')";
			$log_result = $this->query($query);
			
			if($log_result){
				//Таймер
				if($type == "arenaUser")
					$this->db->setField("users", "timerAttack", $time, "id", $agressorInfo["id"]);		//ЗДЕСЬ ДОБАВИТЬ + 900
					
				$log = $this->db->getAllOnField("logs", "Time", $time, "Time", "");
				$idLog = $log["idLog"];
				
				$date = date("Y").date("m").date("d").date("H").date("i").date("s");
				$time = date("H").":".date("i").":".date("s");
				$beautifulDate = date("d").".".date("m").".".date("y");
				$defenderNick = $defenderInfo["login"];
				$agressorNick = $agressorInfo["login"];
				
				//Почта
				$mass = array("idDialog" =>$idLog, "idUser"=>$agressorInfo["id"], "idSender" => 0, "textMessage"=>"<a href='?view=fightLog&id=$idLog'>Отчет о сражении.</a>",
				"time"=>$time, "date"=>$date, "beautifulDate"=>$beautifulDate, "type"=>2, "title"=>"Вы напали на $defenderNick", "status" => 1, "extra" => serialize(array("winner" => $winner, "resources" => $prize)));
				$this->db->insert("mail", $mass);
				if($type == "arenaUser"){
					$mass = array("idDialog" =>$idLog, "idUser"=>$defenderInfo["id"], "idSender" => 0, "textMessage"=>"<a href='?view=fightLog&id=$idLog'>Отчет о сражении.</a>",
					"time"=>$time, "date"=>$date, "beautifulDate"=>$beautifulDate, "type"=>2, "title"=>"На вас напал $agressorNick", "status" => 1, "extra" => serialize(array("winner" => $winner, "resources" => $prize)));
					$this->db->insert("mail", $mass);
				}
				$this->mysqli->commit();
			}
			die($idLog);
	}

    private function setUserStatistic($agressor, $defender, $agrStatistic, $defStatistic, $winner, $prize, $totalAgrDamage,
                                      $totalDefDamage, $typesAgrDamage, $typesDefDamage, $allAgrDamage, $allDefDamage, $agrDamageBlocked, $defDamageBlocked){
        $agrUserStatistic = unserialize($agrStatistic["userStatistic"]);
        $agrDamageStatistic = unserialize($agrStatistic["damageStatistic"]);
        $defUserStatistic = unserialize($defStatistic["userStatistic"]);
        $defDamageStatistic = unserialize($defStatistic["damageStatistic"]);
        $agrDamageStatistic["damageDealt"] += $totalAgrDamage;
        $agrDamageStatistic["damageReceived"] += $totalDefDamage;
        $defDamageStatistic["damageDealt"] += $totalDefDamage;
        $defDamageStatistic["damageReceived"] += $totalAgrDamage;
        for($i = 0; $i < 5; $i++){
            if($typesAgrDamage[$i] == "damage" or $typesAgrDamage[$i] == "crit"){
                $agrDamageStatistic["hits"]++;
            }
            if($typesAgrDamage[$i] == "crit"){
                $agrDamageStatistic["crits"]++;
                $agrDamageStatistic["critDamage"] += $allAgrDamage[$i];
            }
            if($typesAgrDamage[$i] == "Dodge"){
                $agrDamageStatistic["misses"]++;
                $defDamageStatistic["dodges"]++;
            }
            if($typesAgrDamage[$i] == "SecondHit")
                $agrDamageStatistic["secondHits"]++;
            if($typesAgrDamage[$i] == "shield"){
                $defDamageStatistic["damageBlocked"] += $defDamageBlocked[$i];
                $defDamageStatistic["Blocks"]++;
            }

            if($typesDefDamage[$i] == "damage" or $typesDefDamage[$i] == "crit"){
                $defDamageStatistic["hits"]++;
            }
            if($typesDefDamage[$i] == "crit"){
                $defDamageStatistic["crits"]++;
                $defDamageStatistic["critDamage"] += $allDefDamage[$i];
            }
            if($typesDefDamage[$i] == "Dodge"){
                $defDamageStatistic["misses"]++;
                $agrDamageStatistic["dodges"]++;
            }
            if($typesDefDamage[$i] == "SecondHit")
                $defDamageStatistic["secondHits"]++;
            if($typesDefDamage[$i] == "shield"){
                $agrDamageStatistic["damageBlocked"] += $agrDamageBlocked[$i];
                $agrDamageStatistic["Blocks"]++;
            }
        }

        if($winner == $agressor){
            $agrUserStatistic["wins"]++;
            $agrUserStatistic["goldStolen"] += $prize["coinBlack"];
            $defUserStatistic["lose"]++;
            $defUserStatistic["goldLost"] += $prize["coinBlack"];
        }
        if($winner == $defender){
            $agrUserStatistic["lose"]++;
            $agrUserStatistic["goldLost"] += $prize["coinBlack"];
            $defUserStatistic["wins"]++;
            $defUserStatistic["goldStolen"] += $prize["coinBlack"];
        }
        if($winner == 0){
            $agrUserStatistic["draw"]++;
            $defUserStatistic["draw"]++;
        }
        $this->updateStatistic($agrUserStatistic, $agrDamageStatistic, $agressor);
        $this->updateStatistic($defUserStatistic, $defDamageStatistic, $defender);
    }

    private function setBotStatistic($bot, $winner, $statistic, $prize, $agressor){
        $allBots = unserialize($statistic["allBots"]);
        $botStatistic = unserialize($statistic["arenaBot".$bot["user"]["lvl"]]);
        for($i = 0; $i <= count($botStatistic); $i++){
            if($botStatistic[$i]["id"] == $bot["user"]["id"]){
                $thisBotStatistic = $botStatistic[$i];
                break;
            }
        }
        if($winner){
            $_SESSION["goNextLvl"] = 1;
            $thisBotStatistic["wins"]++;
            $allBots["wins"]++;
            if($prize["coinBlack"] != 0){
                $allBots["stoleGold"] += $prize["coinBlack"];
                $thisBotStatistic["stoleGold"] += $prize["coinBlack"];
            }
        }
        if($winner == 0){
            $thisBotStatistic["draw"]++;
        }
        if(!$winner){
            $thisBotStatistic["lose"]++;
            $allBots["lose"]++;
            if($prize["coinBlack"] != 0){
                $allBots["lostGold"] += $prize["coinBlack"];
                $thisBotStatistic["lost"] += $prize["coinBlack"];
            }
        }
        $botStatistic[$i] = $thisBotStatistic;
        $this->db->update("user_statistic", array("arenaBot".$_SESSION["botLvl"] => serialize($botStatistic), "allBots" =>  serialize($allBots)), "`id` = '".$agressor."'");
    }

	private function getPrize($winner, $loser, $type, $winnerStatistic, $loserStatistic, $Iwin, $bot){
		if($type == "arenaUser"){
            $loserResources = $this->db->getAllOnField("user_resources", "id", $loser["id"], "", "");
            $winnerResources = $this->db->getAllOnField("user_resources", "id", $winner["id"], "", "");
			$loserHouse = $this->db->getAllOnField("user_house", "id", $loser["id"], "", "");
			$safeGold = $loserHouse["safe"] * 5;
			
			//грабеж
			$gold = round(($loserResources["Gold"] - $loserResources["Gold"]* $safeGold / 100)/10,0);
				$this->db->setField("user_resources", "Gold", $loserResources["Gold"] - $gold, "id", $loser["id"]);
				$this->db->setField("user_statistic", "goldLost", $loserStatistic["goldLost"] + $gold, "id", $loser["id"]);
				$this->db->setField("user_resources", "Gold", $winnerResources["Gold"] + $gold, "id", $winner["id"]);
				$this->db->setField("user_statistic", "goldStolen", $winnerStatistic["goldStolen"] + $gold, "id", $winner["id"]);
				
				//опыт
				if($winner["lvl"] >= $loser["lvl"]) $exp = 1;
				if($loser["lvl"] - $winner["lvl"] == 1 or $loser["lvl"] - $winner["lvl"] == 2) $exp = 2;
				if($loser["lvl"] - $winner["lvl"] == 3 ) $exp = 3;
				$this->db->setField("users", "currentExp", $winner["currentExp"] + $exp, "id", $winner["id"]);
			
			$this->checkUpLvl($winner["id"]);
			$resources = array("coinBlack"=>$gold, "expBlack" => $exp);
		}
		if($type == "arenaBot"){
			$botResources = unserialize($bot["prize"]);
            $userResources = $this->db->getAllOnField("user_resources", "id", $winner["id"], "", "");
			if($Iwin){
				$exp = $botResources["exp"];
				if($exp != 0)
					$this->db->setField("users", "currentExp", $winner["currentExp"] + $exp, "id", $winner["id"]);
				$this->checkUpLvl($winner["id"]);
				$gold = round(rand(75,125) * $botResources["Gold"] /100, 0);
				$another = round(rand(75,125) * $botResources["Another"] /100, 0);
				$tournament_icon = $botResources["tournament_icon"];
				$values = "";
				if($gold != 0){
					$goldWin = $gold + $userResources["Gold"];
					$values .= "`Gold` = '$goldWin',";
				}
				if($another != 0){
					$anotherWin = $another + $userResources["Another"];
					$values .= " `Another` = '$anotherWin',";
				}
				if($tournament_icon != 0){
					$tournament_icon_win = $tournament_icon + $userResources["tournament_icon"];
					$values .= " `tournament_icon` = '$tournament_icon_win'";
				}
				$table_name = $this->config->db_prefix."user_resources";
				$query = "UPDATE $table_name SET $values WHERE id=".$winner["id"];
				$this->query($query);
				$resources = array("coinBlack"=>$gold, "diamond"=>$another, "tournament_icon"=>$tournament_icon, "expBlack"=>$exp);
			}
			if(!$Iwin){
				$gold = round($userResources["Gold"]/20, 0);
				$this->db->setField("user_resources", "Gold", $userResources["Gold"] - $gold, "id", $winner["id"]);
				$resources = array("coinBlack"=>$gold);
			}
		}
		return $resources;
	}
	
	public function getDamage($agressor, $defender){
        $armorTypes = $defender["armorTypes"];
        $Weapon  = $agressor["user"]["primaryWeapon"];
        $secWeapon =  $agressor["user"]["secondaryWeapon"];
        $second = false;
        if(!is_array($Weapon)){
            $Weapon = array();
            $Weapon["damage"] = 0.1;
            $Weapon["crit"] = 0.2;
        }

        //Расчет бонуса от оружия против брони
        $DamageBonus = $this->db->ancillary->getDamageBonus($Weapon["typedamage"], $armorTypes);
        $secDamageBonus = $this->db->ancillary->getDamageBonus($secWeapon["typedamage"], $armorTypes);

        //Проверка на уворот
        $dodgeChance =  $agressor["user"]["Strengh"] /($agressor["user"]["Strengh"] + $defender["user"]["Agility"]) * 100;
        $dodgeRand = rand(0, 100);
        if( $dodgeRand < $dodgeChance){

            //Проверка на крит
            if($defender["user"]["Mastery"] > 15)	$critChance = ($agressor["user"]["Mastery"] + $defender["user"]["Mastery"])/($defender["user"]["Mastery"]/15);
            else $critChance = 100;
            $critRand = rand(0, 100);
            if( $critRand <= $critChance){
                $DamageBonus += $Weapon["crit"] - 1;
                $typeHit = "crit";
            }
            else $typeHit = "damage";

            //Расчет наносимого урона
            $damageRand = rand(75, 125);
            $damageRand = $damageRand / 100;
            $Damage = $agressor["user"]["Strengh"] * $damageRand * $DamageBonus * $Weapon["damage"];
            $defenceRand = rand(50, 100);
            $defenceRand = $defenceRand / 100;
            $TakenDamage = $defender["user"]["Defence"] * $defenceRand * $defender["totalArmor"];

            $Dmg = $Damage - $TakenDamage;
            if($Dmg < 0){
                $Dmg = 0;
                $typeHit = "armor";
            }
        }
        else{
            $Dmg = 0;
            $typeHit = "Dodge";
        }

        //Вычисляем второй удар
        if($secWeapon["type"] == 1 and $second == false){
            $secondHitChance = ($agressor["user"]["Strengh"]/2 + $agressor["user"]["Mastery"])/($defender["user"]["Mastery"]/10);
            $secondRand = rand(0, 100);
            if($secondHitChance >= $secondRand){
                $second = true;
                if(rand(0,100) <= $dodgeChance){
                    if(rand(0,100) <= $critChance) $secDamageBonus += $secWeapon["crit"] - 1;
                    $newDamage = $agressor["user"]["Strengh"] * $damageRand * $secDamageBonus * $secWeapon["damage"];
                }
                $Dmg += $newDamage;
                $typeHit = "SecondHit";
            }
        }
        //Шанс на блок щитом
        if($defender["user"]["secondaryWeapon"]["thing"] == 6){
            $blockChance = (($agressor["user"]["Strengh"] + $agressor["user"]["Mastery"])/($defender["user"]["Defence"] + $defender["user"]["Mastery"]) * 10);
            if(rand(0,100) <= $blockChance){
                $blockDamage = $defender["user"]["Defence"] * $defenceRand * $defender["user"]["secondaryWeapon"]["defence"];
                $Dmg -= $blockDamage;
                $typeHit = "shield";
            }
        }
         if ($Dmg < 0)
             $Dmg = 0;
        return array(round($Dmg,0),$typeHit, $blockDamage);
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
	
	private function updateStatistic($userStatistic, $damageStatistic, $id){
		$this->db->update("user_statistic", array("userStatistic" => serialize($userStatistic), "damageStatistic" =>  serialize($damageStatistic)), "`id` = '".$id."'");
	}
}

$attackFunctions = new attackFunctions();
if($_REQUEST["WhatIMustDo"] == "attackUser")		$attackFunctions->attack($_REQUEST["type"], $_REQUEST["avatar"], $_REQUEST["id"]);
?>