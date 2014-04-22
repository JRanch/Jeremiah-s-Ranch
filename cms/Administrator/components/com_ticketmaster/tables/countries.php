<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableCountries extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_country' , 'country_id' , $db );
	}	
}
?>