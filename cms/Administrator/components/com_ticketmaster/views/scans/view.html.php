<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## No Direct Access - Kill this Script!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewScans extends JViewLegacy {

	function display($tpl = null) {
	
		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
		
		$db    		= JFactory::getDBO();	
		$mainframe  = JFactory::getApplication();
		
		$filter_scan_result = $mainframe->getUserStateFromRequest( 'filter_scan_result', 'filter_scan_result','0','cmd' );
		$search	= $mainframe->getUserStateFromRequest( 'searchbox', 'searchbox', '', 'string' );
		$search	= JString::strtolower( $search );
		
		$lists['search']= $search;
		
		## Filling the Array() for doors and make a select list for it.
		$result = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_PLS_SELECT' )),
			'1' => array('value' => '100', 'text' => JText::_( 'COM_TICKETMASTER_SCAN_SUCCESS' )),
			'2' => array('value' => '101', 'text' => JText::_( 'COM_TICKETMASTER_SCAN_BLACKLISTED' )),
			'3' => array('value' => '102', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_WAS_UNPAID' )),
			'4' => array('value' => '103', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_WAS_SCANNED_BEFORE' )),
			'5' => array('value' => '104', 'text' => JText::_( 'COM_TICKETMASTER_UNAUTHORIZED_SCANNER' )),
			'6' => array('value' => '105', 'text' => JText::_( 'COM_TICKETMASTER_NO_BARCODE_FOUND' )),
		);
		
		$lists['result'] = JHTML::_('select.genericList', $result, 'filter_scan_result', ' class="input-medium" ', 'value', 'text', (int)$filter_scan_result );		
				
		## Model is defined in the controller
		$model		= $this->getModel();
		## Getting the items into a variable
		$items		= $this->get('list');
		$pagination	= $this->get('pagination');
		$data		= $this->get('config');

		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
		$this->assignRef('data', $data);
		parent::display($tpl);

	}
	
}
?>
