<?php
require_once "global_class.php";

class User extends GlobalClass {

	public function __construct($db) {
		parent:: __construct("users", $db);
	}
	
	public function addUser($login, $password, $regdate) {
		if(!$this->checkValid($login, $password, $regdate)) return false;
		return $this->add(array("login" => $login, "password" => $password, "regdate" => $regdate));
	}
	
	public function editUser($id, $login,$password, $regdate) {
		if(!$this->checkValid($login, $password, $regdate)) return false;
		return $this->edit($id, array("login" => $login, "password" => $password, "regdate"=>$regdate));
	}
	
	public function isExist($login) {
		return $this->isExists("login", $login);
	}
	
	public function checkUser($login, $password) {
		$user = $this->getUserOnLogin($login);
		if (!$user) return false;
		return $user["password"] === $password;
	}
	
	public function getUserOnLogin($email) {
		$id = $this->getField("id", "email", $email);
		return $this->get($id);
	}
	
	public function checkValid($login, $password, $regdate) {
		if(!$this->valid->validLogin($login)) return false;
		if(!$this->valid->validHash($password)) return false;
		if(!$rhis->valid->validEmail($email)) return false;
		if(!$this->valid->validTimeStamp($regdate)) return false;
		return true;
	}
}
?>