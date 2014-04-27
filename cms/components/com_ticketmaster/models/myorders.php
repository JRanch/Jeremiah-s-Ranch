<?php

/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelMyOrders extends JModelLegacy {


   function __construct(){
   
      parent::__construct();
 
		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		$user = & JFactory::getUser();  
  		$this->userid = $user->id;
		
   }

   function getItems() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT o.*, SUM(ticketprice) as totalprice
				  FROM #__ticketmaster_orders AS o, #__ticketmaster_tickets AS t 
				  WHERE o.userid = '.(int)$this->userid.'
				  AND o.ticketid = t.ticketid
				  GROUP BY o.ordercode 
				  ORDER BY o.orderdate ASC'; 

		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   } 
   
   function getConfig() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT * FROM #__ticketmaster_config WHERE configid = 1'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
   }      
}
?>