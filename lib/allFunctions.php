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
				if($totalPrice != 0) $this->db->setField("user_resources", "Gold", $this->user["Gold"] - $totalPrice, "id", $user["id"]);
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