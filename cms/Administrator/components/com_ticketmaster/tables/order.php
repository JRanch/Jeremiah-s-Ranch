<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableOrder extends JTable {

	
	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_orders' , 'orderid' , $db );
	}	
}
?>