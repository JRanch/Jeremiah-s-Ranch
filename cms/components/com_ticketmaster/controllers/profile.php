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

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerProfile extends JControllerLegacy {

	function __construct() {
		parent::__construct();

		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		## Check if the user is logged in.
		$user = & JFactory::getUser();
		$this->userid = $user->id;
		
		$this->email	= JRequest::getVar('emailaddress');
		$this->name		= JRequest::getVar('name');	
		$this->remarks	= JRequest::getVar('remarks');	

	}

	
	function save()
	{

		## Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		## GETTING THE MODEL TO SAVE
		$model	=& $this->getModel('profile');
		
		$app	= JFactory::getApplication();	
		
		## If the userid is present, don't insert the client again.
		if(!$this->userid) {
		
			$link = JRoute::_('index.php?option=com_ticketmaster&view=profile');
			$this->setRedirect($link , JText::_( 'COM_TICKETMASTER_YOU_NEED_TO_BE_LOGGED_IN' ));

		}else{

			## Getting the rest of the post variables
			$post   			= JRequest::get('post');
			$post['userid']		= $this->userid;
			$userid 			= $this->userid;
			$username			= $post['username'];
		
		}
		
		## Pattern to check users email address
		$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		## Check if email is valid.
		if (preg_match($pattern, JRequest::getVar('emailaddress')) ) {
			$msg = JText::_( 'COM_TICKETMASTER_USER_EMAIL_INCORRECT');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;	
		}	

		if (JRequest::getVar('firstname') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_FIRSTNAME_NOT_FILLED');
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);
			return false;
		}		
		
		if (JRequest::getVar('name') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_NAME_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}
		
		if (JRequest::getVar('address') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_ADDRESS_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}		
		
		if (JRequest::getVar('city') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_CITY_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}			
		
		$post['published']		= 1;

		## Link for redirection.
		$link = JRoute::_('index.php?option=com_ticketmaster&view=payment');
				
		## Let's save all data now.
		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_STORED_PROFILE' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_STORED_PROFILE_FAILED' );
		}
		
		## Let's change order status now.
		if ($model->itemsupdate($userid, $this->ordercode)) {
			$msg = JText::_( 'COM_TICKETMASTER_ITEMS_UPDATED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_ITEMS_UPDATE_FAILED' );
		}	

		$db = JFactory::getDBO();
		
		## Get the config values needed for the signups.
		$sql = "SELECT activation_email, mailchimp_listid, mailchimp_api, show_mailchimp_signup, send_profile_mail
				FROM #__ticketmaster_config 
				WHERE configid = 1 ";
				
		$db->setQuery($sql);
		$config = $db->loadObject();

		if ($config->send_profile_mail == 1){ 
			$model->sendconfirmation($userid, $password, $username, $this->email, $this->ordercode, $this->name);		
		}
	
		
		$this->setRedirect($link , $message);
	}

	function myprofile()
	{

		## Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		## GETTING THE MODEL TO SAVE
		$model	=& $this->getModel('profile');
		
		$app	= JFactory::getApplication();	
		
		## If the userid is present, don't insert the client again.
		if(!$this->userid) {
		
			$link = JRoute::_('index.php?option=com_ticketmaster');
			$this->setRedirect($link , JText::_( 'COM_TICKETMASTER_YOU_NEED_TO_BE_LOGGED_IN' ));

		}else{

			## Getting the rest of the post variables
			$post   			= JRequest::get('post');
			$post['userid']		= $this->userid;
			$userid 			= $this->userid;
			$username			= $post['username'];
		
		}
		
		## Pattern to check users email address
		$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		## Check if email is valid.
		if (!eregi($pattern, JRequest::getVar('emailaddress'))){
			$msg = JText::_( 'COM_TICKETMASTER_USER_EMAIL_INCORRECT');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}

		if (JRequest::getVar('name') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_NAME_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}
		
		if (JRequest::getVar('address') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_ADDRESS_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}		
		
		if (JRequest::getVar('city') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_CITY_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=profile', $msg);	
			return false;			
		}			
		
		$post['published']		= 1;
		
		$post['birthday'] = $post['year'].'-'.$post['month'].'-'.$post['day'];

		## Link for redirection.
		$link = JRoute::_('index.php?option=com_ticketmaster&view=myprofile');
				
		## Let's save all data now.
		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_STORED_PROFILE' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_STORED_PROFILE_FAILED' );
		}
		
		$this->setRedirect($link , $message);
	}

}	
?>
