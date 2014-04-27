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

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelCancel extends JModelLegacy {


   function __construct(){
   
      parent::__construct();
		
		## Removing the session, it's not needed anymore.
		$session =& JFactory::getSession();
		## We need the order code before removing the order.
		$this->ordercode = $session->get('ordercode');
		## We can destroy the session as they want to cancel.
		$session->clear('ordercode');
  
   }				

	function removeTickets($cid){
		
		return true;

	}
		
}
?>