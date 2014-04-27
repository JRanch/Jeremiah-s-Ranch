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


class TicketmasterModelValidate extends JModelLegacy {


   function __construct(){
   
      parent::__construct();

  
   }	 				

	function updateWaitingList($oc) {
	
		## Update the tickets-totals that where removed.
		$query = 'UPDATE #__ticketmaster_waitinglist 
				  SET confirmed = 1 
				  WHERE ordercode = '.(int)$oc.' ';
		
		## Do the query now	
		$this->_db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}

	function update($oc, $id) {
	
		## Update the tickets-totals that where removed.
		$query = 'UPDATE #__ticketmaster_orders SET published = 1 WHERE ordercode = '.(int)$oc.' ';
		
		## Do the query now	
		$this->_db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$this->_updateClient($oc, $id);		
	}
	
	function _updateClient($eid, $id){
			
			$mainframe = JFactory::getApplication();
			
			## Include the confirmation class to sent the tickets. 
			$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
			$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'confirmation.php';
			
			## Check if the override is there.
			if (file_exists($override)) {
				## Yes, now we use it.
				require_once($override);
			} else {
				## No, use the standard
				require_once($path);
			}	
			
			if(isset($eid)) {  
			
				$sendconfirmation = new confirmation( (int)$eid );  
				$sendconfirmation->doConfirm();
				$sendconfirmation->doSend();
			
			}  			
			
			$mainframe->redirect('index.php', JText::_('COM_TICKETMASTER_CONFIRM_OK') );	
	
	}	
	
}
?>