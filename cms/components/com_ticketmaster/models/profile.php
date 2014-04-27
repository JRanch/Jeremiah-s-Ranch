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


class TicketmasterModelProfile extends JModelLegacy {


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

	function saveremark($data) {
		
		$row =& $this->getTable('remarks');

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

	function itemsupdate($userid, $ordercode){

			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET userid = '.(int) $userid
				. ' WHERE ordercode =  '.$ordercode.' ';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			## TRIGGER INVOICING PLUGIN AND RELATED onAfterCheckout
			JPluginHelper::importPlugin('rdmediahelpers');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('OnAfterCheckout', array($vars) );
				
			$db = JFactory::getDBO();
				
			## Check if waiting list is turned on:
			$sql = "SELECT show_waitinglist
					FROM #__ticketmaster_config
					WHERE configid = 1";
			
			$db->setQuery($sql);
			$config = $db->loadObject();
				
			if($config->show_waitinglist == 1) {
			
				$query = 'UPDATE #__ticketmaster_waitinglist'
						. ' SET userid = '.(int) $userid
						. ' WHERE ordercode =  '.$ordercode.' ';
					
											## Do the query now
				$this->_db->setQuery( $query );
			
				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			
				$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
				include_once( $path_include );
				
				$sendconfirmation = new confirmation( (int)$ordercode );
				$sendconfirmation->SendWaitingList();
			
			}			
			
		return true;	
	
	}
	

	function store($data) {
		
		$row =& $this->getTable('visitors');

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

	function sendconfirmation($userid, $password, $username, $email, $ordercode, $name){
		
		## loading the database.
		$db    = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 2";
		 
        $db->setQuery($sql);
        $config = $db->loadObject();
        
		## Getting the desired info from the configuration table
		$sql = 'SELECT name, firstname FROM #__ticketmaster_clients WHERE userid = '.(int)$userid.'';
		 
        $db->setQuery($sql);
        $userdata = $db->loadObject();            
		 
		$message        = str_replace('%%NAME%%', $name, $config->mailbody);
		$message		= str_replace('%%FIRSTNAME%%', $userdata->firstname, $message);

		$link = JURI::base().'index.php?option=com_ticketmaster&controller=validate&task=validate&oc='.$ordercode.'&cid='.$userid;
		 
		$message		= str_replace('%%LINK%%', $link, $message);
		$message		= str_replace('%%USERNAME%%', $username, $message);
		
		if(!$this->userid){
			$message	= str_replace('%%PASSWORD%%', $password, $message);	
		}else{
			$message	= str_replace('%%PASSWORD%%', '*******', $message);
		}	
		
		$code = $ordercode;
		
		## Imaport mail functions:
		jimport( 'joomla.mail.mail' );
							
		## Set the sender of the email:
		$sender[0] = $config->from_email;
		$sender[1] = $config->from_name;					
		## Compile mailer function:			
		$obj = JFactory::getMailer();
		$obj->setSender( $sender );
		$obj->isHTML( true );
		$obj->setBody ( $message );				
		$obj->addRecipient($email);
		## Send blind copy to site admin?
		if ($config->receive_bcc == 1){
			if ($config->reply_to_email != ''){
				$obj->addBCC($mail->reply_to_email);
			}	
		}					
		## Add reply to and subject:					
		$obj->addReplyTo($config->reply_to_email);
		$obj->setSubject($config->mailsubject);
		
		if ($config->published == 1){						
			
			$sent = $obj->Send();						
		}		

		return true;		
		
	}

   function getData() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT * FROM #__ticketmaster_clients'
				  .' WHERE userid = '.$this->userid.''; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
   }   
}
?>