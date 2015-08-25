<?php
require_once "template.php";
require_once "config_class.php";
require_once "checkvalid_class.php";

abstract class Modules extends Template{

	protected $data;
	protected $db;
	protected $valid;
	protected $user;
	
	public function __construct($db) {
		session_start();
		
		$this->db = $db;
		$this->config = $this->db->config;
		
		$this->valid = new CheckValid($db);
		$this->data = $this->secureData(array_merge($_POST, $_GET));
		if(!$this->getAuthUser()){
            unset($_SESSION);
            header("Location: auth.html");
            return false;
        }
		$this->user = $this->db->selectFromTables(array("users", "user_resources", "user_settings", "user_statistic"), "id", $_SESSION["id"]);
	}
	
	private function secureData($data) {
		foreach($data as $key => $value) {
			if (is_array($value)) $this->secureData($value);
			else $data[$key] = htmlspecialchars($value);
		}
		return $data;
	}
	
	public function getContent() {
		$sr["menu"] = $this->getFileContent("mainMenu");
		$sr["header"] = $this->getHeader();
		$sr["center"] = $this->getCenter();
		if($this->data["ajax"] == 1)
			return $this->getReplaceTemplate($sr, "mainWrapper");
		else
		return $this->getReplaceTemplate($sr, "main");
	}
	
	abstract protected function getCenter();
	
	protected function getHeader(){
		$user = $this->user;
		$sr["gold"] = $user["Gold"];
		$sr["tournament_icon"] = $user["tournament_icon"];
		$sr["another"] = $user["Another"];
		$sr["donat"] = $user["Donat"];
		$sr["currentExp"] = $user["currentExp"];
		$sr["needExp"] = $user["needExp"];
		$sr["exp"] = $this->getImage($user["currentExp"],$user["needExp"]);
		$sr["lvl"] = $user["lvl"];
		$sr["currentHp"] = $user["currentHp"];
		$sr["maxHp"] = $user["maxHp"];
		$sr["hp"] = $this->getImage($user["currentHp"],$user["maxHp"]);
			
			//Таймер нападения
			$timeToAttack = $user["timerAttack"];
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
			
			//Таймер работы
			if($user["typeJob"] == 1 and $user["lastRegen"] - time() > 3600 and $user["currentHp"] != $user["maxHp"]){
				$house = $this->db->getAllOnField("user_house", "id", $user["id"], "", "");
				for($i = 1; $i < ($user["lastRegen"] - time()) / 3600;$i++){
					$this->db->setField("users", "currentHp", $user["currentHp"] +($user["currentHp"] * ($house["bed"]*5) /100), "id", $user["id"]);
					$this->db->setField("users", "lastRegen", time(), "id", $user["id"]);
				}
			}
			if($user["typeJob"] == 2 and $user["jobEnd"] < time()){
				for($i = 1; $i <= $user["installedNetworks"]; $i++){
					$rand = rand(0,1);
					if($rand)
						$summaryAnother += 1;
				}
				if($summaryAnother > 0){
					$this->db->setField("user_resources", "Another", $user["Another"] + $summaryAnother, "id", $user["id"]);
					$this->db->setField("user_resources", "installedNetworks", 0, "id", $user["id"]);
					$this->db->setField("users", "typeJob", 0, "id", $user["id"]);
					$date = date("Y").date("m").date("d").date("H").date("i").date("s");
					$time = date("H").":".date("i").":".date("s");
					$beautifulDate = date("d").".".date("m").".".date("y");
					$mass = array("idDialog" =>"0|".$user["id"], "idUser"=>$user["id"], "idSender" => 0, "textMessage"=>"В ваши сети попалось $summaryAnother молюсков.", "time"=>$time, "date"=>$date, "beautifulDate"=>$beautifulDate, "type"=>1, "title"=>"Отчёт с ручья", "status" => 1);
					print_r($mass);
					$this->db->insert("mail", $mass);
				}
			}
			
			if(substr($user["typeJob"], 0, 5) == "work_" and $user["jobEnd"] < time()){
				$idWork = substr($user["typeJob"], -1);
				$work = $this->db->getAllOnField("available_works", "id", $idWork, "", "");
				$rand = rand(75, 125) / 100;
				$this->db->setField("user_resources", "Gold", $user["Gold"] + round($work["gold"] * $rand), "id", $user["id"]);
				if($work["another"] != 0)
					$this->db->setField("user_resources", "another", $user["Another"] + round($work["another"] * $rand), "id", $user["id"]);
				$this->db->setField("users", "typeJob", 0, "id", $user["id"]);
			}
			
			if(substr($user["typeJob"], 0, 7) == "battle_" and $user["jobEnd"] > time() and $_GET["view"] != "battleField"){
				$id = substr($user["typeJob"], 7);
				header("Location:http://zadanie/?view=battleField&id=$id");
			}
			$jobTime = $user["jobEnd"];
			$allSeconds = $jobTime - time();
			if($allSeconds <= 0 and $jobTime != 0){
				$allSeconds = 0;
				$this->db->setField("users", "typeJob", 0, "id", $user["id"]);
				$this->db->setField("users", "jobEnd", 0, "id", $user["id"]);
			}
			$sr["mda"] = $allSeconds;
		$text = $this->getReplaceTemplate($sr, "header");
		return $text;
	}
	
	protected function getMenu(){
		$menu = $this->db->getAll("menu", "", "");
		for ($i=0;$i<count($menu);$i++){
			$sr["image"] = $menu[$i]["image"];
			$sr["title"] = $menu[$i]["title"];
			$sr["link"] = $menu[$i]["link"];
			$text .= $this->getReplaceTemplate($sr, "menu_item");
		}
		return $text;
	}
	
	final protected function getAuthUser() {
		if(!$_SESSION["id"]){
            return false;
		}
		$user = $this->db->getFieldsBetter("users", "id", $_SESSION["id"], array("id", "user_hash", "currentHp", "maxHp", "lastRegen"), "=");
		$user = $user[0];
        $time = time();
		if($user["lastRegen"] + 3600 < $time and $user["currentHp"] < $user["maxHp"]){
			$maxCycle = round(($time - $user["lastRegen"])/3600,0);
			if($maxCycle > 20)	$maxCycle = 20;
			for($i = 0; $i < $maxCycle; $i++){
				$bonusHP += $user["maxHp"]/20;
			}
			$bonusHP = round($bonusHP,0);
			if($user["currentHp"] + $bonusHP >= $user["maxHp"]) {
                $this->db->setFieldOnID("users", $user["id"], "currentHp", $user["maxHp"]);
            }
			else $this->db->setFieldOnID("users", $user["id"], "currentHp", $user["currentHp"] + $bonusHP);
			$this->db->setFieldOnID("users", $user["id"], "lastRegen", $time);
		}
		if($_SESSION["hash"] === $user["user_hash"]){
			return true;
		}
		else{
            return false;
		}
	}
	
	protected function redirect($link) {
		header("Location: $link");
		exit;
	}
	
	private function getImage($current, $max){
			$procent = 100/$max;
			$dlina = $procent * $current;
			$dlina = round($dlina, 0);
			return $dlina;
	}
	
	protected function notFound() {
		$this->redirect($this->config->address."?view=notfound");
	}
}
?>