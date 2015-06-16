<?php
require_once "modules_class.php";
require_once "search_class.php";

class searchContent extends Modules {

	
	public function __construct($db) {
		parent::__construct($db);
		$this->search = new search($db);
	}
	
	protected function getCenter() {
			$sr["searchResultClients"] = $this->searchResultClients();
			$sr["searchResultClans"] = $this->searchResultClans();
			$sr["value"] = $this->data["value"];
		return $this->getReplaceTemplate($sr, "search");
	}
	
	private function searchResultClans(){
		$value = $this->data["value"];
		if(!$value)
			return false;
		$descOrNot = $this->data["clanDesc"];
		$sort = $this->data["clanSort"];
		if(!$this->valid->check_sql($value) or $this->valid->isContainQuotes($value)){
			header("Location:?view=notfound");
			exit;
		}
		if($descOrNot != "up" and $descOrNot != "down"){
			header("Location:?view=notfound");
			exit;
		}
		if($sort != "league" and $sort != "name" and $sort != "tag" and $sort != "middle" and $total != "power"){
			header("Location:?view=notfound");
			exit;
		}
		$data = $this->search->getSearchClans($value, $sort, $descOrNot);
		for($i=0;$i<count($data);$i++){
				if($data[$i]["league"] == 0) $league = "laziness";
				if($data[$i]["league"] == 1) $league = "good";
				if($data[$i]["league"] == 2) $league = "evil";
					$middle = 2;
					$total = 2;
					$sr["league"] = $league;
					$sr["name"] = $data[$i]["name"];
					$sr["short"] = $data[$i]["tag"];
					$sr["middle"] = $middle;
					$sr["id"] = $data[$i]["id"];
					$sr["total"] = $total;
					$text .= $this->getReplaceTemplate($sr, "searchResultClans");
				}
				return $text;
	}
	
	private function searchResultClients(){
		$value = $this->data["value"];
		if(!$value)
			return false;
		$descOrNot = $this->data["desc"];
		$sort = $this->data["sort"];
		if(!$this->valid->check_sql($value) or $this->valid->isContainQuotes($value)){
			header("Location:?view=notfound");
			exit;
		}
		if($descOrNot != "up" and $descOrNot != "down"){
			header("Location:?view=notfound");
			exit;
		}
		if($sort != "league" and $sort != "login" and $sort != "lvl" and $sort != "clan" and $sort != "power"){
			header("Location:?view=notfound");
			exit;
		}
			$data = $this->search->getSearchClients($value, $sort, $descOrNot);
			$clanText = "";
			for($i = 0; $i < count($data); $i++){
				if($data[$i]["clan"] != 0){
					if($clanText == "")
						$clanText .= "id = '".$data[$i]["clan"]."' ";
					else
						$clanText .= "|| id = '".$data[$i]["clan"]."' ";
				}
			}
			$clans = $this->db->select("clans", array("id", "name", "tag"), $clanText , "", "", count($data));
				for($i=0;$i<count($data);$i++){
				if($data[$i]["league"] == 0) $league = "laziness";
				if($data[$i]["league"] == 1) $league = "good";
				if($data[$i]["league"] == 2) $league = "evil";
					$sr["league"] = $league;
					$sr["nick"] = $data[$i]["login"];
					$sr["lvl"] = $data[$i]["lvl"];
					$sr["id"] = $data[$i]["id"];
					if($data[$i]["clan"] == 0) $clan = "Без клана";
					else{
						for($x = 0; $x < count($clans); $x++){
							if($clans[$x]["id"] == $data[$i]["clan"]){
								$clan = "<a href='?view=clan&id=".$clans[$x]["id"]."'>".$clans[$x]["name"]."</a>";
								break;
							}
						}
					}
					$sr["clan"] = $clan;
					$sr["power"] = $data[$i]["power"];
					$text .= $this->getReplaceTemplate($sr, "searchResultClients");
				}
				return $text;
	}
}
?>