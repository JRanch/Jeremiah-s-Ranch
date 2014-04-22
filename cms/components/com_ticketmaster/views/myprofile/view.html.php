<?php
/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org
 */

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewmyProfile extends JView {

	function display() {

		$db    = JFactory::getDBO();
		$app   = JFactory::getApplication();	

		## Making the query to check if there are active orders.
		$sql = "SELECT * FROM #__ticketmaster_config WHERE configid = 1 ";
		$db->setQuery($sql);
		$config = $db->loadObject();		 
		
		## Model is defined in the controller
		$model	=& $this->getModel('myprofile');
		
		$data	=& $this->get('data');

		## Check if the user is logged in.
		$user = & JFactory::getUser();
		
		if (!$user->id) {
			$link = JRoute::_('index.php?option=com_ticketmaster');
			$app->redirect($link , JText::_( 'COM_TICKETMASTER_PLEASE_LOGIN' ));
		}

		## Creating the drop down menu for days
		for ($i = 1, $n = 31; $i <= $n; $i++ ){
			 $days[] = JHTML::_('select.option', $i, $i);
		}	
		
		$birthday = explode('-', $data->birthday);						
		
		## Create <select name="year_from" class="inputbox"></select> ##
		$lists['day'] = JHTML::_('select.genericlist', $days, 'day', 'class="inputbox"', 'value', 'text', $birthday[3]);

		## Filling the Array() for doors and make a select list for it.
		$month = array(
			1 => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_JANUARI' )),
			2 => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_FEBRUARI' )),
			3 => array('value' => '3', 'text' => JText::_( 'COM_TICKETMASTER_MARCH' )),
			4 => array('value' => '4', 'text' => JText::_( 'COM_TICKETMASTER_APRIL' )),
			5 => array('value' => '5', 'text' => JText::_( 'COM_TICKETMASTER_MAY' )),
			6 => array('value' => '6', 'text' => JText::_( 'COM_TICKETMASTER_JUNE' )),
			7 => array('value' => '7', 'text' => JText::_( 'COM_TICKETMASTER_JULY' )),
			8 => array('value' => '8', 'text' => JText::_( 'COM_TICKETMASTER_AUGUST' )),
			9 => array('value' => '9', 'text' => JText::_( 'COM_TICKETMASTER_SEPTEMBER' )),
			10 => array('value' => '10', 'text' => JText::_( 'COM_TICKETMASTER_OCTOBER' )),
			11 => array('value' => '11', 'text' => JText::_( 'COM_TICKETMASTER_NOVEMBER' )),
			12 => array('value' => '12', 'text' => JText::_( 'COM_TICKETMASTER_DECEMBER' )),

		);
		
		$lists['month'] = JHTML::_('select.genericList', $month, 'month', ' class="inputbox" '. $javascript , 'value', 'text', $birthday[1] );

		## Creating the drop down menu for years,		
		for ($i = 1930, $n = 2012; $i <= $n; $i++ ){
			 $years[] = JHTML::_('select.option', $i, $i);
		}												
		
		## Create <select name="year_from" class="inputbox"></select> ##
		$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="inputbox"', 'value', 'text', $birthday[0]);
		
		$query = "SELECT country_id AS id, country AS name 
				  FROM #__ticketmaster_country 
				  WHERE published = 1
				  AND country_id != 1 
				  ORDER BY country ASC"; 
				  
		$db->setQuery($query);
		
		$countrylist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'id', 'name' );
		$countrylist	      = array_merge( $countrylist, $db->loadObjectList() );
		$lists['country'] = JHTML::_('select.genericlist',  $countrylist, 'country_id', 'class="inputbox" size="1" '.$javascript,'id',
		 'name', intval($data->country_id) );
 		
		
		$this->assignRef('lists', $lists);
		$this->assignRef('data', $data);
		$this->assignRef('config', $config);

		parent::display($tpl);		

	
	}

}
?>
