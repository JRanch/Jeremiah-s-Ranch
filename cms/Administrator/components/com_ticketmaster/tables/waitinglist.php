<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableWaitingList extends JTable {

	
	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_waitinglist' , 'id' , $db );
	}	
}
?>