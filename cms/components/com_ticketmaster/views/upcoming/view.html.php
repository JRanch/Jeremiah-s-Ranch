<?php
/************************************************************
 * @version			ticketmaster 3.0.1
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

class TicketmasterViewUpcoming extends JViewLegacy {

	function display($tpl=null) {

		$mainframe = JFactory::getApplication();
		
		## Include functions for Bootstrap:
		include_once( 'components/com_ticketmaster/assets/functions.php' );
		
		$filter_order = $mainframe->getUserStateFromRequest( 'filter_ordering', 'filter_ordering_t', 'a.ticketdate', 'cmd' );
		
		## Model is defined in the controller
		$model		= $this->getModel();
		
		## Getting the items into a variable
		$items		= $this->get('list');
		$added		= $this->get('added');
		$sold		= $this->get('sold');
		$pagination	= $this->get('pagination');

		$db    = JFactory::getDBO();
		## Making the query to get the website configuration.
		$sql = "SELECT load_bootstrap, dateformat, priceformat, valuta, show_quantity_eventlist, show_price_eventlist  
				FROM #__ticketmaster_config 
				WHERE configid = 1";
				
		 $db->setQuery($sql);
		 $config = $db->loadObject();
		 
		## Getting the page template for the header to show up.
		$sql = "SELECT mailbody, mailsubject 
				FROM #__ticketmaster_emails 
				WHERE emailid = 51";
		
		$db->setQuery($sql);
		$tmpl = $db->loadObject();
		
		## Showing default template or bootstrap?
		$tpl = Template($config->load_bootstrap);

		$this->assignRef('added', $added);
		$this->assignRef('sold', $sold);
		$this->assignRef('items', $items);
		$this->assignRef('config', $config);
		$this->assignRef('tmpl', $tmpl);
		$this->assignRef('pagination', $pagination);

		parent::display($tpl);		

	
	}

}
?>
