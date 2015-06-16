<?php
require_once "modules_class.php";

class clanContent extends Modules {
	
	public function __construct($db) {
		parent::__construct($db);
		$this->clan = $this->db->getAllOnField("clans", "id", $this->data["id"], "", "");	
	}
	
	protected function getCenter() {
		if($this->clan == 0){
			header("Location:?view=notfound");
			exit;
		}
		$sr["name"] = $this->clan["name"]." [".$this->clan["tag"]."]";
		$sr["current"] = count(unserialize($this->clan["users"]));
		$workshop = unserialize($this->clan["workshop"]);
		$sr["max"] = $workshop["barracks"] * 2;
		$sr["image"] = $this->clan["image"];
		$sr["id"] = $this->clan["id"];
		$sr["users"] = $this->getUsers();
		$sr["description"] = $this->clan["description"];
		$sr["minLvl"] = "(".$this->clan["minLvl"]."+)";
		if($this->clan["minLvl"] == 0)	$sr["minLvl"] = "";
		if($this->clan["description"] == "") $sr["description"] = "Описание клана отсутствует.";
		return $this->getReplaceTemplate($sr, "clanview");
	}
	
	private function getUsers(){
		$users = unserialize($this->clan["users"]);
		$text = "<ul id='clanUsersUl' class='clanViewUsers'>
		<li id='liFirst'>
			<div class='nameClanUser'>Ник</div>
			<div class='nameClanUser'>Уровень</div>
			<div class='nameClanUser'>Могущество</div>
			<div class='titleClanUser'>Звание</div>
		</li>";
		$where = "id =".$users[0];
		if(count($users) > 1){
			for($i = 1; $i < count($users); $i++){
				$where .= " || id =".$users[$i];
			}
		}
		$userInfo = $this->db->select("users", array("*"), $where, "power", "", count($users));	
		$user_titles = unserialize($this->clan["user_titles"]);
		for($i = 0; $i < count($userInfo); $i++){
			$sr["name"] = $userInfo[$i]["login"];
			$sr["lvl"] = $userInfo[$i]["lvl"];
			$sr["power"] = $userInfo[$i]["power"];
			$sr["id"] = $userInfo[$i]["id"];
			$sr["more"] = "onclick ='document.location.href=".'"?view=client&id='.$userInfo[$i]["id"].'"'."'";
			$sr["moreAdmin"] = "";
			$sr["control"] = "";
			$sr["title"] = $user_titles[$userInfo[$i]["id"]];
			$text .=  $this->getReplaceTemplate($sr, "clan_users");
		}
		$text .= "</ul>";
		return $text;
	}
}
?>