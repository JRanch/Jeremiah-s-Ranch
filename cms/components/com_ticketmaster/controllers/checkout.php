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

class TicketmasterControllerCheckout extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->amount 	= JRequest::getInt('amount', 0);
		$this->session 	= JRequest::getInt('ordercode', 0);
		$this->id		= JRequest::getInt('ticketid');
		$this->email	= JRequest::getVar('emailaddress');
		$this->name		= JRequest::getVar('name');
		$this->remarks	= JRequest::getVar('remarks');
		
		$this->password = JRequest::getVar('password');
		$this->username = JRequest::getVar('username');

		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		## Check if the user is logged in.
		$user = & JFactory::getUser();
		$this->userid = $user->id;

	}


	function coupon(){
			
		$db  = JFactory::getDBO();
		$app = JFactory::getApplication();

		## Getting the global DB session
		$session = JFactory::getSession();
		## Gettig the orderid if there is one.
		$ordercode = $session->get('ordercode');
		
		$couponcode = JRequest::getVar('couponcode', 'NONE');
		
		$link = JRoute::_('index.php?option=com_ticketmaster&view=checkout');		

		if ($couponcode == 'NONE') {
			## Redirect the customer back. No product to add.
			$msg = JText::_( 'COM_TICKETMASTER_INVALID_COUPON' );
			$this->setRedirect($link , $message);
		}
			
		$post = JRequest::get('post');

		## GETTING THE MODEL TO SAVE
		$model	= $this->getModel('checkout');

		if ($model->updateCart($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_COUPON_APPLIED_TO_CART' );
			JFactory::getApplication()->enqueueMessage($msg);
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_INVALID_COUPON' );
			$link = JRoute::_('index.php?option=com_ticketmaster&view=cart');
			JError::raiseNotice( 100, $msg );
		}	
			
		$this->setRedirect($link);		
	
	}	

	public function login()
	{
		JRequest::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();
		
		## Getting the config.
		$db = JFactory::getDBO();
		
		## Get the config for redirection after logging in.
		$sql = "SELECT redirect_after_login, send_profile_mail FROM #__ticketmaster_config WHERE configid = 1 ";
		$db->setQuery($sql);
		$config = $db->loadObject();			

		// Populate the data array:
		$data = array();
		$data['return'] = base64_decode(JRequest::getVar('return', '', 'POST', 'BASE64'));
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		// Set the return URL if empty.
		if (empty($data['return'])) {
			$data['return'] = 'index.php?option=com_users&view=profile';
		}

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];

		// Perform the log in.
		$error = $app->login($credentials, $options);

		// Check if the log in succeeded.
		if (!JError::isError($error)) {
			$app->setUserState('users.login.form.data', array());
			
			## Check if the user is logged in.
			$user = JFactory::getUser();
			$userid = $user->id;
			
			if (!$userid){
				
				## User has not been logged in, redirect back to the checkout pages.
				$msg = $msg = JText::_( 'COM_TICKETMASTER_YOU_CANNOT_LOGIN');
				$uri = JRoute::_('index.php?option=com_ticketmaster&view=checkout');
				$app->redirect($uri, $msg);
			
			}else{
			
				$model	= $this->getModel('checkout');
				## Get the model to update the order..
				$model->itemsupdate($userid, $this->ordercode);

				$user =  JFactory::getUser();  
				
				## Making the query for showing all the clients in list function
				$sql='SELECT * FROM #__ticketmaster_clients 
						WHERE userid = '.(int)$userid.''; 

				$db->setQuery($sql);
				$user_info = $db->loadObject();

				
				if ($config->send_profile_mail == 1){ 
					$model->sendconfirmation($userid, $post['password'], $post['username'], $user->email, $this->ordercode, $user->name);		
				}
				
				if ($config->redirect_after_login == 0){ 
					
					$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=profile'));
				
				}else{

					if(!$user_info) {
						$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=profile'));
					}else{
						$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=payment'));
					}

				}				

			}
				
		} else {
			
			$data['remember'] = (int)$options['remember'];
			$app->setUserState('users.login.form.data', $data);
			$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=checkout', false));
			
		}
	}

	public function activate(){
	
		$user		= JFactory::getUser();
		$uParams	= JComponentHelper::getParams('com_users');

		## If the user is logged in, return them back to the homepage.
		if ($user->get('id')) {
			$this->setRedirect('index.php?option=com_ticketmaster&view=checkout');
			return true;
		}

		## If user registration or account activation is disabled, throw a 403.
		if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) {
			JError::raiseError(403, JText::_('COM_TICKETMASTER_ACCESS_FORBIDDEN'));
			return false;
		}

		$model	=& $this->getModel('checkout');
		$token = JRequest::getVar('token', null, 'request', 'alnum');

		## Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32) {
			JError::raiseError(403, JText::_('COM_TICKETMASTER_TOKEN_INVALID'));
			return false;
		}

		## Attempt to activate the user.
		$return = $model->activate($token);

		## Check for errors.
		if ($return === false) {
			## Redirect back to the checkout.
			$this->setMessage(JText::sprintf('COM_TICKETMASTER_ACTIVATION_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php?option=com_ticketmaster&view=checkout');
			return false;
		}
		
		$this->setRedirect('index.php?option=com_ticketmaster&view=checkout', JText::_('COM_TICKETMASTER_TOKEN_ACTIVATED'));
	}

	function save(){

		## Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app	= JFactory::getApplication();
		
		## Pattern to check users email address
		$pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
		$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		$db = JFactory::getDBO();
		
		## Get the config values needed for the signups.
		$sql = "SELECT activation_email, use_automatic_login, auto_username, show_birthday, mailchimp_api, mailchimp_listid, show_mailchimps
				FROM #__ticketmaster_config 
				WHERE configid = 1 ";
				
		$db->setQuery($sql);
		$config = $db->loadObject();		
		
		## Register submitted data.
		$requestData = JRequest::get('post');
		## We do need this again for failed registrations.
		$app->setUserState('com_ticketmaster.registration', $requestData);

		## If registration is disabled - Redirect to login page.
		if(JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
			$msg = JText::_( 'COM_TICKETMASTER_USER_REGISTRATION_OFF');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;
		}
		
		if (preg_match($pattern, JRequest::getVar('emailaddress')) ) {
			$msg = JText::_( 'COM_TICKETMASTER_USER_EMAIL_INCORRECT');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;	
		}		

		if (JRequest::getVar('name') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_NAME_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;			
		}
		
		if (JRequest::getVar('address') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_ADDRESS_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;			
		}		
		
		if (JRequest::getVar('city') == ''){
			$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_CITY_NOT_FILLED');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;			
		}
		
		if($config->auto_username == 0 ){
		
			if (JRequest::getVar('username') == ''){
				$msg = JText::_( 'COM_TICKETMASTER_CHECKOUT_USERNAME_NOT_FILLED');	
				$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
				return false;			
			}	
			
			## Check if passwords are the same.
			if (JRequest::getVar('password') != JRequest::getVar('password2')){
				$msg = JText::_( 'COM_TICKETMASTER_USER_PASS_INCORRECT');	
				$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
				return false;			
			}				
		
		}
		
		if (JRequest::getVar('emailaddress') != JRequest::getVar('email2')){
			$msg = JText::_( 'COM_TICKETMASTER_EMAILADDRESSES_DO_NOT_COMPARE');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;			
		}	

		## Get the user id based on user input.
		$db->setQuery('SELECT id FROM #__users WHERE username = '.$db->Quote(JRequest::getVar('username')).'');
		## Loading the results of the query
		$userId = (int) $db->loadResult();				
		
		if ($userId) {
			$msg = JText::_( 'COM_TICKETMASTER_USER_IN_DB');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;		
		}

		$db->setQuery('SELECT email FROM #__users WHERE email = '.$db->Quote(JRequest::getVar('emailaddress')).'');
		## Loading the results of the query
		$email_used = $db->loadResult();	
		
		if ($email_used) {
			$msg = JText::_( 'COM_TICKETMASTER_EMAIL_IN_DB');	
			$app->redirect('index.php?option=com_ticketmaster&view=checkout', $msg);	
			return false;		
		}
		
		##### OK - ALL DATA SEEMS OK #####

		if ($config->show_mailchimps == 1){
			
			## SUBSCRIBE TO MAILING LIST OPTION - ADD TO MAILCHIMP USING API
			if ( JRequest::getVar('emailUpdates') == 'Yes' ) {
				
				## Include Mailchimp API class
				$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'mailchimp.class.php';
				require_once( $path_include );				
			 
				## Your API Key: http://admin.mailchimp.com/account/api/
				$api = new MCAPI($config->mailchimp_api);
			 
				## Your List Unique ID: http://admin.mailchimp.com/lists/ (Click "settings")
				$list_id = $config->mailchimp_listid;
			 
				$merge_vars = array(
					'FNAME' => JRequest::getVar('name'),
					'LNAME' => JRequest::getVar('name')
				);
			 
				## SUBSCRIBE TO LIST 
				if ( $api->listSubscribe($list_id, JRequest::getVar('emailaddress'), $merge_vars) === true ){
					$mailchimp_result = 'Success! Check your email to confirm sign up.';
				} else {
					$mailchimp_result = 'Error: ' . $api->errorMessage;
					
				}
			}
		}

		$post = JRequest::get('post');

		$params = JComponentHelper::getParams('com_users');
		$useractivation = $params->get('useractivation');		
		
		
		if($config->auto_username == 1 ){
			
			$data['username'] 	= $post['emailaddress'];
			$chars 				= '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
			$data['password']   = self::password( 8 , $chars );
		
		}else{
			
			$to_be_replaced  = array("#", "&", "!", "$", "^", "%", "(", ")", "=", "+", "/", "<", ">", ";", ":", "[", "]", "{", "}", 
									 "FROM", "from", "UPDATE", "update", "DELETE", "delete", "+");
			
			$data['username'] = str_replace($to_be_replaced, "", $post['username']);
			$data['password'] = $post['password'];
		}
		
		$data['name']		= $post['firstname'].' '.$post['name'];
		$data['firstname']	= $post['firstname'];
		$data['email']		= $post['emailaddress'];

		$model	= $this->getModel('checkout');

		## Attempt to save the data.
		## receive back the userid.
		$userid	= $model->register($data, $config);
		
		$post['userid'] = $userid;
		$post['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		$post['published'] = 1;
		
		if($config->show_birthday == 1) { 
			
			## Creating the birthday
			$post['birthday'] = $post['year'].'-'.$post['month'].'-'.$post['day'];
			
			
			## Make date save:
			jimport ('joomla.utilities.date');
			$date = new JDate($post['birthday']);
			
			$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
			
			if($isJ30) {
				$birthday = $date->Format('Y-m-d');
			}else{
				$birthday = $date->toFormat('Y-m-d');
			}
			$post['birthday'] = $birthday;
			
		}
		
		## Save userdata in Ticketmaster tables.
		$model->store($post);

		## Flush the data from the session.
		$app->setUserState('com_ticketmaster.registration', null);

		## Let's change order status now. They are now 
		## connected to a user with an id.
		$model->itemsupdate($userid, $this->ordercode);
		
		## Mail the userinformation ot this customer.
		$model->sendconfirmation($userid, $data['password'], $data['username'], $this->email, $this->ordercode, $this->name);		
		
		
		## Check what type of registration it is in config.
		$useractivation = $params->get('useractivation');

		if ($useractivation == 1) {

			## Message for showing redirections
			$msg = JText::_( 'COM_TICKETMASTER_ACTIVATE_ACCOUNT');
			## URL to redirect the customer after registration.
			$url = JRoute::_('index.php?option=com_ticketmaster&view=checkout');	
			## Redirect customer to specific page
			$app->redirect( $url, $msg );					

		}elseif($useractivation == 2){

			## Message for showing redirections
			$msg = JText::_( 'COM_TICKETMASTER_CANNOT_COMPLETE_ORDER');
			## URL to redirect the customer after registration.
			$url = JRoute::_('index.php?option=com_ticketmaster&view=checkout');	
			## Redirect customer to specific page
			$app->redirect( $url, $msg );		
			
		}else{

			## Get the config values needed for the signups.
			$sql = "SELECT use_automatic_login, redirect_after_login, send_profile_mail
					FROM #__ticketmaster_config 
					WHERE configid = 1 ";
					
			$db->setQuery($sql);
			$config = $db->loadObject();
			
			###############################################
			######### START WITH AUTO LOGIN IF ON #########
			###############################################
			
			if ($config->use_automatic_login == 1){
			
				$app = JFactory::getApplication();
		
				// Set the return URL if empty.
				if (empty($data['return'])) {
					$data['return'] = 'index.php?option=com_users&view=profile';
				}
		
				// Get the log in options.
				$options = array();
				$options['remember'] = JRequest::getBool('remember', false);
				$options['return'] = $data['return'];
		
				// Get the log in credentials.
				$credentials = array();
				$credentials['username'] = $data['username'];
				$credentials['password'] = $data['password'];
		
				// Perform the log in.
				$error = $app->login($credentials, $options);
		
				// Check if the log in succeeded.
				if (!JError::isError($error)) {
					$app->setUserState('users.login.form.data', array());
					
					## Check if the user is logged in.
					$user = & JFactory::getUser();
					$userid = $user->id;
					
					if (!$userid){
						
						## User has not been logged in, redirect back to the checkout pages.
						$msg = $msg = JText::_( 'COM_TICKETMASTER_AUTOLOGIN_LOGIN_FAILURE');
						$uri = JRoute::_('index.php?option=com_ticketmaster&view=checkout');
						$app->redirect($uri, $msg);
					
					}else{
					
						$model	=& $this->getModel('checkout');
						## Get the model to update the order..
						$model->itemsupdate($userid, $this->ordercode);
						## Mail the userinformation ot this customer.
						
						if ($config->send_profile_mail == 1){ 
							$model->sendconfirmation($userid, $data['password'], $data['username'], $user->email, $this->ordercode, $user->name);		
						}
						
						## Redirect to payment page immediatly.
						$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=payment'));			
		
					}
						
				} else {
					
					$data['remember'] = (int)$options['remember'];
					$app->setUserState('users.login.form.data', $data);
					$app->redirect(JRoute::_('index.php?option=com_ticketmaster&view=checkout', false));
					
				}			
			
			}
			
			#############################################
			######### END WITH AUTO LOGIN IF ON #########
			#############################################		
			
			## Message for showing redirections
			$msg = JText::_( 'COM_TICKETMASTER_YOU_CAN_LOGIN');
			## URL to redirect the customer after registration.
			$url = JRoute::_('index.php?option=com_ticketmaster&view=checkout');	
			## Redirect customer to specific page
			$app->redirect( $url, $msg );		
		
		}
		
	}


	function cancel_order() {
			
		global $mainframe;
		
		$link = JRoute::_('index.php?option=com_ticketmaster&view=cancel');
		$mainframe->redirect( $link );	
	
	}

	function manual_payment() {
			
			global $mainframe;
			$link = JRoute::_('index.php?option=com_ticketmaster&view=ordercomplete');
			$mainframe->redirect( $link );	
	
	}
	
	function password($length = 7, $chars = '123456789'){
		## Length of character list
		$chars_length = (strlen($chars) - 1);
		## Start our string
		$string = $chars{rand(0, $chars_length)};
		## Generate random string
		for ($i = 1; $i < $length; $i = strlen($string)){
			## Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};
			## Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		## Return the string
		return $string;
	}	
}	
?>
