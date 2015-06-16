<?php
require_once "config_class.php";

class CheckValid{

	private $config;
	private $mysqli;
	
	public function __construct($db) {
		$this->config = new Config();
		$this->mysqli = $db->mysqli;
	}
	
	private function query($query){
		return $this->mysqli->query($query);
	}
		
	public function validID($id){
		if(!$this->isIntNumber($id)) return false;
		if($id <= 0) return false;
		return true;
	}
	
	public function validLogin($login){
		if (strlen($login) > 25 || strlen($login) < 3){
			echo "Недопустимая длина логина. ";
			return false;
		}
		if ($this->isContainQuotes($login)){
			echo 'Нельзя использовать кавычки в логине.';
			return false;
		}
		if (preg_match("/^\d*$/", $login)){
			echo "Логин не может состоять только из цифр. ";
			return false;
		}
		return true;
	}
	
	public function checkLogin($login){
		if(!$this->validLogin($login)) return false;
		if(!$this->check_sql($login)) return false;
		if ($this->isContainQuotes($login)) return false;
			$query = "SELECT id FROM `smgys_users` WHERE `login`='".htmlspecialchars($login)."'";
			$result_set = $this->query($query);
			$num = $result_set->num_rows;
			if($num == 0) return true;
			else{
				echo "Пользователь с таким логином уже существует.";
				return false;
			}
			$result_set->close();
	}
	
	public function validEmail($email){
		if($email == ''){
			echo "Заполните поле E-mail. ";
			return false;
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo "Неправильный адрес электронной почты.";
			return false;
		}
		else return true;
	}
	
	public function checkEmail($email){
		if(!$this->validEmail($email)) return false;
		if(!$this->check_sql($email)) return false;
		else{
			$table_name = $this->config->db_prefix."users";
			$query ="SELECT `id` FROM `$table_name` WHERE `email`='$email'";
			$result_set = $this->query($query);
			$num = $result_set->num_rows;
			if($num == 0) return true;
			else{ 
				echo "E-mail уже занят.";
				return false;
			}
			$result_set->close();
		}
		return false;
	}
	
	public function validHash($hash){
		if(!$this->validString($hash, 32, 32)) return false;
		if(!$this->isOnlyLettersAndDigits($hash)){ 
			echo "Что-то не так с паролем.";
			return false;
		}
		return true;
	}
	
	public function checkPassword($password){
		if(strlen($password)<6){
			echo "Слишком маленький пароль. ";
			return false;
		}
		if(!$this->check_sql($password)) return false;
		$a = "/([0-9]{1,})/";
		$b = "/([a-zA-Z]{1,})/";
		if(preg_match($a, $password) and preg_match($b, $password))	return true;
		else{
			echo 'Пароль неудоволетворяет требованиям.';
			return false;
		}
	}
	
	public function checkUser($email, $password){
		if(!$this->validEmail($email)) return false;
		if(!$this->check_sql($email)) return false;
		if(!$this->checkPassword($password)) return false;
		$table_name = $this->config->db_prefix."users";
			$query = "SELECT * FROM `$table_name` WHERE `email`='$email'";
			$result_set = $this->query($query);
			$num = $result_set->num_rows;
			while ($row = $result_set->fetch_assoc()){
				$data[] = $row;
			}
			$result_set->close();
			$passwordget = $data[0]["password"];
			if($num == 1){
				$password = $this->hashPassword($password);
				if($password == $passwordget) return true;
				else{
					echo "Пароль неверный.";
					return false;
				}
			}
			else{ 
				echo "Нет такого пользователя.";
				return false;
			}
	}
	
	public function isIntNumber($number) {
		if(!is_numeric($number)) return false;
		if (!preg_match("/^-?(([1-9][0-9]*|0))$/", $number)) return false;
		return true;
	}
	
	public function isNoNegativeInteger($number) {
		if (!$this->isIntNumber($number)) return false;
		if ($number < 0) return false;
		return true;
	}
	
	public function isOnlyLettersAndDigits($string) {
		if (!is_int($string) && (!is_string($string))) return false;
		if (!preg_match("/([a-zа-я0-9]*)/i", $string)) return false;
		return true;
	}
	
	public function isContainQuotes($string){
		$array = array("\"", "'","`", "&quot", "&apos");
		foreach ($array as $key=>$value){
			if (strpos($string, $value) !== false) return true;
		}
		return false;
	}
	
	public function hashPassword($password) {
		return md5($this->config->pass_prefix .$password);
	}
	
	public function check_sql($text_sql,$max_sql_words=2){
		preg_match_all("/(INFORMATION_SCHEMA|select|alter|table|update|CONCAT|from|where|schema|delete|insert|GROUP BY|UNION)/i",$text_sql, $sqlin);
		if(count($sqlin[0])>=$max_sql_words){
			$fwords="";
			for ($i=0; $i< count($sqlin[0]); $i++) {$fwords.= $sqlin[0][$i]." ";}
			echo "Нельзя использоваться слова SQL";
			return false;
		}
		return true;
	}
	
	public function secureText($text){
		$text = strip_tags($text);
		$text = htmlspecialchars($text);
		return $text;
	}
	
	public function validString($string, $min_length, $max_length) {
		if (!is_string($string)) return false;
		if (strlen($string) < $min_length) return false;
		if (strlen($string) > $max_length) return false;
		return true;
	}
}
?>