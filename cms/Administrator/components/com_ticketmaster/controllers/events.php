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
class ticketmasterControllerEvents extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'events');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'events');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}
	
	function modal() {
	
		JRequest::setVar( 'layout', 'modal');
		JRequest::setVar( 'view', 'events');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}	
	
	function save() {

		$post	            	  = JRequest::get('post');
		$post['eventdescription'] = JRequest::getVar('eventdescription', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		
		## Make a proper date of the event date and closing date.
		jimport ('joomla.utilities.date');
		$date = new JDate($post['closingdate']);
		$post['closingdate'] = $date->Format('Y-m-d');			
		
		$date = new JDate($post['eventdate']);
		$post['eventdate'] = $date->Format('Y-m-d');
		
		if( JRequest::getVar('venue', 0 ) ) {
			$msg = JText::_( 'COM_TICKETMASTER_EVENT_NOT_SAVED_ERROR_VENUE' );
			$link = 'index.php?option=com_ticketmaster&controller=events';
			$this->setRedirect($link, $msg);			
		}
		
		$model	=& $this->getModel('events');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_EVENT_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_EVENT_NOT_SAVED' );
		}
		$link = 'index.php?option=com_ticketmaster&controller=events';
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
			$link = 'index.php?option=com_ticketmaster&controller=events';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_SELECT_EVENT'));
		}

		$model = $this->getModel('events');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=events';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PRUBLISH_EVENT'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=events';
		$this->setRedirect($link);
	}

	function remove() {
	
		global $option, $mainframe;
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('events');
		
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=events', JText::_( 'COM_TICKETMASTER_EVENT_DELETED'));
		
	}

   
}	
?>
