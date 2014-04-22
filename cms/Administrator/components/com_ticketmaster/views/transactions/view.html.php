<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## No Direct Access - Kill this Script!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewTransactions extends JViewLegacy {

	function display($tpl = null) {
	
		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
		
		$db    		= JFactory::getDBO();	
		## Model is defined in the controller
		$model		= $this->getModel();
		## Getting the items into a variable
		$items		= $this->get('list');
		$pagination	= $this->get('pagination');
		$data		= $this->get('config');

		$this->assignRef('pagination', $pagination);
		$this->assignRef('items', $items);
		$this->assignRef('data', $data);
		parent::display($tpl);

	}
	
	function _displayForm($tpl = null) {
		
		global $mainframe, $option;

		## Model is defined in the controller
		$model	=& $this->getModel();
		
		## Getting the items into a variable
		$data	=& $this->get('data');
		$config	=& $this->get('config');		

		$this->assignRef('data', $data);
		$this->assignRef('config', $config);
		parent::display($tpl);
		
	}    
}
?>
