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


class TicketmasterModelPayment extends JModelLegacy {


   function __construct(){
   
      parent::__construct();
 
		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
  
   }				

	function _buildContentWhereTicket() {
		
		$db =& JFactory::getDBO();
		
		$where = array();

		$where[] = 'a.eventid = e.eventid';
		$where[] = 'a.ticketid = t.ticketid';
		$where[] = 'a.ordercode = '.(int)$this->ordercode;


		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getData() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			$where		= $this->_buildContentWhereTicket();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, t.ticketname, t.ticketprice, t.starttime, t.ticketdate, e.eventname
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where.' AND a.paid != 1'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }   

   function getPrice() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			$where		= $this->_buildContentWhereTicket();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, SUM(t.ticketprice) AS total
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where.' GROUP BY a.ordercode'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
   }  

   function getMsg() {
   
		$db = JFactory::getDBO();
	
		## Making the query for showing all the cars in list function
		$sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid =103';
	 
		$db->setQuery($sql);
		$data = $db->loadObject();
		
		return $data;
    }

   function getConfig() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
		 	$db->setQuery($sql);
		 	$data = $db->loadObject();
			
			if($data->show_waitinglist == 1) {
				
				## Check if waiting list is turned on:
				$sql = 'SELECT COUNT(id) AS total 
						FROM #__ticketmaster_waitinglist 
						WHERE sent = 0
						AND ordercode = '.(int)$this->ordercode.'';
				 
				$db->setQuery($sql);
				$waiters = $db->loadObject();				
				
				if ($waiters->total != 0){
					
					$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
					include_once( $path_include );

					$sendconfirmation = new confirmation( (int)$this->ordercode );  
					$sendconfirmation->SendWaitingList();					
				}
			
			}
			
		}
		return $data;
	}

   function getTos() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid = 50';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();

		}
		return $this->data;
	}
	
   function getExtData() {
   		
		## this data is for PRO only
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$sql='SELECT c.id, c.orderid, c.seatid, c.row_name
				  FROM #__ticketmaster_orders AS a, #__ticketmaster_coords AS c
				  WHERE a.orderid = c.orderid
				  AND a.ordercode = '.(int)$this->ordercode;

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }  

   function getDataCheck() {
   		
		## this data is for PRO only
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$sql='SELECT COUNT(orderid) AS total 
				  FROM #__ticketmaster_orders
				  WHERE requires_seat = 1
				  AND seat_sector = 0
				  AND ordercode = '.(int)$this->ordercode;

		 	$db->setQuery($sql);
			
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }  
   
   function getDataFailed() {
   		
		## this data is for PRO only
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$sql='SELECT a.eventid, a.ticketid, t.ticketname, e.eventname, t.parent, ext.seat_chart
				  FROM #__ticketmaster_orders AS a, #__ticketmaster_tickets AS t, #__ticketmaster_events AS e, #__ticketmaster_tickets_ext AS ext
				  WHERE a.ticketid = t.ticketid
				  AND a.eventid = e.eventid
				  AND a.requires_seat = 1
				  AND a.seat_sector = 0
				  AND t.parent = ext.ticketid
				  AND a.ordercode = '.(int)$this->ordercode.' 
				  GROUP BY t.ticketid';

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }  	
		
}
?>