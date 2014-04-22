<?php
/****************************************************************
 * @version			Ticketmaster 2.5.5
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class ticketmasterModelVenues extends JmodelLegacy
{
	function __construct(){
		parent::__construct();
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 		
	}

   function getList() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * 
					FROM #__ticketmaster_venues';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}

   function getData() {
   		
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		## Getting the information for just one car. 
		## The ID is prvided by the URL
		$sql = 'SELECT * FROM #__ticketmaster_venues
				WHERE id = '. (int) $this->id.'  ';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObject();
      }
      return $this->data;
   }   

	function publish($cid = array(), $publish = 1) {
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_venues'
				. ' SET published = '.(int) $publish
				. ' WHERE id IN ( '.$cids.' )';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function store($data) {
		
		$row =& $this->getTable();

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

	function remove($cid = array()){
			
			$db = JFactory::getDBO();
			
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			## Get all tickets affected with this party/event.
			$sql = 'SELECT orderid FROM #__ticketmaster_venues WHERE tid IN ( '.$cids.' )';
			
			$db->setQuery( $sql );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			## Getting the ticket id's
			$data = $db->loadObjectList();
			
			## Loop the ticketnumbers for deletion
			for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = &$data[$i];
							
				$this->_deleteTicket($row->orderid);
	
			}
			
			## Delete all tickets in the event list.
			$query = 'DELETE FROM #__ticketmaster_events WHERE eventid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			## Deleting all tickets in ticket table..
			$query = 'DELETE FROM #__ticketmaster_tickets WHERE eventid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			## Deleting all tickets in ordertable table..
			$query = 'DELETE FROM #__ticketmaster_orders WHERE eventid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		
		return true;
		

	}
}
?>