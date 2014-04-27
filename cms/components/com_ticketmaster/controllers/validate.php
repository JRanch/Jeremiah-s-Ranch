<?php

/************************************************************
 * @version			ticketmaster 3.0.3
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerValidate extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->ordercode 	= JRequest::getInt('oc', 0);
		$this->id 			= JRequest::getInt('cid', 0);

	}
	
	function confirm(){
		
		## Below the option for paylater will be checked.
		## Check if this is an payment order;
		$jinput = JFactory::getApplication()->input;
		$order = $jinput->get('order', '', 'STRING');
		
		if($order != '') {	
			
			$decoded = base64_decode($order);
			$ordercode = explode('=', $decoded);
			
			## Connecting the DB 
			$db  = JFactory::getDBO();
			$app = JFactory::getApplication();	
			
			## Ordercode is empty!
			if(!isset($ordercode[1])){
				
				### redirect the customer to another page - not allowed to come here.
				$link = JRoute::_('index.php?option=com_ticketmaster&view=upcoming', $msg);	
				$this->setMessage(JText::_( 'COM_TICKETMASTER_INVALID_ORDER' ), 'warning');
				$this->setRedirect($link);	
				
			}else{ 

				## GETTING THE MODEL TO SAVE
				$model	= $this->getModel('validate');	
				
				if(!$model->updateWaitingList($ordercode[1])) {
					$msg = JText::_( 'COM_TICKETMASTER_VALIDATION_WAITINGLIST_FAILED' );
					$app->redirect('index.php?option=com_ticketmaster', $msg );
				}else{
				
					$msg = JText::_( 'COM_TICKETMASTER_VALIDATION_WAITINGLIST_COMPLETED' );
					$app->redirect('index.php?option=com_ticketmaster', $msg );				
				
				}
			
			}
		
		}
	
	}
	
	function pay(){
		
		## Below the option for paylater will be checked.
		## Check if this is an payment order;
		$jinput = JFactory::getApplication()->input;
		$paylater = $jinput->get('order', '', 'STRING');
		
		if($paylater != '') {
			
			$decoded = base64_decode($paylater);
			$ordercode = explode('=', $decoded);
			
			## Connecting the DB 
			$db  = JFactory::getDBO();
			$app = JFactory::getApplication();
			
			## Ordercode is empty!
			if(!isset($ordercode[1])){
				
				### redirect the customer to another page - not allowed to come here.
				$link = JRoute::_('index.php?option=com_ticketmaster&view=upcoming', $msg);	
				$this->setMessage(JText::_( 'COM_TICKETMASTER_INVALID_ORDER' ), 'warning');
				$this->setRedirect($link);	
				
			}else{ 
				
				//http://nas.rd-media.org/rdmedia2013/index.php?option=com_ticketmaster&controller=validate&task=pay&order=cGF5Zm9yb3JkZXI9NzI1NDcxMw==
				
				## OK, there is sanitized ordercode now.
				## We have to check if it still not paid.
				$sql = 'SELECT COUNT(orderid) AS total 
						FROM #__ticketmaster_orders 
						WHERE paid != 1
						AND ordercode = '.(int)$ordercode[1].''; 		
				
				$db->setQuery($sql);
				$data = $db->loadObject();			
				
				### If there are no paid orders.
				if ($data->total > 0) {
					
					## No paid orders, let's force to change the session
					## Customer needs to login for savety.
					$session = JFactory::getSession();
					$session->clear('ordercode');
					$session->set('ordercode', $ordercode[1]);
					
					## Redirect to the cart page to checkout again.
					$link = JRoute::_('index.php?option=com_ticketmaster&view=cart');	
					$this->setMessage(JText::_( 'COM_TICKETMASTER_THANK_YOU_FOR_MAKING_PAYMENT' ), 'message');
					$this->setRedirect($link, $msg);	
				
				}else{
					
					## It has been paid before -- redirect immediatly.
					$link = JRoute::_('index.php?option=com_ticketmaster&view=upcoming');	
					$this->setMessage(JText::_( 'COM_TICKETMASTER_PAYMENT_HAS_BEEN_PROCESS_BEFORE' ), 'message');
					$this->setRedirect($link, $msg);	
										
				}
					
			}
				
		}				
		
	}
	
	function validate(){
	
		$mainframe = JFactory::getApplication();
		
		## Getting the POST variables.
		$post = JRequest::get('post');
		
		if ($this->ordercode == 0) { 	
			$msg = JText::_( 'COM_TICKETMASTER_NO_VALID_ID' );
			$mainframe->redirect('index.php?option=com_ticketmaster', $msg );	
		}				

		if ($this->id == 0) { 	
			$msg = JText::_( 'COM_TICKETMASTER_NO_VALID_ID' );
			$mainframe->redirect('index.php?option=com_ticketmaster', $msg );	
		}	
		
		## GETTING THE MODEL TO SAVE
		$model	= $this->getModel('validate');
		
		## OK, all tickets have been added to the database session.<br />
		## We need to update the table for total-tickets.

		$path_include = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
		include_once( $path_include );
		
		if(isset($this->ordercode)) {  
		
			$sendconfirmation = new confirmation( (int)$this->ordercode );  
			$sendconfirmation->doConfirm();
			$sendconfirmation->doSend();
		
		}  		
		
		if(!$model->update($this->ordercode, $this->id)) {
			$msg = JText::_( 'COM_TICKETMASTER_VALIDATION_FAILED' );
			$mainframe->redirect('index.php?option=com_ticketmaster', $msg );
		}
		
		$msg = JText::_( 'COM_TICKETMASTER_VALIDATED' );
		$mainframe->redirect('index.php?option=com_ticketmaster', $msg );		
	
	}

}	
?>
