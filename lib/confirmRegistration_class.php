<?php
require_once "checkvalid_class.php";
require_once "database_class.php";
	
class Confirm{
	
	public function __construct($db) {
		$this->db = $db;
		$this->user = $this->db->getFieldsBetter( "users", "confirmed", htmlspecialchars($_GET["id"]), array("id", "confirmed"));
        $this->user = $this->user[0];
    }
	
	public function getContent(){
		if($this->user){
			$hash = md5($this->generateCode());
			$this->db->update("users", array("user_hash" => $hash, "confirmed" => "1"), "`id` = ".$this->user["id"]."");
			session_start();
			header("Location: auth.html");
		}
		else{
			die("Неправильный хеш");
		}
	}
	
	private function generateCode($length=8) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  
		while (strlen($code) < $length) {
				$code .= $chars[mt_rand(0,$clen)];  
		}
		return $code;
	}
}
?>