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

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerCodereadr extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->barcode 		= JRequest::getVar('tid', 0);
		$this->sid			= JRequest::getInt('sid', 0);
		$this->udid 		= JRequest::getVar('udid');
		$this->userid		= JRequest::getInt('userid', 0);
		$this->ordercode 	= JRequest::getInt('oc', 0);
		$this->id			= JRequest::getInt('id', 0);
		$this->ticketid		= JRequest::getInt('ticketid', 0);	
		$this->eventid		= JRequest::getInt('eventid', 0)	;
		
		## Special Variables for RD-Media Scanner
		$this->auth_code	= JRequest::getInt('auth_code', 0);
		$this->code			= JRequest::getInt('barcode', 0);
		$this->phone_id		= JRequest::getInt('phone_id', 0);

	}

	###########################################################
	### This function will avoid the 0's (null values) at   ###
	### the start of the barcode. EG: 0001234569871 	    ###
	### The three nulls needs to be removed as the DB won't	###
	### accept the null values								###
	###########################################################

	function checkBarcode($barcode, $type) {
		
		## Get the lenght of the code
		$lenght = strlen($barcode);
		
		if ($lenght > 0){
			
			$array = str_split($barcode);
			$code = '';
			
			## Let's go trough the array and 
	   		for ($i = 0, $n = count($array); $i <= $n; $i++ ){
				
				## Pick the number now	
				$status = $array[$i];
				
				## As long as $i is maller then 6 remove the 0.
				## Otherwise we need it into it because of orderid's with 0's (EG; 120)
				
				if ($i < 6){
					## If status > 0 add it to string.
					if ($status > 0){ 
						$code = $code.$status;
					}	
				}else{
					$code = $code.$status;
				}
			}
			
		}else{
		
			## No 13 digits = No Check!		
			$status = '0';
			$msg = 'Invalid Barcode';
			
			if( $type == 0) {
				$this->outputXML($status, $msg);
			}else{
				$this->outputJSON($status, $msg);
			}
			
		}
		
		return $code;
	}
	
	function outputXML($status, $msg) {

		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF-8'?>";
			echo "<xml>";
				echo "<status>".$status."</status>";
				echo "<text>".$msg."</text>";
		echo "</xml>";
		
		exit();		
	
	}
	
	function outputJSON($status, $msg){	
		
		header("Content-type: application/json");
		
		## Functionality to scan barcodes by a custom scanner
		$results = array();
		$results['status']  = $status;
		$results['text'] 	= $msg;
		
		## JSON encode the array
		echo json_encode($results);	
		
		exit();	## Exit is needed -> otherwise it will read the complete page :( ##
					
	
	}
	
	function validateRDMediaScan() {
		
		## Check if barcode was sent?
		if ($this->code == 0) { 	
			$this->outputJSON('0', JText::_( 'COM_TICKETMASTER_RDMSCANNER_NO_BARCODE_SENT' ));
		}	
		
		if ($this->auth_code == 0) { 	
			$this->outputJSON('0', JText::_( 'COM_TICKETMASTER_RDMSCANNER_AUTHORISATION_NOT_SENT' ));
		}
		
		## Connecting the DB 
		$db = JFactory::getDBO();

		## Now check if the authorisation code is OK.
		## This prevents user to call this function without a valid Authorisation Code.
	
		$sql = 'SELECT configid FROM #__ticketmaster_config 
				WHERE scan_api = "'.(int)$this->auth_code.'"'; 

		$db->setQuery($sql);
		$data = $db->loadObject();
		
		## If the id is not present in the tables --> Do NOT proceed with scan!
		if ($data->configid != 1) {
			$this->outputJSON('0', JText::_( 'COM_TICKETMASTER_RDMSCANNER_AUTHORISATION_NOT_OK' ));	
		}
		
		## Check has been done!
		
		## Now we have the barcode, let's check it:
		$code = $this->checkBarcode($this->code, 1);
		
		## Let's check if the ticket is valid:
		$sql='SELECT * FROM #__ticketmaster_orders WHERE barcode = "'.(int)$code.'"'; 

		$db->setQuery($sql);
		$data = $db->loadObject();		
		
		## Now let's check the barcode --> If no $data == 0 --> (ERROR: 100)
		if ( count($data) < 1 ) { 		
			$this->outputJSON('100', JText::_( 'COM_TICKETMASTER_RDMSCANNER_INVALID_BARCODE' ));
		}

		## Check if the ticket is not blacklisted: (ERROR: 101)
		if ($data->blacklisted == 1) { 		
			$this->outputJSON('101', JText::_( 'COM_TICKETMASTER_RDMSCANNER_BLACLISTED_BARCODE' ));		
		}				

		## Check if the ticket is not blacklisted: (ERROR: 102)
		if ($data->paid == 0) { 		
			$this->outputJSON('102', JText::_( 'COM_TICKETMASTER_RDMSCANNER_TICKET_NOT_PAID' ));		
		}
		
		## Check if the ticket is scanned before: (ERROR: 103)		
		if ($data->scanned == 1) { 		
			$this->outputJSON('103', JText::_( 'COM_TICKETMASTER_RDMSCANNER_SCANNED_BEFORE' ));
		}	
		
		## Perform update query now.
		$query = 'UPDATE #__ticketmaster_orders SET scanned = 1 WHERE barcode = "'.$code.'"';
		
		## All checks done -- Set to scanned now.
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->outputJSON('104', JText::_( 'COM_TICKETMASTER_RDMSCANNER_ERROR_DURING_UPDATE' ));
		}			
		
		if ($data->seat_sector != 0) {
			
			## Get ticket information:
			$sql='SELECT ticketname FROM #__ticketmaster_tickets WHERE ticketid = "'.(int)$data->ticketid.'"'; 
	
			$db->setQuery($sql);
			$ticket = $db->loadObject();
			
			## Get ticket information:
			$sql='SELECT seatid FROM #__ticketmaster_coords WHERE id = "'.(int)$data->seat_sector.'"'; 
	
			$db->setQuery($sql);
			$obj = $db->loadObject();			 			
			
			## Show seat and sector in message:
			$msg = $ticket->ticketname.' - '.JText::_( 'COM_TICKETMASTER_RDMSCANNER_SEAT_NUMBER' ).': '.$obj->seatid;		
			## Send message to phone app now:
			$this->outputJSON('200', $msg);
		
		}else{

			## The ticket is valid and may be used:
			$this->outputJSON('200', JText::_( 'COM_TICKETMASTER_RDMSCANNER_ENTRANCE_OK' ));
			
		}

	}
	
	function iCody(){
		
		## Getting the post from the scan:
		$post = JRequest::get('post');
		$post['auth'] = JRequest::getInt('auth', 0);
		
		## Trigger plugin and send post data:
		JPluginHelper::importPlugin('rdmedia');
		$dispatcher = JDispatcher::getInstance();
		$html = $dispatcher->trigger('onAfterScan', array($post));		
		
		header("Content-type: text/xml");
		echo $html[0]['message'];
		exit();
		
	}
	
	function validation(){
		
		## Also when another server is posting data we need to check it.
		## you never know what happens if strange things are being sent.
		
		if ($this->barcode == 0) { 	
			$this->outputXML('0', 'Contact codeREADr - Barcode was not sent.');
		}
			
		## If $this->sid is empty --> stop!	
		if ($this->sid == 0) { 	
			$this->outputXML('0', 'Contact codeREADr  - Sid was not sent.');			
		}
		
		$code = $this->checkBarcode($this->barcode, 0);

		## Connecting the DB 
		$db = JFactory::getDBO();

		## OK, checked invalid posts. Load order.
		## We can only load integeres as we're using that also.
			
		## Ticket ID has nt been sent by validator.
		$sql='SELECT * FROM #__ticketmaster_orders
			  WHERE barcode = "'.$code.'"'; 

		
		$db->setQuery($sql);
		$data = $db->loadObject();	

		if ( count($data) < 1 ) { 		
			$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_INVALID_BARCODE' ));
		}
		
		if($this->eventid != 0){
			
			if($data->eventid != $this->eventid){
				$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_WRONG_EVENT' ));
			}
			
		}		
		
		if($this->ticketid != 0){
			
			if($data->ticketid != $this->ticketid){
				$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_WRONG_TICKET' ));
			}
			
		}		
		
		if ($data->blacklisted == 1) { 		
			$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_BLACLISTED_BARCODE' ));		
		}

		if ($data->scanned == 1) { 		
			$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_SCANNED_BEFORE' ));
		}
		
		if ($data->paid == 0) { 		
			$this->outputXML('0', JText::_( 'COM_TICKETMASTER_CODEREADR_UNPAID_TICKET' ));
		}
		
		$query = 'UPDATE #__ticketmaster_orders SET scanned = 1 WHERE barcode = "'.$code.'"';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}	

		$sql='SELECT * FROM #__ticketmaster_clients WHERE userid = "'.$data->userid.'"'; 

		
		$db->setQuery($sql);
		$data = $db->loadObject();	
		
		$msg = str_replace("%NAME%", $data->name, JText::_( 'COM_TICKETMASTER_CODEREADR_WELKOM' ));

		$this->outputXML(1, $msg);

	}
}	

