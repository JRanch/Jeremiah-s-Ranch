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
class ticketmasterControllerTickets extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'tickets');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'tickets');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}
	
	function modal() {
	
		JRequest::setVar( 'layout', 'modal');
		JRequest::setVar( 'view', 'tickets');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}		
	
	function save() {

		$post = JRequest::get('post');
		$post['ticketdescription'] = JRequest::getVar('ticketdescription', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		
		$event = JRequest::getInt('eventid', 0);

		$model	= $this->getModel('tickets');	

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_TICKET_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_TICKET_NOT_SAVED' );
		}
                
         ## Count only parents
         if ($post['parent'] == 0){
             if(!$model->update($event)) {
                 $link = 'index.php?option=com_ticketmaster&controller=tickets';
                 $this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_DURING_UPDATE'));
               }		
          }
		
        ## obtain the ticket id that has been created.  
        $post['ticketid'] = $model->getTicketID();
          
        ## TRIGGER INVOICING PLUGIN AND RELATED onAfterSaveTicket
        JPluginHelper::importPlugin('rdmediahelpers');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('OnAfterSaveTicket', array($post) );
                
		$link = 'index.php?option=com_ticketmaster&controller=tickets';
		$this->setRedirect($link, $msg);
	}

	function cleanuptickets() {
		
		## Get desired model for cleanup perform
		$model	=& $this->getModel('tickets');
		
		$count = $model->cleanup($cleanup);
		$msg = $count.' '.JText::_( 'COM_TICKETMASTER_CLEANUP_FUNCTION' );

		$link = 'index.php?option=com_ticketmaster&controller=tickets';
		$this->setRedirect($link, $msg);
	}
	
	function noactivation() {
		
		## Get desired model for cleanup perform
		$model	=& $this->getModel('tickets');
		
		$count = $model->notactivated($cleanup);
		$msg = $count. ' ' .JText::_( 'COM_TICKETMASTER_NOACTVATION_FUNCTION' );

		$link = 'index.php?option=com_ticketmaster&controller=tickets';
		$this->setRedirect($link, $msg);
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
			$link = 'index.php?option=com_ticketmaster&controller=tickets';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_NO_SELECTION'));
		}

		$model = $this->getModel('tickets');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=tickets';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_DURING_PUBLISH'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=tickets';
		$this->setRedirect($link);
	}

	function remove() {
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('tickets');
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=tickets', JText::_( 'COM_TICKETMASTER_DELETED_TICKETS_OK'));
		
	}

    public function orderup() {
    	
        ## Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $cids    = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cids);
        
        ## Get model
        $model = $this->getModel('tickets');
        $model->move($cids[0],-1);

        ## Redirecting
        $this->setRedirect('index.php?option=com_ticketmaster&controller=tickets');
    }

    public function orderdown() {
		
		global $option;
    	
        ## Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $cids    = JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger($cids);
        
        ## Get model
        $model = $this->getModel('tickets');
        $model->move($cids[0],1);

        ## Redirecting
        $this->setRedirect('index.php?option=com_ticketmaster&controller=tickets');
    }

	function saveorder(){
	
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('tickets');
		$model->saveorder($cid, $order);

		$msg = JText::_( 'COM_TICKETMASTER_ORDERING_SAVED' );
		$this->setRedirect( 'index.php?option=com_ticketmaster&controller=tickets', $msg );

	}
   
}	
?>
