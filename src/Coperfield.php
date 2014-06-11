<?php

class Coperfield {

	private $pdo;
	private $table;
	private $req;
	private $order;
	private $db_prefix;
	
	public function __construct($host, $dbname, $user, $pass, $db_prefix) {
	
			try {
			  		$this->host 	= 	'mysql:host='.$host.';dbname='.$dbname;
			  		$this->user 	= 	$user;
			  		$this->pass 	= 	$pass;
			  		$this->pdo		=	new PDO($this->host, $this->user, $this->pass);
			} catch ( Exception $e ) {
			  		echo "Connexion à MySQL impossible : ", $e->getMessage();
			  		die();
			}
			
			$this->db_prefix=	$db_prefix;
			$this->req 		=	'';
			$this->order	= 	false;
	}
	
	public function last_id() {
			return $this->pdo->lastInsertId();
	}
	
	public function __get($value) {
			$this->req 		=	'';
			$this->order	=	'';
			$this->table	=	$value;
			return $this;
	}
	
	public function get( $what ) {
			$this->req 	.=	'SELECT '.$what.' FROM '.$this->db_prefix.$this->table.' ';
			return $this;
	}
	
	public function where( $condition ) {
			$this->req 	.=	'WHERE '.$condition.' ';
			return $this;
	}
	
	public function a( $condition ) {
			$this->req 	.=	'AND '.$condition.' ';
			return $this;
	}
	
	public function o( $condition ) {
			$this->req 	.=	'OR '.$condition.' ';
			return $this;
	}
	
	public function group( $by ) {
			$this->req 	.=	'GROUP BY '.$by.' ';
			return $this;
	}
	
	public function order( $champ ) {
			if (!$this->order) {
					$this->order = 	true;
					$this->req 	.=	'ORDER BY ';
			} else	$this->req 	.=	', ';
			$this->req 	.=	$champ.' ';
			return $this;
	}
	
	public function asc() {
			$this->req 	.=	'ASC ';
			return $this;
	}
	
	public function desc() {
			$this->req 	.=	'DESC ';
			return $this;
	} 
	
	public function number( $number ) {
			$this->req 	.=	'LIMIT '.$number.' ';
			return $this;
	}
	
	public function offset( $offset ) {
			$this->req 	.=	'OFFSET '.$offset.' ';
			return $this;
	}
	
	public function affiche() {
			return $this->req;
	}
	
	public function query() {
			$sortie	=	$this->pdo->query( $this->req )->fetchAll(PDO::FETCH_ASSOC);

			$tab	=	array();
			foreach ($sortie as $item) {
					$values	=	array();
					foreach ($item as $key=>$data) {
							$values[$key]	=	utf8_decode($data);
					}
					
					$item	=	new Item($this->table, $values);
					$tab[]	=	$item;
			}
			
			return $tab;
	}
	
	public function aff_order() {
		 	return $this->order;
	}
	
	// Sauvegarde en base de données des éléments de la collection
	// Si existe déjà -> update
	// Sinon -> insert
	public function save( $collection ) {
			foreach ($collection as $objet) {

					if 	($objet->id)
							$this->update( $objet );
					else	$this->insert( $objet );
			}
			return $this;
	}
	
	public function insert($datas) {
			$this->req 	=	'INSERT INTO '.$this->db_prefix.$this->table.' SET ';
			
			$i 	=	1;
			$end=	count($datas);
			$tab_prepare 	=	array();
			foreach ($datas as $key => $data) {
					$this->req 	.=	'`'.$key.'` = :'.$key;
					$tab_prepare[':'.$key]	=	utf8_encode($data);

					if 	($i == $end)
							$this->req 	.=	' ';
					else	$this->req 	.=	', ';
					$i++;
			}
			$this->req 	.=	';';
			$result	=	$this->pdo->prepare( $this->req );
			if ($result->execute( $tab_prepare ))		return true;
			else									return false;
	}
	
	public function update($datas) {	
			$this->req 	=	'UPDATE '.$this->db_prefix.$this->table.' SET ';
			
			$i 	=	1;
			$end=	count($datas);
			$tab_prepare	=	array();
			foreach ($datas as $key => $data) {
					$this->req 	.=	'`'.$key.'` = :'.$key;

					$tab_prepare[':'.$key]	=	utf8_encode($data);
					if 	($key == 'id')
							$where 	=	'WHERE `'.$key.'` = :'.$key.' ';
					
					if 	($i == $end)
							$this->req 	.=	' ';
					else	$this->req 	.=	', ';
					$i++;
			}
			
			$this->req 	.=	$where.' ';
			$this->req 	.=	';';

			$result	=	$this->pdo->prepare($this->req);

			if ($result->execute( $tab_prepare ))	return true;
			else									return false;
	}
	
	public function delete( $id ) {
			$this->req 	=	'DELETE FROM `'.$this->db_prefix.$this->table.'` WHERE `id` = '.$id.';';

			if ($this->pdo->exec( $this->req ))		return true;
			else 									return false;
	}
	
}