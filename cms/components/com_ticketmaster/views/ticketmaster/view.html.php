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

## No direct access to this script.
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewTicketmaster extends JViewLegacy {

	function display($tpl = null) {
		
		## Model is defined in the controller
		$model	= $this->getModel();
		
		## Getting the items into a variable
		$items	= $this->get('list');
		$config	= $this->get('config');
	
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		
		## Check if this is Joomla 2.5 or 3.0.+
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
		
		if($isJ30) {
				
			$tpl = 'bootstrap';
			
		}else{
			
			## J25, but want to load bootstrap!
			if($config->load_bootstrap_tpl == 1){
				$tpl = 'bootstrap';
			}
				
		}
		
		parent::display($tpl);		

	
	}

}
?>
