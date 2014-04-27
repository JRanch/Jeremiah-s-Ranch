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

class ticketmasterModelEvent extends JmodelLegacy{

	function __construct(){
		parent::__construct();
		
		$this->id  = JRequest::getInt('id');
	}

   function getList() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the tickets in list function
			
			$sql = 'SELECT a.*, b.eventname, b.eventdate, b.closingdate, v.*, v.id AS vid, b.ticketcounter 
					FROM #__ticketmaster_tickets as a 
					LEFT JOIN #__ticketmaster_events as b ON a.eventid = b.eventid 
					LEFT JOIN #__ticketmaster_venues AS v ON a.venue = v.id
					WHERE a.ticketid = '.$this->id.'';

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
	
   function getChilds() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the tickets in list function
			
			$sql = 'SELECT a.*
					FROM #__ticketmaster_tickets as a 
					LEFT JOIN #__ticketmaster_events as b ON a.eventid = b.eventid 
					WHERE a.parent = '.$this->id.' 
					AND a.published = 1 
					ORDER BY a.ordering';

		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}	

   function getConfig() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid =1';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}

}
?>