<?php
/****************************************************************
 * @version				2.5.5 ticketmaster 						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org							
 * @website				http://www.rd-media.org						
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class TicketmasterControllerTransactions extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
			## Register Extra tasks
			$this->registerTask( 'add' , 'edit' );
			$this->registerTask('unpublish','publish');
			$this->registerTask('apply','save' );	
	}

	## This function will display if there is no choice.
	function display() {
	
		JRequest::setVar( 'layout', 'default');
		JRequest::setVar( 'view', 'transactions');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'transactions');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}


	function remove() {
		
		$mainframe =& JFactory::getApplication();
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('transactions');
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$link = 'index.php?option=com_ticketmaster&controller=transactions';
		$msg  = JText::_( 'COM_TICKETMASTER_SELECTED_TO_MUCH').' '.$config->persending.' '.JText::_( 'COM_TICKETMASTER_TRANSACTIONS_REMOVED');
		$mainframe->redirect($link, $msg);

	}

   
}	
?>
