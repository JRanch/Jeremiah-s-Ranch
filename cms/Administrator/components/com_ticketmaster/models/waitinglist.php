<?php
/****************************************************************
 * @package			Ticketmaster 2.5.5								
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class TicketmasterModelWaitingList extends JmodelLegacy{
	
	function __construct(){
		parent::__construct();

		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();
		
		## Get the pagination request variables
		$limit         = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart    = JRequest::getInt('limitstart', 0);
		
		## In case limit has been changed, adjust limitstart accordingly
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
			 $sql='SELECT a.*, t.ticketname, e.eventname, c.address, c.name, c.city, SUM(t.ticketprice) AS orderprice, COUNT(a.id) AS totaltickets
			       FROM #__ticketmaster_waitinglist AS a, #__ticketmaster_clients AS c, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where.' GROUP BY a.ordercode ORDER BY date_added ASC'; 
					
            $this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

	function _buildContentWhere() {
	
		$mainframe =& JFactory::getApplication();
		
		$db				  = JFactory::getDBO();

		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_e', 'filter_ordering_e','0','cmd' );
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
			
		if ($search) {
			$where[] = 'LOWER(a.ordercode) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where		= $this->_buildContentWhere();
		
			## Making the query for showing all the clients in list function
			$sql='SELECT a.*, t.ticketname, e.eventname, c.address, c.name, c.city, SUM(t.ticketprice) AS orderprice, COUNT(a.id) AS totaltickets
			      FROM #__ticketmaster_waitinglist AS a, #__ticketmaster_clients AS c,
				  #__ticketmaster_events AS e, #__ticketmaster_tickets AS t'
				  .$where
				  .' GROUP BY a.ordercode  ORDER BY date_added ASC'; 

		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}	    

	function processOrder($cid = array()) {
		
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
				$process['userid'] 	= $row->userid;
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
				
				## Counting the tickets, for updating the counter:
				$query = 'SELECT totaltickets, parent AS parentticket, counter_choice 
						  FROM #__ticketmaster_tickets 
						  WHERE ticketid = '.(int)$row->ticketid.''; 			
		
				$db->setQuery($query);
				$tickets = $db->loadObject();
				
				## Nnow update the parent ticket totals:
				if($tickets->parentticket != 0){ 
				
					$query = 'UPDATE #__ticketmaster_tickets'
						  . ' SET totaltickets = totaltickets-1'
						  . ' WHERE ticketid = '.(int) $tickets->parentticket.' ';
		
					## Do the query now	
					$db->setQuery( $query );
					$db->query();	
				
				}
					
				## Update the tickets-totals that where removed.
				$query = 'UPDATE #__ticketmaster_tickets'
					. ' SET totaltickets = totaltickets-1'
					. ' WHERE ticketid = '.(int) $row->ticketid.' ';
				
				## Do the query now	
				$db->setQuery( $query );	
			    $db->query();								

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
			$sql = 'SELECT c.firstname, c.name, c.emailaddress
					FROM #__ticketmaster_clients AS c, #__ticketmaster_orders AS o 
					WHERE o.userid = c.userid
					AND o.ordercode = '.(int)$row->ordercode.'
					GROUP BY ordercode'; 		
			
			$db->setQuery($sql);
			$client = $db->loadObject();			
			
			if( $item->total == 0 ){
				
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
		
		return true;
	}	
	
	function store($data)
	{
		
		$row = $this->getTable('order');

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
	

	function confirmOrder($cid = array(), $confirmed = 1) {
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_waitinglist'
				. ' SET confirmed = '.(int) $confirmed
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

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			$db = JFactory::getDBO();
			
			## Delete all categories from DB
			$query = 'DELETE FROM #__ticketmaster_waitinglist WHERE ordercode IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		
		return true;
		
		}
	}	
}
?>