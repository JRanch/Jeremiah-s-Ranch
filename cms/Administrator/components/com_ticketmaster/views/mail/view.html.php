<?php

/****************************************************************
 * @version			2.5.5											
 * @package			com_ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## No Direct Access - Kill this Script!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

## Make sure the user is authorized to view this page
## If client is not an admin, redirect to the mainpage.
$user = & JFactory::getUser();

class TicketmasterViewMail extends JViewLegacy {

	function display($tpl = null) {
	
		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}

		$mainframe = JFactory::getApplication();		
		
		$db    = JFactory::getDBO();	
		
		## Model is defined in the controller
		$model	=& $this->getModel();
		
		## Getting the items into a variable
		$items	=& $this->get('list');     
        		
		$this->assignRef('items', $items);
		
		parent::display($tpl);

	}

	
	function _displayForm($tpl = null) {
		
		$mainframe = JFactory::getApplication();		
		
		## Connecting the Database
		$db    = JFactory::getDBO();
		
		$id = JRequest::getInt('cid', 0);
		
		## Model is defined in the controller
		$model	=& $this->getModel();
		
		## Getting the items into a variable
		$data	=& $this->get('data');
		
		if(!$data){
			$data->published = 0;
			$data->template_type = 0;
			$data->receive_bcc = 0;
		}

		## Radio Buttons for published and featured
		$publish = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PUBLISHED' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_UNPUBLISHED' )),
		);	
		$lists['published'] = JHTML::_('select.genericList', $publish, 'published', ' class="inputbox" '. '', 
		'value', 'text', $data->published );	
		
		## Radio Buttons for published and featured
		$template = array(
			'1' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_MAIL_TEMPLATE' )),
			'0' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PAGE_TEMPLATE' )),
		);	
		$lists['template'] = JHTML::_('select.genericList', $template, 'template_type', ' class="inputbox" '. '', 
		'value', 'text', $data->template_type );			

		$yn = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		);	

		$lists['bcc'] = JHTML::_('select.genericList', $yn, 'receive_bcc', ' class="inputbox" '. '', 
		'value', 'text', $data->receive_bcc );			

		$this->assignRef('data', $data);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
		
	}    
}
?>
