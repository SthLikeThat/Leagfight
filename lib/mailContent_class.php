<?php
require_once "modules_class.php";
require_once "database_class.php";
require_once "mail_class.php";

class mailContent extends Modules {

	protected $db;
	protected $user;
	
	public function __construct($db) {
		parent::__construct($db);
		$this->mail = new mail($db);
		$this->allMail = $this->mail->getMail($this->user["id"]);
		$this->mailUsers = $this->mail->getMailUsers($this->user["id"]);
	}
	
	protected function getCenter() {
		$sr["content"] = $this->getMail();
		$sr["menuItem"] = $this->getFileContent("mailMenu");
		$sr["onOff"] = "off";
		return $this->getReplaceTemplate($sr, "mail");
	}
	
	private function getMail(){
			$typeMessage = $this->data["type"];
			$message = $this->allMail;
			$mailUsers = $this->mailUsers;
			$count = count($mailUsers);
			if($count > 30) $count = 30;
			for ($i=0;$i < $count;$i++){
				$sr = $this->getPageMail($i,$typeMessage);
				if($sr != 0)	$text .= $this->getReplaceTemplate($sr, "mailItem");
			}
		return $text;
	}
	
	private function getPageMail($x,$typeMessage){
			$messageAll = $this->allMail;
			$mailUsers = $this->mailUsers;
			if(!$mailUsers) return false;
			$y = 0;
			for($i=0;$i<count($messageAll);$i++){
				if($messageAll[$i]["idDialog"] == $mailUsers[$x]){
					$massMails[$y] = $messageAll[$i];
					$y++;
				}
			}
				$message = $massMails;
				$i = 0;
				if($typeMessage == 0)  $typeMessage = $message[$i]["type"];
				if($message[$i]["type"] == $typeMessage){
				$idSender = $message[$i]["idSender"];
				if($message[$i]["idSender"] == $this->user["id"]) $idSender = $message[$i]["idUser"];
				if($message[$i]["idSender"] != 0){
					$sender = unserialize($message[$i]["extra"]);
					$sr["sender"] = $sender["login"];
					$sr["avatar"] = $sender["avatar"];
				}
				else{
					$sr["sender"] = "";
					$sr["avatar"] = 1;
				}
				$sr["textMessage"] = $message[$i]["textMessage"];
				
				//лог боя
				if($idSender == 0 and $message[$i]["type"] == 2){
					$sr["messageId"] = "";
					$extra = unserialize($message[$i]["extra"]);
					$resources = unserialize($extra["resources"]);
					foreach($resources as $name => $value){
						if($value != 0){
							$sr["textMessage"] .= '<img src="images/'.$name.'.png" height="20" style="float:none;"/> '.$value." ";
						}
					}
					if($extra["winner"] == $this->user["id"]) 
						$sr["styleBackground"] = "winnerMail";
					else	$sr["styleBackground"] = "loserMail";
				}
				else{
					$sr["styleBackground"] = "";
					$sr["messageId"] = $id;
				}
				$sr["title"] = $message[$i]["title"];
				$messageTime = $message[$i]["time"];
				$date = date("Y").date("m").date("d");
				if($date > substr($message[$i]["date"],0,8))
				$messageTime = $message[$i]["beautifulDate"];
				$sr["time"] = $messageTime;
				if($message[$i]["textMessage"] == "")	$sr["textMessage"] = $mailUsers[$x];
				$sr["type"] = $message[$i]["type"];
				$sr["id"] = $message[$i]["id"];
				$sr["idSender"] = $idSender;
				$sr["who"] = $idSender;
				$length = strlen($message[$i]["textMessage"]);
				$padding = 72;
				if($length > 162)	$padding += 15;
				if($length > 320)	$padding += 17;
				$sr["padding"] = $padding;
				
					for($y = $i; $y >= 0; $y--){
						if($message[$i]["idSender"] != 0 and $message[$i]["idSender"] == $message[$y]["idSender"] and $i!= $y or 
						$message[$i]["idSender"] != 0 and $message[$i]["idSender"] == $this->user["id"] and $i!= $y){
						
							$sr["onOff"] = "off";
							$sr["counter"] = $i;
							break;
						}
						else{
							$sr["counter"] = $y;
							$sr["onOff"] = "on";
						}
					}
					if($message[$i]["type"] == 1)
						$sr["loadMore"] = "on";
					else $sr["loadMore"] = "off";
				}
				return $sr;
	}
}
?>