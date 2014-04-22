<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableRemarks extends JTable {
	
	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_remarks' , 'id' , $db );
	}	
}
?>