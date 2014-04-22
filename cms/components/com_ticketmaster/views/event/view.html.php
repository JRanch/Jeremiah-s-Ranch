<?php
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright � 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/
 
## No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewEvent extends JViewLegacy {

	function display($tpl = null) {
		
		$db  	= JFactory::getDBO();
		$app 	= JFactory::getApplication();
		## Model is defined in the controller
		$model	= $this->getModel();

		## Getting the items into a variable
		$items	= $this->get('list');
		$childs = $this->get('childs'); 
		$config	= $this->get('config');
		
		if(count($items) < 1) {

			$link = JRoute::_('index.php?option=com_ticketmaster');
			$msg = JText::_( 'COM_TICKETMASTER_EVENT_IN_PAST' );
			$app->redirect($link, $msg);	

		}	
		
		$n = count($childs);
		
		if ($n > 0) {

			## Getting the dropdown for make search.
			$query = 'SELECT ticketid, ticketname FROM #__ticketmaster_tickets 
					  WHERE published = 1 
					  AND parent = '.$items->ticketid.' 
					  AND totaltickets > 0
					  ORDER BY ticketname'; 
			
			$db->setQuery($query);
			
			$childlist[]	   = JHTML::_('select.option',  '0', JText::_( 'SELECT TICKET' ), 'ticketid', 'ticketname' );
			$childlist	       = array_merge( $childlist, $db->loadObjectList() );
			$lists['tickets']  = JHTML::_('select.genericlist',  $childlist, 'ticketid', 'class="inputbox" size="1" ', 'ticketid',
			 'ticketname', 0);
			
		} else {

			## Getting the dropdown for make search.
			$query = 'SELECT ticketid, ticketname FROM #__ticketmaster_tickets 
					  WHERE published = 1 AND ticketid = '.$items->ticketid.'
					  ORDER BY ticketname'; 
			
			$db->setQuery($query);

			$childlist[]	   = JHTML::_('select.option',  $items->ticketid, $items->ticketname, 'ticketid', 'ticketname' );
			$lists['tickets']  = JHTML::_('select.genericlist',  $childlist, 'ticketid', 'class="inputbox" size="1" ', 'ticketid',
			 'ticketname', 0);		
		
		}
	    
		## Starting a session.
		$session = JFactory::getSession();
		## Gettig the orderid if there is one.
		$ordercode = $session->get('ordercode');
		
		$sql = 'SELECT COUNT(orderid) AS total FROM #__ticketmaster_orders WHERE ordercode = '.(int)$ordercode.' ';		
		$db->setQuery($sql);
		$ticket = $db->loadObject();
		
		## This query will only be executed when Ticketmaster Extended is installed.
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmasterext'.DS.'assets'.DS.'menu.php';
		
		if (file_exists($path)) { 
		
			$query = 'SELECT * 
					  FROM #__ticketmaster_tickets_ext 
					  WHERE ticketid = '.$items->ticketid.''; 			
					  
			$db->setQuery($query);
			$extended = $db->loadObject();					  
		
			## Ticketmaster Extended Assignment
			$this->assignRef('extended', $extended);		
			
		}

		## Include functions for Bootstrap: (template choice)
		include_once( 'components/com_ticketmaster/assets/functions.php' );
		
		## Showing default template or bootstrap?
		$tpl = Template($config->load_bootstrap);			
		
		$this->assignRef('ticket', $ticket);
		$this->assignRef('items', $items);
		$this->assignRef('childs', $childs);
		$this->assignRef('config', $config);
		$this->assignRef('lists', $lists);

		parent::display($tpl);		

	
	}

}
?>
