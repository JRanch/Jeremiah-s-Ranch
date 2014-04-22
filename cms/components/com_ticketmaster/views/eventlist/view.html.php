<?php

/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewEventlist extends JViewLegacy {

	function display($tpl=null) {

		$mainframe 	  = JFactory::getApplication();
		
		## Include functions for Bootstrap: (template choice)
		include_once( 'components/com_ticketmaster/assets/functions.php' );
		
		$filter_order = $mainframe->getUserStateFromRequest( 'filter_ordering', 'filter_ordering_t', 'a.ticketdate', 'cmd' );
		
		## Model is defined in the controller
		$model		  = $this->getModel();
		
		## Getting the items into a variable
		$items		  = $this->get('list');
		$data		  = $this->get('data');
		$pagination	  = $this->get('pagination');
		$config		  = $this->get('config');
		
		$javascript = 'onchange="document.adminForm.submit();"';

		## Filling the Array() for doors and make a select list for it.
		$ordering = array(
			'a.price' => array('value' => 'a.ticketprice', 'text' => JText::_( 'COM_TICKETMASTER_ORDER_PRICE' )),
			'a.ticketdate' => array('value' => 'a.ticketdate', 'text' => JText::_( 'COM_TICKETMASTER_ORDER_DATE' )),
			'a.totaltickets' => array('value' => 'a.totaltickets', 'text' => JText::_( 'COM_TICKETMASTER_AVAILEBILLITY' )),
		);
		
		$lists['ordering'] = JHTML::_('select.genericList', $ordering, 'filter_ordering_t', ' class="inputbox" '. $javascript, 'value', 'text', $filter_order );
		
		## Showing default template or bootstrap?
		$tpl = Template($config->load_bootstrap);		
		
		$uri = JFactory::getURI();
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		$this->assignRef('data', $data);
		$this->assignRef('pagination', $pagination);
		$this->assign('action', $uri->toString());
		$this->assign('ordering', $filter_order);
		
		parent::display($tpl);		

	
	}

}
?>
