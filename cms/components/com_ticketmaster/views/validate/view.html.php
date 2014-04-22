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

class TicketmasterViewValidate extends JView {

	function display($tpl=null) {

		$order = JRequest::getInt('oc', 0);
	 
		parent::display($tpl);		

	
	}

}
?>
