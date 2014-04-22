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

class ticketmasterViewExport extends JViewLegacy{
	

	function display($tpl=null) {
		
		$db     = JFactory::getDBO();	
		$model	= $this->getModel('export');
		
		## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_config
				WHERE configid = 1 ";
		 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		## Getting the items into a variable
		$items	= $this->get('list');
        $sold       = $this->get('sold');
		$added      = $this->get('added');		

		## Filling the Array() for a dropdown list.
		$orders = array(
			'1' => array('value' => '1', 'text' => ''.JText::_( 'COM_TICKETMASTER_ALL_ORDERS' )),
			'2' => array('value' => '2', 'text' => ''.JText::_( 'COM_TICKETMASTER_PAIDORDERS' )),
			'3' => array('value' => '3', 'text' => ''.JText::_( 'COM_TICKETMASTER_UNPAIDORDERS' )),
		);
		$lists['orders'] = JHTML::_('select.genericList', $orders, 'orders', '  '. '', 'value', 'text', 1 );

		$yesno = array(
			'1' => array('value' => '1', 'text' => ''.JText::_( 'COM_TICKETMASTER_YES' )),
			'0' => array('value' => '0', 'text' => ''.JText::_( 'COM_TICKETMASTER_NO' )),
		);

		$lists['send'] = JHTML::_('select.genericList', $yesno, 'send', '  '. '', 'value', 'text', 1 );

		$lists['save'] = JHTML::_('select.genericList', $yesno, 'save', '  '. '', 'value', 'text', 1 );

		
		$this->assignRef('lists', $lists);
		$this->assignRef('items', $items);
        $this->assignRef('sold', $sold);
		$this->assignRef('added', $added);		
		$this->assignRef('config', $config);
		parent::display($tpl);		

	
	}

}
?>