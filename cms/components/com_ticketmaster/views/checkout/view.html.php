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

class TicketmasterViewCheckout extends JViewLegacy {

	function display($tpl = null) {

		$app = JFactory::getApplication();
		$db  = JFactory::getDBO();	
		
		## Check if we are in Joomla 3 or not.
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
		$info = $app->getUserState('com_ticketmaster.registration');

		## Making the query to check if there are active orders.
		$sql = "SELECT * FROM #__ticketmaster_config WHERE configid = 1 ";
		$db->setQuery($sql);
		$config = $db->loadObject();		 
		
		## Model is defined in the controller
		$model	= $this->getModel('checkout');
		
		$data	= $this->get('data');

		if ($config->pro_installed == 1){
			
			## will only be loaded if PRO is installed.
			## it won't work if you don't have the pro tables and views.
			$require  = $this->get('datacheck');
			
			if ($require->total > 0){
				$link = JRoute::_('index.php?option=com_ticketmaster&view=cart');
				$app->redirect($link, $require->total.' '.JText::_( 'COM_TICKETMASTER_TICKETS_REQUIRES_SEAT' ));					
			}
			
		}

		## Check if the user is logged in.
		$user =  JFactory::getUser();
		
		if ($user->id) {
			$link = JRoute::_('index.php?option=com_ticketmaster&view=profile');
			$app->redirect($link);	
		}

		## Filling the Array() for doors and make a select list for it.
		$gender = array(
			1 => array('value' => '1', 'text' => JText::_( 'COM_TICKETMASTER_MR' )),
			2 => array('value' => '2', 'text' => JText::_( 'COM_TICKETMASTER_MRS' )),
			3 => array('value' => '3', 'text' => JText::_( 'COM_TICKETMASTER_MISS' )),
			4 => array('value' => '4', 'text' => JText::_( 'COM_TICKETMASTER_FAMILY' )),

		);
		
		$lists['gender'] = JHTML::_('select.genericList', $gender, 'gender', ' class="inputbox" ' , 'value', 'text', 1 );

		## Creating the drop down menu for days
		for ($i = 1, $n = 31; $i <= $n; $i++ ){
			 $days[] = JHTML::_('select.option', $i, $i);
		}												
		
		## Create <select name="year_from" class="inputbox"></select> ##
		$lists['day'] = JHTML::_('select.genericlist', $days, 'day', 'class=" input-mini"', 'value', 'text', $info['day']);

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
		
		$lists['month'] = JHTML::_('select.genericList', $month, 'month', ' class="input input-small" ' , 'value', 'text', $info['month'] );
		
		## Get current year for dropdown menu:
		$current_year = date('Y');
		
		## Creating the drop down menu for years,		
		for ($i = 1930, $n = $current_year; $i <= $n; $i++ ){
			 $years[] = JHTML::_('select.option', $i, $i);
		}												
		
		## Create <select name="year_from" class="inputbox"></select> ##
		$lists['year'] = JHTML::_('select.genericlist', $years, 'year', 'class="input  input-mini"', 'value', 'text', $info['year']);
		

		$query = "SELECT country_id AS id, country AS name 
				  FROM #__ticketmaster_country 
				  WHERE published = 1
				  AND country_id != 1 
				  ORDER BY country ASC"; 
				  
		$db->setQuery($query);

		$db->setQuery($query);
		
		$countrylist[]	  = JHTML::_('select.option',  '0', JText::_( 'COM_TICKETMASTER_PLS_SELECT' ), 'id', 'name' );
		$countrylist	      = array_merge( $countrylist, $db->loadObjectList() );
		$lists['country'] = JHTML::_('select.genericlist',  $countrylist, 'country_id', 'class="inputbox"','id',
		 'name', intval($info['country_id']) );
 		
		
		$this->assignRef('lists', $lists);
		$this->assignRef('data', $data);
		$this->assignRef('config', $config);

		parent::display($tpl);		

	
	}

}
?>
