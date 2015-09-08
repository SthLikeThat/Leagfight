<?php
require_once "../lib/modules_class.php";
require_once "town_functions.php";

class townContent extends Modules {

	private $unctions;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->functions = new townFunctions($db, $this->account, $this->user_information);
	}
	
	protected function getCenter() {
		$sr["house"] = $this->functions->get_town_item(3);
		$sr["menuItem"] = $this->getReplaceTemplate($sr, "townMenu");
		return $this->getReplaceTemplate($sr, "town");
	}
	
	protected function getTitle(){
		return "Город - LF";
	}
	
	protected function getShortcut_icon(){
		return "town_title";
	}
	
	private function getBattle(){
		$list = $this->db->getFieldsBetter("mass_battle", "time_end", time(), array("id", "time_end", "white", "grey", "black"), ">");
		if(count($list) > 0){
			foreach( $list as $key => $value){
				if($value["grey"] !== "0"){
					$league = unserialize($value["grey"]);
					$src["grey"] = count($league);
				}
				else	$src["grey"] = 0;
				
				if($value["black"] !== "0"){
					$league = unserialize($value["black"]);
					$src["black"] = count($league);
				}
				else	$src["black"] = 0;
				
				if($value["white"] !== "0"){
					$league = unserialize($value["white"]);
					$src["white"] = count($league);
				}
				else	$src["white"] = 0;
				$src["id"] = $value["id"];
				$src["time_end"] = date("H:i:s",$value["time_end"] - 60 * 60);
				$text .= $this->getReplaceTemplate($src, "battle_choice_item");
			}
			$sr["list"] = $text;
		}
		else $sr["list"] = "";
		return $this->getReplaceTemplate($sr, "battle_choice");
	}
	
	private function getWork(){
		$sr["riverButton"] = '<a href="#" onclick="goWork(\'river\')">Поставить сети</a>';
		$sr["palaceButton"] = '<a href="/?view=availableWorks" >Искать работу</a>';
		if($this->user["typeJob"] == 2){
			$sr["riverButton"] = "Трудитесь";
		}
		if($this->user["typeJob"] == 3){
			$sr["palaceButton"] = "Трудитесь";
		}
		
		$text .= $this->getReplaceTemplate($sr, "works");
		return $text;
	}
	
	private function getAdvertising(){
		$section = $this->data["section"];
		$allADV = $this->db->getAll("advertisings", "time", "");	
		for($i=0; $i<count($allADV); $i++){
			if($section == "all")	{	$section = $allADV[$i]["section"];	$sectionFiltr = true;	}
			if( $section == $allADV[$i]["section"]){
				$author = $this->db->getAllOnField("users", "id", $allADV[$i]["idAuthor"], "", "");
				if($allADV[$i]["section"] == "buy") $section = "Куплю";
				if($allADV[$i]["section"] == "swap") $section = "Обменяю";
				if($allADV[$i]["section"] == "set") $section = "Набор в клан";
				if($allADV[$i]["section"] == "admin") $section = "Админу";
				$sr["autor"] = $author["login"];
				$sr["id"] = $allADV[$i]["idAuthor"];
				$sr["section"] = $section;
				$sr["title"] = $allADV[$i]["title"];
				$sr["text"] = $allADV[$i]["text"];
				if($allADV[$i]["image"] != "")
					$sr["image"] = "<img src='images_advertisings/".$allADV[$i]["image"].".png'/>";
				else $sr["image"] = "";
				$sr["sectionType"] = $allADV[$i]["section"];
				$length = strlen($allADV[$i]["text"]);
				$padding = 22;
				//if($length > 162)	$padding += 15;
				//if($length > 320)	$padding += 15;
				$sr["padding"] = $padding;
				$text .= $this->getReplaceTemplate($sr, "advertisingItem");
			}
			if($sectionFiltr){	$section = "all";	$sectionFiltr=false;	}
		}
		$srAll["contentAdv"] = $text;
		return $this->getReplaceTemplate($srAll, "advertising");
	}
	
	private function addAdvertising(){
		$time = $this->user["lastAdvertising"];
		$pos = strpos($time, "&");
		if(substr($time,0,$pos) + 86400 < time() and time() <= substr($time,0,$pos) + 86400){
			$timeOfNewDay = time() - ((date('G') * date('i') * 60) + date('s')) + 86400;
			if(substr($time,0,$pos)/86400 < $timeOfNewDay/86400) $day = "завтра";
			else $day = "сегодня";
			$sr["time"] = "Можно давать лишь одно объявление в сутки. Вы сможете дать следущее $day в ".substr($time,$pos+1).".";
		}
		else $sr["time"] = "";
		$text .= $this->getReplaceTemplate($sr, "addAdvertising");
	return $text;
	}
	
	private function getClan(){
		if($this->user["clan"] == 0 and $this->user["lvl"] >= 3){
			$sr["create"] = "<div class='createClan'>
				<form id='createClan'>
					<input type='text' id='name' placeholder='Название' />
					<input type='text' id='tag' placeholder='Тег' />
					<a href='#' onclick='createClan()'>Создать клан </a>
				</form>
			</div>";	
		}
		if($this->user["clan"] == 0 and $this->user["lvl"] < 3){
			$sr["create"] = "<div class='createClan'>Вы пока не можете создать клан. Возвращайтесь, когда достигните 5 уровня.</div>";
		}
		if($this->user["clan"] != 0){
			$srs["mda"] = 0;
			$sr["create"] = $this->getReplaceTemplate($srs, "clanMenu");
		}
		$text =  $this->getReplaceTemplate($sr, "clan");
		return $text;
	}
}
?>