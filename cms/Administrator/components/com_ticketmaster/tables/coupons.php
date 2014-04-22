<?php
defined ('_JEXEC') or die ('Restricted Acces - No Access');


class TableCoupons extends JTable {

	function __construct(&$db) {
		parent::__construct( '#__ticketmaster_coupons' , 'coupon_id' , $db );
	}	
}
?>