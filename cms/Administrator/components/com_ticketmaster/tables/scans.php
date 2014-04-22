<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableScans extends JTable {

	
	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_scans' , 'scanid' , $db );
	}	
}
?>