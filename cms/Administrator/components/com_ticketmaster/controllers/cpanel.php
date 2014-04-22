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
class TicketmasterControllerCPanel extends JControllerLegacy {

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
		JRequest::setVar( 'view', 'cpanel');
		parent::display();
	}
	
	function turnOffTicketmasterProSetting(){

		## Connect the model:
		$model = $this->getModel('cpanel');
		
		if(!$model->changeSettingPro()) {
			$link = 'index.php?option=com_ticketmaster&controller=cpanel';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_ERROR_ON_TICKETMASTER_PRO_CHANGE'));
		}
		
		$msg  = JText::_( 'COM_TICKETMASTER_TICKETMASTER_PRO_CHANGE_DONE');
		$link = 'index.php?option=com_ticketmaster&controller=cpanel';
		$this->setRedirect($link, $msg);
		
	}
   
   
}	
?>
