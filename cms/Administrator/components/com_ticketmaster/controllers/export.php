<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright � 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

## This Class contains all data for the car manager
class ticketmasterControllerExport extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
			## Register Extra tasks
			$this->registerTask( 'add' , 'edit' );
			$this->registerTask('unpublish','publish');
			$this->registerTask('apply','save' );

			$this->eventid    = JRequest::getInt('eventid'); 

							
	}

	## This function will display if there is no choice.
	function display() {
	
		JRequest::setVar( 'layout', 'default');
		JRequest::setVar( 'view', 'export');
		parent::display();
	}
	
	function export(){
		
		$mainframe = JFactory::getApplication();
		
		## Make DB connections
		$db    = JFactory::getDBO();
		
		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  pro_installed FROM #__ticketmaster_config WHERE configid = 1';
		
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		if($config->pro_installed == 1) { 

			$sql = 'SELECT a.orderid, a.ordercode, a.orderdate, b.eventname, d.ticketname,
	                       a.blacklisted, a.paid, a.pdfcreated, a.pdfsent, a.barcode, a.coupon, c.*, e.remarks, ext.seatid
	                FROM #__ticketmaster_orders AS a
	                LEFT JOIN #__ticketmaster_clients AS c ON a.userid = c.userid
	                LEFT JOIN #__ticketmaster_events AS b ON a.eventid = b.eventid
	                LEFT JOIN #__ticketmaster_tickets AS d ON a.ticketid = d.ticketid
	                LEFT OUTER JOIN #__ticketmaster_remarks AS e ON a.ordercode = e.ordercode
					LEFT OUTER JOIN #__ticketmaster_coords AS ext ON a.orderid = ext.orderid
	                WHERE a.userid > 0 AND a.eventid = '.(int)$this->eventid.'';

		}else{
			
			$sql = 'SELECT a.orderid, a.ordercode, a.orderdate, b.eventname, d.ticketname,
	                       a.blacklisted, a.paid, a.pdfcreated, a.pdfsent, a.barcode, a.coupon, c.*, e.remarks
	                FROM #__ticketmaster_orders AS a
	                LEFT JOIN #__ticketmaster_clients AS c ON a.userid = c.userid
	                LEFT JOIN #__ticketmaster_events AS b ON a.eventid = b.eventid
	                LEFT JOIN #__ticketmaster_tickets AS d ON a.ticketid = d.ticketid
	                LEFT OUTER JOIN #__ticketmaster_remarks AS e ON a.ordercode = e.ordercode
	                WHERE a.userid > 0 AND a.eventid = '.(int)$this->eventid.'';			
			
		}

		$db->setQuery($sql);
		$rows = $db->loadAssocList();
	
		
		## If the query doesn't work..
		if (!$db->query() ){
			echo "<script>alert('Please report your problem. (Code: Supervisor-Model-132)');
			window.history.go(-1);</script>\n";		 
		}	
		
		## Empty data vars
		$data = "" ;
		## We need tabbed data
		$sep = "\t"; 
		
		$fields = (array_keys($rows[0]));
		
		## Count all fields(will be the collumns
		$columns = count($fields);
		## Put the name of all fields to $out.  
		for ($i = 0; $i < $columns; $i++) {
		  $data .= $fields[$i].$sep;
		}
		
		$data .= "\n";
		
		## Counting rows and push them into a for loop
		for($k=0; $k < count( $rows ); $k++) {
			$row = $rows[$k];
			$line = '';
			
			## Now replace several things for MS Excel
			foreach ($row as $value) {
			  $value = str_replace('"', '""', $value);
			  $line .= '"' . utf8_decode($value) . '"' . "\t";
			}
			$data .= trim($line)."\n";
		}
		
		$data = str_replace("\r","",$data);

		## If count rows is nothing show o records.
		if (count( $rows ) == 0) {
			JError::raiseWarning(100, $error.' '. JText::_( 'COM_TICKETMASTER_NO_RECORDS' ));
			$mainframe->redirect('index.php?option=com_ticketmaster&controller=export');
		}		
		
		$date = 'export-eventid-'.$this->eventid;

		## Push the report now!
		
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$date.".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Lacation: excel.htm?id=yes");
		print $data ;
		die();	
	}

}	
?>
