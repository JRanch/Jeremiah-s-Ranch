<?php
## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Import library dependencies
jimport('joomla.plugin.plugin');
 
class plgRDmediaRDMpaypal extends JPlugin
{
/**
 * Constructor
 *
 * For php4 compatability we must not use the __constructor as a constructor for
 * plugins because func_get_args ( void ) returns a copy of all passed arguments
 * NOT references.  This causes problems with cross-referencing necessary for the
 * observer design pattern.
 */
 function plgRDMediaRDMpaypal( &$subject, $params  ) {
 
    parent::__construct( $subject , $params  );
	
	## Loading language:	
	$lang = JFactory::getLanguage();
	$lang->load('plg_rdmedia_paypal', JPATH_ADMINISTRATOR);	

	## load plugin params info
 	$plugin =& JPluginHelper::getPlugin('rdmedia', 'rdmpaypal');

	$this->pp_email = $this->params->def( 'pp_email', 2112 );
	$this->currency = $this->params->def( 'currency', 'EUR' );
	$this->paypal_authcode = $this->params->def( 'paypal_authcode', 'EUR' );
	$this->sandbox_on = $this->params->def( 'sandbox_on', 1 );
	$this->success_tpl = $this->params->def( 'success_tpl', 1 );
	$this->failure_tpl = $this->params->def( 'failure_tpl', 1 );
	$this->itemid = $this->params->def( 'itemid', 1 );
	$this->infobox = $this->params->def( 'infobox', 'enter a message in the backend.' );
	$this->layout = $this->params->def( 'layout', 0 );	
	
	## Including required paths to calculator.
	$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
	include_once( $path_include );

	## Getting the global DB session
	$session =& JFactory::getSession();
	## Gettig the orderid if there is one.
	$this->ordercode = $session->get('ordercode');
	
	## Getting the amounts for this order.
	$this->amount = _getAmount($this->ordercode);
	$this->fees	  = _getFees($this->ordercode); 

	## Return URLS to your website after processing the order.
	$this->return_url = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=paypal&Itemid='.$this->itemid;
	$this->cancel_url = JURI::root().'index.php?option=com_ticketmaster&view=transaction&payment_type=paypal_failed&Itemid='.$this->itemid;
	
	## Use the sandbox if you're testing. (Required: Sandbox Account with PayPal)
	if ($this->sandbox_on == 1){
		## We're in a testing environment.
		$this->url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	}else{
		## Use the lines below for a live site.
		$this->url = 'https://www.paypal.com/cgi-bin/webscr';
	}
	
 }
 
/**
 * Plugin method with the same name as the event will be called automatically.
 * You have to get at least a function called display, and the name of the processor (in this case paypal)
 * Now you should be able to display and process transactions.
 * 
*/

	 function display()
	 {
		$app = &JFactory::getApplication();
		
		## Loading the CSS file for ideal plugin.
		$document = &JFactory::getDocument();
		$document->addStyleSheet( JURI::root(true).'/plugins/rdmedia/rdmpaypal/rdmedia_paypal/css/paypal.css' );	
		
		$user =& JFactory::getUser();
		
		## Making sure PayPal getting the right amount (23.00)
		$ordertotal = number_format($this->amount, 2, '.', '');		
		
		## Check the amount, if higher then 0.00 then show the plugin data.	
		if ($ordertotal > '0.00') {
			
			## Check if this is Joomla 2.5 or 3.0.+
			$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
			
			## This will only be used if you use Joomla 2.5 with bootstrap enabled.
			## Please do not change!
			
			if(!$isJ30){
				if($config->load_bootstrap == 1){
					$isJ30 = true;
				}
			}	
			
			if($this->layout == 1 && $isJ30 == true ){

				echo '<img src="plugins/rdmedia/rdmpaypal/rdmedia_paypal/images/paypal_vertical_view.png" />';
				
				echo '<form action="'.$this->url.'" method="post" name="paypalForm">';
				## Low let's get some information about your payment.	
				echo    '<input type="hidden" name="cmd" value="_xclick" />';
				echo    '<input type="hidden" name="item_number" value="'.$this->ordercode.'" />';
				echo    '<input type="hidden" name="amount" value="'.$ordertotal.'" />';	
				echo 	'<input type="hidden" name="item_name" value="Order: '.$this->ordercode.'">';
				echo	'<input type="hidden" name="business" value="'.$this->pp_email.'">';
				echo	'<input type="hidden" name="custom" value="'.$this->ordercode.'">';
				echo	'<input type="hidden" name="return" value="'.$this->return_url.'">';
				echo	'<input type="hidden" name="cancel_return" value="'.$this->cancel_url.'">';
				echo	'<input type="hidden" name="currency_code" value="'.$this->currency.'">';
				echo	'<input type="hidden" name="no_note" value="1">';	
				
				echo    '<button class="btn btn-block btn-success" style="margin-top: 8px;" type="submit">'.JText::_( 'Make Payment' ).'</button>';			
				echo 	'</form>';
					
				
			}else{
			
				## Let's build the form now, we need to have some information.
				echo '<form action="'.$this->url.'" method="post" name="paypalForm">';
				
				echo '<div id="plg_rdmedia_paypal">';
				
				echo '<div id="plg_rdmedia_paypal_cards">';
				echo $this->infobox;
				echo '</div>';
					
					## Low let's get some information about your payment.	
					echo    '<input type="hidden" name="cmd" value="_xclick" />';
					echo    '<input type="hidden" name="item_number" value="'.$this->ordercode.'" />';
					echo    '<input type="hidden" name="amount" value="'.$ordertotal.'" />';	
					echo 	'<input type="hidden" name="item_name" value="Order: '.$this->ordercode.'">';
					echo	'<input type="hidden" name="business" value="'.$this->pp_email.'">';
					echo	'<input type="hidden" name="custom" value="'.$this->ordercode.'">';
					echo	'<input type="hidden" name="return" value="'.$this->return_url.'">';
					echo	'<input type="hidden" name="cancel_return" value="'.$this->cancel_url.'">';
					echo	'<input type="hidden" name="currency_code" value="'.$this->currency.'">';
					echo	'<input type="hidden" name="no_note" value="1">';			
					
					echo '<div id="plg_rdmedia_paypal_confirmbutton">';
					echo    '<input type="submit" name="submit" value="" class="paypal_button" style="width: 116px;">';
					echo '</div>';	
				
				echo '</div>';
				
				echo '</form>';
			
			}
		
		}
		
		return true;
	 }

	function _showmsg($msgid, $msg){
		
		$db = JFactory::getDBO();
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$msgid."";
		$db->setQuery($sql);
		$config = $db->loadObject();
	
		echo '<h1>'.$config->mailsubject.'</h1>';
		$message = str_replace('%%MSG%%', $msg, $config->mailbody);
		echo $message;
		echo '<br/><br/><br/><br/><br/><br/>';

		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		$session->clear('ordercode');													
				
	}


	function paypal() {

		// Load user_profile plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_rdmedia_paypal', JPATH_ADMINISTRATOR);
	
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
		$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';	
		
		## using $sig, $tx, validate that payment was successful
		$item_number_array	= explode( ':', strval( JRequest::getVar( 'item_number' ) ) );
		$item_number	= intval( @$item_number_array[0] );
		$action 		= strval( JRequest::getVar( 'action' ) );
		$tx 			= strval( JRequest::getVar( 'tx' ) );
		$st 			= strval( JRequest::getVar( 'st' ) );
		$sig 			= strval( JRequest::getVar( 'sig' ) );
		
		## Initializing cURL
		$request = curl_init();
		
		## Set request options to paypal
		curl_setopt_array($request, array
		(
		  CURLOPT_URL => $this->url,
		  CURLOPT_POST => TRUE,
		  CURLOPT_POSTFIELDS => http_build_query( array( 'cmd' => '_notify-synch','tx' => $tx,'at' => $this->paypal_authcode ) ),
		  CURLOPT_RETURNTRANSFER => TRUE,
		  CURLOPT_HEADER => FALSE,
		));
		
		## Execute request and get response and status code
		$response = curl_exec($request);
		$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
		
		## Close connection
		curl_close($request);
		
		## Check the status and success message from paypal.
		if($status == 200 AND strpos($response, 'SUCCESS') === 0) {
		
			## Remove SUCCESS part (7 characters long)
			$response = substr($response, 7);
			
			## URL decode
			$response = urldecode($response);
			
			## Turn into associative array
			preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
			$response = array_combine($m[1], $m[2]);
			
			## Fix character encoding if different from UTF-8 (in my case)
			if(isset($response['charset']) AND strtoupper($response['charset']) !== 'UTF-8')
			{
			  foreach($response as $key => &$value) {
			  
				$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
			  }
			  
			  $response['charset_original'] = $response['charset'];
			  $response['charset'] = 'UTF-8';
			}
			
			## Sort on keys for readability (handy when debugging)
			ksort($response);
			### Showing the result for test environments.
			### print_r($response);
			
			if ($response['payment_status'] != 'Completed') {
			
				## Show error.
				
			}else{
				
				## Connecting the database
				$db = JFactory::getDBO();
				## Current date for database.
				$trans_date = date("d-m-Y H:i");
				## Getting the transaction info from PP.
				$payment_id = $response['txn_id'];
				
				## Check that txn_id has not been previously processed
				$sql = 'SELECT COUNT(pid) AS total 
						FROM #__ticketmaster_transactions 
						WHERE transid = "'.$payment_id.'" ';
						
				$db->setQuery($sql);
				$results = $db->loadObject();
				
				if($results->total > 0){				
				
					## Show error on failed - Transaction may not exsist in DB.
					$msg = JText::_( 'COM_TICKETMSTER_PP_TRANSACTION_PROCESSED' );
					$this->_showmsg($this->failure_tpl, $msg);		
				
				}else{
				
					## Paid amount to PayPal
					$payment_amount = $response['mc_gross'];
					## Get the email address from the buyer.
					$payer_email 	= $response['payer_email'];
					## Get the order information sent by PP.
					$orderid 		= $response['custom'];
					
					## Including required paths to calculator.
					$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
					include_once( $path_include );
					
					## Getting the amounts for this order.
					$amount = _getAmount($response['custom'], 1);
					
					##Requested amount for this order.
					$amount_req = number_format($amount, 2, '', '');
					## Sent amount by PP (needs the same notation as ours)							
					$amount_pp = number_format($response['mc_gross'], 2, '', '');
					
					## Check if the amount is the same as the paid amount.
					if ($amount_req != $amount_pp) {
						
						## Amounts are not the same. Show the message to the client.
						$msg = JText::_( 'COM_TICKETMSTER_PP_AMOUNT_IS_NOT_CORRECTLY' );
						$this->_showmsg($this->failure_tpl, $msg);						
					
					}else{
						
						## Getting the latest logged in user.
						$user = & JFactory::getUser();
			
						JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
						$row =& JTable::getInstance('transaction', 'Table');	
					
						## Pickup All Details and create foo=bar&baz=boom&cow=milk&php=hypertext+processor
						$payment_details = http_build_query($response);
						$payment_type = 'PayPal';
						$orderid = $response['custom'];
						
						## Now store all data in the transactions table
						$row->transid = $response['txn_id'];
						$row->userid = $user->id;
						$row->details = $payment_details;
						$row->amount = $response['mc_gross'];
						$row->type = 'PayPal';
						$row->email_paypal = $response['payer_email'];
						$row->orderid = $response['custom'];
						
						## Store data
						$row->store();		
						
						$query = 'UPDATE #__ticketmaster_orders'
							. ' SET paid = 1, published = 1'
							. ' WHERE ordercode = '.(int)$response['custom'].'';
						
						## Do the query now	
						$db->setQuery( $query );
						
						## When query goes wrong.. Show message with error.
						if (!$db->query()) {
							$this->setError($db->getErrorMsg());
							return false;
						}
						
						$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode = '.(int)$response['custom'].'';
	
						## Do the query now	
						$db->setQuery($query);
						$data = $db->loadObjectList();
			
						
						$k = 0;
						for ($i = 0, $n = count($data); $i < $n; $i++ ){
							
							$row  = &$data[$i];
						
							## Check if the override is there.
							if (file_exists($override)) {
								## Yes, now we use it.
								require_once($override);
							} else {
								## No, use the standard
								require_once($path);
							}	
							
							if(isset($row->orderid)) {  
							
								$creator = new ticketcreator( (int)$row->orderid );  
								$creator->doPDF();
							
							}  				
							
							$k=1 - $k;
							
						}
						
						## Include the confirmation class to sent the tickets. 
						$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
						include_once( $path_include );
						
						## Sending the ticket immediatly to the client.
						$creator = new sendonpayment( (int)$response['custom'] );  
						$creator->send();
						
						## Removing the session, it's not needed anymore.
						$session =& JFactory::getSession();
						$session->clear($response['custom']);
						$session->clear('ordercode');
						$session->clear('coupon');

						## Getting the desired info from the configuration table
						$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = ".(int)$this->success_tpl."";
						$db->setQuery($sql);
						$config = $db->loadObject();

						## Getting the desired info from the configuration table
						$sql = "SELECT * FROM #__users WHERE id = ".(int)$user->id."";
						$db->setQuery($sql);
						$user = $db->loadObject();							
						
						echo '<h1>'.$config->mailsubject.'</h1>';
						
						$message = str_replace('%%TID%%', $response['txn_id'], $config->mailbody);
						$message = str_replace('%%OID%%', $response['custom'], $message);
						$message = str_replace('%%AMOUNT%%', $response['mc_gross'], $message);
						$message = str_replace('%%DATE%%', $trans_date, $message);
						$message = str_replace('%%NAME%%', $user->name, $message);
						$message = str_replace('%%EMAIL%%', $response['payer_email'], $message);
										
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
						$obj->addRecipient($user->email);
						## Send blind copy to site admin?
						if ($config->receive_bcc == 1){
							if ($config->reply_to_email != ''){
								$obj->addRecipient($obj->reply_to_email);
							}	
						}					
						## Add reply to and subject:					
						$obj->addReplyTo($config->reply_to_email);
						$obj->setSubject($config->mailsubject);
						
						if ($mail->published == 1){						
							
							$sent = $obj->Send();						
						}								
						
						echo $message;										
																								
					
					}

				}							
			
			}
		
		} else {
		
				## Amounts are not the same. Show the message to the client.
				$msg = JText::_( 'COM_TICKETMSTER_PP_TRANSACTION_FAILED' );
				$this->_showmsg($this->failure_tpl, $msg);		
		
		}		
	
	}	
}	 
?>