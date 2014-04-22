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
class TicketmasterControllerJQuery extends JControllerLegacy {

	function __construct(){
		
		parent::__construct();
		
		$this->id    = JRequest::getInt('id', 0); 		
	}

	## This function will display if there is no choice.
	function getdata() {
		
		$id  = JRequest::getInt('id', 0); 
		
		$db    = JFactory::getDBO();	
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_tickets WHERE ticketid = '.(int)$id.''; 
	 
		$db->setQuery($sql);
		$t = $db->loadObject();
	
		$arr = array('eventcode' => $t->eventcode, 
					 'ticketname' => $t->ticketname, 
					 'location' => $t->location, 
					 'locationinfo' => $t->locationinfo,
					 'starttime' => $t->starttime,
					 'ticketprice' => $t->ticketprice,
					 'totaltickets' => $t->totaltickets,
					 'ticket_fontcolor_r' => $t->ticket_fontcolor_r,
					 'ticket_fontcolor_g' => $t->ticket_fontcolor_g,
					 'ticket_fontcolor_b' => $t->ticket_fontcolor_b,
					 'ticket_fontsize' => $t->ticket_fontsize,
					 'ticketnr_fontcolor_r' => $t->ticketnr_fontcolor_r,
					 'ticketnr_fontcolor_g' => $t->ticketnr_fontcolor_g,
					 'ticketnr_fontcolor_b' => $t->ticketnr_fontcolor_b,
					 'ticketnr_fontsize' => $t->ticketnr_fontsize,
					 'ticketid_nr_fontcolor_r' => $t->ticketid_nr_fontcolor_r,
					 'ticketid_nr_fontcolor_g' => $t->ticketid_nr_fontcolor_g,
					 'ticketid_nr_fontcolor_b' => $t->ticketid_nr_fontcolor_b,
					 'ticketid_nr_fontsize' => $t->ticketid_nr_fontsize,
					 'eventname_position' => $t->eventname_position,
					 'date_position' => $t->date_position,
					 'location_position' => $t->location_position,
					 'orderid_position' => $t->orderid_position,
					 'ordernumber_position' => $t->ordernumber_position,
					 'price_position' => $t->price_position,
					 'bar_position' => $t->bar_position, 
					 'name_position' => $t->name_position, 
					 'free_text_2' => $t->free_text_2, 
					 'end_date' => $t->end_date, 
					 'free_text_1' => $t->free_text_1, 
					 'min_ordering' => $t->min_ordering, 
					 'max_ordering' => $t->max_ordering, 
					 'ticketdate' => $t->ticketdate, 
					 'sale_stop' => $t->sale_stop, 
					 'ticketdescription' => $t->ticketdescription, 
					 'show_seatplans' => $t->show_seatplans, 
					 'ticket_orientation' => $t->ticket_orientation, 
					 'ticket_size' => $t->ticket_size, 
					 'combine_multitickets' => $t->combine_multitickets, 
					 'eventid' => $t->eventid,
					 'venue' => $t->venue,
					 'published' => $t->published,
					 'free_text1_position' => $t->free_text1_position,
					 'free_text2_position' => $t->free_text2_position,
					 'show_end_date' => $t->show_end_date,
					 'use_sale_stop' =>  $t->use_sale_stop, 
					 'counter_choice' => $t->counter_choice, 
					 'clientdata_fontsize' => $t->clientdata_fontsize, 
					 'clientdata_fontcolor_r' => $t->clientdata_fontcolor_r, 
					 'clientdata_fontcolor_g' => $t->clientdata_fontcolor_b, 
					 'clientdata_fontcolor_b' => $t->clientdata_fontcolor_g, 
					 'seatnumber_fontsize' => $t->seatnumber_fontsize, 
					 'seatnumber_fontcolor_r' => $t->seatnumber_fontcolor_r, 
					 'seatnumber_fontcolor_g' => $t->seatnumber_fontcolor_b, 
					 'seatnumber_fontcolor_b' => $t->seatnumber_fontcolor_g, 					 
					 'start_price' => $t->start_price, 
					 'end_price' => $t->end_price, 
					 'orderdate_position' => $t->orderdate_position);
		
		echo json_encode($arr);
	
	}
 
	## This function will display if there is no choice.
	function getSampleData() {
		
		## Load default ticket settings:
		$db     = JFactory::getDBO();
		$id  	= JRequest::getInt('ticketid', 0);
		
		if($id != 0){
			
			$sql='SELECT eventname_position, date_position, location_position, orderid_position, ordernumber_position, price_position,
					     bar_position, name_position, free_text2_position, free_text1_position, position_seatnumber, orderdate_position 
				  FROM #__ticketmaster_tickets 
				  WHERE ticketid = '.(int)$id.'';

		}else{
			
			## Making the query for getting the config
			$sql='SELECT eventname_position, date_position, location_position, orderid_position, ordernumber_position, price_position,
				     bar_position, name_position, free_text2_position, free_text1_position, position_seatnumber, orderdate_position
			  FROM #__ticketmaster_config
			  WHERE configid = 1';			
			
		}
		
		$db->setQuery($sql);
		$config = $db->loadObject();		
			
		$arr = array('eventname_position' => $config->eventname_position,
					 'date_position' => $config->date_position,
					 'location_position' => $config->location_position,
					 'orderid_position' => $config->ordernumber_position,
					 'ordernumber_position' => $config->ordernumber_position,
					 'price_position' => $config->price_position,
					 'bar_position' => $config->bar_position, 
					 'name_position' => $config->name_position, 
					 'free_text2_position' => $config->free_text2_position,
					 'free_text1_position' => $config->free_text1_position, 
					 'position_seatnumber' => $config->position_seatnumber, 
					 'orderdate_position' => $config->orderdate_position);
		
		echo json_encode($arr);
	
	} 

   
}	
?>
