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
class ticketmasterControllerCountries extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'countries');
		parent::display();
	}
	
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'countries');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

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
			$link = 'index.php?option=com_ticketmaster&controller=countries';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_SELECT_ITEM'));
		}

		$model = $this->getModel('countries');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=countries';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PRUBLISH_COUNTRY'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=countries';
		$this->setRedirect($link);
	}   

	function remove() {
	
		global $option, $mainframe;
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('countries');

		if(!$model->remove($cid)) {
			$link = 'index.php?option=com_ticketmaster&controller=countries';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_DELETE_COUNTRY'));
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=countries', JText::_( 'COM_TICKETMASTER_COUNTRIES_DELETED'));
		
	}

	function save() {

		$post = JRequest::get('post');

		$model	=& $this->getModel('countries');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_COUNTRY_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_COUNTRY_SAVED_FAILED' );
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=countries';
		$this->setRedirect($link, $msg);
	}
	   
}	
?>
