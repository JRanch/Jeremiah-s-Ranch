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

class TicketmasterControllerCancel extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		## We need the order code before removing the order.
		$this->ordercode = $session->get('ordercode');
		## We can destroy the session as they want to cancel.
		$session->clear('ordercode');

	}
	
	function remove() {
	
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('cancel');
		if(!$model->removeTickets($this->ordercode)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_ticketmaster', JText::_( 'COM_TICKETMASTER_TICKETS_REMOVED'));
		
	}

}	
?>
