
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

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class TicketmasterViewCountries extends JViewLegacy {
	

	function display($tpl = null) {

		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
	
		$db    = JFactory::getDBO();			
		## Model is defined in the controller
		$model	=& $this->getModel('countries');
		
		## Getting the items into a variable
		$items	=& $this->get('list');
		$config	=& $this->get('config');
		$pagination = $this->get('Pagination');

		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);	

	
	}
		
	function _displayForm($tpl = null) {
		
		## Connecting the Database
		$db    = JFactory::getDBO();
		
		$model	= $this->getModel('countries');
		
		## Get the data for the product
		$items	= $this->get('data');
		## Get the configuration
		$config	= $this->get('config');	
		
		if(!$items){
			$items->published = 0;
			$items->requires_vat = 0;
		}
									 
		$state = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_UNPUBLISHED' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PUBLISHED' )),
		);
		$lists['published'] = JHTML::_('select.genericList', $state, 'published', ' class="inputbox" ', 'value', 'text', $items->published );							
		
		$yes_no = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
		);
		$lists['requires_vat'] = JHTML::_('select.genericList', $yes_no, 'requires_vat', ' class="styled-select" '. '', 
										'value', 'text', $items->requires_vat );	

		
		$this->assignRef('data', $items);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		
		parent::display($tpl);
		
	}    

}
?>