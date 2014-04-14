<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

class ticketcreator{					


	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;  
	
	 }  


	function doPDF() {
		
		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		$sql='SELECT  * FROM #__ticketmaster_orders WHERE orderid = '.(int)$this->eid.''; 
	 
		$db->setQuery($sql);
		$order = $db->loadObject();
		
		## include the functions for price views.
		$file_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $file_include );

		## Making the query for getting the order
		$sql='SELECT  a.*, t.*, e.eventname, c.address, c.name, c.city, c.firstname, 
			  t.ticketdate, t.starttime, t.location, t.locationinfo, e.groupname, e.eventid, t.eventcode, t.show_end_date, t.end_date
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND orderid = '.(int)$this->eid.'';
		 
		$db->setQuery($sql);
		$order = $db->loadObject();

		$orderdate = date ($config->dateformat, strtotime($order->ticketdate));
		
		## Required helpers to create this PDF invoice
		## Do NOT edit the required files! It will damage your component.
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdf'.DS.'fpdf.php');
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdi_ean13.php');

		## Making the query for getting the order
		$sql='SELECT *
			  FROM #__ticketmaster_venues
			  WHERE id = '.(int)$order->venue.'';
		 
		$db->setQuery($sql);
		$locations = $db->loadObject();
		
		## initiate FPDI
		
		$pdf = new FPDI_EAN13($order->ticket_orientation,'mm',$order->ticket_size);
		
		## add a page
		$pdf->AddPage();
		
		## Image for the background :) <-- JPG file is now better to use then PDF. So use it if it exists
		$background = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS.'eTicket-'.$order->ticketid.'.jpg';	
		
		if (!file_exists($background)) {
		
			## This should be the source file if there is an uploaded PDF file for this event.
			$sourcefile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS.'eTicket-'.$order->ticketid.'.pdf';
			
			## Check if there is an updated source PDF file.
			if (!file_exists($sourcefile)) {
				$sourcefile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS.'eTicket.pdf';
			}		
							
			## set the sourcefile
			$pdf->setSourceFile($sourcefile);
			## import page 1
			$tplIdx = $pdf->importPage(1);
			## use the imported page and place it at point 0,0 with a width of 210 mm (A4 Format)
			$pdf->useTemplate($tplIdx, 0, 0, 0);
		
		}else{
			
			## OK the background image should be there, now print it please :) 
			$printable = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS;
			
			if ($order->ticket_size == 'A4') {
				if($order->ticket_orientation == 'P'){
					$pdf->Image($background ,0 ,0 ,210 ,290);	
				}else{
					$pdf->Image($background ,0 ,0 ,290 ,210);	
				}					
			}else{
				if($order->ticket_orientation == 'P'){
					$pdf->Image($background ,0 ,0 ,148.5 ,210);	
				}else{
					$pdf->Image($background ,0 ,0 ,210 ,148.5);	
				}	
			}
			
		} 	
				

		## Writing the Ticket Event Name
		## Text writing (height, width)		$pdf->SetFont('Arial', 'B', 8);
		// Check each position and if not set do not attempt to write to PDF
		if (strpos($order->eventname_position,'-') !== false) {
			$position_event = explode("-", $order->eventname_position);
			$pdf->SetFont('Arial', '', $order->ticket_fontsize);
			$pdf->SetTextColor($order->ticket_fontcolor_r,$order->ticket_fontcolor_g,$order->ticket_fontcolor_b);
			$pdf->SetXY($position_event[0], $position_event[1]);		
			
			## Writing the eventname on the ticket now.
			$pdf->Write(0, utf8_decode($order->eventname).' - '.utf8_decode($order->ticketname));	
		}
		
		## Writing the Ticket Date
		if (strpos($order->date_position,'-') !== false) {
			## Date definition - Change if you like. (The config is showing the dateformats)

			if($order->show_end_date != 1){
				$ticketdate = date ($config->dateformat, strtotime($order->ticketdate));
			}else{
				$ticketdate = date ($config->dateformat, strtotime($order->ticketdate)).' - '.date ($config->dateformat, strtotime($order->end_date));
			}
			
			$position_date = explode("-", $order->date_position);
			$pdf->SetFont('Arial', 'B', $order->ticket_fontsize);
			$pdf->SetTextColor($order->ticket_fontcolor_r,$order->ticket_fontcolor_g,$order->ticket_fontcolor_b);
			$pdf->SetXY($position_date[0], $position_date[1]);
			$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_DATE' ).' '.$ticketdate.' '.JText::_( 'COM_TICKETMASTER_PDF_START' ).' '.$order->starttime);	
		}
		
		## Writing the Ticket Order Date information
		if (strpos($order->orderdate_position,'-') !== false) {
		
			## Date definition - Change if you like.
			$orderdate = date ($config->dateformat, strtotime($order->orderdate));
	
			$orderdate_position = explode("-", $order->orderdate_position);
			$pdf->SetFont('Arial', '', $order->ticket_fontsize);
			$pdf->SetTextColor($order->ticket_fontcolor_r,$order->ticket_fontcolor_g,$order->ticket_fontcolor_b);
			$pdf->SetXY($orderdate_position[0], $orderdate_position[1]);
			$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_ORDERDATE' ).' '. $orderdate);
		}
		
		## Writing the Name information
		if (strpos($order->name_position,'-') !== false) {
			$name_position = explode("-", $order->name_position);
			$pdf->SetFont('Arial', '', $order->clientdata_fontsize);
			$pdf->SetTextColor($order->clientdata_fontcolor_r, $order->clientdata_fontcolor_g,$order->clientdata_fontcolor_b);		
			$pdf->SetXY($name_position[0], $name_position[1]);
			$pdf->Write(0, utf8_decode($order->firstname).' - '.utf8_decode($order->name).' - '.utf8_decode($order->address).' - '.utf8_decode($order->city));		
		}
		
		
		## Writing the Ticket Order Id
		if (strpos($order->orderid_position,'-') !== false) {
			$orderid_position = explode("-", $order->orderid_position);
			$pdf->SetFont('Arial', 'B', $order->ticketnr_fontsize);
			$pdf->SetTextColor($order->ticketnr_fontcolor_r,$order->ticketnr_fontcolor_g,$order->ticketnr_fontcolor_b);
			$pdf->SetXY($orderid_position[0], $orderid_position[1]);
			$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_ORDERID' ).' '. $order->orderid);		
		}
		
		
		## Writing the ordernumber on the ticket.
		if (strpos($order->ordernumber_position,'-') !== false) {
			$ordernumber_position = explode("-", $order->ordernumber_position);
			$pdf->SetFont('Arial', 'B', $order->ticketid_nr_fontsize);
			$pdf->SetTextColor($order->ticketid_nr_fontcolor_r,$order->ticketid_nr_fontcolor_g,$order->ticketid_nr_fontcolor_b);
			$pdf->SetXY($ordernumber_position[0], $ordernumber_position[1]);
			$pdf->Write(0, $order->groupname.'/'.$order->ordercode.'/'.$order->eventcode);
		}
		
		## Writing the seatnumber on the ticket if pro is installed. 
		## Not advised to change it yourself as we cannot guarantee a proper working
		## Changes for this are at own risk :) (No support without valid PRO subscription)
		
		if ($order->seat_sector != 0) {
			
			$sql='SELECT * FROM #__ticketmaster_coords WHERE id = '.(int)$order->seat_sector.'';

		    $db->setQuery($sql);
		    $seat = $db->loadObject();				  			
		 	if (strpos($order->position_seatnumber,'-') !== false) {
				$position_seatnumber = explode("-", $order->position_seatnumber);
				$pdf->SetFont('Arial', 'B', $order->font_size_seatnumber);
				$pdf->SetTextColor($order->seatnumber_fontcolor_r, $order->seatnumber_fontcolor_g, $order->seatnumber_fontcolor_b);
				$pdf->SetXY($position_seatnumber[0], $position_seatnumber[1]);
				$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_SEAT_NUMBER' ).' '. $seat->row_name.$seat->seatid);	
			}
		}
		
		if ($order->free_text_1 != '') {  			
		 	if (strpos($order->free_text1_position,'-') !== false) {
				unset($position);
				$position = explode("-", $order->free_text1_position);
				$pdf->SetFont('Arial', 'B', $order->ticket_fontsize);
				$pdf->SetTextColor($order->ticket_fontcolor_r, $order->ticket_fontcolor_g, $order->ticket_fontcolor_b);
				$pdf->SetXY($position[0], $position[1]);
				$pdf->Write(0, utf8_decode($order->free_text_1));	
			}
				
		}	
		
		if ($order->free_text_2 != '') {  			
		 	if (strpos($order->free_text2_position,'-') !== false) {
				unset($position);
				$position = explode("-", $order->free_text2_position);
				$pdf->SetFont('Arial', 'B', $order->ticket_fontsize);
				$pdf->SetTextColor($order->ticket_fontcolor_r, $order->ticket_fontcolor_g, $order->ticket_fontcolor_b);
				$pdf->SetXY($position[0], $position[1]);
				$pdf->Write(0, utf8_decode($order->free_text_2));	
			}
		}			
		
		## Prepare the price for usage in PDF.
		$price = showprice($config->priceformat ,$order->ticketprice ,$config->valuta);
		
		## Writing the price at the ticket.
		if (strpos($order->price_position,'-') !== false) {
			$price_position = explode("-", $order->price_position);
			$pdf->SetFont('Arial', 'B', $order->ticketnr_fontsize);
			$pdf->SetTextColor($order->ticketnr_fontcolor_r,$order->ticketnr_fontcolor_g,$order->ticketnr_fontcolor_b);
			$pdf->SetXY($price_position[0], $price_position[1]);

			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$order->ticketprice , '');
				$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_PRICE' ).' '.chr(128).' '.$price);
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$order->ticketprice , '');
				$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_PRICE' ).' '.chr(0x00A3).' '.$price);
			}else{
				$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_PRICE' ).' '.$price);
			}			
		}
		
		
		## Writing the Ticket Location	
		if (strpos($order->location_position,'-') !== false) {
			$location_position = explode("-", $order->location_position);
			$pdf->SetFont('Arial', 'B', $order->ticket_fontsize);
			$pdf->SetTextColor($order->ticket_fontcolor_r, $order->ticket_fontcolor_g, $order->ticket_fontcolor_b);
			$pdf->SetXY($location_position[0], $location_position[1]);
			$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PDF_LOCATION' ).' '.utf8_decode($locations->venue).' - '.utf8_decode($locations->street).' - '.
							utf8_decode($locations->zipcode).' - '.utf8_decode($locations->city));	
		}
		
		## Let's go for the Barcode Now!!
		if (strpos($order->bar_position,'-') !== false) {
			$code = $order->ordercode.$order->orderid;
			$this->orderid = $order->orderid;
			
			## Writing the code on the ticket.
			$bar_position = explode("-", $order->bar_position);
			$pdf->EAN13($bar_position[0], $bar_position[1], $code, $order->pdf_use_qrcode );	
			
			$session = JFactory::getSession();
			## Gettig the orderid if there is one.
			$barcode = $session->get('barcode');
	
			## Updating the order, PDF created = 1
			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET barcode = '.$barcode.''
				. ' WHERE orderid = '.(int)$this->eid.'';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}						
			
			if ($order->pdf_use_qrcode == 1) {
				## Gettin the genrated code.
				$cache_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS;
				$pdf->Image($cache_folder.$barcode.'.png' , $bar_position[0] , $bar_position[1] ,0 ,0);
				
				$pdf->SetFont('Arial','B',8);
				$pdf->SetXY($bar_position[0], $bar_position[1]-2);
				$pdf->Write(0, $ordercode);
				
				jimport('joomla.filesystem.file');
				## We do want to remove the QR code again.
				## It is not needed anymore, as ticket has been printed.
				JFile::delete($cache_folder.$barcode.'.png');			
			}
		}
	

		$file = basename(tempnam('.', 'tmp'));
		rename($file, JPATH_SITE.DS.'tmp'.DS.$file.'.pdf');
		$file .= '.pdf';

		## Save PDF to file now!!
		$pdf->Output(JPATH_SITE.DS.'tmp'.DS.$file, 'F');		
		
		## Now move the file away for security reasons
		## Import the Joomla! Filesystem.
		jimport('joomla.filesystem.file');
		
		## Copy the file to a new directory.
		$src  = JPATH_SITE.DS.'tmp'.DS.$file;		
		
		## Now move the file away for security reasons
		## Import the Joomla! Filesystem.
		jimport('joomla.filesystem.file');
		
		## Copy the file to a new directory.
		$src  = JPATH_SITE.DS.'tmp'.DS.$file;		
		
		## The new name for the ticket
		$dest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$order->orderid.'.pdf';
		
		## Copy the file now.
		JFile::copy($src, $dest);
		## The old temporary file needs to be deleted.
		JFile::delete($src);
			
		## Updating the order, PDF created = 1
		$query = 'UPDATE #__ticketmaster_orders'
			. ' SET pdfcreated = 1'
			. ' WHERE orderid = '.(int)$this->eid.'';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}		
		
	}

}


	
?>