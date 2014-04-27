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

class ticketmasterModelTicketmaster extends JmodelLegacy{

	function __construct(){
		parent::__construct();
	}

   function getList() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT a.*, COUNT(b.ticketid) AS totalevents
					FROM #__ticketmaster_events AS a, #__ticketmaster_tickets AS b
					WHERE a.eventid = b.eventid
					AND a.published = 1
					AND b.published = 1
					AND b.parent = 0
					GROUP BY a.eventid';

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