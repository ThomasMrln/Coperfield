<?php

class Item {

		private $table;
		private $datas 	=	array();
		
		public function __construct( $table, array $datas = array()) {
				$this->set_table( $table );
				foreach ($datas as $key	=>	$value) {
						$this->$key	=	$value;
				}
		}
		
		public function __get($name) {
				return $this->datas[$name];
		}
		
		public function __set($name, $value) {
				$this->datas[$name]	=	$value;
		}
		
		public function __isset($name) {
	        	if (array_key_exists($name, $this->datas))	return true;
				else										return false;
	    }
		
		public function set_table($value) {
				$this->table	=	htmlspecialchars(trim($value));
		}
		
		public function get_table() {
				return $this->table;
		}
		
		
		public function insert() {
				global $cop;
				$table	=	$this->get_table();
				
				$cop->$table->insert($this->datas);
		}
		
		public function update() {
				global $cop;
				if ($this->id == "") 
						trigger_error('You must have a real item imported from the database for the update.');
						
				$table	=	$this->get_table();
				
				if ($cop->$table->update( $this->datas )) 	return true;
				else 										return false;
		}
		
		public function delete() {
				global $cop;
				
				if 	($this->id == "")
						trigger_error("The item which you want delete isn't save in database.");
						
				$table	=	$this->get_table();
				
				$cop->$table->delete( $this->datas );
		}
}
