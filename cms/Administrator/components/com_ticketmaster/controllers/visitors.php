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
class ticketmasterControllerVisitors extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'visitors');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'visitors');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}
	
	function save() {

		$post = JRequest::get('post');

		$model	=& $this->getModel('visitors');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_VISITOR_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_VISITOR_SAVED_FAILED' );
		}
		$link = 'index.php?option=com_ticketmaster&controller=visitors';
		$this->setRedirect($link, $msg);
	}


	function publish()
	{
		global $mainframe;

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		## Getting the task (publish/upnpublish)
		if ($this->getTask() == 'publish') {
			$publish = 1;
		} else {
			$publish = 0;
		}		

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=tickets';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PLEASE_SELECT_VISITOR'));
		}

		$model = $this->getModel('visitors');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=visitors';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_FAILED_PUBLISH_VISITOR'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=visitors';
		$this->setRedirect($link);
	}

	function remove() {
	
		global $option;
	
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('visitors');
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=visitors', JText::_( 'COM_TICKETMASTER_DELETED_VISITORS'));
		
	}

   
}	
?>
