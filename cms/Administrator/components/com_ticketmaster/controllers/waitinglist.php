<?php

/****************************************************************
 * @package			Ticketmaster 3.1.0								
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class TicketmasterControllerWaitingList extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'waitinglist');
		parent::display();
	}
	
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'waitinglist');
		parent::display();
	}	
	

	function confirm()
	{

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_SELECT_ITEM'));
		}

		$model = $this->getModel('waitinglist');
		if(!$model->confirmOrder($cid, 1)) {
			$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PRUBLISH_WAITINGLIST_ITEM'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_CONFIRMED_WAITINGLIST_ITEM'));
	} 
	
	function process()
	{

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_SELECT_ITEM'));
		}

		$model = $this->getModel('waitinglist');
		if(!$model->processOrder($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PROCESSING_WAITINGLIST_ITEM'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PROCESSED_WAITINGLIST_ITEM'));
	}  	  

	function remove() {
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('waitinglist');

		if(!$model->remove($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=waitinglist';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_DELETE_WL_ITEMS'));
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=waitinglist', JText::_( 'COM_TICKETMASTER_WAITINGLIST_DELETED'));
		
	}

	function save() {

		$post = JRequest::get('post');

		$model	=& $this->getModel('coupons');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_COUPON_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_COUPON_SAVED_FAILED' );
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=coupons';
		$this->setRedirect($link, $msg);
	}
	   
}	
?>
