<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableVisitors extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_clients' , 'clientid' , $db );
	}	
}
?>