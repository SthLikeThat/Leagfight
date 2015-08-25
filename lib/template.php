<?php
require_once "config_class.php";
require_once "checkvalid_class.php";

abstract class Template{
	
	public $config;
	public $mysqli;
	
	public function __construct() {
		$this->config = new Config();
		$this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
	}
	
	public function query($query){
		return $this->mysqli->query($query);
	}
	
	protected function getTemplate($name) {
		if(file_exists ($_SERVER['DOCUMENT_ROOT'].$this->config->dir_tmpl.$name.".tpl")){
			$text = file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->config->dir_tmpl.$name.".tpl");
			return str_replace("%address%", $this->config->address, $text);
		}
		else{
			echo "<script>alert('Не найдена tpl - $name');</script>";
			return false;
		}
			
	}
	
	public function getReplaceTemplate($sr, $template) {
		return $this->getReplaceContent($sr, $this->getTemplate($template));
	}
	
	private function getReplaceContent($sr, $content) {
		$search = array();
		$replace = array();
		$i = 0;
		foreach ($sr as $key => $value) {
			$search[$i] = "%$key%";
			$replace[$i] = $value;
			$i++;
		}
		return str_replace($search, $replace, $content);
	}
	
	public function getFileContent($file){
		if(file_exists ($_SERVER['DOCUMENT_ROOT'].$this->config->dir_tmpl."ready/".$file.".tpl"))
			return file_get_contents($_SERVER['DOCUMENT_ROOT'].$this->config->dir_tmpl."ready/".$file.".tpl");
		else{
			echo "<script>alert('Не найдена tpl - $file');</script>";
			return false;
		}
	}
}
?>