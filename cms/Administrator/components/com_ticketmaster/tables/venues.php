<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableVenues extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_venues' , 'id' , $db );
	}	
}
?>