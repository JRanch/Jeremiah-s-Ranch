<?php
/****************************************************************
 * @version			Ticketmaster 2.5.5
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

class TicketmasterViewVenues extends JViewLegacy {
	

	function display($tpl = null) {

		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
		
		## If we want the add/edit form..
		if($this->getLayout() == 'modal') {
			$this->_displayModal($tpl);
			return;
		}			
		
		## Model is defined in the controller
		$model	=& $this->getModel('venues');
		
		## Getting the items into a variable
		$items	=& $this->get('list');

		$this->assignRef('items', $items);
		parent::display($tpl);		

	
	}

	function _displayModal($tpl = null) {
		
		$db    = JFactory::getDBO();	

		## prepare list array
		$lists = array();
		
		## Model is defined in the controller
		$model	= $this->getModel();
		
		## Getting the items into a variable
		$items	= $this->get('list');

		$this->assignRef('items', $items);

		parent::display($tpl);

	}

	
	function _displayForm($tpl = null) {
			
		## Connecting the Database
		$db     = JFactory::getDBO();
		$model	= $this->getModel();
		$data	= $this->get('data');

		$yesno = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		);	
		$lists['map'] = JHTML::_('select.genericList', $yesno, 'map', ' class="inputbox" '. '', 
		'value', 'text', $data->map );

		$publish = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		);	
		
		$lists['published'] = JHTML::_('select.genericList', $publish, 'published', ' class="input" ','value', 'text', $data->published );		
		
		$own_ll = array(
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
		);
		
		$lists['own_ll'] = JHTML::_('select.genericList', $own_ll, 'own_ll', ' class="input" ', 'value', 'text', 0 );			
		
		$query = "SELECT country_2_code AS id, country AS name FROM #__ticketmaster_country WHERE published = 1"; 
		$db->setQuery($query);
		
		$country[] = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'id', 'name' );
		$country	 = array_merge( $country, $db->loadObjectList() );
		## Creating a list for the activation email.
		$lists['country']  = JHTML::_('select.genericlist', $country, 'country', 'class="input" ','id', 'name', $data->country );	


		$this->assignRef('data', $data);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
		
	}    


}
?>