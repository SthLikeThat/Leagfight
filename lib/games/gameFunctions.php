<?php
require_once $_SERVER['DOCUMENT_ROOT']."/lib/template.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/database_class.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/config_class.php";

class gameFunctions extends Template{
	
	private $config;
	private $db;
	
	public function __construct() {
		$this->config = new Config();
		$this->db = new DataBase();
		$this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->password, $this->config->db);
		$this->mysqli->query("SET NAMES 'utf8'");
	}
	
	private function query($query){
		return $this->mysqli->query($query);
	}
	
	public function newMemoryPuzzle($cells){
		if($cells % 2 != 0) return false;
		$values = array();
		$tableArray = array();
		$number = 1;
		for($i = 0; $i < $cells * $cells; $i+=2){
			$values[$i] = $number;
			$values[$i+1] = $number;
			$number++;
		}
		
		$newMas = array();
		for( $i = 0; $i < $cells; $i++){
			for( $j = 0; $j < $cells; $j++){
				$rand = rand(0, count($values)-1);
				$newMas[$i][$j] = array("num"=>$values[$rand], "ready"=>" ");
				unset($values[$rand]);
				$values = array_values($values);
			}
		}
		for( $i = 0; $i < $cells; $i++){
			$tableArray["row$i"] = serialize($newMas[$i]);
		}
		$this->db->insert("memoryPuzzle", $tableArray);
		$result = $this->db->getLastID("memoryPuzzle");
		echo $result["id"];
	}
	
	public function getNumberPuzzle($tag, $id){
		$tr = substr($tag,0,1);
		$td = substr($tag,2,2);
		$array = $this->db->getElementOnID("memoryPuzzle", $id);
		$row = unserialize($array["row$tr"]);
		echo $row[$td]["num"];
	}
	
	public function checkNumbersPuzzle($id, $tag1, $tag2 ){
		$tr1 = substr($tag1,0,1);
		$td1 = substr($tag1,2,2);
		$tr2 = substr($tag2,0,1);
		$td2 = substr($tag2,2,2);
		$array = $this->db->getElementOnID("memoryPuzzle", $id);
		$row1 = unserialize($array["row$tr1"]);
		$row2 = unserialize($array["row$tr2"]);
		if( $row1[$td1]["num"] === $row2[$td2]["num"]){
			echo "true";
			if("row$tr1" != "row$tr2"){
				$row1[$td1]["ready"] = "ready";
				$row2[$td2]["ready"] = "ready";
				$this->db->setFieldOnID("memoryPuzzle", $id, "row$tr1", serialize($row1));
				$this->db->setFieldOnID("memoryPuzzle", $id, "row$tr2", serialize($row2));
			}
			else{
				$row1[$td1]["ready"] = "ready";
				$row1[$td2]["ready"] = "ready";
				$this->db->setFieldOnID("memoryPuzzle", $id, "row$tr1", serialize($row1));
			}
		}
		else
			echo "false";
	}
	
	public function giveChanceMemory($id){
		$resultArray = array();
		$array = $this->db->getElementOnID("memoryPuzzle", $id);
		$cells = count(unserialize($array["row0"]));
		for($i = 0; $i < $cells; $i++){
			$row = unserialize($array["row$i"]);
			$resultArray[$i] = json_encode($row);
		}
		$resultArray = json_encode($resultArray);
		echo $resultArray;
	}
	
}
$gameFunctions = new gameFunctions();
if($_REQUEST["WhatIMustDo"] == "newMemoryPuzzle")	$gameFunctions->newMemoryPuzzle($_REQUEST["cells"]);
if($_REQUEST["WhatIMustDo"] == "giveChanceMemory")	$gameFunctions->giveChanceMemory($_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] == "getNumberPuzzle")	$gameFunctions->getNumberPuzzle($_REQUEST["tag"],$_REQUEST["id"]);
if($_REQUEST["WhatIMustDo"] == "checkNumbersPuzzle")	$gameFunctions->checkNumbersPuzzle($_REQUEST["id"], $_REQUEST["tag1"],$_REQUEST["tag2"]);