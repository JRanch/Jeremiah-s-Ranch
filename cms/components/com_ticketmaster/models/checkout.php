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


class TicketmasterModelCheckout extends JModelLegacy {


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
			
		 	$this->data = $db->loadObject();
		}
		return $this->data;
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

	function updateCart($data) {
		
		$db = JFactory::getDBO();
		
		$couponcode = $data['couponcode'];
		$replace = array(":", "/", "\\", "@", "#", "@", "!", "$", "?");
		$couponcode = str_replace($replace, "", $couponcode);	
		$couponcode = strtoupper($couponcode);	
		
		$sql = 'SELECT * FROM #__ticketmaster_coupons WHERE coupon_code = "'.$couponcode.'" AND published = 1';					  
		$db->setQuery($sql);
		$coupon = $db->loadObject();
		
		if(!$coupon->coupon_id) {
			return false;
		}
		
		## If coupon is limited
		if($coupon->coupon_limit != 0){
			
			## Check if we have reached the limit already.	
			if($coupon->coupon_limit == $coupon->coupon_used){
				return false;
			}
			
		}
		
		## Get date for now
		$today = date('Y-m-d');
		
		if($coupon->coupon_valid_to	>= $today){			
		
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_orders 
					  SET coupon = "'.$coupon->coupon_code.'"
					  WHERE ordercode = '.(int)$this->ordercode.' ';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			## Update the tickets-totals that where removed.
			$query = "UPDATE #__ticketmaster_coupons 
					  SET coupon_used = coupon_used+1 WHERE coupon_id = '$coupon->coupon_id' ";
			
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			
			## Starting a session.
			$session =& JFactory::getSession();
			## Gettig the orderid if there is one.
			$coupon = $session->get('coupon');
			## If there none.. Create a session for the order process.
			if ($coupon == ''){
				## Setting the coupon.
				$session->set('coupon', $couponcode);
			}				
			
		}
		
		return true;	
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
		 
		$message        = str_replace('%%NAME%%', $userdata->name, $config->mailbody);
		$message		= str_replace('%%FIRSTNAME%%', $userdata->firstname, $message);

		$link           = JURI::base().'index.php?option=com_ticketmaster&controller=validate&task=validate&oc='.$ordercode.'&cid='.$userid;
		 
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
				$obj->addRecipient($sender);
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

	public function getUserData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_users');

			## Override the base user data with any data in the session.
			$temp = (array)$app->getUserState('com_ticketmaster.registration', array());
			foreach ($temp as $k => $v) {
				$this->data->$k = $v;
			}

			## Get the groups the user should be added to after registration.
			$this->data->groups = isset($this->data->groups) ? array_unique($this->data->groups) : array();

			## Get the default new user group, Registered if not specified.
			$system	= $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			## Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			##Get the dispatcher and load the users plugins.
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			## Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_users.registration', $this->data));

			## Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	public function register($temp, $configuration=array())
	{
		$config = JFactory::getConfig();
		$params = JComponentHelper::getParams('com_users');

		## Initialise the table with JUser.
		$user = new JUser;
		$data = (array)$this->getUserData();

		## Merge in the registration data.
		foreach ($temp as $k => $v) {
			$data[$k] = $v;
		}
		
		## If we want autologin this is needed.
		$userlogin['username'] = $data[username];
		$userlogin['password'] = $data[password];
		
		$useractivation = $params->get('useractivation');
		
		## Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			
			jimport('joomla.user.helper');
			$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
			
			$db = JFactory::getDBO();
			
			$sql = "SELECT activation_email, companyname 
					FROM #__ticketmaster_config 
					WHERE configid = 1";
			 
			$db->setQuery($sql);
			$configuration = $db->loadObject();

			## We need to send an email to let the user activate their account.
			## Getting the desired info from the configuration table
			$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$configuration->activation_email."";
			 
			$db->setQuery($sql);
			$config = $db->loadObject();
			
			$actvivation = JURI::base().'index.php?option=com_ticketmaster&controller=checkout&task=activate&token='.$data['activation'];
			 
			$message     = str_replace('%%ACTIVATION_CODE%%', $actvivation, $config->mailbody);
			$message	 = str_replace('%%NAME%%', $data['name'], $message);
			$message	 = str_replace('%%FIRSTNAME%%', $data['firstname'], $message);
			$message	 = str_replace('%%COMPANY%%', $configuration->companyname, $message);
			$message	 = str_replace('%%EMAIL%%', $data['email'], $message);
			$message	 = str_replace('%%USERNAME%%', $userlogin['username'], $message);
			$message	 = str_replace('%%PASSWORD%%', $userlogin['password'], $message);	
			$message	 = str_replace('%%CODE_ONLY%%', $data['activation'], $message);

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
			$obj->addRecipient($data['email']);
			## Send blind copy to site admin?
			if ($config->receive_bcc == 1){
				if ($config->reply_to_email != ''){
					$obj->addRecipient($mail->reply_to_email);
				}	
			}					
			## Add reply to and subject:					
			$obj->addReplyTo($config->reply_to_email);
			$obj->setSubject($config->mailsubject);
			
			if ($config->published == 1){						
				
				$sent = $obj->Send();						
			}	
			
		}else{
			$data['activation'] = '';
			$data['block'] = 0;		
		}

		## Bind the data.
		if (!$user->bind($data)) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}

		## Load the users plugin group.
		JPluginHelper::importPlugin('user');

		## Store the data.
		$user->save();
		
		## getting the userid.
		$userid = $user->get('id');

		return $userid;
		
	}

	public function activate($token) {
	
		$config	= JFactory::getConfig();
		$userParams	= JComponentHelper::getParams('com_users');
		$db	= $this->getDbo();
		
		## Get the user id based on the token.
		$db->setQuery( 'SELECT id FROM #__users WHERE activation = "'.$token.'" AND block  = 1' );
		$obj = $db->loadObject();	
		
		$userId = $obj->id;

		## Check for a valid user id.
		if (!$userId) {
			$this->setError(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));
			return false;
		}

		## Load the users plugin group.
		JPluginHelper::importPlugin('user');

		## Activate the user now.
		$user = JFactory::getUser($userId);
		$user->set('activation', '');
		$user->set('block', '0');


		## Store the user object.
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_TICKETMASTER_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()));
			return false;
		}

		return $user;
	}


	function store($data) {
	
		global $mainframe;
		
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
   
   function getUserInformation() {

		$db   = JFactory::getDBO();
		$user =  JFactory::getUser();  
		
		## Making the query for showing all the clients in list function
		$sql='SELECT * FROM #__ticketmaster_clients WHERE userid = '.(int)$user->id.''; 

		$db->setQuery($sql);
		$this->data = $db->loadObject();

		return $this->data;
   } 
       
}
?>