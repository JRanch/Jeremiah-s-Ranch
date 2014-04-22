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

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewPayment extends JViewLegacy {

	function display($tpl=null) {
		
		## Model is defined in the controller
		$model	= $this->getModel('payment');
		$db     = JFactory::getDBO();
		
		## Getting the global DB session
		$session = JFactory::getSession();
		## Gettig the orderid if there is one.
		$ordercode = $session->get('ordercode');		
		
		## Check if there are any tickets on the waiting list.
		$sql='SELECT COUNT(id) AS total
			  FROM #__ticketmaster_waitinglist
			  WHERE ordercode = '.$ordercode.'
			  AND processed = 0';
		
		$db->setQuery($sql);
		$waitlist = $db->loadObject();

		## Count the tickets in the order table:
		$sql='SELECT COUNT(orderid) AS total
			  FROM #__ticketmaster_orders
			  WHERE ordercode = '.$ordercode.'';

		$db->setQuery($sql);
		$orders = $db->loadObject();	

		
		if($orders->total == 0 && $waitlist->total > 0){

			$model	= $this->getModel('payment');
			
			$msg = $this->get('msg');
			$this->assignRef('msg', $msg);
			
			$tpl = 'message';
			parent::display($tpl);
			
		}else{ 

			## Getting the items into a variable
			$items	= $this->get('data'); 
			$price	= $this->get('price');
			$config	= $this->get('config'); 
			$tos	= $this->get('tos'); 	
			
			if ($config->pro_installed == 1){
				
				## will only be loaded if PRO is installed.
				## it won't work if you don't have the pro tables and views.
				$coords	  = $this->get('extdata');
				$require  = $this->get('datacheck');
				$failed   = $this->get('datafailed');
				
				## Assign data to the view ;) 
				$this->assignRef('coords', $coords);
				$this->assignRef('required', $require);
				$this->assignRef('failed', $failed);
				
			}		
	
			## Include functions for Bootstrap: (template choice)
			include_once( 'components/com_ticketmaster/assets/functions.php' );
			
			## Showing default template or bootstrap?
			$tpl = Template($config->load_bootstrap);			
		
			$this->assignRef('items', $items);
			$this->assignRef('waitlist', $waitlist);
			$this->assignRef('config', $config);
			$this->assignRef('price', $price);
			$this->assignRef('tos', $tos);
			parent::display($tpl);	

		}	
	
	}
	
	function _message($tpl=null) {
		
		$model	= $this->getModel('payment');
		
		$msg = $this->get('msg');
		$this->assignRef('msg', $msg);		
		
		$tpl = 'message';
		parent::display($tpl);
		
	}

}
?>
