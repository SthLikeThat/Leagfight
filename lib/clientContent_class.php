<?php
require_once "modules_class.php";

class clientContent extends Modules {

	private $client_info;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->client_info = $this->db->getElementOnID("users", $this->data["id"]);
		$this->settings = $this->db->getElementOnID("user_settings", $this->data["id"], true);
		if(!$this->client_info) $this->notFound();
	}
	
	protected function getCenter() {
		$sr["characteristics"] = $this->getCharacteristics();
		$sr["nick"] = $this->client_info["login"];
		$sr["lvl"] = $this->client_info["lvl"];
		$sr["id"] = $this->client_info["id"];
		$sr["avatar"] = $this->client_info["avatar"];
		$sr["description"] = $this->settings["description"];
		if($this->settings["description"] == "")
			$sr["description"] = "Информация о персонаже.";
		return $this->getReplaceTemplate($sr, "client");
	}
	
	protected function getCharacteristics(){
			$sr["strengh"] = $this->client_info["Strengh"];
			$sr["defence"] = $this->client_info["Defence"];
			$sr["agility"] = $this->client_info["Agility"];
			$sr["physique"] = $this->client_info["Physique"];
			$sr["mastery"] = $this->client_info["Mastery"];
			$sr["power"] = $this->client_info["power"];
			$sr["onOff"] = "on";
			
			$massiv = array ($this->client_info["Strengh"], $this->client_info["Defence"], $this->client_info["Agility"], $this->client_info["Physique"], $this->client_info["Mastery"]);
			$sr["percentStrengh"] = $this->getImage($massiv,0);
			$sr["percentDefence"] = $this->getImage($massiv,1);
			$sr["percentAgility"] = $this->getImage($massiv,2);
			$sr["percentPhysique"] = $this->getImage($massiv,3);
			$sr["percentMastery"] = $this->getImage($massiv,4);
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