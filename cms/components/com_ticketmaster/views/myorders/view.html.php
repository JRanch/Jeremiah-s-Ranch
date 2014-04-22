<?php
/**
 * @version		2.5.4
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

class TicketmasterViewMyOrders extends JView {

	function display() {

		$db    = JFactory::getDBO();
		$app   = JFactory::getApplication();	

		## Check if the user is logged in.
		$user = & JFactory::getUser();
		
		if (!$user->id) {
			$link = JRoute::_('index.php?option=com_ticketmaster');
			$app->redirect($link , JText::_( 'COM_TICKETMASTER_PLEASE_LOGIN' ));
		}	 
		
		## Model is defined in the controller
		$model	=& $this->getModel('myorders');
		
		$items	=& $this->get('items');	
		$config	=& $this->get('config');				

		$this->assignRef('items', $items);
		$this->assignRef('config', $config);

		parent::display($tpl);		

	
	}

}
?>
