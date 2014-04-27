<?php

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelMyProfile extends JModelLegacy {


   function __construct(){
   
      parent::__construct();
 
		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		$user = & JFactory::getUser();  
  		$this->userid = $user->id;
		
		## constructor for username
		$this->username	= JRequest::getVar('emailaddress');
		
   }

   function getData() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT * FROM #__ticketmaster_clients'
				  .' WHERE userid = '.(int)$this->userid.''; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
   }   
}
?>