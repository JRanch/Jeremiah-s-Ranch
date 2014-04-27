<?php

/************************************************************
 * @version			ticketmaster 3.1.0
 * @package			com_ticketmaster
 * @copyright		Copyright ? 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerIpn extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		
	}

	function ipnProcessor(){
		
		## TEST URL RD-MEDIA
		// http://nas.rd-media.org/ticketmaster255/index.php?option=com_ticketmaster&controller=ipn&task=ipnProcessor&plg=rdmpaypal
		
		## Get plugin name:
		$pg_plugin = JRequest::getVar( 'plg' );
		## Get $_POST data from message:
		$post 	   = JRequest::get('post');
		
		## To TRIGGER PAYMENT PLUGIN
		$dispatcher = JDispatcher::getInstance();
		## Trigger plugin group "rdmedia" and plugin name:
		JPluginHelper::importPlugin('rdmedia', $pg_plugin); 
		## Trigger IPNProcessPayment() validate the payment response and return the payment detail.
		$data = $dispatcher->trigger('IPNProcessPayment', array($post));  //$post - contain payment response
		## $data = $data[0];
		
	}	

	
}	
?>