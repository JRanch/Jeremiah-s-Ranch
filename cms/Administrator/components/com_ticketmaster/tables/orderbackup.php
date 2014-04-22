<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableOrderBackup extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_orders_bak' , 'orderid' , $db );
	}	
}
?>