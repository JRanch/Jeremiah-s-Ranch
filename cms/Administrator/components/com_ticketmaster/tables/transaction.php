<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableTransaction extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_transactions' , 'pid' , $db );
	}	
}
?>