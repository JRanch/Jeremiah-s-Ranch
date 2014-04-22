<?php

/****************************************************************
 * @package			Ticketmaster 2.5.5								
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class TicketmasterControllerCoupons extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'coupons');
		parent::display();
	}
	
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'coupons');
		parent::display();
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
			$link = 'index.php?option=com_ticketmaster&controller=coupons';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_SELECT_ITEM'));
		}

		$model = $this->getModel('coupons');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=coupons';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PRUBLISH_COUPON'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=coupons';
		$this->setRedirect($link);
	}   

	function remove() {
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('coupons');

		if(!$model->remove($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=coupons';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_DELETE_COUPON'));
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=coupons', JText::_( 'COM_TICKETMASTER_COUPON_DELETED'));
		
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
