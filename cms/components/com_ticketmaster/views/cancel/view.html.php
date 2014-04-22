<?php

/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewCancel extends JView {

	function display() {
	
		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		## We need the order code before removing the order.
		$ordercode = $session->get('ordercode');

		## Database driver
		$db = JFactory::getDBO();
		
		## Model is defined in the controller
		$model	=& $this->getModel('cancel');
		
		## Getting the items into a variable
		$data	=& $this->get('data'); 		
		 
		$this->assignRef('item', $data);
		 
		parent::display($tpl);		

	
	}

}
?>
