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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class TicketmasterModelVenue extends JmodelLegacy{

	function __construct(){
		parent::__construct();

		$mainframe =& JFactory::getApplication();
		$config = JFactory::getConfig();
			
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 
		
		$this->id = JRequest::getInt('id', 0);			
	}

   function getConfig() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1 ';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
	
   function getData() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT *
					FROM #__ticketmaster_venues
					WHERE id = '.(int)$this->id.''; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
	
   function getItems() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT v.*, t.ticketname, t.ticketdate, t.ticketprice, t.ticketid AS tid
					FROM #__ticketmaster_venues as v, #__ticketmaster_tickets as t
					WHERE t.parent = 0
					AND v.id = t.venue
					AND v.id = '.(int)$this->id.'
					AND t.published = 1		
					ORDER BY t.ticketdate ASC'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}	
	
}
?>