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

class ticketmasterViewTickets extends JViewLegacy {
	

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

		$mainframe = JFactory::getApplication();
		
		$db = JFactory::getDBO();	
		
		$filter_order 	  = $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'filter_order_Dir', 'word' ); 

		## table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;		
		
		## Model is defined in the controller
		$model	= $this->getModel();
		
		## Getting the items into a variable
		$items			= $this->get('list');
		$childs			= $this->get('childs');
		$pagination 	= $this->get( 'pagination' );
		
		$db    = JFactory::getDBO();	
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();		

		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_t', 'filter_ordering_t','a.fueltype','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );

		$query = "SELECT eventid, CONCAT(groupname, ' - ' , eventname) AS name FROM #__ticketmaster_events WHERE published = 1 ORDER BY groupname ASC"; 
		$db->setQuery($query);
		
		$eventlist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'eventid', 'name' );
		$eventlist	      = array_merge( $eventlist, $db->loadObjectList() );
		$lists['eventid'] = JHTML::_('select.genericlist',  $eventlist, 'filter_ordering_t', 'class="input pull-right" ',
			'eventid', 'name', intval($filter_order) );

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('config', $config);
		$this->assignRef('childs', $childs);
		$this->assignRef('lists', $lists);
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
		$childs	= $this->get('childs');

		$this->assignRef('items', $items);
		$this->assignRef('childs', $childs);

		parent::display($tpl);

	}
	
	function _displayForm($tpl) {
		
		$mainframe = JFactory::getApplication();
		
		## Connecting the Database
		$db    = JFactory::getDBO();
		
		## prepare list array
		$lists = array();	
		
		$model	= $this->getModel();
		
		$data	= $this->get('data');
		$config	= $this->get('config');
		
		$query = "SELECT eventid, CONCAT(groupname, ' - ' , eventname) AS name FROM #__ticketmaster_events"; 
		$db->setQuery($query);
		
		$eventlist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'eventid', 'name' );
		$eventlist	      = array_merge( $eventlist, $db->loadObjectList() );
		$lists['eventid'] = JHTML::_('select.genericlist',  $eventlist, 'eventid', 'class="input-large" ','eventid',
		 'name', intval($data->eventid) );

		## Radio Buttons for published and featured
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="input-medium"', $data->published);	

		$query = "SELECT ticketid, CONCAT('-- ' , ticketname) AS name FROM #__ticketmaster_tickets
				  WHERE published = 1 AND parent = 0"; 
		$db->setQuery($query);
		
		$childlist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_MAIN_TICKET' ), 'ticketid', 'name' );
		$childlist	      = array_merge( $childlist, $db->loadObjectList() );
		$lists['parent'] = JHTML::_('select.genericlist',  $childlist, 'parent', 'class="input-large" ','ticketid',
		 'name', intval($data->parent) );

		unset($childlist);
		
		$query='SELECT ticketid, ticketname AS name 
				FROM #__ticketmaster_tickets 
				WHERE parent = 0';
		
		$db->setQuery($query);
		$faq_categories = $db->loadObjectList();
		
		$query='SELECT ticketid, ticketname AS name, parent AS ticketparent 
				FROM #__ticketmaster_tickets 
				WHERE parent != 0';
		
		$db->setQuery($query);
		$faq_cat_childs = $db->loadObjectList();
		
		## Create dropdown menu
		$options[] = JHTML::_('select.option', '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ));
		
		for ($i = 0, $n = count($faq_categories); $i < $n; $i++ ){
	
			$row        = $faq_categories[$i];
			$cid        = $row->ticketid;
			$options[]  = JHTML::_('select.option', $row->ticketid, $row->name);
				
			for ($i2 = 0, $n2 = count($faq_cat_childs); $i2 < $n2; $i2++ ){
				
				$childs     = $faq_cat_childs[$i2];
		
				if ($childs->ticketparent == $cid){
					$options[]  = JHTML::_('select.option', $childs->ticketid, '&nbsp;|-- '.$childs->name);
				}
	
			}
					
		}
		
		$lists['jquerselect1'] = JHTML::_('select.genericlist', $options, 'jquerselect', 'class="input"', 'value', 'text', 0);				
		
		## Remove options
		unset($options);
		
		## Create dropdown menu for the sample data:
		$options[] = JHTML::_('select.option', '0', JText::_( 'COM_TICKETMASTER_LOAD_FROM_CONFIGURATION' ));
		
		for ($i = 0, $n = count($faq_categories); $i < $n; $i++ ){
		
			$row        = $faq_categories[$i];
			$cid        = $row->ticketid;
			$options[]  = JHTML::_('select.option', $row->ticketid, $row->name);
		
			for ($i2 = 0, $n2 = count($faq_cat_childs); $i2 < $n2; $i2++ ){
		
				$childs     = $faq_cat_childs[$i2];
		
				if ($childs->ticketparent == $cid){
					$options[]  = JHTML::_('select.option', $childs->ticketid, '&nbsp;|-- '.$childs->name);
				}
		
			}
				
		}		
		
		$lists['jquerselect2'] = JHTML::_('select.genericlist', $options, 'sampledata', 'class="input pull-right"', 'value', 'text', 0);

		$query = "SELECT id, venue AS name FROM #__ticketmaster_venues WHERE published = 1"; 
		$db->setQuery($query);
		
		$venues[]	     = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_CHOOSE_VENUE' ), 'id', 'name' );
		$venues	         = array_merge( $venues, $db->loadObjectList() );
		$lists['venues'] = JHTML::_('select.genericlist',  $venues, 'venue', 'class="input-medium" ','id',
		 'name', intval($data->venue) );

		$show_orderdate = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
		);
		$lists['show_orderdate'] = JHTML::_('select.genericList', $show_orderdate, 'published', ' class="input-medium" '. '', 
		'value', 'text', $data->published );
		
		$lists['use_sale_stop'] = JHTML::_('select.genericList', $show_orderdate, 'use_sale_stop', ' class="input-medium" '. '', 
		'value', 'text', $data->use_sale_stop );
		
		$lists['show_seatplans'] = JHTML::_('select.genericList', $show_orderdate, 'show_seatplans', ' class="input-medium" '. '', 
		'value', 'text', $data->show_seatplans );	

		$lists['show_end_date'] = JHTML::_('select.genericList', $show_orderdate, 'show_end_date', ' class="input-medium" '. '',
				'value', 'text', $data->show_end_date );	

		$lists['scans_on'] = JHTML::_('select.genericList', $show_orderdate, 'scans_on', ' class="input-medium" '. '',
				'value', 'text', $data->scans_on );		
		
		$lists['requires_name'] = JHTML::_('select.genericList', $show_orderdate, 'named_tickets_required', ' class="input-medium" '. '',
				'value', 'text', $data->named_tickets_required );		

		$pdf_qrcode = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_USE_1D_BARCODE' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_USE_QR_BARCODE' )),
		);
		$lists['pdf_use_qrcode'] = JHTML::_('select.genericList', $pdf_qrcode, 'pdf_use_qrcode', ' class="input-medium" '. '', 
		'value', 'text', $data->pdf_use_qrcode );
		
		$counter_choice = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_USE_PARENT' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_USE_CHILD' )),
		);
		$lists['counter_choice'] = JHTML::_('select.genericList', $counter_choice, 'counter_choice', ' class="input" '. '', 
		'value', 'text', $data->counter_choice );		
		
		
		$ticket_size = array (
		'0' => array('value' => 'A5', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_SIZE_A5' )),
			'1' => array('value' => 'A4', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_SIZE_A4' )),
		);		
			
		$lists['ticket_size'] = JHTML::_('select.genericList', $ticket_size, 'ticket_size', ' class="input-medium" '. '', 
		'value', 'text', $data->ticket_size );	
		
		$ticket_orientation = array (
		'0' => array('value' => 'P', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_ORIENTATION_PORTRAIT' )),
			'1' => array('value' => 'L', 'text' => JText::_( 'COM_TICKETMASTER_TICKET_ORIENTATION_LANDSCAPE' )),
		);
		
			
		$lists['ticket_orientation'] = JHTML::_('select.genericList', $ticket_orientation, 'ticket_orientation', ' class="input-medium" '. '', 
		'value', 'text', $data->ticket_orientation );	
		
		$combine_multitickets = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
		);
			$lists['combine_multitickets'] = JHTML::_('select.genericList', $combine_multitickets, 'combine_multitickets', ' class="input-medium" '. '', 
		'value', 'text', $data->combine_multitickets );		
		
		$query = "SELECT COUNT(id) AS countvenues FROM #__ticketmaster_venues"; 
		$db->setQuery($query);
		$venue = $db->loadObject();
	

		$this->assignRef('venue', $venue);
		$this->assignRef('data', $data);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
		
	}    


}
?>