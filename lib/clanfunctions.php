<?php
require_once "database_class.php";

class clanFunctions extends DataBase{

	private $db;
	
	public function __construct() {
		parent::__construct();
		$this->db = $this;
		session_start();
		$this->user = $this->db->getAllOnField("users", "id",$_SESSION["id"], "", "");
		$this->resources = $this->db->getAllOnField("user_resources", "id",$this->user["id"], "", "");
	}
	
	public function clanMenu($type){
		if($type == "users"){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$users = unserialize($clan["users"]);
			$text = "<ul id='clanUsersUl' style='width:75%;'>
			<li id='liFirst'>
				<div class='nameClanUser' >Ник</div>
				<div class='nameClanUser' '>Уровень</div>
				<div class='nameClanUser' >Могущество</div>
				<div class='titleClanUser' >Звание</div>
			</li>";
			$where = "id =".$users[0];
			if(count($users) > 1){
				for($i = 1; $i < count($users); $i++){
					$where .= "|| id =".$users[$i];
				}
			}
			$userInfo = $this->db->select("users", array("*"), $where, "power", "", count($users));	
			$user_titles = unserialize($clan["user_titles"]);
			for($i = 0; $i < count($userInfo); $i++){
				$sr["name"] = $userInfo[$i]["login"];
				$sr["lvl"] = $userInfo[$i]["lvl"];
				$sr["power"] = $userInfo[$i]["power"];
				$sr["id"] = $userInfo[$i]["id"];
				if($user_titles[$this->user["id"]] == "Полемарх"){
					$sr["more"] = "";
					$sr["moreAdmin"] = "class='clanUserAdmin' onclick='document.location.href =\"?view=client&id=".$userInfo[$i]["id"]."\" ' ";
				}
				else{
					$sr["more"] = " onclick='document.location.href =\" ?view=client&id=".$userInfo[$i]["id"]."\" '";
					$sr["moreAdmin"] = "";
				}
				
				if($user_titles[$this->user["id"]] == "Полемарх"){
					$sr["title"] = $user_titles[$userInfo[$i]["id"]];
					if($userInfo[$i]["id"] != $users[0])
						$sr["control"] = "<a href='#' onclick='wantDeleteClanMember(".$userInfo[$i]["id"].")'><img src='images/deleteClanMember.png' height='15' /></a>".
						"<a href='#' onclick='editTitle(".$userInfo[$i]["id"].")'><img src='images/editTitle.png' height='15' /></a>";
					else 
						$sr["control"] = "";
				}
				else{
					$sr["title"] = $user_titles[$userInfo[$i]["id"]];
					$sr["control"] = "";
				}
				$text .=  $this->getReplaceTemplate($sr, "clan_users");
			}
			$text .= "</ul>";
			if($user_titles[$this->user["id"]] == "Полемарх") $text .= "<div class='editTitleClass'></div>";
			echo $text;
			exit;
		}
		if($type == "treasury"){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			
			$text = "<div class='formTreasury' id='formTreasury'>
					<ul>
						<li><a href='#' onclick='clanMenu(".'"rate"'.")'>&nbsp;&nbsp;Ставка  &nbsp;&nbsp;</a></li>
						<li><input type='text' id='goldTreasury' placeholder='Золото'/></li>
						<li><input type='text' id='anotherTreasury' placeholder='Жемчуг'/></li>
						<li><a href='#' onclick='depositTreasury()'>Пополнить</a></li>
					</ul></div>
				<ul id='totalResourceClan'>
					<li class='titleClanUser' style='font-size:x-large; border:0;'><img src='images/coinBlack.png' height='48'/>".$clan["Gold"]."</li>
					<li class='titleClanUser' style='font-size:x-large; border:0;'><img src='images/diamond.png' height='48'/>".$clan["Another"]."</li>
				</ul>
			<ul id='offers'>
				<li id='liFirst'>
					<div class='nameClanUser'>Ник</div>
					<div class='goldClanUser' style='line-height:0;'><img src='images/coinBlack.png' height='18'/>Внёс</div>
					<div class='goldClanUser' style='line-height:0;'><img src='images/diamond.png' height='18'/>Внёс</div>
					<div class='goldClanUser' style='line-height:0;'><img src='images/coinBlack.png' height='18'/>Долг</div>
					<div class='titleClanUser' style='line-height:0; padding:0; height:21px;'><img src='images/diamond.png' height='18'/>Долг</div>
				</li>";
			$treasury =  unserialize($clan["treasury"]);
			for($i = 0; $i < count($treasury); $i++){
				$sr["name"] = $treasury[$i][0];
				$sr["gold"] = $treasury[$i][1];
				$sr["another"] = $treasury[$i][2];
				$sr["goldDebt"] = $treasury[$i][3];
				$sr["anotherDebt"] = $treasury[$i][4];
				$text .=  $this->getReplaceTemplate($sr, "clan_resources");
			}
			$text .= "</ul>";
			echo $text;
			exit;
		}
		if($type == "workshop"){
			$workshop = $this->db->getAll("clan_workshop", "", "");
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$clanWorkshop = unserialize($clan["workshop"]);
			$text = "<ul style='width:75%;'>";
			for($i = 0; $i < count($workshop); $i++){
				$sr["image"] = $workshop[$i]["image"];
				$sr["name"] = $workshop[$i]["title"];
				$sr["nameEng"] = $workshop[$i]["name"];
				$sr["value"] = $workshop[$i]["value"] * $clanWorkshop[$workshop[$i]["name"]];
				$sr["prefix"] = $workshop[$i]["prefix"];
				$sr["text"] = $workshop[$i]["text"];
				$sr["price"] = $workshop[$i]["startPrice"] * $clanWorkshop[$workshop[$i]["name"]] * 1.5;
				$sr["lvl"] = $clanWorkshop[$workshop[$i]["name"]];
				$text .=  $this->getReplaceTemplate($sr, "clan_workshop");
			}
			$text .= "</ul>";
			echo $text;
			exit;
		}
		if($type == "settings"){
			//$mda = array(0=>array("name"=>"Полемарх","treasury"=>1,"platz"=>1,"workshop"=>1,"diplomacy"=>1,"academy"=>1,"hikes"=>1));
			//$mass = array(0=>array("Полемарх",1,1,1,1,1,1),1=>array("Новобранец",0,0,0,0,0,0));
			//$this->db->setFieldOnID("clans", $this->user["clan"], "titles", serialize($mass));
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$user_titles = unserialize($clan["user_titles"]);
			$titles = unserialize($clan["titles"]);
			if($user_titles[$this->user["id"]] != "Полемарх"){
				echo "<br/>Только полемарх может вносить изменения в устройство клана.<br/><br/>";
				exit;
			}
			$sr["minLvl"] = $clan["minLvl"];
			$sr["description"] = $clan["description"];
			if($clan["minLvl"] == 0)	$sr["minLvl"] = "";
			for($i = 0; $i < count($titles);$i++){
				$table .= "<tr>";
				if($i == 0 or $i == 1) $dis = "disabled";
				else $dis ="";
				for($x = 0; $x < count($titles[$i]); $x++){
					if($titles[$i][$x] == 1)	$value = "checked";
					if($titles[$i][$x] == 0)	$value = "";
					if($x == 0) $table .= "<td>".$titles[$i][$x]."</td>";
					else $table .= "<td><input type='checkbox' $dis onclick='titleChanged($i, $x)' id=".$i."_".$x." ".$value."/></td>";
				}
				$table .= "<td ><div class='editingTitle' onclick='rollBackTitle($i)' id=$i><img src ='images/editTitle.png' height='15' /></div>
				<div class='deleteTitle' onclick='deleteTitle($i)' id=$i><img src ='images/trash.png' height='15' /></div>
				</td></tr>";
			}	
			$sr["table"] = $table;
			$text .=  $this->getReplaceTemplate($sr, "clan_settings");
			echo $text;
			exit;
		}
		if($type == "platz"){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			if($clan["platz"] == "a:0:{}" or $clan["platz"] == ""){ 
				echo "<ul>
					<li id='liFirst'>
						<div class='nameClanUser'>Ник</div>
						<div class='nameClanUser'>Уровень</div>
						<div class='nameClanUser'>Могущество</div>
						<div class='titleClanUser'>Звание</div>
					</li>
					<br/>
					На плацу тишина и только ветер гоняет перекати-поле.";
				exit;
			}
			else{
				$platz = unserialize($clan["platz"]);
				$text = "<ul>
					<li id='liFirst'>
						<div class='nameClanUser'>Ник</div>
						<div class='nameClanUser'>Уровень</div>
						<div class='nameClanUser'>Могущество</div>
						<div class='titleClanUser'>Решение</div>
					</li>";
					$where = "id =".$platz[0];
					if(count($platz) > 1){
						for($i = 1; $i < count($platz); $i++){
							$where .= "|| id =".$platz[$i];
						}
					}
					$userInfo = $this->db->select("users", array("*"), $where, "", "", "");	
				for($i = 0; $i < count($userInfo); $i++){
					$sr["name"] = $userInfo[$i]["login"];
					$sr["lvl"] = $userInfo[$i]["lvl"];
					$sr["power"] = $userInfo[$i]["power"];
					$sr["id"] = $userInfo[$i]["id"];
					$sr["title"] = "<img src='images/Like.png' id='likePlatz' height='18' onclick='decisionPlatz(".$userInfo[$i]["id"].",1)'/><img src='images/Dislike.png' id='dislikePlatz' height='18' onclick='decisionPlatz(".$userInfo[$i]["id"].",0)'/>";
					$text .=  $this->getReplaceTemplate($sr, "clan_platz");
				}
				$text .= "</ul>";
				echo $text;
				exit;
			}
		}
		if($type == "rate"){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$text = "<ul id='rateForm'><li><div class='lvlRate'>Уровень</div><div class='rate'>Золото</div><div class='rate'>Жемчужины</div></li>";
			if($clan["rates"] != ""){
				$rates = unserialize($clan["rates"]);
				for ($i = count($rates); $i>=0; $i--) {
					for ($j = 0; $j<($i-1); $j++)
						if ($rates[$j][0]>$rates[$j+1][0]) {
							$temp = $rates[$j];
							$rates[$j] = $rates[$j+1];
							$rates[$j+1] = $temp;
						}
				}
				for($i = 0; $i < count($rates); $i++){
					$text .= "<li class='canEdit' id='".$i."' onclick='loadForEdit(".$i.")'><div id='".$i."_lvlRate'class='lvlRate'>".$rates[$i][0]." - ".$rates[$i][1]."</div><div id='".$i."_gold' class='rate'>".$rates[$i][2]."</div><div id='".$i."_another' class='rate'>".$rates[$i][3]."</div></li>";
				}
			}
			if(!$this->admission(1))
			$text .= "<li><div class='lvlRate'><input type='text' id='minLvl' class='inputClass'/> - <input type='text' id='maxLvl' class='inputClass'/></div><div class='rate'><input type='text' id='goldRate' class='inputClass' value ='0'/></div><div class='rate'><input type='text' id='anotherRate' class='inputClass' value ='0'/></div></li>
					<li><div class='lvlRate'><a href='#' onclick='newRate()'>Добавить</a></div><div class='rate'><a href='#' onclick='editRate()'>Изменить</a></div><div class='rate'><a href='#' onclick='delRate()'>Удалить</a></div></li>";
			$text .= "</ul>";
			echo $text;
			exit;
		}
		else{
			echo "Ой, всё.";
			exit;
		}
	}
	
	public function depositTreasury($gold, $another){
		if($gold == '') $gold = 0;
		if($another == '') $another = 0;
		if($gold == 0 and $another == 0) exit;
		if($this->valid->isNoNegativeInteger($gold) and $this->valid->isNoNegativeInteger($another) ){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$treasury =  unserialize($clan["treasury"]);
			
			if($gold > 0 and $this->resources["Gold"] > $gold){
				for($i =0; $i < count($treasury); $i++){
					if($treasury[$i][0] == $this->user["login"]){
						$treasury[$i][1] = $treasury[$i][1] + $gold;
						$treasury[$i][3] = $treasury[$i][3] + $gold;
					}
				}
				$this->db->setFieldOnID("user_resources", $this->user["id"], "Gold", $this->resources["Gold"] - $gold);
				$this->db->setFieldOnID("clans", $this->user["clan"], "Gold", $clan["Gold"] + $gold);
				$this->db->setFieldOnID("clans", $this->user["clan"], "treasury", serialize($treasury));
			}
			if($another > 0 and $this->resources["Another"] > $another){
				for($i =0; $i < count($treasury); $i++){
					if($treasury[$i][0] == $this->user["login"]){
						$treasury[$i][2] = $treasury[$i][2] + $another;
						$treasury[$i][4] = $treasury[$i][4] + $another;
					}
				}
				$this->db->setFieldOnID("user_resources", $this->user["id"], "Another", $this->resources["Another"] - $another);
				$this->db->setFieldOnID("clans", $this->user["clan"], "Another", $clan["Another"] + $another);
				$this->db->setFieldOnID("clans", $this->user["clan"], "treasury", serialize($treasury));
			}
			$newGold = $this->resources["Gold"] - $gold;
			$newAnother = $this->resources["Another"] - $another;
			echo $newGold."&".$newAnother;
			exit;
		}
		else exit;
	}
	
	public function newTitle($name, $treasury, $platz, $workshop, $diplomacy, $academy, $hike){
		if($this->valid->validString($name,3 ,25) and $this->valid->check_sql($name)){
			if($this->valid->isOnlyLettersAndDigits($name)){
				if(!$this->valid->isNoNegativeInteger($treasury) or !$this->valid->isNoNegativeInteger($platz) or !$this->valid->isNoNegativeInteger($workshop) 
				or !$this->valid->isNoNegativeInteger($diplomacy) or !$this->valid->isNoNegativeInteger($academy) or !$this->valid->isNoNegativeInteger($hike))
					exit;
				$name = $this->valid->secureText($name);
				$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
				$workshopClan = unserialize($clan["workshop"]);
				$titles =  unserialize($clan["titles"]);
				if($workshopClan["table"] < count($titles))
					die("Предел количества званий.");
				$titles[count($titles)] = array($name, $treasury, $platz, $workshop, $diplomacy, $academy, $hike);
				$this->db->setFieldOnID("clans", $this->user["clan"], "titles", serialize($titles));
			}
			else{
				echo "Только буквы и цифры.";
				exit;
			}
		}
		else{
			echo "Название не должно быть 3 - 25 символов.";
			exit;
		}
	}
	
	public function enterClan($id){
	
		if($this->valid->validID($id) and $this->db->existsID("clans", $id) and $this->user["clan"] == 0){
			$clan = $this->db->getAllOnField("clans", "id", $id, "", "");
			if($clan == 0) exit;
			if($this->user["lvl"] < $clan["minLvl"]){
				die("Уровнем не дорос еще.");
			}
			if($clan["platz"] == "a:0:{}" or $clan["platz"] == ""){
				$platz = serialize(array($this->user["id"]));
				$ok = $this->db->setFieldOnID("clans", $id, "platz", $platz);
				if($ok)
					die("Заявка подана.");
				else
					die("Возникла какая-то ошибка.");
			}
			else{
				$platz = unserialize($clan["platz"]);
				for($i = 0; $i < count($platz); $i++){
					if($platz[$i] == $this->user["id"]){
						die("Вы уже подали заявку.");
					}
				}
				$platz[] = $this->user["id"];
				$ok = $this->db->setFieldOnID("clans", $id, "platz", serialize($platz));
				if($ok)
					die("Заявка подана.");
				else
					die("Возникла какая-то ошибка.");
			}
		}
	}
	
	public function decisionPlatz($id, $decision){
		if($this->valid->validID($id) and $this->db->existsID("users", $id) and $decision >=0 and $decision <=1){
		if($this->admission(2)){
			die("У вас нет прав на принятие новых членов клана.");
		}
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$platz = unserialize($clan["platz"]);
			for($i = 0; $i < count($platz); $i++){
				if($platz[$i] == $id){
					unset($platz[$i]);
					$platz = array_values($platz);
					$this->db->setFieldOnID("clans", $this->user["clan"], "platz", serialize($platz));
				}
			}
			if($decision == 1){
				$users = unserialize($clan["users"]);
				$users[count($users)] = $id;
				$this->db->setFieldOnID("clans", $this->user["clan"], "users", serialize($users));
				$treasury = unserialize($clan["treasury"]);
				$user = $this->db->getElementOnID("users", $id);
				for($i = 0; $i < count($treasury); $i++){
					if($treasury[$i][0] == $user["login"]) $exist = true;
				}
				if(!$exist){
					$treasury[] = array($user["login"], 0 , 0 , 0, 0);
					$this->db->setFieldOnID("clans", $this->user["clan"], "treasury", serialize($treasury));
				}
				$user_titles = unserialize($clan["user_titles"]);
				$user_titles[$id] = "Новобранец";
				$this->db->setFieldOnID("clans", $this->user["clan"], "user_titles", serialize($user_titles));
				$this->db->setFieldOnID("users", $id, "clan", $clan["id"]);
				exit;
			}
			else exit;
		}
		else exit;
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
	
	public function newRate($minLvl, $maxLvl, $goldRate, $anotherRate){
		if($this->valid->isNoNegativeInteger($minLvl) and $this->valid->isNoNegativeInteger($maxLvl)and $this->valid->isNoNegativeInteger($goldRate) and $this->valid->isNoNegativeInteger($anotherRate) and !$this->admission(1)){
			if($minLvl > $maxLvl){
				echo "Минимальный уровень должен быть меньше максимального.";
				exit;
			}
			if($maxLvl > 999){
				echo "Уровень не может быть больше 999.";
				exit;
			}
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			if($clan["rates"] == ""){
				$rates = serialize(array(0 => array($minLvl, $maxLvl, $goldRate, $anotherRate)));
				$this->db->setFieldOnID("clans", $this->user["clan"], "rates", $rates);
				exit;
			}
			else{
				$rates = unserialize($clan["rates"]);
				$x = 0;
				for($i = 0; $i < count($rates); $i++){
					for($y = $rates[$i][0]; $y <= $rates[$i][1]; $y++){
						$mass[$y] = true;
						$x++;
					}
				}
				for($i = $minLvl; $i <= $maxLvl; $i++){
					if($mass[$i]){
						echo "Для этого уровня уже есть ставка.";
						exit;
					}
				}
				$rates[count($rates)] = array($minLvl, $maxLvl, $goldRate, $anotherRate);
				$this->db->setFieldOnID("clans", $this->user["clan"], "rates", serialize($rates));
				exit;
			}
		}
		else exit;
	}
	
	public function editRate($minLvl, $maxLvl, $goldRate, $anotherRate){
		if($this->valid->isNoNegativeInteger($minLvl) and $this->valid->isNoNegativeInteger($maxLvl)and $this->valid->isNoNegativeInteger($goldRate) and $this->valid->isNoNegativeInteger($anotherRate) and !$this->admission(1)){
			if($minLvl > $maxLvl){
				echo "Минимальный уровень должен быть меньше максимального.";
				exit;
			}
			if($maxLvl > 999){
				echo "Уровень не может быть больше 999.";
				exit;
			}
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$rates = unserialize($clan["rates"]);
			for($i = 0; $i < count($rates); $i++){
					if($minLvl == $rates[$i][0] and $maxLvl == $rates[$i][1]){
						$rates[$i] = array($minLvl, $maxLvl, $goldRate, $anotherRate);
						$this->db->setFieldOnID("clans", $this->user["clan"], "rates", serialize($rates));
						exit;
					}
			}
		}
		else exit;
	}
	
	public function delRate($minLvl, $maxLvl){
		if($this->valid->isNoNegativeInteger($minLvl) and $this->valid->isNoNegativeInteger($maxLvl)){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$rates = unserialize($clan["rates"]);
			for($i = 0; $i < count($rates); $i++){
					if($minLvl == $rates[$i][0] and $maxLvl == $rates[$i][1]){
						unset($rates[$i]);
						$rates = array_values($rates);
						$this->db->setFieldOnID("clans", $this->user["clan"], "rates", serialize($rates));
						exit;
					}
			}
		}
		else exit;
	}
	
	public function editTitle($id){
		if($this->valid->validID($id)){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$user_titles = unserialize($clan["user_titles"]);
			$titles = unserialize($clan["titles"]);
			for($i = 0; $i < count($titles); $i++){
				if($user_titles[$id] == $titles[$i][0]){
					$text = '<select size="1" id="newTitle_'.$id.'">';
					for($x = 0; $x <count($titles); $x++){
						if($user_titles[$id] == $titles[$x][0]) $selected = "selected";
						else $selected = "";
						$text .= "<option $selected value='".$titles[$x][0]."'>".$titles[$x][0]."</option>";
					}
					  $text .= '</select>';
				}
			}
			$text .= "|<a href='#' onclick='deleteClanMember(".$userInfo[$i]["id"].")'><img src='images/deleteClanMember.png' height='15' /></a>".
					"<a href='#' onclick='saveNewTitle(".$id.")'><img src='images/saveTitle.png' height='15'></a>";
			//$text .= "<a href='#' onclick='saveNewTitle(".$id.")'><img src='images/saveTitle.png' height='15'></a>";
			echo $text;
			exit;
		}
		else exit;
	}
	
	public function saveNewTitle($id, $title){
		if($this->valid->validID($id)){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$users = unserialize($clan["users"]);
			$exist = false;
			for($i = 0; $i < count($users); $i++){
				if($users[$i] == $id){
					$exist = true;
					break;
				}
			}
			if(!$exist) exit;
			$user_titles = unserialize($clan["user_titles"]);
			$titles = unserialize($clan["titles"]);
			$count = 0;
			for($i = 0; $i < count($titles); $i ++){
				if($titles[$i][0] == $title){
					$user_titles[$id] = $title;
					for($i = 0; $i < count($user_titles); $i++){
						if($user_titles[$i] == "Полемарх"){
							$count++;
						}
					}
				}
			}
			if($count > 0){
				$this->db->setFieldOnID("clans", $this->user["clan"], "user_titles", serialize($user_titles));
				exit;
			}
			else die("Клан не может остаться без Полемарха");
		}
	}
	
	public function saveTitles($description, $minLvl, $titles){
		$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
		$user_titles = unserialize($clan["user_titles"]);
		if($user_titles[$this->user["id"]] != "Полемарх")
			die("Ты что творишь? Тебе же нельзя!");
			
		if($this->valid->isNoNegativeInteger($minLvl) and $minLvl<999)
			$this->db->setFieldOnID("clans", $this->user["clan"], "minLvl", $minLvl);
		else echo "Уровень должен быть от 0 до 999.";
		
		if($this->valid->check_sql($description) and $this->valid->validString($description,0,255)){
			$description = $this->valid->secureText($description);
			$this->db->setFieldOnID("clans", $this->user["clan"], "description", $description);
		}
		else echo "Слишком длинное описание.";
		
		$clanTitles = unserialize($clan["titles"]);
		$i = 0;
		if($titles != ""){
			foreach($titles as $key => $value){
				$newTitles[$i] = array(substr($value,0, 1),substr($value,2,3 ));
				$i++;
			}
			for($i = 2; $i <= count($clanTitles) - 1; $i++){
				for($y = 0; $y <= count($clanTitles[$i]); $y++){
					for($x = 0; $x <= count($newTitles); $x++){
						if($i == $newTitles[$x][0] and $y == $newTitles[$x][1]){
							if($clanTitles[$i][$y] == 1){
								$clanTitles[$i][$y] = 0;
								break;
							}
							if($clanTitles[$i][$y] == 0)
								$clanTitles[$i][$y] = 1;
						}
					}
				}
			}
			$this->db->setFieldOnID("clans", $this->user["clan"], "titles", serialize($clanTitles));
			echo "Сохранено";
		}
	}
	
	public function deleteTitle($id){
		if($this->valid->validID($id) and $id >=2){
			$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
			$titles = unserialize($clan["titles"]);
			$user_titles = unserialize($clan["user_titles"]);
			if($user_titles[$this->user["id"]] != "Полемарх")
				die("У тебя нет на это прав.");
			foreach($user_titles as $key=>$value){
				if($value == $titles[$id][0]){
					$user_titles[$key] = "Новобранец";
				}
				
			}
			unset($titles[$id]);
			$titles = array_values($titles);
			$this->db->setFieldOnID("clans", $clan["id"], "titles", serialize($titles));
			$this->db->setFieldOnID("clans", $clan["id"], "user_titles", serialize($user_titles));
			echo "Удалено!";
		}
		else die("Этот титул нельзя удалять!");
	}
	
	public function deleteClanMember($id){
		$clan = $this->db->getAllOnField("clans", "id", $this->user["clan"], "", "");
		$users = unserialize($clan["users"]);
		$treasury = unserialize($clan["treasury"]);
		
		for($i = 1; $i < count($users); $i++){
			if($users[$i] == $id){
				unset($users[$i]);
				$users = array_values($users);
				$this->db->setFieldOnID("clans", $clan["id"], "users", serialize($users));
				$this->db->setFieldOnID("users", $id, "clan", 0);
				$login = $this->db->getField("users", "login", "id", $id);
				for($i = 0; $i < count($treasury); $i++){
					if($treasury[$i][0] == $login)
						break;
				}
				if($treasury[$i][1] == 0 and $treasury[$i][2] == 0){
					unset($treasury[$i]);
					$treasury = array_values($treasury);
					$this->db->setFieldOnID("clans", $clan["id"], "treasury", serialize($treasury));
				}
				echo "Выгнан!";
				break;
			}
		}
	}
	
	public function wantDeleteClanMember($id){
		if($this->valid->validID($id) and $this->db->existsID("users", $id)){
			$user = $this->db->getField("users", "login", "id", $id);
			$sr["text"] = "Вы действительно хотите выгнать $user ?";
			$sr["onclick"] = "deleteClanMember($id)";
			$sr["textDelete"] = "Выгнать";
			$text = $this->getReplaceTemplate($sr, "deleteAlert");
			echo $text;
		}
	}
}

$clanFunctions = new clanFunctions();
if($_REQUEST["WhatIMustDo"] === "clanMenu")		$clanFunctions->clanMenu($_REQUEST["type"]);
if($_REQUEST["WhatIMustDo"] === "depositTreasury")		$clanFunctions->depositTreasury($_REQUEST["gold"], $_REQUEST["another"]);
if($_REQUEST["WhatIMustDo"] === "newTitle")		$clanFunctions->newTitle($_REQUEST["name"], $_REQUEST["treasury"], $_REQUEST["platz"], $_REQUEST["workshop"], $_REQUEST["diplomacy"], $_REQUEST["academy"], $_REQUEST["hike"]);
if($_REQUEST["WhatIMustDo"] === "enterClan")		$clanFunctions->enterClan($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "decisionPlatz")		$clanFunctions->decisionPlatz($_REQUEST["id"], $_REQUEST["decision"]);
if($_REQUEST["WhatIMustDo"] === "newRate")		$clanFunctions->newRate($_REQUEST["minLvl"], $_REQUEST["maxLvl"], $_REQUEST["goldRate"], $_REQUEST["anotherRate"]);
if($_REQUEST["WhatIMustDo"] === "editRate")		$clanFunctions->editRate($_REQUEST["minLvl"], $_REQUEST["maxLvl"], $_REQUEST["goldRate"], $_REQUEST["anotherRate"]);
if($_REQUEST["WhatIMustDo"] === "delRate")		$clanFunctions->delRate($_REQUEST["minLvl"], $_REQUEST["maxLvl"]);
if($_REQUEST["WhatIMustDo"] === "editTitle")		$clanFunctions->editTitle($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "deleteTitle")		$clanFunctions->deleteTitle($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "saveNewTitle")		$clanFunctions->saveNewTitle($_REQUEST["id"], $_REQUEST["title"]);
if($_REQUEST["WhatIMustDo"] === "saveTitles")		$clanFunctions->saveTitles($_REQUEST["description"], $_REQUEST["minLvl"], $_REQUEST["titles"]);
if($_REQUEST["WhatIMustDo"] === "wantDeleteClanMember")		$clanFunctions->wantDeleteClanMember($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] === "deleteClanMember")		$clanFunctions->deleteClanMember($_REQUEST["id"]);
?>