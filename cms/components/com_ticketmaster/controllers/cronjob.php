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

class TicketmasterControllerCronjob extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->key = JRequest::getInt('key', 0);
		## Do NOT change the line below.
		$this->pass = '60347603707434778092267';

	}
	
	## The function below will be executed with the followng address:
	## index.php?option=com_ticketmaster&controller=cronjob&key=60347603707434778092267&task=createPDF
	
	function createPDF() {
			
			
			$db = JFactory::getDBO();
			
			include_once( 'components/com_ticketmaster/assets/functions.php' );
			
			## Selecting tickets to create. LIMIT = 10 (otherwise the server will overload)
			## Sometimes it will be better to run an extra cronjob.
			$sql='SELECT  * FROM #__ticketmaster_orders
				  WHERE pdfcreated = 0 AND paid = 1 LIMIT 0, 10'; 

			$db->setQuery($sql);
			$data = $db->loadObjectList();				  


			$k = 0;
			for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = &$data[$i];

				## Include the confirmation class to sent the tickets. 
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
				$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';
				
				## Check if the override is there.
				if (file_exists($override)) {
					## Yes, now we use it.
					require_once($override);
				} else {
					## No, use the standard
					require_once($path);
				}	
				
				$creator = new ticketcreator( (int)$row->orderid );  
				$creator->doPDF();
					
				
			$k=1 - $k;
				
			}	

		
	}	


	## The function below will be executed with the followng address:
	## index.php?option=com_ticketmaster&controller=cronjob&key=60347603707434778092267&task=sendTickets
	
	function sendTickets() {

			
			$db = JFactory::getDBO();
			
			## Selecting tickets to create. LIMIT = 10 (otherwise the server will overload)
			## Sometimes it will be better to run an extra cronjob.
			$sql='SELECT ordercode FROM #__ticketmaster_orders
				  WHERE pdfcreated = 1 
				  AND paid = 1 
				  AND pdfsent = 0 
				  GROUP BY ordercode 
				  LIMIT 0, 10'; 

			$db->setQuery($sql);
			$data = $db->loadObjectList();				  


			$k = 0;
	   		for ($i = 0, $n = count($data); $i < $n; $i++ ){

				$row  = &$data[$i];

				## Include the confirmation class to sent the tickets. 
				$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
				include_once( $path_include );
				
				## Sending the ticket immediatly to the client.
				$creator = new sendonpayment( (int)$row->ordercode );  
				$creator->send();

				$k=1 - $k;
				
			}
		
	}	
	
	## index.php?option=com_ticketmaster&controller=cronjob&task=CleanupTickets
	## this function will cleanup unordered and cancelled orders.
	
	function CleanupTickets(){
		
		global $mainframe;
		
		$db = JFactory::getDBO();

		## Create a new date NOW()-1h. (database session is not longer than 2 hours in global config.
		$cleanup = date('Y-m-d H:i:s', mktime(date('H')-1, date('i'), date('s'), date('m'), date('d'), date('Y')));

		## Pickup the items that will be deleted. We need to update the ticketcounter.
		## First getting the selected item in an object.
		$update = 'SELECT ticketid 
				   FROM #__ticketmaster_orders 
				   WHERE orderdate < "'.$cleanup.'"  AND userid = 0';
	
		
		$db->setQuery($update);
		$this->data = $db->loadObjectList();	
		
		$count = count($this->data);
		
		if ($count < 1){
			exit();
		}		
		
		## Now go on with the delete functions. All expired orders have been saved.
		$query = 'DELETE FROM #__ticketmaster_orders WHERE orderdate < "'.$cleanup.'"  AND userid = 0';
		
		## Do the query now	and delete all selected invoices.
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}	

		## Tickets have been removed successfull
		## Now we need to update the totals from the Object earlier this script.
		$k = 0;
		for ($i = 0, $n = count($this->data); $i < $n; $i++ ){
		
			$row = &$this->data[$i];
			
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET totaltickets = totaltickets+1'
				. ' WHERE ticketid = '.$row->ticketid.' ';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}		
			
		$k=1 - $k;
		}			
	
	}

	## index.php?option=com_ticketmaster&controller=cronjob&task=NotActivated
	## this function will delete all unactivaed orders 

	function NotActivated(){
		
		$db = JFactory::getDBO();
		
		## Create a new date NOW()-3days.
		$cleanup = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d')-3, date('Y')));

		## Pickup the items that will be deleted. We need to update the ticketcounter.
		## First getting the selected item in an object.
		$update = 'SELECT ticketid 
				   FROM #__ticketmaster_orders 
				   WHERE orderdate < "'.$cleanup.'"  AND published = 0';
		
		$db->setQuery($update);
		$this->data = $db->loadObjectList();	
		
		$count = count($this->data);
		
		if ($count < 1){
			exit();
		}		
		
		## Now go on with the delete functions. All expired orders have been saved.
		$query = 'DELETE FROM #__ticketmaster_orders WHERE orderdate < "'.$cleanup.'"  AND published = 0';
		
		## Do the query now	and delete all selected invoices.
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}	

		## Tickets have been removed successfull
		## Now we need to update the totals from the Object earlier this script.
		$k = 0;
		for ($i = 0, $n = count($this->data); $i < $n; $i++ ){
		
			$row        = &$this->data[$i];
			
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET totaltickets = totaltickets+1'
				. ' WHERE ticketid = '.$row->ticketid.' ';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}		
			
		$k=1 - $k;
		}			
	}
}	
?>
