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

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class ticketmasterModelTicketbox extends JmodelLegacy
{
	function __construct(){
		
		parent::__construct();
	
		$config = JFactory::getConfig();
		$mainframe = JFactory::getApplication();
		
		// Get the pagination request variables
		$limit         = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart    = $mainframe->getUserStateFromRequest( 'limitstart', 'limitstart', 0, 'int' );
		//$limitstart    = JRequest::getInt('limitstart', 0);
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 		
	}

	function getPagination() {
		
		if (empty($this->_pagination)) {
		
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
	
		return $this->_pagination;
	}
    
    function getTotal() {
	
        if (empty($this->_total)) {

			$where		= $this->_buildContentWhere();
		
			 ##Making the query for showing all the clients in list function
			 $query='SELECT a.*, t.ticketname, e.eventname, c.name, c.city 
			         FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
				      #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
					 .$where
					 .' GROUP BY a.ordercode'; 
					
            $this->_total = $this->_getListCount($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

	function _buildContentWhere() {
	
		$mainframe 		  = JFactory::getApplication();
		$db				  = JFactory::getDBO();

		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_e', 'filter_ordering_e','0','cmd' );
		$filter_sent      = $mainframe->getUserStateFromRequest( 'filter_ordering_sent', 'filter_ordering_sent','0','cmd' );
		$filter_pdf       = $mainframe->getUserStateFromRequest( 'filter_ordering_pdf', 'filter_ordering_pdf','0','cmd' );
		$filter_paid      = $mainframe->getUserStateFromRequest( 'filter_ordering_paid', 'filter_ordering_paid','0','cmd' );
		$filter_event     = $mainframe->getUserStateFromRequest( 'filter_ordering_event', 'filter_event','0','cmd' );		
		$search			  = $mainframe->getUserStateFromRequest( 'searchbox', 'searchbox', '', 'string' );
		$search			  = JString::strtolower( $search );
		
		$where = array();

		$where[] = 'a.userid = c.userid';
		$where[] = 'a.eventid = e.eventid';
		$where[] = 'a.ticketid = t.ticketid';
		
		if ($filter_event != 0){
			$where[] = 'e.eventid = '.$filter_event;
		}else{
			$where[] = 'e.eventid > 0';
		}
		
		if ($filter_order != 0){
			$where[] = 'a.ticketid = '.$filter_order;
		}else{
			$where[] = 'a.ticketid > 0';
		}
		
		if ($filter_sent == 0) {
			$where[] = 'a.pdfsent >= "0" ';
		}	
		if ($filter_sent == 1) {
			$where[] = 'a.pdfsent = "1" ';
		}	
		if ($filter_sent == 2) {
			$where[] = 'a.pdfsent = "0" ';
		}	

		if ($filter_pdf == 0) {
			$where[] = 'a.pdfcreated >= "0" ';
		}	
		if ($filter_pdf == 1) {
			$where[] = 'a.pdfcreated = "1" ';
		}	
		if ($filter_pdf == 2) {
			$where[] = 'a.pdfcreated = "0" ';
		}	

		if ($filter_paid == 0) {
			$where[] = 'a.paid >= "0" ';
		}	
		if ($filter_paid == 1) {
			$where[] = 'a.paid = "1" ';
		}	
		if ($filter_paid == 2) {
			$where[] = 'a.paid = "0" ';
		}
		if ($filter_paid == 3) {
			$where[] = 'a.paid = "2" ';
		}
		if ($filter_paid == 4) {
			$where[] = 'a.paid = "3" ';
		}					
			
		if ($search) {
			$where[] = 'a.ordercode = '. (int) $search;
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where		= $this->_buildContentWhere();
		
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, t.ticketname, e.eventname, c.firstname, c.address, c.name, c.city, SUM(t.ticketprice) AS orderprice, 
			      COUNT(a.orderid) AS totaltickets
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
				  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where
				  .' GROUP BY a.ordercode  ORDER BY orderid ASC'; 

		 
		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}


	function _buildContentWhereTicket() {
		
		$db   	= JFactory::getDBO();
		
		$where 	= array();

		$where[] = 'a.userid = c.userid';
		$where[] = 'a.eventid = e.eventid';
		$where[] = 'a.ticketid = t.ticketid';		
		$where[] = 'a.ordercode = '.(int)$this->id;


		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getData() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			$where		= $this->_buildContentWhereTicket();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, t.ticketname, t.ticketprice, e.eventname, c.firstname, c.address, c.name, c.city
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c,
				  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where; 

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }  
   
   function getRemark() {
	
		if (empty($this->_data)) {
			
			$db = JFactory::getDBO();
		
			## Making the query for showing all the clients in list function
			$sql='SELECT *
				  FROM #__ticketmaster_remarks
				  WHERE ordercode = '.(int)$this->id.'';
	
			$db->setQuery($sql);
			$this->data = $db->loadObject();	
		}
		
		return $this->data;
   }
   
   function getExtData() {
   		
		## this data is for PRO only
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$sql='SELECT c.id, c.orderid, c.seatid, c.row_name
				  FROM #__ticketmaster_orders AS a, #__ticketmaster_coords AS c
				  WHERE a.orderid = c.orderid
				  AND a.ordercode = '.(int)$this->id;

		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
   }      

   function getPrice() {

     	 	$db    = JFactory::getDBO();
			
			$sql='SELECT SUM(t.ticketprice) AS orderprice, c.coupon_discount, c.coupon_type
			      FROM #__ticketmaster_orders AS a 
				  LEFT JOIN #__ticketmaster_tickets AS t
				  ON a.ticketid = t.ticketid
				  RIGHT JOIN #__ticketmaster_coupons AS c
				  ON a.coupon = c.coupon_code
				  WHERE a.ordercode = '.(int)$this->id;
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
			
			if(!$this->data->coupon_discount){

				$sql='SELECT SUM(t.ticketprice) AS orderprice
					  FROM #__ticketmaster_orders AS a 
					  LEFT JOIN #__ticketmaster_tickets AS t
					  ON a.ticketid = t.ticketid
					  WHERE a.ordercode = '.(int)$this->id;
			 
				$db->setQuery($sql);
				$this->data = $db->loadObject();
				
				$this->data->coupon_discount = '';
				$this->data->coupon_type = '';

			}

		return $this->data;
   }  

   function getClient() {
   		
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, c.*
			      FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c, #__ticketmaster_tickets AS t
				  WHERE a.userid = c.userid
				  AND a.ordercode = '.(int)$this->id;
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
   } 

	function publish($cid = array(), $publish = 1) {
		
		## Count the cids
		if (count( $cid )) {
					
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($data);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET published = '.(int) $publish
				. ' WHERE ordercode IN ( '.$cids.' )';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
		}
		return true;
	}

	function unblockticket($cid = array(), $paid = 0) {

		## Count the cids
		if (count( $cid )) {
					
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($data);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET blacklisted = '.(int) $paid
				. ' WHERE orderid IN ( '.$cids.' )';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
		}
		return true;
	
	}

	function blockticket($cid = array(), $paid = 1) {

		## Count the cids
		if (count( $cid )) {
					
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($data);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET blacklisted = '.(int) $paid
				. ' WHERE orderid IN ( '.$cids.' )';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
		}
		return true;
	
	}

	function paymentprocessor($cid = array(), $paid = 1) {
		
		## Count the cids
		if (count( $cid )) {
					
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($data);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_orders'
				. ' SET paid = '.(int) $paid
				. ' WHERE orderid IN ( '.$cids.' )';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
		}
		return true;
	}

	function paymentsdone($cid = array(), $paid = 1) {
		
		## Count the cids
		if (count( $cid )) {
					
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($data);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			if($paid == 2){
			
				## Now set the tickets to paid, we have done the mails.
				$query = 'UPDATE #__ticketmaster_orders'
						. ' SET paid = '.(int) $paid
						. ' WHERE ordercode IN ( '.$cids.' )';
					
				## Do the query now
				$this->_db->setQuery( $query );
					
				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}				
			
				return true;
			}	
			
			## Including required paths to calculator.
			$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
			include_once( $path_include );				

			$query = 'SELECT payment_email_send, priceformat, valuta FROM #__ticketmaster_config WHERE configid = 1';
			
			$db = JFactory::getDBO();
		 	$db->setQuery($query);
		 	$config = $db->loadObject();
			
			## Include the functions for price views.
			$file_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'functions.php';
			require_once( $file_include );	
					
			
			if ($config->payment_email_send == 1){
				
				## Getting the email now -- ID 7 from message center.
				$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 7";
				 
				$db->setQuery($sql);
				$mail = $db->loadObject();
			
				$query = 'SELECT o.*, c.emailaddress, c.name, c.firstname
						  FROM #__ticketmaster_orders AS o, #__ticketmaster_clients AS c
					      WHERE ordercode IN ( '.$cids.' )
					   	  AND  o.userid = c.userid
						  GROUP BY userid';	  
				
				$db = JFactory::getDBO();
				## Do the query now	
				$db->setQuery($query);
				$data = $db->loadObjectList();	
			
				$k = 0;
				for ($i = 0, $n = count($data); $i < $n; $i++ ){
					
					$row = $data[$i];				
					
					## Getting the order amount. 
					$total = _getAmount($row->ordercode, 1);
					$price = showprice($config->priceformat ,$total , $config->valuta);

					$message 	 = str_replace('%%PRICE%%', $price, $mail->mailbody);
					$message     = str_replace('%%NAME%%', $row->name, $message);
					$message     = str_replace('%%FIRSTNAME%%', $row->firstname, $message);
					$message     = str_replace('%%ORDERCODE%%', $row->ordercode, $message);
					
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
					$obj->addRecipient($row->emailaddress);
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
					
					$k=1 - $k;
					
				}					
			
			}
			
			## Now set the tickets to paid, we have done the mails.
			$query = 'UPDATE #__ticketmaster_orders'
					. ' SET paid = '.(int) $paid
					. ' WHERE ordercode IN ( '.$cids.' )';
				
			## Do the query now
			$this->_db->setQuery( $query );
				
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			
		}
		return true;
	}
	
	function paymentResender($cid=array()){
		
		## Make cids safe, against SQL injections
		JArrayHelper::toInteger($data);
		## Implode cids for more actions (when more selected)
		$cids = implode( ',', $cid );

		$query = 'SELECT * FROM #__ticketmaster_orders
			      WHERE ordercode IN ( '.$cids.' )
				  GROUP BY ordercode';
		
		$db = JFactory::getDBO();
		## Do the query now	
		$db->setQuery($query);
		$data = $db->loadObjectList();
		
		## Getting the email now -- ID 101 from message center.
		$sql = "SELECT * FROM #__ticketmaster_emails WHERE emailid = 101";
		 
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
					WHERE paid != 1
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
			
			if( $item->total > 0 ){
				
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
		
	}

	function store($data) {
	
		$row =& $this->getTable();

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
	return true;
	}

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			## Making the tickets inactive, they still be present at the database.

			$query = 'DELETE FROM #__ticketmaster_clients WHERE clientid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		
		return true;
		
		}
	}

	function removeTickets($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			## Database driver
			$db = JFactory::getDBO();
			
			## Making the tickets inactive, they still be present at the database.
			$query = 'SELECT o.* , t.parent AS parentticket
					  FROM #__ticketmaster_orders  AS o, #__ticketmaster_tickets AS t
					  WHERE o.ordercode IN ( '.$cids.' )
					  AND o.ticketid = t.ticketid';
			
			## Do the query now	and delete all selected invoices.
			## Do the query now	
		 	$db->setQuery($query);
		 	$data = $db->loadObjectList();

			$query = 'DELETE FROM #__ticketmaster_orders WHERE ordercode IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			## Set FTP credentials, if given
			jimport('joomla.client.helper');
			JClientHelper::setCredentialsFromRequest('ftp');
	
			## Import the file system
			jimport('joomla.filesystem.file');

			## Tickets have been removed successfull
			## Now we need to update the totals from the Object earlier this script.
			$k = 0;
			for ($i = 0, $n = count($data); $i < $n; $i++ ){
			
				$row = &$data[$i];
				
				
				## Path to a combined ticket is as below:
				$combined_ticket = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTickets-'.$row->ordercode.'.pdf';
				## remove tickets if there is a combined one.
				if (file_exists( $combined_ticket )) {
					JFile::delete( $combined_ticket );
				}				
				
				## Path to a normal ticket is as below:
				$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'eTicket-'.$row->orderid.'.pdf';
				## Remove single ticket
				if (file_exists($path)) {
					JFile::delete( $path );
				}
				
				## Path to a combined ticket is as below:
				$multi_ticket = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS.'multi-'.$row->ordercod.'.pdf';
				## remove tickets if there is a combined one.
				if (file_exists( $multi_ticket )) {
					JFile::delete( $multi_ticket );
				}					
				

				## Update the tickets-totals that where removed.
				## This is for the single ticket. (no parent ticket is present)
				$query = 'UPDATE #__ticketmaster_tickets'
					. ' SET totaltickets = totaltickets+1'
					. ' WHERE ticketid = '.$row->ticketid.' ';
				
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
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
						$this->setError($db->getErrorMsg());
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
	}

	function createconfirmation($cid = array()) {
	
		global $mainframe;

		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );	
			
			$db = JFactory::getDBO();	
			
			$query = 'SELECT * FROM #__ticketmaster_orders 
					  WHERE ordercode IN ( '.$cids.' )
					  GROUP BY ordercode';
			
			
			## Do the query now	
		 	$db->setQuery($query);
		 	$data = $db->loadObjectList();
			
			$k = 0;
	   		for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = &$data[$i];
				## Activating the class to produce PDF
				$this->_doConfirm($row->ordercode);
				
				$k=1 - $k;
				
			}	
		}
		
		return true;
	}

	function _doConfirm($eid) {
	
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'confirmation.php';
		$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'confirmation.php';
		
		## Check if the override is there.
		if (file_exists($override)) {
			## Yes, now we use it.
			require_once($override);
		} else {
			## No, use the standard
			require_once($path);
		}	
		
		if(isset($eid)) {  
		
			$sendconfirmation = new confirmation( (int)$eid );  
			$sendconfirmation->doConfirm();
			$sendconfirmation->doSend();
		
		}  
		
	}

	function ticketprocessor($cid = array()) {

		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );	
			
			$db = JFactory::getDBO();	
			
			$query = 'SELECT * FROM #__ticketmaster_orders WHERE ordercode IN ( '.$cids.' )';
			
			## Do the query now	
		 	$db->setQuery($query);
		 	$data = $db->loadObjectList();

			
			$k = 0;
	   		for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = &$data[$i];

				## Include the confirmation class to sent the tickets. 
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'createtickets.class.php';
				$override = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'override'.DS.'createtickets.class.php';
				
				## Check if the override is there.
				if (file_exists($override)) {
					## Yes, now we use it.
					require_once($override);
				} else {
					## No, use the standard
					require_once($path);
				}	
				
				if(isset($row->orderid)) {  
				
					$creator = new ticketcreator( (int)$row->orderid );  
					$creator->doPDF();
				
				}  				
				
				$k=1 - $k;
				
			}	
		}
	}

	
	function sendtickets($cid = array()) {

		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );	
			
			$db = JFactory::getDBO();	
			
			## OK, pickup tickets where a PDF is created, not send and the payment is done.
			$query = 'SELECT orderid FROM #__ticketmaster_orders 
					  WHERE ordercode IN ( '.$cids.' )
					  AND pdfcreated = 1
					  AND pdfsent = 0
					  AND paid = 1';
			
			## Do the query now	
		 	$db->setQuery($query);
		 	$data = $db->loadObjectList();
			
			$counttickets = count($data);

			## OK, pickup tickets where all cids are selcted
			$query = 'SELECT orderid FROM #__ticketmaster_orders 
					  WHERE ordercode IN ( '.$cids.' ) ';
			
			## Do the query now	
		 	$db->setQuery($query);
		 	$items = $db->loadObjectList();
			
			$countalltickets = count($items);
			
			## Let's do the check if both counts are equal? 
			## If they're not equal? DO NOT SEND THE TICKETS.
			if 	($counttickets != $countalltickets) {
				
				$error = $countalltickets-$counttickets;
				if($error > 1){
					JError::raiseWarning(100, JText::_( 'COM_TICKETMASTER_ERROR_BEFORE_SENDING' ));
					return false;
				}else{
					JError::raiseWarning(100, JText::_( 'COM_TICKETMASTER_ERROR_BEFORE_SENDING' ));
					return false;				
				}
					
			}	
			
			## OK, all checks have been done. We do now know the next things:
			## An PDF file has been created, the payments are done, and these tickets haven't been sent.
			## Let's group it again by order and send the tickets to the customer.
			$query = 'SELECT ordercode, ticketid FROM #__ticketmaster_orders 
					  WHERE ordercode IN ( '.$cids.' )
					  AND pdfcreated = 1
					  AND pdfsent = 0
					  AND paid = 1
					  GROUP BY ordercode';
			
			## Do the query now	
		 	$db->setQuery($query);
		 	$data = $db->loadObjectList();			
			
			## Let's send the order one by one into another function to send them
			$k = 0;
	   		for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = $data[$i];
				$this->_sendTickets($row->ordercode);
				$this->_updateTickets($row->ordercode);

				$k=1 - $k;
				
			}	
		}
	}

	function _sendTickets($eid) {
	
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendonpayment.class.php';
		require_once($path);
			
		$order = new sendonpayment( (int)$eid );  
		$order->send();

	}

	function reSendTickets($eid) {
		
		## Include the confirmation class to sent the tickets. 
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'sendticketcopy.class.php';
		require_once($path);
			
		$order = new resender( (int)$eid );  
		$order->send();

	}


	function _updateTickets($eid) {
		
		$db = JFactory::getDBO();

		## Making the query for getting the orders
		$sql='SELECT  a.ticketid
			  FROM #__ticketmaster_orders AS a
			  WHERE ordercode = '.(int)$eid.'';
	 
		$db->setQuery($sql);
		$info = $db->loadObjectList();
		
		$k = 0;
		for ($i = 0, $n = count($info); $i < $n; $i++ ){
			
				$row  = &$info[$i]; 

				## Updatading the collumn ticketssent
				$query = 'UPDATE #__ticketmaster_tickets'
					. ' SET ticketssent = ticketssent+1'
					. ' WHERE ticketid = '.$row->ticketid.'';
				
				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}

			$k=1 - $k;
	
		}
	}	
}
?>