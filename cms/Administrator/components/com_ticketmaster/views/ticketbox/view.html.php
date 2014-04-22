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

class ticketmasterViewTicketbox extends JViewLegacy {
	

	function display($tpl = null) {

		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}

		$mainframe = JFactory::getApplication();
		
		$db    = JFactory::getDBO();	
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config
			  WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();

		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_e', 'filter_ordering_e','0','cmd' );
		$filter_sent      = $mainframe->getUserStateFromRequest( 'filter_ordering_sent', 'filter_ordering_sent','0','cmd' );
		$filter_pdf       = $mainframe->getUserStateFromRequest( 'filter_ordering_pdf', 'filter_ordering_pdf','0','cmd' );
		$filter_paid      = $mainframe->getUserStateFromRequest( 'filter_ordering_paid', 'filter_ordering_paid','0','cmd' );
		$filter_event     = $mainframe->getUserStateFromRequest( 'filter_ordering_event', 'filter_ordering_event','0','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', '', 'word');
		
		$sql='SELECT  * FROM #__ticketmaster_tickets
			  WHERE published = 1'; 
	 
		$db->setQuery($sql);		

		$eventlist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_SELECTTICKET' ), 'ticketid', 'ticketname' );
		$eventlist	      = array_merge( $eventlist, $db->loadObjectList() );
		$lists['eventid'] = JHTML::_('select.genericlist',  $eventlist, 'filter_ordering_e', 'class="input-medium" ', 'ticketid', 
		'ticketname', intval($filter_order) );
		
		$query='SELECT eventid, eventname FROM #__ticketmaster_events'; 
	 
		$db->setQuery($query);
		$eventing = $db->loadObjectList();		

		$eventcurrent[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_SELECT_EVENT' ), 'eventid', 'eventname' );
		$eventcurrent	      = array_merge( $eventcurrent, $db->loadObjectList() );
		$lists['eventcurrent'] = JHTML::_('select.genericlist',  $eventcurrent, 'filter_event', 'class="input-medium"', 
		'eventid', 'eventname', intval($filter_event) );	
		
		## Model is defined in the controller
		$model		= $this->getModel();
		
		## Getting the items into a variable
		$items		= $this->get('list');
		$pagination = $this->get('Pagination');

		$search	= $mainframe->getUserStateFromRequest( 'searchbox', 'searchbox', '', 'string' );
		$search	= JString::strtolower( $search );	
		
		$lists['search']= $search;

		## Filling the Array() for doors and make a select list for it.
		$sent = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_PLS_SELECT' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_SENT_TICKETS' )),
			'2' => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_UNSENT_TICKETS' )),
		);
		$lists['sent'] = JHTML::_('select.genericList', $sent, 'filter_ordering_sent', ' class="input-medium" ', 'value', 'text', 
		(int)$filter_sent ); 

		## Filling the Array() for doors and make a select list for it.
		$pdf_created = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_PLS_SELECT' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PROCESSED_PDF' )),
			'2' => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_UNPROCESSED_PDF' )),
		);
		$lists['pdf_created'] = JHTML::_('select.genericList', $pdf_created, 'filter_ordering_pdf', ' class="input-medium" ', 
		'value', 'text', (int)$filter_pdf ); 

		## Filling the Array() for doors and make a select list for it.
		$paid = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_PLS_SELECT' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PAID' )),
			'2' => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_UNPAID' )),
			'3' => array('value' => '3', 'text' => JText::_( 'COM_TICKETMASTER_REFUNDED' )),
			'4' => array('value' => '4', 'text' => JText::_( 'COM_TICKETMASTER_PENDING' )),
		);
		$lists['paid'] = JHTML::_('select.genericList', $paid, 'filter_ordering_paid', ' class="input-medium" ', 
		'value', 'text', (int)$filter_paid ); 		
		
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('lists', $lists);
		parent::display($tpl);		

	
	}
	
	function _displayForm($tpl = null) {
		
		$mainframe = JFactory::getApplication();
		
		$db    = JFactory::getDBO();	
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config
			  WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		$model	= $this->getModel();
		
		$data	= $this->get('data');
		$remark	= $this->get('remark');
		$items	= $this->get('client');
		$price	= $this->get('price');
		
		if ($config->pro_installed == 1){
			
			## will only be loaded if PRO is installed.
			## it won't work if you don't have the pro tables and views.
			$coords	= $this->get('extdata');
			## Assign data to the view ;) 
			$this->assignRef('coords', $coords);
			
		}

		## Filling the Array() for doors and make a select list for it.
		$paid = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_UNPAID' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_PAID' )),
		);
		$lists['paid'] = JHTML::_('select.genericList', $paid, 'paid', ' class="inputbox" '. '', 'value', 'text', $items->paid ); 

		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0];
		
		
		$this->assignRef('data', $data);
		$this->assignRef('remark', $remark);
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		$this->assignRef('price', $price);
		parent::display($tpl);
		
	}    


}
?>