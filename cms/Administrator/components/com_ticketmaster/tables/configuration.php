<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');

class TableConfiguration extends JTable {


	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_config' , 'configid' , $db );
	}	
}
?>