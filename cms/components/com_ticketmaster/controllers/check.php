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

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerCheck extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->ordercode 	= JRequest::getInt('oc', 0);
		$this->id			= JRequest::getInt('id', 0);

	}
	
	function verify(){
	
	global $mainframe;

		$UserId=$_GET['user'];
		$Email=$_GET['email'];
		
		$db		=& JFactory::getDBO();
		
		if ( $UserId != "")
			{
				
				$query="SELECT username FROM jos_users WHERE username='$UserId'";
				$db->setQuery( $query );
				$result = $db->loadObjectList();
				if ( $result )
					{
			
						echo "invalid";
			
					}
				else 
					{
						echo "valid";
					}
			}
		if ( $Email != "")
			{
				
				$query_email="SELECT email FROM jos_users WHERE email='$Email'";
				$db->setQuery( $query_email );
				$result_email = $db->loadObjectList();
				if ( $result_email )
					{
						echo "invalid";
					}
				else 
					{
						echo "valid";
					}
			}
	
	}
}	
?>
