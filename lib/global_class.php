<?php
require_once "config_class.php";
require_once "checkvalid_class.php";
require_once "database_class.php";

abstract class GlobalClass {

	private $db;
	private $table_name;
	protected $config;
	protected $valed;
	
	protected function __construct($table_name, $db) {
		$this->db = $db;
		$this->table_name = $table_name;
		$this->config = new Config();
		$this->valid = new CheckValid($db);
	}
	
	protected function add($new_values) {
		return $this->db->insert($this->table_name, $new_values);
	}
	
	public function query($query){
		return $this->db->query($query);
	}
	
	protected function edit($id, $upd_fields) {
		return $this->db->updateOnID($this-table_name, $id, $upd_fields);
	}
	
	public function delete($id) {
		return $this->db->deleteOnID($this->table_name, $id);
	}
	
	protected function getField($field_out, $field_in, $value_in){
		return $this->db->getField($this->table_name, $field_out, $field_in, $value_in);
	}
	
	protected function getFieldOnID($id, $field){
		return $this->db->getFieldOnID($this->table_name, $id, $field);
	}
	
	public function get($id, $exists = false){
		return $this->db->getElementOnID($this->table_name, $id, $exists = false);
	}
	
	public function getElementOnID($table_name, $id, $exists = false) {
		return $this->db->getElementOnID($table_name, $id, $exists = false);
	}
	
	public function getThing($thing){
		return $this->db->getAllonThing($this->table_name,$thing);
	}
	
	protected function setFieldOnID($id, $field, $value) {
		return $this->db->setFieldOnID($this->table_name, $id, $field, $value);
	}
	
	protected function getAllOnField($field, $value, $order = "", $up = true) {
		return $this->db->getAllOnField($this->table_name, $field, $value, $order, $up);
	}
	
	public function getAll($order = "", $up = true) {
		return $this->db->getAll($this->table_name, $order, $up);
	}
	
	public function setField($table_name, $field, $value, $field_in, $value_in) {
		return $this->db->setField($table_name, $field, $value, $field_in, $value_in);
	}
	
	public function putThingOn($id, $field, $value){
		if (!$this->db->existsID("users", $id)) return false;
		return $this->db->setField("users", $field, $value, "id", $id);
	}
	
	public function getCount() {
		return $this->db->getCount($this->table_name);
	}
	
	public function isExists($field, $value) {
		return $this->db->isExists($this->table_name, $field, $value);
	}
	
	public function existsID($table_name, $id) {
		return $this->db->existsID($table_name, $id);
	}
	
	public function innerJoinAll($table_names, $field, $value){
		return $this->db->innerJoinAll($table_names, $field, $value);
	}
	
	public function selectFromTables($table_names, $field, $value){
		return $this->db->electFromTables($table_names, $field, $value);
	}
}

?>