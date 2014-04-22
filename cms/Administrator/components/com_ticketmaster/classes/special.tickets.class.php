<?php

/****************************************************************
 * @version			3.0.2											
 * @package			ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

class special{					

	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;    
	 }  


	public function create() {

		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		## include the functions for price views.
		$file_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $file_include );		
		
		## Required helpers to create this PDF invoice
		## Do NOT edit the required files! It will damage your component.
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdf'.DS.'fpdf.php');
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdi_ean13.php');

		## Making the query for getting the order
		$sql='SELECT  a.*, t.*, e.eventname, c.*, 
			  t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND ordercode = '.$this->eid.'';
		 
		$db->setQuery($sql);
		$order = $db->loadObject();
		
		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();		
		
		## CSetting the size of the document.
		$pdf = new FPDI_EAN13('P','mm','A4');
		
		## add a page
		$pdf->AddPage();

		## Getting the order date:
		$orderdate = date ($config->dateformat, strtotime($order->orderdate));
		
		### setting the header for the page: (w) 210mm x (h) 90mm 
		$folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'images'.DS;
		$pdf->Image($folder.'header.jpg',0 ,0 ,210 ,90);
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );		
		
		$total 		= _getAmount($this->eid, 0, 0);
		$fees 		= _getFees($this->eid);
		$discount 	= _getDiscount($this->eid);			
		
		###############################################
		## WRITING THE COMPANY INFORMATION ON TICKET ##
		###############################################
		
		if($config->address_format_company == ''){
			
			##Writing the company name
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 35);
			$pdf->Write(0, utf8_decode($config->companyname) );	
				
	
			##Writing the company address
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 45);
			$pdf->Write(0, utf8_decode($config->address1) );
	
			##Writing the company zipcode+city
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 50);
			$pdf->Write(0, $config->zipcode.' '.utf8_decode($config->city) );
	
			##Writing the company phone
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 55);
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PHONE')).' '.utf8_decode($config->phone) );	
	
			##Writing the company email
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 60);
			$pdf->Write(0, $config->email );	
	
			##Writing the company website
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(10, 65);
			$pdf->Write(0, $config->website );
			
			$start_height = $pdf->GetY()+10;
			
		}else{

			if(ini_get('magic_quotes_gpc')=='1'){
				$body = stripslashes($config->address_format_company);
			}else{
				$body = utf8_decode($config->address_format_company);
			}

			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);			
			$pdf->SetXY(10, 40);
			$pdf->MultiCell(0,5,"$body",0,'L',0);
			
			$start_height = $pdf->GetY()+10;
			
		}

		################################################
		## WRITING THE CUSTOMER INFORMATION ON TICKET ##
		################################################
			
		if($order->gender == 1){
			$salutation = JText::_( 'COM_TICKETMASTER_MR' );
		}else if($order->gender == 2){
			$salutation = JText::_( 'COM_TICKETMASTER_MRS' );
		}else if($order->gender == 3){
			$salutation = JText::_( 'COM_TICKETMASTER_MISS' );
		}else{
			$salutation = JText::_( 'COM_TICKETMASTER_FAMILY' );
		}	

		
		if($config->address_format_client == ''){
		
	
		
			## Writing the clientname.
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(135, 51);
			if($order->firstname == ''){
			   $pdf->Write(0, utf8_decode($order->name) );	
			}else{
				$pdf->Write(0, utf8_decode($salutation). utf8_decode($order->firstname).' '.utf8_decode($order->name) );
			}
	
			## Writing the client address.
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(135, 55);
			$pdf->Write(0, utf8_decode($order->address) );				
	
			## Writing the zipcode & city.
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(135, 59);
			$pdf->Write(0, $order->zipcode.' '.utf8_decode($order->city) );
	
			## Writing the zipcode & city.
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(135, 63);
			$pdf->Write(0, $order->emailaddress );
		
		}else{
			
			$sql='SELECT * FROM #__ticketmaster_country
			  	  WHERE country_id = '.$order->country_id.'';

			$db->setQuery($sql);
			$obj = $db->loadObject();
	

			
			$client_address = str_replace('%%FIRSTNAME%%', utf8_decode($order->firstname), $config->address_format_client);
			$client_address = str_replace('%%LASTNAME%%', utf8_decode($order->name), $client_address);
			$client_address = str_replace('%%SALUTATION%%', utf8_decode($salutation), $client_address);
			$client_address = str_replace('%%ADDRESS1%%', utf8_decode($order->address), $client_address);
			$client_address = str_replace('%%ADDRESS2%%', utf8_decode($order->address2), $client_address);
			$client_address = str_replace('%%ZIPCODE%%', utf8_decode($order->zipcode), $client_address);
			$client_address = str_replace('%%CITY%%', utf8_decode($order->city), $client_address);
			$client_address = str_replace('%%COUNTRY_FULL%%', utf8_decode($obj->country), $client_address);
			$client_address = str_replace('%%COUNTRY_2D%%', utf8_decode($obj->country_2_code), $client_address);
			$client_address = str_replace('%%COUNTRY_3D%%', utf8_decode($obj->country_3_code), $client_address);
			
			
			if(ini_get('magic_quotes_gpc')=='1'){
				$body = stripslashes($client_address);
			}else{
				$body = $client_address;
			}

			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);			
			$pdf->SetXY(135, 51);
			$pdf->MultiCell(0,5,"$body",0,'L',0);			
			
		}		
		
		## PRINT THE TICKETS NOW IN LIST ##
		## FIRST WE HAVE TO GET ALL ITEMS FOR THIS TICKET ##
		
		$sql='SELECT  a.*, t.*, e.eventname, c.*, 
			  t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode, t.show_end_date, t.end_date
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND ordercode = '.$this->eid.'
			  GROUP BY a.orderid';
				
		$db->setQuery($sql);
		$data = $db->loadObjectList();
		
		## Setting the order number:
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(10, $start_height);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_ENTRANCE_TICKETS_FOR')).' '.$this->eid);		
		## Writing the name customer:
		$pdf->SetXY(10, $start_height+5);
		
		if($order->firstname == ''){
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TICKETS_FOR_CUSTOMER')).' '.utf8_decode($order->name) );
		}else{
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TICKETS_FOR_CUSTOMER')).' '.utf8_decode($order->firstname).' '.utf8_decode($order->name) );
		}		
		
		$pdf->SetXY(10, $start_height+10);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TICKETS_ORDER_DATE')).' '.$orderdate);								
		
		$pdf->SetDrawColor(0,0,0);
		$pdf->Line( 10,105,200,105);	
		
		## Setting the startgrid, DO NOT change this!
		$height = 105;
		$height = $pdf->GetY()+20;
		
		## Setting font color & font for all items below:
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		
		$k = 0;
		
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
			
			$row = $data[$i];
			
			if ($row->seat_sector != 0) {
				
				$sql='SELECT * FROM #__ticketmaster_coords WHERE id = '.(int)$row->seat_sector.'';
					  
				$db->setQuery($sql);
				$seat = $db->loadObject();				  			
				
				$seatnumber = JText::_( 'COM_TICKETMASTER_SEAT_NR' ).': '.$seat->row_name.$seat->seatid;
				
				$pdf->SetXY(10, $height+2);
				if ($row->parentname != $row->ticketname) {
					$pdf->SetFont('Arial', '', 9);
					$pdf->Write(0, utf8_decode($row->eventname).' ('.utf8_decode($row->eventcode).') - '. utf8_decode($row->ticketname).' - '.utf8_decode($seatnumber));
				}else{
					$pdf->SetFont('Arial', '', 9);
					$pdf->Write(0, utf8_decode($row->eventcode).' / '.utf8_decode($row->ticketname).' - '.utf8_decode($seatnumber));
				}				
				
			}else{
			
				$pdf->SetXY(10, $height+2);
				if ($row->parentname != $row->ticketname) {
					$pdf->SetFont('Arial', '', 9);
					$pdf->Write(0, utf8_decode($row->eventname).' ('.utf8_decode($row->eventcode).') - '. utf8_decode($row->ticketname));
				}else{
					$pdf->SetFont('Arial', '', 9);
					$pdf->Write(0, utf8_decode($row->eventcode).' / '.utf8_decode($row->ticketname));
				}			
				
			}

			$pdf->SetXY(10, $height+7);

			if($row->show_end_date != 1){
				$ticketdate = date ($config->dateformat, strtotime($row->ticketdate));
			}else{
				$ticketdate = date ($config->dateformat, strtotime($row->ticketdate)).' - '.date ($config->dateformat, strtotime($row->end_date));
			}			

			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$row->ticketprice , '');
				$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_ORDER' )).' '.$row->orderid.' :: '.utf8_decode(JText::_( 'COM_TICKETMASTER_PDF_PRICE' )).' '.chr(128).' '.$price.' :: '.JText::_( 'COM_TICKETMASTER_PDF_DATE' ).' '.$ticketdate);
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$row->ticketprice , '');
				$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_ORDER' )).' '.$row->orderid.' :: '.utf8_decode(JText::_( 'COM_TICKETMASTER_PDF_PRICE' )).' '.chr(0x00A3).' '.$price.' :: '.JText::_( 'COM_TICKETMASTER_PDF_DATE' ).' '.$ticketdate);
			}else{
			    $price = showprice($config->priceformat ,$row->ticketprice , $config->valuta);
				$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_ORDER' )).$row->orderid.' :: '.utf8_decode(JText::_( 'COM_TICKETMASTER_PDF_PRICE' )).' '.$price.' :: '.JText::_( 'COM_TICKETMASTER_PDF_DATE' ).' '.$ticketdate);
			}	
			
			## Getting the heigth for the closing line -- will only show after the for loop has been completed:
			$y = $pdf->GetY();						
			
			## Creating the QR Code for printing.	
			if ($row->pdf_use_qrcode == 1) {
				
				## Creating the code:
				$code = $row->ordercode.$row->orderid;
				
				## Getting the barcode from the EAN script.
				$pdf->EAN13(1, 1, $code, $order->pdf_use_qrcode, 1 );		
				
				$session = JFactory::getSession();
				## Gettig the orderid if there is one.
				$barcode = $session->get('barcode');						
				
				$remoteFile ='http://chart.apis.google.com/chart?chs='.$config->qr_width.'x'.$config->qr_width.'&cht=qr&chld=L|0&chl='.$barcode.'';
		   		$localFile  = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS.$barcode.'.png';	
				self::get_qr_image($remoteFile,$localFile);			
					
				## Gettin the genrated code.
				$cache_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS;
				$pdf->Image($cache_folder.$barcode.'.png' , 175 , $height ,20 ,20);
				
				$pdf->SetDrawColor(193,193,193);				
								
				jimport('joomla.filesystem.file');
				## We do want to remove the QR code again.
				## It is not needed anymore, as ticket has been printed.
				JFile::delete($cache_folder.$barcode.'.png');
				$session->clear('barcode');			   
			
			}else{ 			
			
				## Writing the code on the ticket.
				$pdf->EAN13(160, $height, $row->barcode, 0 );
				
				$pdf->SetDrawColor(193,193,193);
				$pdf->Line( 10,$height+20,200,$height+20);				
			
			}
			
			$pdf->SetXY(10, $height+12);
			$pdf->SetFont('Arial', '', 9);
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_BARCODE' )).': '. $row->barcode);
			
			$height = $height+25;	
			
			## Path to a combined ticket is as below:
			$combined_ticket = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTickets-'.$this->eid.'.pdf';
			## remove tickets if there is a combined one.
			if (file_exists( $combined_ticket )) {
				jimport('joomla.filesystem.file');
				JFile::delete( $combined_ticket );
			}				
			
			## Path to a normal ticket is as below:
			$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$row->orderid.'.pdf';
			## Remove single ticket
			if (file_exists($path)) {
				jimport('joomla.filesystem.file');
				JFile::delete( $path );
			}
			
			$space_left = 290 - $pdf->GetY();
			
			if($space_left < 40) {
				
				$pdf->AddPage(); // page break.
				
				### setting the header for the page: (w) 210mm x (h) 90mm
				$folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'images'.DS;
				$pdf->Image($folder.'header.jpg',0 ,0 ,210 ,90);
				## Set margin for next page:
				$height = 50;
			
			}
		
		}
		
		$file = basename(tempnam('.', 'tmp'));
		rename($file, JPATH_SITE.DS.'tmp'.DS.'multi-'.$this->eid.'.pdf');
		$file .= '.pdf';

		## Save PDF to file now!!
		$pdf->Output(JPATH_SITE.DS.'tmp'.DS.$file, 'F');		
		
		## Now move the file away for security reasons
		## Import the Joomla! Filesystem.
		jimport('joomla.filesystem.file');
		
		## Copy the file to a new directory.
		$src  = JPATH_SITE.DS.'tmp'.DS.$file;	
		
		## The new name for the ticket
		$dest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.$this->eid.'.pdf';
		
		## Copy the file now.
		JFile::copy($src, $dest);
		## The old temporary file needs to be deleted.
		JFile::delete($src);
	
	}
	
	function get_qr_image($remoteFile,$localFile){

		## Using the cache folder to save the file.
		$cache_folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'cache'.DS;
		
		## If the folder doesn't exsist.
		if ( !is_dir($cache_folder) )
		{
			## Making the folder right now.			
			## Now move the file away for security reasons
			## Import the Joomla! Filesystem.
			jimport('joomla.filesystem.file');
			JFolder::create($cache_folder, 0755);
		}
		
		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $remoteFile);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		$image = curl_exec($ch);
		curl_close($ch); 
		$f = fopen($localFile, 'w');
		fwrite($f, $image);
		fclose($f);
	} 

}


	
?>