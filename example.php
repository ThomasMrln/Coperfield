<?php

define("DB_PREFIX", "mariages_");
header("Content-Type:text/html;charset=UTF-8;");

include('./src/Coperfield.php');
include('./src/Item.php');

$db	=	new Coperfield();
$result	=	$db->items	->get('*')
						//->where( 'idUser = 14' )->a( 'etat = 1' )->o( 'idClient = 12' )
						//->group( 'id' )
						//->order( 'id' )->asc()
						//->order( 'etat' )->desc()
						//->number(5)->offset(0)
						->query();
print '<pre>';
print_r($result);
print '</pre>';


//print_r( $result );

//$result[0]->delete();

//$item	=	new Item('items', array(
//						'lib'	=>	"Test création bidule",
//						'amount'=>	"12.99"
//					));
					
//print_r($item);

//$item->insert();