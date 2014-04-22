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

class TicketmasterViewCart extends JViewLegacy {

	function display($tpl = null) {
		
		## Model is defined in the controller
		$model	= $this->getModel('cart');
		
		## Getting the items into a variable
		$items	 = $this->get('data');
		$waiting = $this->get('waiters'); 
		$price	 = $this->get('price');
		$config	 = $this->get('config'); 
		
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
	
		$this->assignRef('items', $items);
		$this->assignRef('waiters', $waiting);
		$this->assignRef('config', $config);
		$this->assignRef('price', $price);
		
		parent::display($tpl);		

	
	}

}
?>
