<?php
/****************************************************************
 * @version				2.5.5 ticketmaster 						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org							
 * @website				http://www.rd-media.org						
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class ticketmasterControllerVenues extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
			## Register Extra tasks
			$this->registerTask( 'add' , 'edit' );
			$this->registerTask('unpublish','publish');
			$this->registerTask('apply','save' );	
	}

	## This function will display if there is no choice.
	function display() {
	
		JRequest::setVar( 'layout', 'default');
		JRequest::setVar( 'view', 'venues');
		parent::display();
	}
   
	function edit() {
	
		JRequest::setVar( 'layout', 'form');
		JRequest::setVar( 'view', 'venues');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}
	
	function modal() {
	
		JRequest::setVar( 'layout', 'modal');
		JRequest::setVar( 'view', 'venues');		
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();

	}	
	
	function save() {

		$post	            	= JRequest::get('post');
		$post['locdescription'] = JRequest::getVar('locdescription', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		$post['website'] 		= JRequest::getVar('website', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		
		## Getting the street and city for Google maps
		$street = str_replace(' ', ',', JRequest::getVar('street'));
		$city = JRequest::getVar('city');
		$country = JRequest::getVar('country');
		
		if ($post['own_ll'] == 0) {		
			
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$street.'+'.$city.'+'.$country.'&sensor=true';
		    $raw = file_get_contents($url);
		    $data = json_decode($raw);

		    $lat = $data->results[0]->geometry->location->lat;
		    $lng = $data->results[0]->geometry->location->lng;
	
			if ($data != '') {
				
				$post['googlemaps_latitude'] = $lat;
				$post['googlemaps_longitude'] = $lng;
			
			} else {
			
				$msg = "Error in geocoding! Http error ".substr($data,0,3);
				$link = 'index.php?option=com_ticketmaster&controller=venues';
				$this->setRedirect($link, $msg);			
			}
		
		}
		
		$model	= $this->getModel('venues');

		if ($model->store($post)) {
			$msg = JText::_( 'COM_TICKETMASTER_VENUE_SAVED' );
		} else {
			$msg = JText::_( 'COM_TICKETMASTER_VENUE_NOT_SAVED' );
		}
		
		$link = 'index.php?option=com_ticketmaster&controller=venues';
		$this->setRedirect($link, $msg);
	}


	function publish()
	{

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		## Getting the task (publish/upnpublish)
		if ($this->getTask() == 'publish') {
			$publish = 1;
		} else {
			$publish = 0;
		}		

		if (count( $cid ) < 1) {
			$link = 'index.php?option=com_ticketmaster&controller=venues';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_VENUE_SELECT'));
		}

		$model = $this->getModel('venues');
		if(!$model->publish($cid, $publish)) {
			$link = 'index.php?option=com_ticketmaster&controller=events';
			$this->setRedirect($link, JText::_( 'COM_TICKETMASTER_VENUE_ERROR_CONTROLLER'));
		}
		$link = 'index.php?option=com_ticketmaster&controller=venues';
		$this->setRedirect($link);
	}

	function remove() {
	
		global $option, $mainframe;
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('venues');
		
		if(!$model->remove($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_ticketmaster&controller=venues', JText::_( 'COM_TICKETMASTER_VENUE_DELETED'));
		
	}

   
}	
?>
