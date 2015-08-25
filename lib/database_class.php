<?php
require_once "template.php";
require_once "checkvalid_class.php";
require_once "ancillaryClass.php";

class DataBase extends Template{
	
	public $valid;
	public $ancillary;
	
	public function __construct() {
		parent::__construct();
		$this->valid = new CheckValid($this);
		$this->ancillary = new Ancillary($this);
	}
	
	protected function checkAll($values){
		foreach($values as $value){
			if(is_array($value))
				$this->checkAll($value);
			else{
				if(!$this->valid->check_sql($value))
					return false;
				$value = $this->mysqli->real_escape_string($value);
			}
		}
		return $values;
	}
	
	public function select($table_name, $fields, $where ="", $order = "", $up = true, $limit = ""){
		$fields = $this->checkAll($fields);
		for ($i=0; $i<count($fields);$i++){
			if((strpos($fields[$i], "(") === false) && ($fields[$i] !="*")){
			$fields[$i] = "`".$fields[$i]."`";
			}
		}
		$fields = implode(",", $fields);
		$table_name = $this->config->db_prefix.$table_name;
		if (!$order) $order = "ORDER BY `id`";
		else{
			if ($order != "RAND()") {
				$order = "ORDER BY `$order`";
				if (!$up) $order .= " DESC";
		}
		else $order = "ORDER BY $order";
		}
		if ($limit) $limit = "LIMIT $limit";
		if ($where) $query = "SELECT $fields FROM $table_name WHERE $where $order $limit";
		else $query = "SELECT $fields FROM $table_name $order $limit";
		$result_set = $this->query($query);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		return $data;
	}
	
	public function getLastID($table_name) {
			$query = "SELECT `id` FROM smgys_$table_name ORDER BY id DESC LIMIT 1";
			$result_set = $this->query($query);
			if (!$result_set) return false;
			$i = 0;
			while ($row = $result_set->fetch_assoc()){
				$data[$i] = $row;
				$i++;
			}
			$result_set->close();
			return $data[0];
		}
	
	public function insert($table_name, $new_values){
		$new_values = $this->checkAll($new_values);
		$table_name = $this->config->db_prefix.$table_name;
		$query = "INSERT INTO $table_name (";
		foreach ($new_values as $field =>$value) $query .="`".$field."`,";
		$query = substr($query, 0, -1);
		$query .= ") VALUES (";
		foreach ($new_values as $value) 
			$query .= "'".addslashes($value)."',";
		$query = substr($query, 0, -1);
		$query .= ")";
		return $this->query($query);
	}
	
	public function update($table_name, $upd_fields, $where) {
		$upd_fields = $this->checkAll($upd_fields);
		$table_name = $this->config->db_prefix.$table_name;
		$query = " UPDATE $table_name SET ";
		foreach ($upd_fields as $field => $value) $query .= "`$field` = '".addslashes($value)."',";
		$query = substr($query, 0, -1);
		if ($where) {
			$query .= " WHERE $where ;";
				return $this->query($query);
		}
		else return false;
	}
	
	public function delete($table_name, $where ="") {
		$table_name = $this->config->db_prefix.$table_name;
		if ($where) {
			$query = "DELETE FROM $table_name WHERE $where";
			return $this->query($query);
		}
		else return false;
	}
	
	public function getField($table_name, $field_out, $field_in, $value_in) {
		$data = $this->select($table_name, array($field_out), "`$field_in`='".addslashes($value_in)."'");
		if (count($data) !=1) return false;
		return $data[0][$field_out];
	}
	
	public function getElementOnID($table_name, $id, $exists = false) {
		if( !$exists){
			if (!$this->existsID($table_name, $id))
				return false;
		}
		$arr = $this->select($table_name, array("*"), "`id` = '".$id."'", "", "", 1);
		return $arr[0];
	}
		
	public function getAllonThing($table_name, $thing) {
		$arr = $this->select($table_name, array("*"), "`thing` = '".$thing."'", "", "");
		return $arr;
	}
		
	public function getAllOnField($table_name, $field, $value, $order, $up) {
		$result = $this->select($table_name, array("*"),  "`$field`='".addslashes($value)."'", $order, $up, "");
		return $result[0];
	}
	
	public function getAll($table_name, $order, $up) {
		return $this->select($table_name, array("*"), "", $order, $up);
	}	
	
	public function setField($table_name, $field, $value, $field_in, $value_in) {
		return $this->update($table_name, array($field=>$value), "`$field_in` = '".addslashes($value_in)."'");
	}
	
	public function setFieldOnID($table_name, $id, $field, $value) {
		if (!$this->existsID($table_name, $id)) return false;
		return $this->setField($table_name, $field, $value, "id", $id);
	}
	
	public function getCount($table_name) {
		$data = $this->select($table_name, array("COUNT (`id`)"));
		return $data[0]["COUNT (`id`)"];
	}
	
	public function isExists($table_name, $field, $value) {
		$data = $this->select($table_name, array("id"), "`$field` = '".addslashes($value)."'");
		if (count($data) ===0) return false;
		return true;
	}
	
	public function existsID($table_name, $id) {
			if(!$this->valid->validID($id)) return false;
			$data = $this->select($table_name, array("id"), "`id` = '".addslashes($id)."'");
			if (count($data) === 0) return false;
			return true;
	}
	
	public function innerJoinAll($table_names, $field, $value){
		$prefix = $this->config->db_prefix;
		$text = "SELECT * FROM `$prefix$table_names[0]`";
		if(count($table_names) > 1){
			$count = count($table_names);
			for($i = 1; $i < $count; $i++){
				$text .= " INNER JOIN `$prefix$table_names[$i]` ON $prefix$table_names[0].$field = $prefix$table_names[$i].$field";
			}
		}
		else return false;
		$result_set = $this->query($text);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		
		return $data[0];
	}
	
	public function getForManyFields($table_name, $field, $allfiled){
		$table_name = $this->config->db_prefix.$table_name;
		$text = "SELECT * FROM `$table_name` WHERE ";
		$count = count($allfiled);
		foreach($allfiled as $tag){
			$text .= "$field = '".$tag."' ||";
		}
		$text = substr($text, 0, -2);
		$result_set = $this->query($text);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		
		return $data;
	}
	
	public function selectIN($table_name, $field, $values){
		$table_name = $this->config->db_prefix.$table_name;
		$text = "SELECT * FROM `$table_name` WHERE ";
		$count = count($allfiled);
		$text .= "`$field` IN (";
		foreach($values as $tag){
			$text .=  $tag.", ";
		}
		$text = substr($text, 0, -2);
		$text .= ")";
		$result_set = $this->query($text);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		
		return $data;
	}
	
	public function selectFromTables($table_names, $field, $value){
		$prefix = $this->config->db_prefix;
		$text = "SELECT * FROM `$prefix$table_names[0]`";
		if(count($table_names) > 1){
			for($i = 1; $i < count($table_names); $i++){
				$text .= ", `$prefix$table_names[$i]`";
			}
			$text .= " WHERE";
			for($i = 0; $i < count($table_names); $i++){
				$text .= " $prefix$table_names[$i].$field = $value &&";
			}
			$text = substr($text, 0, -2);
			$text .= "LIMIT 1";
			
		}
		else return false;
		$result_set = $this->query($text);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		
		return $data[0];
	}
	
	public function getFieldsBetter( $table_name, $field_in, $value_in, $fields, $sign = "="){
		$table_name = $this->config->db_prefix.$table_name;
		$text = "SELECT ";
		foreach($fields as $key => $value)
			$text .= $value." , ";
		$text = substr($text, 0, -2);
		$text .= "FROM $table_name WHERE `$field_in` $sign '$value_in'";
		$result_set = $this->query($text);
		if (!$result_set) return false;
		$i = 0;
		while ($row = $result_set->fetch_assoc()){
			$data[$i] = $row;
			$i++;
		}
		$result_set->close();
		return $data;
	}
	
	public function nofound(){
		require_once "../notfound.php";
		$notfound = new NotFoundContent($this);
		return $notfound->getCenter();
	}
}
?>