<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableEvents extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_events' , 'eventid' , $db );
	}	
}
?>