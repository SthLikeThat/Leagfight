<?php
require_once "database_class.php";
require_once "global_class.php";
require_once "config_class.php";
require_once "checkvalid_class.php";

class Auth {
	
	private $db;
	
	public function __construct($mda) {
		if($mda == 0)
			$this->db = new DataBase();
		else 
			$this->db = $mda;
		$this->config = new Config();
		$this->valid = new CheckValid($this->db);
		$this->mysqli = $this->db->mysqli;
	}
	
	private function query($query){
		return $this->db->query($query);
	}
	
	public function checkAuth($email, $password){
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($this->valid->checkUser($email, $password)){
				$hash = md5($this->generateCode());
				$user = $this->db->getAllOnField("accounts", "email", $email, "id_account", "");
				if($user["confirmed"] !== "1")
					die("Подтвердите регистрацию!");
				$user_id = $user["id"];
				$this->db->update("accounts", array("user_hash"=>$hash), "`email` = '$email'");
				session_start();
				$_SESSION["id_account"] = $user["id_account"];
				$_SESSION["hash"] = $hash;
				die("OK");
			}
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
if($_REQUEST["WhatIMustDo"] == "checkAuth"){
	$auth = new Auth(0);
	$auth->checkAuth($_REQUEST["email"], $_REQUEST["password"]);
}
?>