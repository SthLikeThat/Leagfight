<?php
require_once "database_class.php";
require_once "global_class.php";

class massBattleFunctions extends DataBase{
	
	public function __construct() {
		parent::__construct();
		$this->db = $this;
		session_start();
		$this->user = $this->db->getAllOnField("users", "id", $_SESSION["id"], "", "");
		$this->inventory = $this->db->getAllOnField("user_inventory", "id", $this->user["id"], "", "");
	}
	
	
	private function getTypeWeapon(){
		for( $i = 1; $i <= count($this->inventory); $i++ ){
			if($this->inventory["slot$i"] == "999")
				break;
			if($this->inventory["slot$i"] != "0"){
				$slot = unserialize($this->inventory["slot$i"]);
				if($slot["hash"] == $this->user["primaryWeapon"]){
					$weapon = $this->db->getElementOnID("weapon", $slot["id"]);
					$primary = $weapon["type"];
				}
				if($slot["hash"] == $this->user["secondaryWeapon"]){
					if($slot["id"] < 500)
						$secondary = "1";
					else
						$secondary = "6";
				}
			}
		}
		return array($primary, $secondary);
	}
	
	public function newBattle(){
		if($this->user["typeJob"] == 0){
			$table_name = $this->config->db_prefix."mass_battle";
			$mass = serialize(array(array("id" => $this->user["id"], "login" => $this->user["login"], "power" => $this->user["power"], "move" => 0, "hit" => 0)));
			$white = 0;
			$black = 0;
			$grey = 0;
			if($this->user["league"] == 0){
				$grey = $mass;
				$league = "grey";
			}
			if($this->user["league"] == 1){
				$black = $mass;
				$league = "black";
			}
			if($this->user["league"] == 2){
				$white = $mass;
				$league = "white";
			}
			$time = time();
			$time_end = $time + 30 * 60;
			for($i = 1; $i <= 10; $i++){
				for($j = 1; $j <= 20; $j++){
					$rand = rand(1, 20);
					$type = "";
					if($rand == 1 and $j > 2 and $j < 19)
						$type = "forest";
					if($rand == 2 and $j > 2 and $j < 19)
						$type = "rock";
					elseif( $type == "")
						$type = "field";
					${"line_$i"}[$j] = array("type" => $type, "user" => 0);
				}
				if($this->user["league"] == "1"  and $i == 1)
					${"line_$i"}[1] = array("type" => $type, "user" => $this->user["login"]);
				if($this->user["league"] == "2" and $i == 1)
					${"line_$i"}[20] = array("type" => $type, "user" => $this->user["login"]);
				${"line_$i"} = serialize( ${"line_$i"} );
			}
			$weapons = $this->getTypeWeapon();
			if($weapons[0] == 1){
				$attack = 1;
				$course = 2;
				$type = "one";
			}
			if($weapons[0] == 1 and $weapons[1] == 6){
				$course = 1;
				$type = "shield";
			}
			if($weapons[0] == 1 and $weapons[1] == 1){
				$type = "SecondHit";
			}
			if($weapons[0] == 2){
				$attack = 1;
				$course = 2;
				$type = "two";
			}
			if($weapons[0] == 3){
				$attack = 2;
				$course = 1;
				$type = "staff";
			}
			$alive = serialize(array(array("login" => $this->user["login"], "maxHp" => $this->user["power"], "currentHp" => $this->user["power"], "course" => $course, "attack" => $attack, "type" => $type)));
			$information = serialize(array("turn" => $league, "endRound" => time() + 30));
			$query = "INSERT INTO {$table_name} (timeUpdate, information, white, black, grey, time, time_end, line_1, line_2, line_3, line_4, line_5, line_6, line_7, line_8, line_9, line_10, alive) 
			VALUES (".time().", '{$information}', '{$white}', '{$black}', '{$grey}', '{$time}', '{$time_end}', '{$line_1}', '{$line_2}', '{$line_3}', '{$line_4}', '{$line_5}', '{$line_6}', '{$line_7}', '{$line_8}', '{$line_9}', '{$line_10}', '{$alive}')";
			$this->query($query);
			$id = $this->db->getLastID("mass_battle");
			echo $id["id"];
			$this->db->setField("users", "typeJob", "battle_".$id["id"], "id", $this->user["id"]);
			$this->db->setField("users", "jobEnd", $time_end, "id", $this->user["id"]);
		}
	}
	
	public function connectBattle($id){	
		if(!$this->valid->validID($id)) exit;
		$battle = $this->db->getFieldsBetter( "mass_battle", "id", $id, array( "white", "grey", "black", "time_end", "alive"), "=");
		$battle = $battle[0];
		if($this->user["league"] == 0){
			$league = $battle["grey"];
			$name = "grey";
		}
		if($this->user["league"] == 1){
			$league = $battle["black"];
			$name = "black";
		}
		if($this->user["league"] == 2){
			$league = $battle["white"];
			$name = "white";
		}
		if($league != "0"){
			$league = unserialize($league);
			$league[] = array("id" => $this->user["id"], "login" => $this->user["login"], "power" => $this->user["power"]);
		}
		else $league = array(array("id" => $this->user["id"], "login" => $this->user["login"], "power" => $this->user["power"], "move" => 0, "hit" => 0));
		
		$weapons = $this->getTypeWeapon();
		$type = "";
		if($weapons[0] == 1){
			$attack = 1;
			$course = 2;
			$type = "one";
		}
		if($weapons[0] == 1 and $weapons[1] == 6){
			$course = 1;
			$type = "shield";
		}
		if($weapons[0] == 1 and $weapons[1] == 1){
			$type = "SecondHit";
		}
		if($weapons[0] == 2){
			$attack = 1;
			$course = 2;
			$type = "two";
		}
		if($weapons[0] == 3){
			$attack = 2;
			$course = 1;
			$type = "staff";
		}
		elseif($type == ""){
			$attack = 1;
			$course = 3;
			$type = "fist";
		}
		$alive = unserialize($battle["alive"]);
		$alive[] = array("login" => $this->user["login"], "maxHp" => $this->user["power"], "currentHp" => $this->user["power"], "course" => $course, "attack" => $attack, "type" => $type);
		
		$league = serialize($league);
		$this->db->setFieldOnID("mass_battle", $id, $name, $league);
		$this->db->update("mass_battle", array($name => $league, "alive" => serialize($alive)), "id = '".$id."'");
		$this->db->update("users", array("typeJob" => "battle_".$id, "jobEnd" => $battle["time_end"]), "id = '".$this->user["id"]."'");
		echo $id;
	}
	
	public function spawnBattle($row, $column, $id){
		$battle = $this->db->getFieldsBetter( "mass_battle", "id", $id, array( "line_$column"), "=");
		$mda = $this->db->getElementOnID("mass_battle", $id);
		$line = unserialize($battle[0]["line_$column"]);
		if($line[$row]["user"] == 0)
			$line[$row]["user"] = $this->user["login"];
		$this->db->setFieldOnID("mass_battle", $id, "line_$column", serialize($line));
	}
	
	public function goCoordinates($coordinates, $rowStand, $columnStand, $id){
		$rowGo = substr($coordinates,  0, stripos($coordinates, "_"));
		if($this->user["league"] == 0) $league = "grey";
		if($this->user["league"] == 1) $league = "black";
		if($this->user["league"] == 2) $league = "white";
		$battle = $this->db->getFieldsBetter( "mass_battle", "id", $id, array( "information", $league, "line_$rowGo", "line_$rowStand", "alive"), "=");
		$battle = $battle[0];
		
		$information = unserialize($battle["information"]);
		if($league != $information["turn"])
			die("Не твоя очередь!");
		$userLeag = unserialize($battle[$league]);
		for($i = 0; $i < count($userLeag); $i++){
			if($userLeag[$i]["id"] == $this->user["id"]){
				$userLeag[$i]["move"] = 1;
			}
		}
		if($rowStand != $rowGo){
			$STAND =  unserialize($battle["line_$rowStand"]);
			$GO =  unserialize($battle["line_$rowGo"]);
		}
		else{
			$STAND =  unserialize($battle["line_$rowStand"]);
			$GO =&  $STAND;
		}
		$columnGo = substr($coordinates, stripos($coordinates, "_") + 1);
		if($GO[$columnGo]["user"] == 0 and $GO[$columnGo]["type"] == "field"){
			$GO[$columnGo]["user"] = $this->user["login"];
			$STAND[$columnStand]["user"] = 0;
		}
		$alive = unserialize($this->battle["alive"]);
		for($i = 0; $i < count($alive); $i++){
			if($alive[$i]["login"] == $this->user["login"]){
				if($rowGo - $rowStand < $alive[$i]["course"] and $rowStand - $rowGo < $alive[$i]["course"])
					return false;
				if($columnStand - $columnGo < $alive[$i]["course"] and $columnGo - $columnStand < $alive[$i]["course"])
					return false;
			}
		}
		if($rowStand != $rowGo)
			$this->db->update("mass_battle", array("timeUpdate" => time(), $league => serialize($userLeag), "line_$rowGo" => serialize($GO), "line_$rowStand" => serialize($STAND)), "id = '".$id."'");
		else
			$this->db->update("mass_battle", array("timeUpdate" => time(), $league => serialize($userLeag), "line_$rowGo" => serialize($GO)), "id = '".$id."'");
		die("OK");
	}
	
	public function updateBattle($id){
		if(!$this->valid->validID($id) or !$this->db->existsID("mass_battle", $id))
			return false;
		$battle = $this->db->getAllOnField("mass_battle", "id", $id, "", "");
		$information = unserialize($battle["information"]);
		$league = 0;
		if($information["endRound"] < time()){
			$information["endRound"] += 30;
			if($information["turn"] == "white"){
				$information["turn"] = "black";
				$league = unserialize($battle["black"]);
				$turnLeague = "black";
			}
			if($information["turn"] == "black" and $league === 0){
				$information["turn"] = "grey";
				$league = unserialize($battle["grey"]);
				$turnLeague = "grey";
			}
			if($information["turn"] == "grey" and $league === 0){
				$information["turn"] = "white";
				$league = unserialize($battle["white"]);
				$turnLeague = "white";
			}
		}
		else return false;
		if($league != 0){
			foreach($league as $user => $values){
				$league[$user]["move"] = 0;
				$league[$user]["hit"] = 0;
			}
		}
		$this->db->update("mass_battle", array( "timeUpdate" => time(), $turnLeague => serialize($league), "information" => serialize($information)), "id = '".$id."'");
		
	}
	
	public function attackCoor($coordinates, $rowStand, $columnStand, $id){
		if(!$this->valid->validID($id) or !$this->db->existsID("mass_battle", $id))
			return false;
		$battle = $this->db->getAllOnField("mass_battle", "id", $id, "", "");
		$columnAttack = substr($coordinates, stripos($coordinates, "_") + 1);
		$rowAttack = substr($coordinates, 0, stripos($coordinates, "_"));
		$alive = unserialize($battle["alive"]);
		foreach ($alive as $user){
			if($user["login"] == $this->user["login"])
				break;
		}
		if($rowAttack - $rowStand > $user["attack"] and $rowStand - $rowAttack < $user["attack"])
			return false;
		if($columnAttack - $columnStand > $user["attack"] and $columnStand - $columnAttack < $user["attack"])
			return false;
		echo "Hey";
	}
}

$battleFunctions = new massBattleFunctions();
if($_REQUEST["WhatIMustDo"] === "newBattle")		$battleFunctions->newBattle();
if($_REQUEST["WhatIMustDo"] === "updateBattle")		$battleFunctions->updateBattle($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "connectBattle")		$battleFunctions->connectBattle($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "spawnBattle")		$battleFunctions->spawnBattle($_REQUEST["row"], $_REQUEST["column"], $_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "goCoordinates")		$battleFunctions->goCoordinates($_REQUEST["coordinates"], $_REQUEST["rowStand"], $_REQUEST["columnStand"], $_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "attackCoor")		$battleFunctions->attackCoor($_REQUEST["coordinates"], $_REQUEST["rowStand"], $_REQUEST["columnStand"], $_REQUEST["id"]);
?>