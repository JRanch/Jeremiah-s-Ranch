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


class TicketmasterModelOrder extends JModelLegacy{


   function __construct(){
   
      parent::__construct();
 
		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		$this->id = JRequest::getInt('ticketid');
  
   }

	function store($data)
	{
		
		$row =& $this->getTable('order');

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
		

	}					

	function storeWaitingList($data)
	{
		
		$row =& $this->getTable('waitinglist');

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
		

	}					

	
	function _buildContentWhereTicket() {
	
		global $mainframe, $option;
		
		$db					=& JFactory::getDBO();
		
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
			$sql='SELECT a.*, t.ticketname, t.ticketprice
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }   

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			## Pickup the items that will be deleted. We need to update the ticketcounter.
			## First getting the selected item in an object.
			$update = 'SELECT ticketid, COUNT(ticketid) AS total 
					   FROM #__ticketmaster_orders 
					   WHERE orderid IN ( '.$cids.' ) GROUP BY ticketid';
			
		 	$this->_db->setQuery($update);
		 	$this->data = $this->_db->loadObjectList();			
			
			## Now delete the selected tickets.
			$query = 'DELETE FROM #__ticketmaster_orders WHERE orderid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			## Tickets have been removed successfull
			## Now we need to update the totals from the Object earlier this script.
			$k = 0;
			for ($i = 0, $n = count($this->data); $i < $n; $i++ ){
			
				$row        = &$this->data[$i];
				
				## Update the tickets-totals that where removed.
				$query = 'UPDATE #__ticketmaster_tickets'
					. ' SET totaltickets = totaltickets+'.(int) $row->total
					. ' WHERE ticketid = '.$row->ticketid.' ';
				
				## Do the query now	
				$this->_db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}		
				
			$k=1 - $k;
			}			
		
		## OK, tickets removed and tables are updated.
		return true;
		
		}
	}
   function getCount() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT totaltickets FROM #__ticketmaster_tickets WHERE ticketid = '.$this->id.'';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
		
}
?>