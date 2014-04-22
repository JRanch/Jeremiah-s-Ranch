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

class ticketmasterViewVisitors extends JViewLegacy {
	

	function display($tpl =null) {

		## If we want the add/edit form..
		if($this->getLayout() == 'form') {
			$this->_displayForm($tpl);
			return;
		}
		
		$mainframe 	= JFactory::getApplication();
		$db    		= JFactory::getDBO();	
		$model		= $this->getModel();
		
		## Getting the items into a variable
		$items	= $this->get('list');
		$pagination = $this->get( 'Pagination' ); 

		$filter_order       = $mainframe->getUserStateFromRequest( 'filter_ordering', 'filter_ordering', 'a.fueltype', 'cmd' );
		$filter_order_Dir   = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '', 'string' );
		$search				= JString::strtolower( $search );	
		
		$lists['search']= $search;

		## Filling the Array() for doors and make a select list for it.
		$ordering = array(
			'a.name' => array('value' => 'a.name', 'text' => JText::_( 'COM_TICKETMASTER_SEARCH_NAME' )),
			'a.address' => array('value' => 'a.address', 'text' => JText::_( 'COM_TICKETMASTER_SEARCH_ADDRESS' )),
			'a.zipcode' => array('value' => 'a.zipcode', 'text' => JText::_( 'COM_TICKETMASTER_SEARCH_ZIPCODE' )),
			'a.city' => array('value' => 'a.city', 'text' => JText::_( 'COM_TICKETMASTER_SEARCH_CITY' )),
			'a.emailaddress' => array('value' => 'a.emailaddress', 'text' => JText::_( 'COM_TICKETMASTER_SEARCH_EMAIL' )),
			
		);
		$lists['ordering'] = JHTML::_('select.genericList', $ordering, 'filter_ordering', ' class="inputbox" ', 
		'value', 'text', $filter_order );
		
		$this->assignRef('pagination', $pagination);
		$this->assignRef('items', $items);
		$this->assignRef('lists', $lists);
		parent::display($tpl);		

	
	}
	
	function _displayForm($tpl =null) {
		
		## Connecting the Database
		$db    = JFactory::getDBO();
		$model	= $this->getModel();
		
		$data	= $this->get('data');
		$items	= $this->get('orderlist');
		$config	= $this->get('config');

		$query = "SELECT country_id AS id, country AS name 
				  FROM #__ticketmaster_country 
				  WHERE published = 1 
				  ORDER BY country ASC"; 
				  
		$db->setQuery($query);
		
		$countrylist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'id', 'name' );
		$countrylist	      = array_merge( $countrylist, $db->loadObjectList() );
		$lists['country'] = JHTML::_('select.genericlist',  $countrylist, 'country_id', 'class="input" ','id',
		 'name', intval($data->country_id) );

		$show_orderdate = array(
			'0' => array('value' => '0', 'text' => JText::_( 'COM_TICKETMASTER_NO' )),
			'1' => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_YES' )),
		);
		
		$lists['published'] = JHTML::_('select.genericList', $show_orderdate, 'published', ' class="inputbox" '. '', 
		'value', 'text', $data->published );	

		## Filling the Array() for doors and make a select list for it.
		$gender = array(
			1 => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_MR' )),
			2 => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_MRS' )),
			3 => array('value' => '3', 'text' => JText::_( 'COM_TICKETMASTER_MISS' )),
			4 => array('value' => '4', 'text' => JText::_( 'COM_TICKETMASTER_FAMILY' )),

		);
		
		$lists['gender'] = JHTML::_('select.genericList', $gender, 'gender', ' class="inputbox" ' , 'value', 'text', $data->gender );

		$this->assignRef('data', $data);
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
		
	}    


}
?>