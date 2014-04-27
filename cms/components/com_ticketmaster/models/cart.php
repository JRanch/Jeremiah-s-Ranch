<?php
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright � 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelCart extends JModelLegacy {


   function __construct(){
   
      parent::__construct();
 
		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
  
   }				

	function _buildContentWhereTicket() {
	
		global $mainframe, $option;
		
		$db	= JFactory::getDBO();
		
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
			$sql='SELECT a.*, t.ticketname, t.ticketprice, t.ticketdate, t.starttime, e.eventname
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where.' AND paid != 1'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }   
   
   function getWaiters() {
   		
		if (empty($this->_data)) {

		 	$db    = JFactory::getDBO();
			$where = $this->_buildContentWhereTicket();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, t.ticketname, t.ticketprice, t.ticketdate, t.starttime, e.eventname
			      FROM #__ticketmaster_waitinglist AS a, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where.' AND a.processed = 0'; 
		 
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

   function getConfig() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
	
   function getRemarks() {
		
		$db = JFactory::getDBO();
	
		## Making the query for showing all the cars in list function
		$sql = 'SELECT id FROM #__ticketmaster_remarks WHERE ordercode = "'.$this->ordercode.'"';
	 
		$db->setQuery($sql);
		$this->data = $db->loadObject();
		

		return $this->data;
	}	
	
   function storeRemark($data) {
			
		$row = $this->getTable('remarks');

		## Bind the form fields to the web link table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		## Make sure the web link table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		} 

		## Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;

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
				  
			$sql='SELECT a.ordercode, t.ticketname, t.parent, t.ticketid, ext.*
				FROM #__ticketmaster_orders AS a, #__ticketmaster_tickets AS t, #__ticketmaster_tickets_ext AS ext
				WHERE a.requires_seat = 1 
				AND a.seat_sector = 0
				AND (a.ticketid = ext.ticketid OR t.parent = ext.ticketid)
				AND ext.seat_chart != ""
				AND a.ticketid = t.ticketid
				AND a.ordercode = '.(int)$this->ordercode.' GROUP BY a.ticketid';

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }    
		
}
?>