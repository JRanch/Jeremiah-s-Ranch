<?php

/****************************************************************
 * @version                    	3.1.0
 * @package                    	ticketmaster
 * @copyright                	Copyright © 2009 - All rights reserved.
 * @license                    	GNU/GPL
 * @author                     	Robert Dam
 * @author mail                	info@rd-media.org
 * @website                    	http://www.rd-media.org
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

class remover{

	function cleanup(){

		$this->setError = '';
		$remove = 0;

		$db = JFactory::getDBO();
		## Making the query for getting the config
		$sql='SELECT  remove_unfinished, removal_hours, show_waitinglist 
			  FROM #__ticketmaster_config 
			  WHERE configid = 1';

		$db->setQuery($sql);
		$config = $db->loadObject();

		if ($config->remove_unfinished != 1) {
				return false;
		}

		## Create a new date NOW()-1h. (database session is not longer than 2 hours in global config.
		$cleanup = date('Y-m-d H:i:s', mktime(date('H')-$config->removal_hours, date('i'), date('s'), date('m'), date('d'), date('Y')));
		
		if($config->show_waitinglist == 1){
		
			## Group By for the waiting list functionality:
			$update = 'SELECT COUNT(o.ticketid) as totals, o.ticketid
					   FROM #__ticketmaster_orders  AS o, #__ticketmaster_tickets AS t
					   WHERE o.orderdate < "'.$cleanup.'"
					   AND o.paid = 0
					   AND o.published = 0
					   AND o.ticketid = t.ticketid
					   GROUP BY o.ticketid';
			
			$db->setQuery($update);
			$items= $db->loadObjectList();
			
			## Now we know the amount of tickets that will be deleted grouped by ticket id and a total amount of tickets.
			
			for ($i = 0, $n = count($items); $i < $n; $i++ ){
				
				$row 		= $items[$i];
				$ticketid 	= $row->ticketid;
				$total 		= $row->totals;

				## $this->items contains the amount of tickets to be removed per ticketid.
				## The ordering is based on date added (First in goes first out if enough tickets.
				
				$sql = 'SELECT COUNT( id ) AS total, ordercode
						FROM #__ticketmaster_waitinglist
						WHERE confirmed = 1
						AND processed = 0
						AND ticketid = '.$ticketid.'
						GROUP BY ordercode, ticketid
						HAVING COUNT( id ) <= '.$total.'
						ORDER BY date_added, total DESC ';	

				$db->setQuery($sql);
				$waiting_items = $db->loadObjectList();
				
				## Loop through the waiting list items:
				for ($i2 = 0, $n2 = count($waiting_items); $i2 < $n2; $i2++ ){
					
					$waitinglist = $waiting_items[$i2];
					
					## If the removable total is smaller or even to waiting list totals:
					if($waitinglist->total <= $total){
						
						## Remaining total to remove:
						$total = $total-$waitinglist->total;
						## Total processed waiting items:
						$remove = $remove+$waitinglist->total;
						
						$process[] =  $waitinglist->ordercode;
						
						self::processWaitingListItem($process);
						
						unset($process);
						
					}
					
				} // end for loop 2.
			
			} //end for loop 1.
			
		} // end if statement when waitinglist is turned on.

		
		## End of Waiting list functionality!
		
		## Now grab the items that needs to be removed from the DB: (This is the query only!)
		$update =  'SELECT o.ticketid , t.parent AS parentticket, o.orderid, o.seat_sector
					FROM #__ticketmaster_orders  AS o, #__ticketmaster_tickets AS t
					WHERE o.orderdate < "'.$cleanup.'"
					AND o.paid = 0
					AND o.published = 0
					AND o.ticketid = t.ticketid';
		
		## If waitinglist is turned on, then we must load the items to be removed only.
		## Else everything can be removed from here.
		if($config->show_waitinglist == 1){
			
			## if remove = 0 then stop here..
			if($remove == 0){

				$db->setQuery($update);
				$this->data = $db->loadObjectList();				
				
			}else{

				$db->setQuery($update, 0, $remove);
				$this->data = $db->loadObjectList();				
				
			}	
		
		}else{

			$db->setQuery($update);
			$this->data = $db->loadObjectList();
		
		}	
		
		$this->counter = count($this->data);
		
		if ($this->counter < 1){
				$this->setError = 'No unfnished orders in the database.';
				return false;
		}
		
		## Everything can be deleted, the waiting list is off.
		if($config->show_waitinglist != 1){
		
			## Now go on with the delete functions.
			$query = 'DELETE FROM #__ticketmaster_orders 
					  WHERE orderdate < "'.$cleanup.'"
					  AND paid = 0
					  AND published = 0';
	
			$db->setQuery( $query );
	
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
					$this->setError = $db->getErrorMsg();
					return false;
			}
		
		}

		## Tickets have been removed successfull
		## Now we need to update the totals from the Object earlier this script.
		$k = 0;
		for ($i = 0, $n = count($this->data); $i < $n; $i++ ){

				$row = $this->data[$i];
				
				if($config->show_waitinglist == 1){
					
					$query = 'DELETE FROM #__ticketmaster_orders WHERE orderid = '.$row->orderid.' ';
						
					## Do the query now	and delete all selected invoices.
					$db->setQuery( $query );
						
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						return false;
					}	
					
				}

				$query = 'UPDATE #__ticketmaster_tickets'
						  . ' SET totaltickets = totaltickets+1'
						  . ' WHERE ticketid = '.$row->ticketid.' ';

				## Do the query now
				$db->setQuery( $query );

				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
						$this->setError = $db->getErrorMsg();
						return false;
				}

				## This is for the parent ticket. (there is a parent available)
				## If not, then the query won't have to run as there is no parent.
				if ($row->parentticket != 0){

						## Update the tickets-totals that where removed.
						$query = 'UPDATE #__ticketmaster_tickets'
								. ' SET totaltickets = totaltickets+1'
								. ' WHERE ticketid = '.$row->parentticket.' ';

						## Do the query now
						$db->setQuery( $query );

						## When query goes wrong.. Show message with error.
						if (!$db->query()) {
								$this->setError = $db->getErrorMsg();
								return false;
						}
				}

				if ($row->seat_sector != 0){

					$query = 'UPDATE #__ticketmaster_coords
							  SET booked = 0, orderid = 0
							  WHERE orderid = '.$row->orderid.' ';

					## Do the query now
					$db->setQuery( $query );

					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
						$this->setError = $db->getErrorMsg();
						return false;
					}

				}

		$k=1 - $k;
		}

		return true;

	}
	
	function processWaitingListItem($cid = array()) {
	
		## Count the cids
		if (count( $cid )) {
	
		## Make cids safe, against SQL injections
		JArrayHelper::toInteger($cid);
		## Implode cids for more actions (when more selected)
		$cids = implode( ',', $cid );
						
		$db = JFactory::getDBO();
	
		$sql = 'SELECT * FROM #__ticketmaster_waitinglist '
			   .'WHERE ordercode IN ( '.$cids.' )';
					  	
		$db->setQuery( $sql );
					  				
			## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	
		## Getting the ticket id's
		$data = $db->loadObjectList();
			
		## Loop the ticketnumbers for deletion
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
	
			$row  = $data[$i];
		
			## Preparing items for saving:
			$process['userid'] 		= $row->userid;
			$process['ordercode'] 	= $row->ordercode;
			$process['eventid'] 	= $row->eventid;
			$process['ticketid'] 	= $row->ticketid;
			## Create a pending payment:
			$process['paid'] 		= 3;
			$process['orderdate'] 	= $row->date_added;
			$process['published'] 	= 1;
			$process['ipaddress'] 	= $row->ip_address;
			## Storing the data immediatly:
			self::store($process);
		
			unset($process);
	
		}
			
		## Update items in the waiting list --> processed = true
		$query = 'UPDATE #__ticketmaster_waitinglist
                  SET processed = 1
				  WHERE ordercode IN ( '.$cids.' )';
		
		$db->setQuery( $query );
		$db->query();
					      	
		## Send people a payment request.
		self::paymentSend($cids);

		}
		
	return true;
	}
	
	function paymentSend($cids=array()){
	
		$query = 'SELECT * FROM #__ticketmaster_orders
			      WHERE ordercode IN ( '.$cids.' )
				  GROUP BY ordercode';
	
		$db = JFactory::getDBO();
		## Do the query now
		$db->setQuery($query);
		$data = $db->loadObjectList();
	
		## Getting the email now -- ID 101 from message center.
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 104";
		
		$db->setQuery($sql);
		$mail = $db->loadObject();
	
		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );
	
		## getting the required data for the configuration:
		$query = 'SELECT priceformat, valuta FROM #__ticketmaster_config WHERE configid = 1';
	
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$config = $db->loadObject();
	
		## Include the functions for price views.
		$file_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
		include_once( $file_include );
	
		for ($i = 0, $n = count($data); $i < $n; $i++ ){
					
			$row = $data[$i];
				
			## Getting the order amount.
			$total = _getAmount($row->ordercode);
			$price = showprice($config->priceformat ,$total , $config->valuta);
				
			## Check if the order has been paid:
			$sql = 'SELECT COUNT(orderid) AS total, userid
					FROM #__ticketmaster_orders
					WHERE paid = 1
					AND ordercode = '.(int)$row->ordercode.'';
		
			$db->setQuery($sql);
			$item = $db->loadObject();
					
			## Check if the order has been paid:
			$sql = 'SELECT c.name, c.emailaddress, c.firstname
					FROM #__ticketmaster_clients AS c, #__ticketmaster_orders AS o
					WHERE o.userid = c.userid
					AND o.ordercode = '.(int)$row->ordercode.'';
		
			$db->setQuery($sql);
			$client = $db->loadObject();		
					
			if( $item->total == 0 ){
				
				$sql='SELECT  a.*, t.*, e.eventname, c.*,
					  t.ticketdate, t.starttime, t.location, t.locationinfo, a.paid, e.groupname, t.eventcode
					  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
					  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t
					  WHERE a.userid = c.userid
					  AND a.eventid = e.eventid
					  AND a.ticketid = t.ticketid
					  AND ordercode = '.(int)$row->ordercode.'
					  GROUP BY a.orderid';
					
				$db->setQuery($sql);
				$order_info = $db->loadObjectList();	

				$orders = '<ul>';
				
				$k = 0;
				for ($i2 = 0, $n2 = count($order_info); $i2 < $n2; $i2++ ){
				
					$ticketdata = $order_info[$i2];
						
					$price = showprice($config->priceformat ,$ticketdata->ticketprice , $config->valuta);
						
					$orders .= '<li>[ '.$ticketdata->orderid.' ] - <strong>'.$ticketdata->ticketname.'</strong> [ '.$price.' ]</li>';
				
						
					$k=1 - $k;
				}
				
				$orders .= '</ul>';				
	
				## Getting the order amount.
				$total = _getAmount($row->ordercode);
				$price = showprice($config->priceformat ,$total , $config->valuta);
	
				## encode the link;
				$encoded = base64_encode('payfororder='.$row->ordercode);
				$paymentlink = JURI::root().'index.php?option=com_ticketmaster&controller=validate&task=pay&order='.$encoded;
	
				$message 	 = str_replace('%%PRICE%%', $price, $mail->mailbody);
				$message     = str_replace('%%NAME%%', $client->name, $message);
				$message     = str_replace('%%FIRSTNAME%%', $client->firstname, $message);
				$message     = str_replace('%%ORDERCODE%%', $row->ordercode, $message);
				$message     = str_replace('%%ORDERLIST%%', $orders, $message);
				$message     = str_replace('%%PAYMENTLINK%%', $paymentlink, $message);
	
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
				$obj->addRecipient($client->emailaddress);
				## Send blind copy to site admin?
				if ($mail->receive_bcc == 1){
					if ($mail->reply_to_email != ''){
						$obj->addRecipient($mail->reply_to_email);
					}
				}
				## Add reply to and subject:
				$obj->addReplyTo($sender);
				$obj->setSubject($mail->mailsubject);
	
				if ($mail->published == 1){		
					$sent = $obj->Send();
				}
	
			}
	
		}
	
		return true;
	}	
	
	function store($data){
		
		## Including the orders table from the backend.
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'tables');
		$row = JTable::getInstance('order', 'Table');
	
		## Bind the form fields to the web link table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	
		## Make sure the web link table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	
		## Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	
	
	}
		

	function counter(){
			return $this->counter;
	}

	function error(){
			return $this->setError;
	}

}