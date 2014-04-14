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

class sendonpayment{					


	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;  
	
	 }  

     function combinetickets($info)
	 {

		 ## Required helpers to create this PDF invoice
		 ## Do NOT edit the required files! It will damage your component.
		 include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdf'.DS.'fpdf.php');
		 include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'pdf'.DS.'fpdi_ean13.php');
		 ## initiate FPDI
		
		 $initrow = &$info[0];
		
		 $pdf = new FPDI_EAN13($initrow->ticket_orientation,'mm',$initrow->ticket_size);
		 $foutn = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTickets-'.$initrow->ordercode.'.pdf';
		
		 for ($i = 0, $n = count($info); $i < $n; $i++ ){
			 
			 $pdf->addPage();
			 $row  = &$info[$i]; 
			 $fn = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$row->orderid.'.pdf';
			 $pdf->setSourceFile($fn);
			 $tplIdx = $pdf->importPage(1);
			 ## use the imported page and place it at point 0,0 with a width of 210 mm (A4 Format)
			 $pdf->useTemplate($tplIdx, 0, 0, 0);
			 ## add a page
		
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
		$dest = $foutn;
		
		## Copy the file now.
		JFile::copy($src, $dest);
		## The old temporary file needs to be deleted.
		JFile::delete($src);
		return $foutn;
		
	 }

	function send() {

		$db = JFactory::getDBO();

		## Making the query for getting the orders
		$sql='SELECT  a.*, c.name, c.emailaddress, e.eventname,  t.ticket_size, t.ticket_orientation, t.combine_multitickets
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t 
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND t.ticketid = a.ticketid
			  AND ordercode = '.(int)$this->eid.''; 
	 
		$db->setQuery($sql);
		$info = $db->loadObjectList();
		
		$k = 0;
		$row  = &$info[0]; 
		
		if(($row->combine_multitickets) &&  (count($info)>1)) {
			
			$fname = $this->combinetickets($info);
			$attachment[] = $fname;
			$name		 = $row->name;
			$email		 = $row->emailaddress;
			$event		 = $row->eventname;
		
		} else {
			
			for ($i = 0, $n = count($info); $i < $n; $i++ ){
				
				$row  = &$info[$i]; 
				## Tickets are saved as: eTicket-1000
				## Create attachments the same as saved
				$attachment[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$row->orderid.'.pdf';
				$name		 = $row->name;
				$email		 = $row->emailaddress;
				$event		 = $row->eventname;
				$k=1 - $k;
				
			}	
		}
		
		## We do now need to get the mail that wil send the ticket.
		## Using email ID: #1
		## We need to select all information.
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 1 ";
		 
        $db->setQuery($sql);
        $mail = $db->loadObject();
		
		#### NEW FOR 3.0.2 - EXTRA DATA IN THE EMAIL FOR TICKETS! ####
		
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $path_include );		
		
		## Loading the configuration table.
		$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
		$db->setQuery($sql);
		$configuration = $db->loadObject();		
		
		$sql='SELECT  a.*, t.*, e.eventname, c.*, 
			  t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
			  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
			  WHERE a.userid = c.userid
			  AND a.eventid = e.eventid
			  AND a.ticketid = t.ticketid
			  AND ordercode = '.$this->eid.'
			  GROUP BY a.orderid';
				
		$db->setQuery($sql);
		$data = $db->loadObjectList();	
		
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'special.tickets.class.php');
		
		if ($configuration->send_multi_ticket_only == 1){
			
			## Create the new multi ticket.
			$multi = new special( (int)$this->eid );  
			$multi->create();

			## Clearing the old fashion PDF:
			unset($attachment);	
			## Send the multi ticket only as attachment: multi-[ordercode]
			$attachment[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.(int)$this->eid.'.pdf';
			$attachment_admin[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.(int)$this->eid.'.pdf';	
			
		}
		
		$sql='SELECT COUNT(orderid) AS total 
		      FROM #__ticketmaster_orders 
			  WHERE ordercode = '.(int)$this->eid.'
			  AND paid = 0';
		
		$ordering_code = $this->eid;
			  
		$db->setQuery($sql);
		$status = $db->loadObjectList();				  		
		
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
			
			if($row->firstname != ''){
				$customer = $row->firstname.' '.$row->name;
			}else{
				$customer = $row->name;
			}	
			$recipient = $row->emailaddress;
			$userid = $row->userid;
			
			$orders .= '<li>[ '.$row->orderid.' ] - [ '.$ticketdate.' ] - <strong>'.$row->ticketname.'</strong> [ '.$price.' ]</li>';	
	
			
		$k=1 - $k;
		}
		
		$orders .= '</ul>';	
		
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );	
		
		## Get the paid amount:
		$to_be_paid = _getAmount($ordering_code);	
		$price = showprice($configuration->priceformat ,$to_be_paid , $configuration->valuta);
		$total_tickets = count($data);			

		#### END ADDONG FOR EMAIL ####
		
		$countattachments = count($attachment);
		
		$message  = str_replace('%%NAME%%', $name, $mail->mailbody);
		$subject  = str_replace('%%EVENTNAME%%', $event, $mail->mailsubject);
		$message = str_replace('%%ORDERCODE%%', $this->eid, $message);	
		$message = str_replace('%%TICKETS%%', $total_tickets, $message);	
		$message = str_replace('%%PRICE%%', $price, $message);
		$message = str_replace('%%ORDERLIST%%', $orders, $message);
		$message = str_replace('%%PAYMENTSTATUS%%', $paymentstatus, $message);
		
		$message = str_replace('%%COMPANYNAME%%', $configuration->companyname, $message);
		$message = str_replace('%%COMPANYADDRESS%%', $configuration->address1, $message);
		$message = str_replace('%%COMPANYCITY%%', $configuration->city, $message);
		$message = str_replace('%%PHONENUMBER%%', $configuration->phone, $message);			
		
		if ($countattachments > 0){

			## Imaport mail functions:
			jimport( 'joomla.mail.mail' );
								
			## Set the sender of the email:
			$sender[0] = $mail->from_email;
			$sender[1] = $mail->from_name;					
			## Compile mailer function:			
			$obj = JFactory::getMailer();
			$obj->setSender( $sender );
			$obj->isHTML( true );
			$obj->setBody ( $message );				
			$obj->addRecipient($email);
			if ($configuration->send_pdf_tickets == 1){
				$obj->addAttachment($attachment);
			}
			## Send blind copy to site admin?
			if ($mail->receive_bcc == 1){
				if ($mail->reply_to_email != ''){
					$obj->addRecipient($mail->reply_to_email);
				}	
			}					
			## Add reply to and subject:					
			$obj->addReplyTo($sender);
			$obj->setSubject($subject);
			
			if ($mail->published == 1){						
				
				$sent = $obj->Send();						
			}
			
			## Add the ordercode & userid to $vars
			$vars['ordercode'] = $this->eid;
			$vars['userid'] = $userid;
			
			## TRIGGER INVOICING PLUGIN AND RELATED onAfterSendTickets	
			JPluginHelper::importPlugin('rdmediahelpers');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('OnAfterSentTickets', array($vars) );
			
			if($configuration->send_multi_ticket_admin == 1){ 

				## Clearing the old fashion PDF:
				unset($attachment);	
				## Check if the multi ticket is there:
				$attachment = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.(int)$this->eid.'.pdf';

				if (!file_exists($attachment)) {
					## Create the new multi ticket.
					$multi = new special( (int)$this->eid );  
					$multi->create();
					
					## Send the multi ticket only as attachment: multi-[ordercode]
					$attachment = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.(int)$this->eid.'.pdf';					
				} 

				## Now preapre message for the admin:
				$admin = JFactory::getMailer();
				## Set the sender of the email:
				$sender[0] = $mail->from_email;
				$sender[1] = $mail->from_name;					
				## Compile mailer function:			
				$admin->setSender( $sender );
				$admin->isHTML( true );
				$admin->setBody ( $message );
				$admin->addRecipient($configuration->email);
				$admin->addAttachment($attachment);
				$admin->addReplyTo($sender);
				$admin->setSubject($subject);
				$sent = $admin->Send();
				
			}

		}

		## Updating the order, PDF sent = 1
		$query = 'UPDATE #__ticketmaster_orders'
			. ' SET pdfsent = 1'
			. ' WHERE ordercode = '.(int)$this->eid.'';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			exit('Query went wrong in sendonpayment.class.php');
			return false;
		} 

		## Updating the order, PDF sent = 1
		$query = 'UPDATE #__ticketmaster_tickets'
			. ' SET ticketssent = ticketssent+'.(int)$countattachments.''
			. ' WHERE ticketid = '.(int)$info->ticketid.'';
		
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