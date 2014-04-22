<?php
/****************************************************************
 * @version				2.5.5 ticketmaster 						
 * @package				ticketmaster								
 * @copyright           Copyright � 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org							
 * @website				http://www.rd-media.org						
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class ticketmasterControllerTicketbox extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'ticketbox');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'ticketbox');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}

	function modal() {
	
		JRequest::setVar( 'layout', 'modal');
		JRequest::setVar( 'view', 'ticketbox');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}
	
	function save() {

		$post = JRequest::get('post');
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		
		$model	=& $this->getModel('tickets');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_TICKET_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_TICKET_NOT_SAVED' );
		}
		$link = 'index.php?option=com_ticketmaster&controller=tickets';
		$this->setRedirect($link, $msg);
	}
	

	function refund()
	{

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 2;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		
		if(!$model->paymentsdone($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PAYMENTSTATUS_REFUNDED'));
	}

	function allpayments()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 1;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->paymentsdone($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PAYMENTS_PROCESSED'));
	}

	function unlock()
	{
		$mainframe =& JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 0;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->unblockticket($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKET_HAS_BEEN_UNBLOCKED'));
	}

	function blocked()
	{
		$mainframe =& JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 1;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->blockticket($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKET_HAS_BEEN_BLOCKED'));
	}
	
	function resendpayment()
	{
		
		$mainframe = JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');	
		
		if(!$model->paymentResender($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PAYMENT_REQUESTS_SENT'));			
		
	}

	function payment()
	{
		$mainframe = JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 1;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->paymentprocessor($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PAYMENT_PROCESSED'));
	}

	function nopayment()
	{
		$mainframe =& JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 0;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_ORDER'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->paymentprocessor($cid, $paid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PAYMENT_REMOVED'));
	}

	function processticket()
	{
		$mainframe =& JFactory::getApplication();

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 0;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_A_TICKET'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->ticketprocessor($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_TICKETBOX'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKETS_CREATED'));
	}

	function sendconfirmation(){
		
		$mainframe =& JFactory::getApplication();
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_A_TICKET'));
		}

		$db    = JFactory::getDBO();
		
		## Getting the config setting for sending.
		$sql = "SELECT persending FROM #__ticketmaster_config WHERE configid = 1 ";
		$db->setQuery($sql);
		$config = $db->loadObject();
		 
		if (count( $cid ) > $config->persending) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$msg  = JText::_( 'COM_TICKETMASTER_SELECTED_TO_MUCH').' '.$config->persending.' '.JText::_( 'COM_TICKETMASTER_TICKETS_SENDING');
			$mainframe->redirect($link, $msg);
		}		

		$model = $this->getModel('ticketbox');
		if(!$model->createconfirmation($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$mainframe->redirect($link, JText::_( 'COM_TICKETMASTER_ERROR_CREATING_CONFIRMATION'));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$msg  = JText::_( 'COM_TICKETMASTER_CONFIRMATIONS_SEND');
		$mainframe->redirect($link, $msg);		

	}

	function sendingticket()
	{
		$mainframe = JFactory::getApplication();
		

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		$paid = 0;	

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKETS_SEND_NO_ITEMS'));
		}
		
		$db    = JFactory::getDBO();
		## Making the query to check if there are active orders.
		$sql = "SELECT persending FROM #__ticketmaster_config
				WHERE configid = 1 ";
		 $db->setQuery($sql);
		 $config = $db->loadObject();
		 
		if (count( $cid ) > $config->persending) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$msg  = JText::_( 'COM_TICKETMASTER_TO_MUCH_ITEMS').' '.$config->persending.' '.JText::_( 'COM_TICKETMASTER_TO_SEND_AT_ONCE');
			$mainframe->redirect($link, $msg);
		}
		
		$model = $this->getModel('ticketbox');
		
		if(!$model->sendtickets($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link);
		}else{
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ITEMS_HAS_BEEN_SENT'));
		}
		

	}

	function sendticketcopy()
	{

		$cid = JRequest::getInt( 'cid' );

		$model = $this->getModel('ticketbox');
		if(!$model->reSendTickets($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_SENDING_ITEMS'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKET_COPIES_SENT'));
	}

	function publish()
	{
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		## Getting the task (publish/upnpublish)
		if ($this->getTask() == 'publish') {
			$publish = 1;
		} else {
			$publish = 0;
		}		

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKETS_PUBLISH_NO_ITEMS'));
		}

		$model = $this->getModel('ticketbox');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_TICKETS_PUBLISH_ERROR'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=ticketbox';
		$this->setRedirect($link);
	}

	function remove() {
	
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('ticketbox');
		if(!$model->removeTickets($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_ticketmaster&controller=ticketbox', JText::_( 'COM_TICKETMASTER_TICKETS_REMOVED'));
		
	}

}	
?>
