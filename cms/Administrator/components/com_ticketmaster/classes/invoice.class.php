<?php

## Direct access is not allowed.
defined('_JEXEC') or die();

class confirmation{					


	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;  
	
	 }  


	public function doConfirm() {
	
		global $mainframe, $option;

		
		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  * FROM #__ticketmaster_config WHERE configid = 1'; 
	 
		$db->setQuery($sql);
		$config = $db->loadObject();
		
		## include the functions for price views.
		$file_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $file_include );		
			
		## Date definition - Change if you like.
		$orderdate = date ($config->dateformat, strtotime($order->ticketdate));
		
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
		$pdf =& new FPDI_EAN13();
		## add a page
		$pdf->AddPage();
				
		## set the sourcefile
		$sourcePDF = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'orderconfirmation.pdf';
		$pdf->setSourceFile($sourcePDF);
		## import page 1
		$tplIdx = $pdf->importPage(1);
		## use the imported page and place it at point 0,0 with a width of 210 mm (A4 Format)
		$pdf->useTemplate($tplIdx, 0, 0, 210);
		
		## Getting the order date:
		$orderdate = date ($config->dateformat, strtotime($order->orderdate));
		
		$logo   = explode("-", $config->position_logo_confirmation);
		$folder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'images'.DS;
		$pdf->Image($folder.'confirmation_logo.jpg' , $logo[0] , $logo[1] ,0 ,20);		
		
		#############################################
		## WRITING THE ORDERDATE                   ##
		#############################################
		
		## Writing the orderdate on the confirmation.
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(7, 63);
		$pdf->Write(0, JText::_( 'COM_TICKETMASTER_ORDERDATE').': '.$orderdate );

		###############################################
		## WRITING THE COMPANY INFORMATION ON TICKET ##
		###############################################
		
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

		################################################
		## WRITING THE CUSTOMER INFORMATION ON TICKET ##
		################################################
		
		## Writing the clientname.
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(135, 51);
		$pdf->Write(0, utf8_decode($order->name) );	

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

		#############################################
		## WRITING THE ORDER CONFIRMATION TEXT     ##
		#############################################
		
		## Writing the orderdate on the confirmation.
		$pdf->SetFont('Arial', '', 11);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(7, 79);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_ORDERCONFIRMATION')).' '.$this->eid);

		#############################################
		## WRITING THE GRID TOP TEXTURES HERE      ##
		#############################################
		
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(14, 88);
		$pdf->Write(0, JText::_( 'ID#'));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(27, 88);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TICKETINFORMATION')));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(129, 88);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_DATE')));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(159, 88);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_TIME')));

		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetXY(182, 88);
		$pdf->Write(0, utf8_decode(JText::_( 'COM_TICKETMASTER_PRICE')));

		#############################################
		## WRITING THE GRID - PLEASE DONT CHANGE   ##
		#############################################

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
		$items = $db->loadObjectList();

		## Setting the startgrid, DON'T change this!
		$height1 = 100;
		$height2 = 104;
		$totalprice = 0.00;
		
		## Setting font color & font
		$pdf->SetFont('Arial', '', 9);
		$pdf->SetTextColor(0,0,0);
		
		$k = 0;
		for ($i = 0, $n = count($items); $i < $n; $i++ ){
			
			$row = &$items[$i];
			
			## Writing the itemid on the left collumn
			## Make sure they are centered, see function below
			$chars = strlen($row->orderid);
			if ($chars == 1) {$pdf->SetXY(15, $height1);}
			if ($chars == 2) {$pdf->SetXY(13, $height1);}
			if ($chars == 3) {$pdf->SetXY(12, $height1);}
			if ($chars == 4) {$pdf->SetXY(11, $height1);}
			if ($chars == 5) {$pdf->SetXY(10, $height1);}
			$pdf->Write(0, $row->orderid );						
			
			## Event information, first line
			$pdf->SetXY(27, $height1);
			if ($row->parentname != $row->ticketname) {
				$pdf->Write(0, utf8_decode($row->eventcode).' / '.utf8_decode($row->parentname).' - '. utf8_decode($row->ticketname));
			}else{
				$pdf->Write(0, utf8_decode($row->eventcode).' / '.utf8_decode($row->ticketname));
			}
			## Event information, second line
			$pdf->SetXY(27, $height2);
			$pdf->Write(0, utf8_decode($row->location).' - '.utf8_decode($row->locationinfo) );

			## Event information, the date field
			$pdf->SetXY(125, $height1);
			$ticketdate = date ($config->dateformat, strtotime($order->ticketdate));
			$pdf->Write(0, $ticketdate );

			## Event information, the date field
			$pdf->SetXY(159, $height1);
			$pdf->Write(0, $row->starttime  );

			## Event information, the date field
			if ($config->use_euros_in_pdf == 1) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$row->ticketprice , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 2){
				$price = showprice($config->priceformat ,$row->ticketprice , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$row->ticketprice , $config->valuta);
			}		
			
			$pdf->SetXY(178, $height1);
			$pdf->Write(0, $price  );
			
			## Add extra row to the FOR loop.
			$height1 = $height1+10;
			$height2 = $height2+10;
			$totalprice = (float)$totalprice+$price;
			
			
		$k=1 - $k;
		}	
		
		#############################################
		## WRITING THE TOTALS OF THE ORDER         ##
		#############################################
		
		$pdf->Line( 174,250,200,250);	
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );		
		
		$total = _getAmount($this->eid);
		$fees = _getFees($this->eid);	
		
		if ($config->use_euros_in_pdf == 1) {
			## Fixing the euro issue..
			$price = showprice($config->priceformat ,$total-$fees , '');
			$price = chr(128).' '.$price; 
		}elseif ($config->use_euros_in_pdf == 2){
			$price = showprice($config->priceformat ,$total-$fees , '');
			$price = chr(0x00A3).' '.$price; 
		}else{
			$price = showprice($config->priceformat ,$total-$fees , $config->valuta);
		}					

		## Event information, the price field		
		$pdf->SetXY(175, 255);
		$pdf->Write(0, $price  );
		
		if ($config->variable_transcosts == 1){
			## Event information, the price field
			$pdf->SetXY(150, 259);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTALFEE'))  );	
			
			## Event information, the price field
			$pdf->SetXY(150, 267);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTAL'))  );			

			if ($config->use_euros_in_pdf == 1) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$fees , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 2){
				$price = showprice($config->priceformat ,$fees , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$fees , $config->valuta);
			}	
			
			$pdf->SetXY(175, 259);
			$pdf->Write(0, $price  );
			
			## Draw a line to count total amount			
			$pdf->Line( 174,263,200,263);

			if ($config->use_euros_in_pdf == 1) {
				## Fixing the euro issue..
				$price = showprice($config->priceformat ,$total , '');
				$price = chr(128).' '.$price; 
			}elseif ($config->use_euros_in_pdf == 2){
				$price = showprice($config->priceformat ,$total , '');
				$price = chr(0x00A3).' '.$price; 
			}else{
				$price = showprice($config->priceformat ,$total , $config->valuta);
			}				
			
			$pdf->SetXY(175, 267);
			$pdf->Write(0, $price);	
			
			$pdf->SetXY(150, 255);
			$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_SUBTOTAL'))  );			
								
		}else{
		
			if ($config->transactioncosts != 0){
			
				## Event information, the price field
				$pdf->SetXY(150, 259);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTALFEE'))  );	
				
				## Event information, the price field
				$pdf->SetXY(150, 267);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_TOTAL'))  );	
			
				$totalfee = $config->transactioncosts;
				$fees = number_format($totalfee, 2, ',', '.');

				if ($config->use_euros_in_pdf == 1) {
					## Fixing the euro issue..
					$price = showprice($config->priceformat ,$config->transactioncosts , '');
					$price = chr(128).' '.$price; 
				}elseif ($config->use_euros_in_pdf == 2){
					$price = showprice($config->priceformat ,$config->transactioncosts , '');
					$price = chr(0x00A3).' '.$price; 
				}else{
					$price = showprice($config->priceformat ,$config->transactioncosts , $config->valuta);
				}					
				
				$pdf->SetXY(175, 259);
				$pdf->Write(0, $price  );
				
				## Draw a line to count total amount
				$pdf->Line( 174,263,200,263);
				
				if ($config->use_euros_in_pdf == 1) {
					## Fixing the euro issue..
					$price = showprice($config->priceformat ,$total , '');
					$price = chr(128).' '.$price; 
				}elseif ($config->use_euros_in_pdf == 2){
					$price = showprice($config->priceformat ,$total , '');
					$price = chr(0x00A3).' '.$price; 
				}else{
					$price = showprice($config->priceformat ,$total , $config->valuta);
				}			
				
				$pdf->SetXY(175, 267);
				$pdf->Write(0, $price);		
				
				$pdf->SetXY(150, 255);
				$pdf->Write(0, utf8_decode(JText::_('COM_TICKETMASTER_SUBTOTAL'))  );										
			}		
		}					
		
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

}


	
?>