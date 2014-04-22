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
 
## No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewVenue extends JViewLegacy {

	function display($tpl = null) {
		
		$db  	= JFactory::getDBO();
		$app 	= JFactory::getApplication();
		## Model is defined in the controller
		$model	= $this->getModel('venue');

		## Getting the items into a variable
		$data	= $this->get('data');
		$items	= $this->get('items');
		$config	= $this->get('config');
		
		## Include functions for Bootstrap: (template choice)
		include_once( 'components/com_ticketmaster/assets/functions.php' );
		
		## Showing default template or bootstrap?
		$tpl = Template($config->load_bootstrap);			
		
		$this->assignRef('data', $data);
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);

		parent::display($tpl);		

	
	}

}
?>
