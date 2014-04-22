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

## This Class contains all data for the dealer manager
class TicketmasterControllerMail extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		## Register Extra tasks
		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'unpublish','publish' );
		$this->registerTask( 'apply','save' );
	}

	## This function will display if there is no choice.
	function display() {
	
		JRequest::setVar( 'layout', 'default');
		JRequest::setVar( 'view', 'mail');
		parent::display();
	}

	## This function will display if there is no choice.
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'mail');
		parent::display();
	}

	function save() {
		
		$app = JFactory::getApplication();
		
		$post	          = JRequest::get('post');
		$post['mailbody'] = JRequest::getVar('mailbody', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		 
		 $model	= $this->getModel('mail');

		 if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_MAILTEMPLATE_SAVED' );
		 } else {
			$msg = JText::_( 'COM_TICKETMASTER_MAILTEMPLATE_NOTSAVED' );
		 }
		 
		 ## OK, everything is done, redirect the user now.
		$app->redirect('index.php?option=com_ticketmaster&controller=mail', $msg);

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
			$link = 'index.php?option=com_ticketmaster&controller=mail';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_NO_ACTIONS_SELECTED' ));
		}

		$model = $this->getModel('mail');
		
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=mail';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_PUBLISHING_ACTIONS' ));
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=mail';
		$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_PUBLISHED_OK' ));
	}

	function remove() {
	
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('mail');
		
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=mail', JText::_( 'COM_TICKETMASTER_TRANSACTION_TRUNCATED' ));
		
	}	

   
}	
?>
