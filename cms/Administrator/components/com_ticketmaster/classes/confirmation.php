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

class confirmation{					


	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;   
		
	 }  


	public function doConfirm() {

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
		
		## initiate FPDI
		$pdf = new FPDI_EAN13();
		## add a page
		$pdf->AddPage();
		
		## Getting the order date:
		$orderdate = date ($config->dateformat, strtotime($order->orderdate));
		
		$logo   = explode("-", $config->position_logo_confirmation);
		$folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'images'.DS;
		$pdf->Image($folder.'confirmation_logo.jpg' , $logo[0] , $logo[1] ,0 ,20);		
		
		#############################################
		## WRITING THE ORDERDATE                   ##
		#############################################
		
		$sql='SELECT COUNT(orderid) AS total 
		      FROM #__ticketmaster_orders 
			  WHERE ordercode = '.$this->eid.'
			  AND (paid = 0 OR paid = 3)';
			  
		$db->setQuery($sql);
		$status = $db->loadObject();				  			
		
		## Writing the orderdate on the confirmation.
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(7, 60);
		$pdf->Write(0, JText::_( 'COM_TICKETMASTER_ORDERDATE').': '.$orderdate );
		
		## Writing the orderdate on the confirmation.
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(7, 65);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PDF_ORDERCONFIRMATION')).$this->eid);
		$pdf->SetXY(7, 70);
						

		if ($status->total > 0) {
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PAYMENTSTATUS')).': '.JText::_( 'COM_TICKETMASTER_ORDERSTATUS_UNPAID' ));
		}else{
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PAYMENTSTATUS')).': '.JText::_( 'COM_TICKETMASTER_ORDERSTATUS_PAID' ));
		}	

		###############################################
		## WRITING THE COMPANY INFORMATION ON TICKET ##
		###############################################
		
		if($config->address_format_company == ''){
			
			##Writing the company name
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 14);
			$pdf->Write(0, utf8_decode($config->companyname) );	
				
	
			##Writing the company address
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 18);
			$pdf->Write(0, utf8_decode($config->address1) );
	
			##Writing the company zipcode+city
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 22);
			$pdf->Write(0, $config->zipcode.' '.utf8_decode($config->city) );
	
			##Writing the company phone
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 26);
			$pdf->Write(0, JText::_( 'COM_TICKETMASTER_PHONE').' '.utf8_decode($config->phone) );	
	
			##Writing the company email
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 30);
			$pdf->Write(0, $config->email );	
	
			##Writing the company website
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(7, 34);
			$pdf->Write(0, $config->website );
			
		}else{

			if(ini_get('magic_quotes_gpc')=='1'){
				$body = stripslashes($config->address_format_company);
			}else{
				$body = utf8_decode($config->address_format_company);
			}

			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);			
			$pdf->SetXY(7, 14);
			$pdf->MultiCell(0,5,"$body",0,'L',0);
			
		}

		################################################
		## WRITING THE CUSTOMER INFORMATION ON TICKET ##
		################################################
		
		if($config->address_format_client == ''){
		
			## Writing the clientname.
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY(135, 51);
			if($order->firstname == ''){
			   $pdf->Write(0, utf8_decode($order->name) );	
			}else{
				$pdf->Write(0, utf8_decode($order->firstname).' '.utf8_decode($order->name) );
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

			$client_address = str_replace('%%FIRSTNAME%%', utf8_decode($order->firstname), $config->address_format_client);
			$client_address = str_replace('%%LASTNAME%%', utf8_decode($order->name), $client_address);
			$client_address = str_replace('%%ADDRESS1%%', utf8_decode($order->address), $client_address);
			$client_address = str_replace('%%ADDRESS2%%', utf8_decode($order->address2), $client_address);
			$client_address = str_replace('%%ZIPCODE%%', utf8_decode($order->zipcode), $client_address);
			$client_address = str_replace('%%CITY%%', utf8_decode($order->city), $client_address);			
			
			if($order->country_id != ''){
				
				$sql='SELECT * FROM #__ticketmaster_country
				  	  WHERE country_id = '.$order->country_id.'';
	
				$db->setQuery($sql);
				$obj = $db->loadObject();

				$client_address = str_replace('%%COUNTRY_FULL%%', utf8_decode($obj->country), $client_address);
				$client_address = str_replace('%%COUNTRY_2D%%', utf8_decode($obj->country_2_code), $client_address);
				$client_address = str_replace('%%COUNTRY_3D%%', utf8_decode($obj->country_3_code), $client_address);
				
			}
			
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

		#############################################
		## WRITING THE GRID TOP TEXTURES HERE      ##
		#############################################
		
		$pdf->SetDrawColor(193,193,193);
		$pdf->Line( 10,96,200,96);	
		$pdf->Line( 10,96.7,200,96.7);			
		
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(12, 91.5);
		$pdf->Write(0, JText::_( 'COM_TICKETMASTER_QTY'));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(27, 91.5);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TICKETINFORMATION')));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(159, 91.5);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TIME')));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(182, 91.5);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PRICE')));

		#############################################
		## WRITING THE GRID - PLEASE DONT CHANGE   ##
		#############################################

		## Making the query for getting the order
		$sql='SELECT  COUNT(a.orderid) AS total, a.*, t.*, e.eventname, c.*, 
			  t.ticketdate, t.starttime, a.paid, e.groupname, t.eventcode, v.venue AS location, v.city, t.show_end_date, t.end_date
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c, #__ticketmaster_venues AS v,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND t.venue = v.id
			  AND ordercode = '.$this->eid.'
			  GROUP BY a.ticketid';
		 
		$db->setQuery($sql);
		$items = $db->loadObjectList();

		
		## Setting the startgrid, DON'T change this!
		$height1 = 100;
		$height2 = 104;
		$grand_totalprice = 0.00;
		$payment_status = 1;
		
		## Setting font color & font
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		
		$k = 0;
		for ($i = 0, $n = count($items); $i < $n; $i++ ){
			
			$row = $items[$i];
			
			if($row->coupon != ''){
				## We need to fill this temporary :)
				$session = JFactory::getSession();
				## Gettig the orderid if there is one.
				$couponcode = $session->set('coupon', $row->coupon); 			
			}
			
			if($row->paid != 1){
				$payment_status = 0;
			}
			
			## Writing the itemid on the left collumn
			## Make sure they are centered, see function below
			$chars = strlen($row->total);
			if ($chars == 1) {$pdf->SetXY(13, $height1);}
			if ($chars == 2) {$pdf->SetXY(12, $height1);}
			if ($chars == 3) {$pdf->SetXY(12, $height1);}
			if ($chars == 4) {$pdf->SetXY(11, $height1);}
			if ($chars == 5) {$pdf->SetXY(10, $height1);}
			$pdf->Write(0, $row->total.'x' );						

			if($row->show_end_date != 1){
				$ticketdate = date ($config->dateformat, strtotime($row->ticketdate));
			}else{
				$ticketdate = date ($config->dateformat, strtotime($row->ticketdate)).' - '.date ($config->dateformat, strtotime($row->end_date));
			}			
			
			## Event information, first line
			$pdf->SetXY(27, $height1);
			if ($row->parentname != $row->ticketname) {
				$pdf->Write(0, utf8_decode($row->eventname).' ('.utf8_decode($row->eventcode).')  '. utf8_decode($row->ticketname).' - ('.$ticketdate.')');
			}else{
				$pdf->Write(0, utf8_decode($row->eventcode).' / '.utf8_decode($row->ticketname).' ('.$ticketdate.')');
			}
			
			if ($row->seat_sector != 0) {
				
				$sql='SELECT * FROM #__ticketmaster_coords WHERE id = '.(int)$row->seat_sector.'';
					  
				$db->setQuery($sql);
				$seat = $db->loadObject();				  			
				
				$seatnumber = JText::_( 'COM_TICKETMASTER_SEAT_NR' ).': '.$seat->row_name.$seat->seatid;
				
				## Event information, second line
				$pdf->SetXY(27, $height2);
				$pdf->Write(0, $seatnumber .' -- '.utf8_decode($row->location).' - '.utf8_decode($row->city)  );			
				
			}else{
				## Event information, second line
				$pdf->SetXY(27, $height2);
				$pdf->Write(0, utf8_decode($row->location).' - '.utf8_decode($row->city)  );				
			}

			## Event information, the date field
			$pdf->SetXY(159, $height1);
			$pdf->Write(0, $row->starttime  );

			## Event information, the date field
			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$row->ticketprice*$row->total , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$row->ticketprice*$row->total , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$row->ticketprice*$row->total , $config->valuta);
			}		
			
			$pdf->SetXY(180, $height1);
			$pdf->Write(0, $price  );
			
			$y = $pdf->GetY();
			$pdf->SetDrawColor(193,193,193);
			$pdf->Line( 10,$y+6.7,200,$y+6.7);			

			## Add extra row to the FOR loop.
			$height1 = $height1+10;
			$height2 = $height2+10;
			
			## Count the price for confirmation with pending tickets.
			$grand_totalprice = $grand_totalprice+($row->ticketprice*$row->total);
			
			
		$k=1 - $k;
		}

		
		## get the session:
		$session = JFactory::getSession();
		if($session->get('coupon') == '') {
			$pdf->Line( 10,$y+7.4,200,$y+7.4);
		}			

		### IF DISCOUNT == TRUE == WRITE AN EXTRA LINE WITH THE DISCOUNT ###

		if($session->get('coupon') != '') {
			
			$pdf->SetXY(10, $height1);

			
			$sql='SELECT * FROM #__ticketmaster_coupons
				  WHERE coupon_code = "'.$session->get('coupon').'"';
			
			$db->setQuery($sql);
			$coupon = $db->loadObject();		
			
			if ($coupon->coupon_type == 1){
				
				## Discount in %
				$discount = ($grand_totalprice/100)*$coupon->coupon_discount;
						
			}else{
				
				## Discount in amounts :)
				$discount = $coupon->coupon_discount;
						
			}		
		
			## Event information, the date field
			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$discount , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$discount , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$discount , $config->valuta);
			}		
			
			$pdf->SetXY(180, $height1);
			$pdf->Write(0, $price .' -/-' );			
			
			$pdf->SetXY(27, $height1);
			
			if($disco->coupon_type == 1) {
			
				$tmp = $disco->coupon_discount.'%';
				$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_YOUR_DISCOUNT_PRICE'). ': ('. $tmp .')' ));
			
			}else{	
				
				$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_YOUR_DISCOUNT_PRICE')));					
				
			}
			
			$pdf->SetXY(27, $height2);
			$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_COUPONCODE'). ': '. $coupon->coupon_code ));
			
			$y = $pdf->GetY();
			$pdf->SetDrawColor(193,193,193);
			$pdf->Line( 10,$y+3,200,$y+3);			
			
		}
		
		if($session->get('coupon') != '') {
			$pdf->Line( 10,$y+3.6,200,$y+3.6);
		}					
		
		#############################################
		## WRITING THE TOTALS OF THE ORDER         ##
		#############################################
		
		## GEt the y-position:
		$y = $pdf->GetY();
		$y = $y+10;
		
		if ($config->use_euros_in_pdf == 2) {
			## Fixing the euro issue..
			$price = showprice($config->priceformat ,$grand_totalprice-$discount , '');
			$price = chr(128).' '.$price; 
		}elseif ($config->use_euros_in_pdf == 3){
			$price = showprice($config->priceformat ,$grand_totalprice-$discount , '');
			$price = chr(0x00A3).' '.$price; 
		}else{
			$price = showprice($config->priceformat ,$grand_totalprices-$discount , $config->valuta);
		}					
		
		## Event information, the price field		
		$pdf->SetXY(180, $y+10);
		$pdf->Write(0, $price  );
		
		if ($config->variable_transcosts == 1){
			
			$fees= ((($grand_totalprice-$discount)/100)*$config->transcosts)+$config->transactioncosts;
						
			## Event information, the price field
			$pdf->SetXY(150, $y+15);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTALFEE'))  );	
			
			## Event information, the price field
			$pdf->SetXY(150, $y+21);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTAL'))  );			

			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$fees , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$fees , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$fees , $config->valuta);
			}	
			
			$pdf->SetXY(180, $y+15);
			$pdf->Write(0, $price  );
			
			## Draw a line to count total amount			
			$pdf->Line( 150,$y+18,200,$y+18);
			$pdf->Line( 150,$y+18.5,200,$y+18.5);

			if ($config->use_euros_in_pdf == 2) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 3){
				$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , $config->valuta);
			}		
			
			## Draw a line to count total amount
			$pdf->Line( 150,$y+23.5,200,$y+23.5);
			$pdf->Line( 150,$y+24.1,200,$y+24.1);					
			
			$pdf->SetXY(180, $y+21);
			$pdf->Write(0, $price);	
			
			$pdf->SetXY(150, $y+10);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_SUBTOTAL'))  );			
								
		}else{
		
			if ($config->transactioncosts != 0){
			
				## Event information, the price field
				$pdf->SetXY(150, $y+15);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTALFEE'))  );	
				
				## Event information, the price field
				$pdf->SetXY(150, $y+21);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTAL'))  );	
			
				$totalfee = $config->transactioncosts;
				$fees = number_format($totalfee, 2, ',', '.');

				if ($config->use_euros_in_pdf == 2) {
					## Fixing the euro issue..
					$price = showprice($config->priceformat ,$config->transactioncosts , '');
					$price = chr(128).' '.$price; 
				}elseif ($config->use_euros_in_pdf == 3){
					$price = showprice($config->priceformat ,$config->transactioncosts , '');
					$price = chr(0x00A3).' '.$price; 
				}else{
					$price = showprice($config->priceformat ,$config->transactioncosts , $config->valuta);
				}					
				
				$pdf->SetXY(180, $y+15);
				$pdf->Write(0, $price  );
				
				## Draw a line to count total amount
				$pdf->Line( 150,$y+23.5,200,$y+23.5);
				$pdf->Line( 150,$y+24.1,200,$y+24.1);
				
				if ($config->use_euros_in_pdf == 2) {
					## Fixing the euro issue..
					$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , '');
					$price = chr(128).' '.$price.'-'; 
				}elseif ($config->use_euros_in_pdf == 3){
					$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , '');
					$price = chr(0x00A3).' '.$price; 
				}else{
					$price = showprice($config->priceformat ,$grand_totalprice-$discount+$fees , $config->valuta);
				}			
				
				$pdf->SetXY(180, $y+21);
				$pdf->Write(0, $price);	
				
				## Draw a line to count total amount			
				$pdf->Line( 150,$y+18,200,$y+18);
				$pdf->Line( 150,$y+18.5,200,$y+18.5);					
				
				$pdf->SetXY(150, $y+10);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_SUBTOTAL'))  );										
			}		
		}					
		
		$this->to_be_paid = $grand_totalprice-$discount+$fees;
		$session->set('coupon', '');
		
		$file = basename(tempnam('.', 'tmp'));
		rename($file, JPATH_SITE.DS.'tmp'.DS.$this->eid.'.pdf');
		$file .= '.pdf';

		## Save PDF to file now!!
		$pdf->Output(JPATH_SITE.DS.'tmp'.DS.$file, 'F');		
		
		## Now move the file away for security reasons
		## Import the Joomla! Filesystem.
		jimport('joomla.filesystem.file');
		
		## Copy the file to a new directory.
		$src  = JPATH_SITE.DS.'tmp'.DS.$file;	
		
		## The new name for the ticket
		$dest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'confirmation'.DS.$this->eid.'.pdf';
		
		## Copy the file now.
		JFile::copy($src, $dest);
		## The old temporary file needs to be deleted.
		JFile::delete($src);
	
	}
	
	public function doSend() {

		$mainframe = JFactory::getApplication();
		
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $path_include );

		$db = JFactory::getDBO();		
		
		## Loading the configuration table.
		$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
		$db->setQuery($sql);
		$configuration = $db->loadObject();
					
		## Getting the desired info from the configuration table
		$sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid = 3';
		 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		if ($config->pro_installed == 1){

			$sql = 'SELECT a . * , t . * , e.eventname, c . * , t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode, ext.seatid
				FROM #__ticketmaster_orders AS a
				LEFT JOIN #__ticketmaster_clients AS c ON a.userid = c.userid
				LEFT JOIN #__ticketmaster_events AS e ON a.eventid = e.eventid
				LEFT JOIN #__ticketmaster_tickets AS t ON a.ticketid = t.ticketid
				LEFT OUTER JOIN #__ticketmaster_coords AS ext ON a.orderid = ext.orderid
				WHERE a.ordercode = '.$this->eid.'';
			
			$db->setQuery($sql);
			$data = $db->loadObjectList();			
			
		}else{

			$sql = 'SELECT a . * , t . * , e.eventname, c . * , t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode
				FROM #__ticketmaster_orders AS a
				LEFT JOIN #__ticketmaster_clients AS c ON a.userid = c.userid
				LEFT JOIN #__ticketmaster_events AS e ON a.eventid = e.eventid
				LEFT JOIN #__ticketmaster_tickets AS t ON a.ticketid = t.ticketid
				WHERE a.ordercode = '.$this->eid.'';
				
			$db->setQuery($sql);
			$data = $db->loadObjectList();
			
		}

		
		
		$sql='SELECT COUNT(orderid) AS total 
		      FROM #__ticketmaster_orders 
			  WHERE ordercode = '.$this->eid.'
			  AND (paid = 0 OR paid = 3)';
			  
		$db->setQuery($sql);
		$status = $db->loadObject();				  		
		
		if ($status->total > 0) {
			$paymentstatus = '<font color="#FF0000">'.JText::_( 'COM_TICKETMASTER_ORDERSTATUS_UNPAID' ).'</font>';
		}else{
			$paymentstatus = '<font color="#006600">'.JText::_( 'COM_TICKETMASTER_ORDERSTATUS_PAID' ).'</font>';
		}
		
		$orders = '<ul>';
		
		$k = 0;
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
	
			$row        = $data[$i];
			
			$price = showprice($configuration->priceformat ,$row->ticketprice , $configuration->valuta);
			$ticketdate = date ($configuration->dateformat, strtotime($row->ticketdate));
			
			$customer = $row->firstname.' '.$row->name;
			$recipient = $row->emailaddress;
			
			if($row->seatid == '') {
				$orders .= '<li>[ '.$row->orderid.' ] - [ '.$ticketdate.' ] - <strong>'.$row->ticketname.'</strong> [ '.$price.' ]</li>';	
			}else{
				$orders .= '<li>[ '.$row->orderid.' ] - [ '.$ticketdate.' ] - <strong>'.$row->ticketname.'</strong> [ '.$price.' ] [ '.JText::_( 'COM_TICKETMASTER_SEAT_NR' ).' '.$row->seatid.' ]</li>';
			}
			
		$k=1 - $k;
		}
		
		$orders .= '</ul>';	

		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );	
		
		$date  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		
		$expired  = mktime(0, 0, 0, date("m")  , date("d")+$configuration->removal_days, date("Y"));
		$releasedate = date($configuration->dateformat, $expired);		
		
		## Get the paid amount:
		$to_be_paid = _getAmount($this->eid);	
		$discount 	= _getDiscount($this->eid, 1);
		$paid = $to_be_paid-$discount;
		
		$price = showprice($configuration->priceformat ,$this->to_be_paid , $configuration->valuta);
		$total_tickets = count($data);	
		
		$message = str_replace('%%NAME%%', $customer, $config->mailbody);
		
		$message = str_replace('%%ORDERCODE%%', $this->eid, $message);	
		$message = str_replace('%%ORDERDATE%%', $date, $message);
		$message = str_replace('%%TICKETS%%', $total_tickets, $message);	
		$message = str_replace('%%PRICE%%', $price, $message);
		$message = str_replace('%%ORDERLIST%%', $orders, $message);
		$message = str_replace('%%PAYMENTSTATUS%%', $paymentstatus, $message);
		$message = str_replace('%%RELEASEDATE%%', $releasedate, $message);
		$message = str_replace('%%COMPANYNAME%%', $configuration->companyname, $message);
		$message = str_replace('%%COMPANYADDRESS%%', $configuration->address1, $message);
		$message = str_replace('%%COMPANYCITY%%', $configuration->city, $message);
		$message = str_replace('%%PHONENUMBER%%', $configuration->phone, $message);

		
		## The place where the attachement is saved:
		$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'confirmation'.DS.$this->eid.'.pdf';
		
		if ($configuration->send_confirmation_pdf == 1) {
			
			if (file_exists($filename)) {
				## Only attach if file is present - Otherwise no attachment will be sent.
				$attachment[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'confirmation'.DS.$this->eid.'.pdf';
			}else{
				$attachment = '';	
			}
			
		}	
				
		## Imaport mail functions:
		jimport( 'joomla.mail.mail' );
							
		## Set the sender of the email:
		$sender[0] = $config->from_email;
		$sender[1] = $config->from_name;
							
		## Compile mailer function:			
		$obj = JFactory::getMailer();
		$obj->setSender( $sender );
		$obj->isHTML( true );
		$obj->setBody ( $message );				
		$obj->addRecipient($recipient);
		$obj->addAttachment($attachment);
		## Send blind copy to site admin?
		if ($config->receive_bcc == 1){
			if ($config->reply_to_email != ''){
				$obj->addRecipient($config->reply_to_email);
			}	
		}					
		## Add reply to and subject:					
		$obj->addReplyTo($config->reply_to_email);
		$obj->setSubject($config->mailsubject);
		
		if ($config->published == 1){						
			
			$sent = $obj->Send();						
		}
		
		return true;	

	}

	public function SendWaitingList() {

		$mainframe = JFactory::getApplication();
		
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $path_include );

		$db = JFactory::getDBO();		
		
		## Loading the configuration table.
		$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
		$db->setQuery($sql);
		$configuration = $db->loadObject();
					
		## Getting the desired info from the configuration table
		$sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid = 102';
		 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		$sql='SELECT  a.*, t.*, e.eventname, c.*, 
			  t.ticketdate, t.starttime, t.location, t.locationinfo, e.groupname, t.eventcode
			  FROM #__ticketmaster_waitinglist AS a, #__ticketmaster_clients AS c,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND a.ordercode = '.$this->eid.'';

		$db->setQuery($sql);
		$data = $db->loadObjectList();	
		
		$orders = '<ul>';
		
		$k = 0;
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
	
			$row        = &$data[$i];
			
			$price = showprice($configuration->priceformat ,$row->ticketprice , $configuration->valuta);
			$ticketdate = date ($configuration->dateformat, strtotime($row->ticketdate));
			
			$customer = $row->firstname.' '.$row->name;
			$recipient = $row->emailaddress;
			
			$orders .= '<li>[ '.$row->orderid.' ] - [ '.$ticketdate.' ] - <strong>'.$row->ticketname.'</strong> [ '.$price.' ]</li>';	
	
			
		$k=1 - $k;
		}
		
		$orders .= '</ul>';	

		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );	
		
		$date  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$date = date ($configuration->dateformat, strtotime($date));
		
		## Get the paid amount:
		$to_be_paid = _getAmount($this->eid);	
		$price = showprice($configuration->priceformat ,$to_be_paid , $configuration->valuta);
		$total_tickets = count($data);	
		
		$encoded = base64_encode('ordercode='.$this->eid);
		$paymentlink = JURI::root().'index.php?option=com_ticketmaster&controller=validate&task=confirm&order='.$encoded;		
		
		$message = str_replace('%%NAME%%', $customer, $config->mailbody);
		
		$message = str_replace('%%ORDERCODE%%', $this->eid, $message);	
		$message = str_replace('%%ORDERDATE%%', $date, $message);
		$message = str_replace('%%TICKETS%%', $total_tickets, $message);
		$message = str_replace('%%CONFIRMATIONLINK%%', $paymentlink, $message);	
		$message = str_replace('%%PRICE%%', $price, $message);
		$message = str_replace('%%COUNT_OF_DAYS%%', $configuration->removal_days, $message);
		$message = str_replace('%%ORDERLIST%%', $orders, $message);
		$message = str_replace('%%COMPANYNAME%%', $configuration->companyname, $message);
		$message = str_replace('%%COMPANYADDRESS%%', $configuration->address1, $message);
		$message = str_replace('%%COMPANYCITY%%', $configuration->city, $message);
		$message = str_replace('%%PHONENUMBER%%', $configuration->phone, $message);
				
		## Imaport mail functions:
		jimport( 'joomla.mail.mail' );
							
		## Set the sender of the email:
		$sender[0] = $config->from_email;
		$sender[1] = $config->from_name;
							
		## Compile mailer function:			
		$obj = JFactory::getMailer();
		$obj->setSender( $sender );
		$obj->isHTML( true );
		$obj->setBody ( $message );				
		$obj->addRecipient($recipient);
		$obj->addAttachment($attachment);
		## Send blind copy to site admin?
		if ($config->receive_bcc == 1){
			if ($config->reply_to_email != ''){
				$obj->addRecipient($config->reply_to_email);
			}	
		}					
		## Add reply to and subject:					
		$obj->addReplyTo($config->reply_to_email);
		$obj->setSubject($config->mailsubject);
		
		if ($config->published == 1){						
			
			$sent = $obj->Send();						
		}
		
		$query = 'UPDATE #__ticketmaster_waitinglist SET sent = 1 WHERE ordercode =  '.$this->eid.'';
					
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}		
		
		return true;	

	}


}


	
?>