<?php

/****************************************************************
 * @version			2.5.5											
 * @package			ticketmaster									
 * @copyright		Copyright Â© 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

class resender{					


	function __construct($eid){  
		
		## Setting the $eid as var
		$this->eid = $eid;  
	
	 }  

     function combinetickets($info)
	 {

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

		## Clearing!
		$this->info = '';
		$this->error = '';

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
		$row  = $info[0];		 
		
		$sql = "SELECT send_multi_ticket_only FROM #__ticketmaster_config WHERE configid = 1";
		 
        $db->setQuery($sql);
        $config = $db->loadObject();
		
		if($config->send_multi_ticket_only == 1){
		
			$attachment = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.$this->eid.'.pdf';

			if (!file_exists($attachment)) {
				
				## Create the new multi ticket.
				include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'special.tickets.class.php');
				
				$multi = new special( (int)$this->eid );  
				$multi->create();
				
				## Send the multi ticket only as attachment: multi-[ordercode]
				$attachment = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.(int)$this->eid.'.pdf';					
							
			} 
		
			$name		 = $row->name;
			$email		 = $row->emailaddress;	
		
		}else{
			
			if(($row->combine_multitickets) && (count($info)>1)) {
				
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
		}
		
		
		## We do now need to get the mail that wil send the ticket.
		## Using email ID: #1
		## We need to select all information.
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 8";
		 
        $db->setQuery($sql);
        $mail = $db->loadObject();
		
		$countattachments = count($attachment);
		
		$message  = str_replace('%%NAME%%', $name, $mail->mailbody);
		$subject  = str_replace('%%EVENTNAME%%', $event, $mail->mailsubject);
		
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
			$obj->addAttachment($attachment);
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
			
		}
	
	}

}	
?>