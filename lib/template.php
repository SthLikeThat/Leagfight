<?php
require_once "config_class.php";
require_once "checkvalid_class.php";

abstract class Template{
	
	protected $config;
	public $mysqli;
	
	public function __construct() {
		$this->config = new Config();
		$this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
		//$this->mysqli->query("UPDATE  `Opros`.`smgys_mda` SET  `mda` =  '$mda' WHERE  `smgys_mda`.`id` =1;");
	}
	
	public function query($query){
		return $this->mysqli->query($query);
	}
	
	protected function getTemplate($name) {
		$text = file_get_contents($this->config->dir_tmpl.$name.".tpl");
		return str_replace("%address%", $this->config->address, $text);
	}
	
	protected function getReplaceTemplate($sr, $template) {
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
	
	protected function getFileContent($file){
		return file_get_contents($this->config->dir_tmpl."ready/".$file.".tpl");
	}
}
?>