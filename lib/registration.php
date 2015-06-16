<?php
require_once "checkvalid_class.php";
require_once "database_class.php";

	$email = $_REQUEST["email"];
	$login = $_REQUEST["login"];
	$password = $_REQUEST["password"];
	
class Rega{
	
	private $valid;
	
	public function __construct() {
		$this->db = new DataBase();
		$this->config = new Config();
		$this->valid = new CheckValid($this->db);
	}
	
	private function query($query){
		return $this->db->query($query);
	}
	
	public function checkReg($email, $login, $password){
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($this->valid->checkLogin($login) && $this->valid->checkEmail($email) && $this->valid->checkPassword($password)){
				$password = $this->valid->hashPassword($password);
				$link = $this->getLink();
				$this->mailMe($email, $link);
				$query = "INSERT INTO `smgys_users` (email, login, password, confirmed) VALUES ('$email', '$login', '$password', '$link')";
				$query2 = "INSERT INTO `smgys_user_settings` () VALUES ()";
				$query3 = "INSERT INTO `smgys_user_statistic` () VALUES ()";
				$query4 = "INSERT INTO `smgys_user_inventory_potions` () VALUES ()";
				$query5 = "INSERT INTO `smgys_user_inventory` () VALUES ()";
				$this->db->mysqli->autocommit(FALSE);
				$this->query($query2);
				$this->query($query3);
				$this->query($query4);
				$this->query($query5);
				$result_set = $this->query($query);
				$this->db->mysqli->commit();
				if($result_set == true){
					echo ("Location: auth.html");
				}
			}
		}
	}
	
	public function getLink(){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;  
		while (strlen($code) < 25) {
				$code .= $chars[mt_rand(0,$clen)];  
		}
		return $code;
	}
	
	public function mailMe($to, $link){
		$subject = "Подтверждение регистрации"; 

		$message = " 
		<html> 
			<head> 
				<title> Подтверждение регистрации на сайте Zadanie</title> 
			</head> 
			<body> 
				<p>Пройдите по ссылки ниже</p> 
				<p><a href='http://zadanie/?view=confirmRegistration&id=$link'>ТЫК</a></p> 
			</body> 
		</html>"; 

		$headers  = "Content-type: text/html; charset=UTF-8 \r\n"; 
		$headers .= "From: e33 <opirus6229078@mail.ru>\r\n"; 

		mail($to, $subject, $message, $headers); 
	}
}
$rega = new Rega();
$rega->checkReg($email, $login, $password);
?>