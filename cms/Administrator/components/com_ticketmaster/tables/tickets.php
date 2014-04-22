<?php
defined ('_JEXEC') or die ('Restricted Acces _ No Access');


class TableTickets extends JTable {
	
	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_tickets' , 'ticketid' , $db );
	}	
}
?>