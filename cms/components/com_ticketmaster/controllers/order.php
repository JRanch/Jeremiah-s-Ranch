<?php

/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright � 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

defined('_JEXEC') or die ('No Access to this file!');

jimport('joomla.application.component.controller');

class TicketmasterControllerOrder extends JControllerLegacy {

	function __construct() {
		parent::__construct();
		
		$this->amount 	= JRequest::getInt('amount', 0);
		$this->session 	= JRequest::getInt('ordercode', 0);
		$this->id		= JRequest::getInt('ticketid');
		$this->togo		= JRequest::getInt('togo');
		$this->eventid	= JRequest::getInt('eventid');
		$this->eventname= JRequest::getVar('parentname');

		## Getting the global DB session
		$session =& JFactory::getSession();
		## Gettig the orderid if there is one.
		$this->ordercode = $session->get('ordercode');
		
		## Check if the user is logged in.
		$user = & JFactory::getUser();
		$this->userid = $user->id;		
		
	}
	
	function updatecart(){
		
		$db  = JFactory::getDBO();
		$app =& JFactory::getApplication();		
		
		## include the format function.
		include_once( 'components/com_ticketmaster/assets/functions.php' );

		## Get the configuration values
		$sql = 'SELECT priceformat, valuta, show_waitinglist  FROM #__ticketmaster_config WHERE configid =1';
	 
		$db->setQuery($sql);
		$config = $db->loadObject();	
		
		## Get the total of the tickets ordered.
		$sql = 'SELECT COUNT(orderid) AS total FROM #__ticketmaster_orders WHERE ordercode = '.(int)$this->ordercode.' ';		
		$db->setQuery($sql);
		$ticket = $db->loadObject();
		
		## Get the total of the tickets ordered.
		$sql = 'SELECT COUNT(id) AS total FROM #__ticketmaster_waitinglist WHERE ordercode = '.(int)$this->ordercode.' ';		
		$db->setQuery($sql);
		$waiting = $db->loadObject();		

		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );	

		if ($ticket->total > 1) {
			$tickets = JText::_('COM_TICKETMASTER_TICKETS');
		}else{
			$tickets = JText::_('COM_TICKETMASTER_TICKET');
		}	
		
		## Total for this order:
		$total = _getAmount($this->ordercode);
		$fees = _getFees($this->ordercode);
		$ordertotal = $total-$fees;
		
		$update = '';
		$update .= $ticket->total.' '.$tickets.' - '.showprice($config->priceformat, $ordertotal, $config->valuta).' '.
				  '<br/>'.JText::_('COM_TICKETMASTER_IN_CART');
		
		## Only show this when waiting list is on.
		if($config->show_waitinglist == 1){ 
			
			if($waiting > 0){ 
			
				if ($waiting->total > 1) {
					$waitingtickets = JText::_('COM_TICKETMASTER_TICKETS');
				}else{
					$waitingtickets = JText::_('COM_TICKETMASTER_TICKET');
				}			
			
				$update .= '<br/><br/>'.$waiting->total.' '.$waitingtickets.' <br/>'.JText::_('COM_TICKETMASTER_IN_WAITNGLIST');		  
			
			}
			
		}
		
		echo '<div style="text-align:center;"><strong>'.$update.'</strong></div>';
	}
	
	## NEW FUNCTION TO BUY TICKETS ##
	
	function buyticket(){
		
		## Check if this is Joomla 2.5 or 3.0.+
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
		
		if($isJ30){
			$failed_class 	= 'alert alert-danger';	
			$success_class 	= 'alert alert-success';	
			$info_class 	= 'alert alert-info';	
		}else{
			$failed_class 	= 'failed';
			$success_class 	= 'success';	
			$info_class 	= 'info';		
		}
		
		## Check the session:
		if($this->session == 0) {
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_FAILED_ADD_TO_CART').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();								 
		
		## Check if the amount is higher than 0
		}elseif($this->amount == 0 ){
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_NO_AMOUNT').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();								 
		
		## Compare sent session and actual session:
		}elseif ( $this->ordercode != $this->session ){
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_FAILED_ADD_TO_CART').' (Error: #100)</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();						 
		
		}else{ 
			
			## Get the minimum amount and maximum amount:
			$db = JFactory::getDBO();
	
			$query = 'SELECT totaltickets, max_ordering AS maximum, min_ordering AS minimum
					  FROM #__ticketmaster_tickets 
					  WHERE ticketid = '.(int)$this->id.''; 			
	
			$db->setQuery($query);
			$tickets = $db->loadObject();	
			
			$query = 'SELECT COUNT(orderid) AS totalorder
					  FROM #__ticketmaster_orders 
					  WHERE ticketid = '.(int)$this->id.' 
					  AND ordercode = '.(int)$this->session.''; 			
	
			$db->setQuery($query);
			$basket = $db->loadObject();				
			
			## Start check of maximum/minimum order ##
 			$newTotal = $basket->totalorder + $this->amount;
			
			if($tickets->maximum != 0){
				
				## If the amount is higher is higher than allowed.
				if ($this->amount > $newTotal){

					$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_MAX_ORDER_PER_TICKET').$tickets->maximum.'</div>';
					
					## No session means no selling!
					$arr = array('status' => '666', 
								 'msg' => $msg);
								 
					echo json_encode($arr);	
					exit();								 
	
				}
				
				## Count the orders from the table and compare with the order.
				if ($basket->totalorder >= $tickets->maximum-1){

					$msg .= '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_MAX_ORDER_PER_TICKET').$tickets->maximum.'</div>';
					
					## No session means no selling!
					$arr = array('status' => '666', 
								 'msg' => $msg);
					
					echo json_encode($arr);				 
					exit();
	
				}
				
			}
			
			## If minimum is not set, go on.
			if($tickets->minimum != 0){
				## If the amount is below the minimum allowed ordering.
				if ( $tickets->minimum > $this->amount){

					$msg .= '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_MIN_ORDER_PER_TICKET').$tickets->minimum.'</div>';
					
					## No session means no selling!
					$arr = array('status' => '666', 
								 'msg' => $msg);
					
					echo json_encode($arr);				 
					exit();
		
				}
			}				
			
			## End check of maximum/minimum order ##
			
			## CHECK for Ticketmaster PRO! Needs to check if the seat is required: ##
			$sql = 'SELECT pro_installed, show_waitinglist FROM #__ticketmaster_config WHERE configid =1';
		 
			$db->setQuery($sql);
			$config = $db->loadObject();				
			
			## Counting the tickets, are there still enough?
			$query = 'SELECT totaltickets, max_ordering, min_ordering, parent AS parentticket, counter_choice 
					  FROM #__ticketmaster_tickets 
					  WHERE ticketid = '.(int)$this->id.''; 			
	
			$db->setQuery($query);
			$tickets = $db->loadObject();			
			
			if ($tickets->parentticket > 0){
				
				## Getting the dropdown for make search.
				$query = 'SELECT totaltickets 
					      FROM #__ticketmaster_tickets 
					      WHERE ticketid = '.(int)$tickets->parentticket.''; 
						  
				$db->setQuery($query);
				$t = $db->loadObject();
				
				if ( $tickets->counter_choice == 0) {
					## Get the total tickets from the parent.
					$totaltickets = $t->totaltickets;						  
				}else{
					## Get the total tickets from the parent.
					$totaltickets = $tickets->totaltickets;						
				}
				
			}else{
				
				
				## Getting the dropdown for make search.
				$query = 'SELECT totaltickets 
					      FROM #__ticketmaster_tickets 
					  	  WHERE ticketid = '.(int)$this->id.''; 
						  
				$db->setQuery($query);
				$t = $db->loadObject();
				
				## No parent available --> Use titckettotal.
				$totaltickets = $t->totaltickets;
				
			}	
			
			if ($this->amount > $totaltickets){
				
				if($config->show_waitinglist == 1){
				
					$msg = '<div class="'.$info_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_ADD_TO_WAITINGLIST').'</div>';
					
					## No session means no selling!
					$arr = array('status' => '666', 
								 'msg' => $msg);
					
					echo json_encode($arr);				 
					exit();
					
				}else{
					
					$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_SOLD_OUT').'</div>';
					
					## No session means no selling!
					$arr = array('status' => '666', 
								 'msg' => $msg);
					
					echo json_encode($arr);				 
					exit();					
					
				}
			
			}			
			
			## End of ticket counting ##			
			
			$post = JRequest::get('post');			
			
			if($config->pro_installed == 1){
				
				## Getting the dropdown for make search.
				$query = 'SELECT show_seatplans AS required 
						  FROM #__ticketmaster_tickets 
						  WHERE ticketid = '.(int)$this->id.''; 			
		
				$db->setQuery($query);
				$seat = $db->loadObject();
				
				if ($seat->required == 1) {
				
					## Getting the dropdown for make search.
					$query = 'SELECT * FROM #__ticketmaster_tickets_ext
							  WHERE ticketid = '.(int)$this->id.''; 			
			
					$db->setQuery($query);
					$type_seat = $db->loadObject();	
					
					if ($type_seat->type == 1){
						$post['requires_seat'] = '1';
					}else{
						$post['requires_seat'] = '0';
					}
					
				}else{
					
					$post['requires_seat'] = '0';
					
				}


			}else{
				
				$post['requires_seat'] = '0';
			
			}
			
			## End of CHECK for Ticketmaster PRO!##			
			
			## GETTING THE MODEL TO SAVE THE ORDER ##
			$model	= $this->getModel('order');
			
			## Creating a proper time stamp
			$now = time();
			## Output: $now = "1074176782";
			$orderdate = date('Y-m-d H:i:s', $now);
			$post['orderdate'] = $orderdate;
			$post['ipaddress'] = $_SERVER['REMOTE_ADDR'];	
			
			if ($this->userid) {
				$post['userid'] = $this->userid;
			}	

			$k = 0;
			for ($i = 0, $n = $this->amount; $i < $n; $i++ ){
			
				## Let's save all data now.
				$added = $model->store($post);
				
				if($added){
					## End of checks --> order is added:
					$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_FAILED_SAVING_CART').'</div>';
					
					## No session means no selling!
					$arr = array('status' => '200', 
								 'msg' => $added);		
					
					echo json_encode($arr);	
					exit();
				}
				
			$k=1 - $k;
			}
			
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_tickets'
				  . ' SET totaltickets = totaltickets-'.(int) $this->amount
				  . ' WHERE ticketid = '.(int) $tickets->parentticket.' ';

			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
			
				## End of checks --> order is added:
				$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_DB_QUERY_FAILED').' (Error: #101)</div>';
				
				## No session means no selling!
				$arr = array('status' => '200', 
							 'msg' => $added);		
				
				echo json_encode($arr);	
				exit();
				
			}	
			
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET totaltickets = totaltickets-'.(int) $this->amount
				. ' WHERE ticketid = '.(int) $this->id.' ';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
			
				## End of checks --> order is added:
				$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_DB_QUERY_FAILED').' (Error: #102)</div>';
				
				## No session means no selling!
				$arr = array('status' => '200', 
							 'msg' => $added);		
				
				echo json_encode($arr);	
				exit();
				
			}								
			
			
			## End of checks --> order is added:
			$msg = '<div class="'.$success_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_ADDED_TO_CART').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '200', 
						 'msg' => $msg);		
			
			echo json_encode($arr);	
			exit();			
		
		}
		
		$msg = '<div class="'.$failed_class.'">'.JText::_('COM_TICKETMASTER_UNEXPECTED_ERROR').'</div>';
		
		## No session means no selling!
		$arr = array('status' => '666', 
					 'msg' => $msg);		
		
		echo json_encode($arr);	
		exit();	
		
	}
	
	function waitinglist(){

		## Check if this is Joomla 2.5 or 3.0.+
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
		
		if($isJ30){
			$failed_class 	= 'alert alert-danger';	
			$success_class 	= 'alert alert-success';	
			$info_class 	= 'alert alert-info';	
		}else{
			$failed_class 	= 'failed';
			$success_class 	= 'success';	
			$info_class 	= 'info';		
		}
		
		## Check the session:
		if($this->session == 0) {
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_FAILED_ADD_TO_WAITINGLIST').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();								 
		
		## Check if the amount is higher than 0
		}elseif($this->amount == 0 ){
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_NO_AMOUNT').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();								 
		
		## Compare sent session and actual session:
		}elseif ( $this->ordercode != $this->session ){
			
			$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_EVENT_FAILED_ADD_TO_WAITINGLIST').' (Error: #105)</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();						 
		
		}else{
			
			$db   = JFactory::getDBO();
			$post = JRequest::get('post');	
			
			## CHECK for Ticketmaster PRO! Needs to check if the seat is required: ##
			$sql = 'SELECT pro_installed, show_waitinglist 
					FROM #__ticketmaster_config 
					WHERE configid =1';
		 
			$db->setQuery($sql);
			$config = $db->loadObject();						
			
			if($config->pro_installed == 1){
				
				## Getting the dropdown for make search.
				$query = 'SELECT show_seatplans AS required 
						  FROM #__ticketmaster_tickets 
						  WHERE ticketid = '.(int)$this->id.''; 			
		
				$db->setQuery($query);
				$seat = $db->loadObject();
				
				if ($seat->required == 1) {
				
					## Getting the dropdown for make search.
					$query = 'SELECT * FROM #__ticketmaster_tickets_ext
							  WHERE ticketid = '.(int)$this->id.''; 			
			
					$db->setQuery($query);
					$type_seat = $db->loadObject();	
					
					if ($type_seat->type == 1){
						$post['requires_seat'] = '1';
					}else{
						$post['requires_seat'] = '0';
					}
					
				}else{
					
					$post['requires_seat'] = '0';
					
				}


			}else{
				
				$post['requires_seat'] = '0';
			
			}
			
			## End of CHECK for Ticketmaster PRO!##			
			
			## GETTING THE MODEL TO SAVE THE ORDER ##
			$model	= $this->getModel('order');
			
			## Creating a proper time stamp
			$now = time();
			## Output: $now = "1074176782";
			$orderdate = date('Y-m-d H:i:s', $now);
			$post['orderdate'] = $orderdate;
			$post['ip_address'] = $_SERVER['REMOTE_ADDR'];	
			
			if ($this->userid) {
				$post['userid'] = $this->userid;
			}	

			$k = 0;
			for ($i = 0, $n = $this->amount; $i < $n; $i++ ){
			
				## Let's save all data now.
				$added = $model->storeWaitingList($post);
				
				if($added){
					## End of checks --> order is added:
					$msg = '<div class="'.$failed_class.'" style="font-size:97%;">'.JText::_('COM_TICKETMASTER_FAILED_SAVING_CART').'</div>';
					
					## No session means no selling!
					$arr = array('status' => '200', 
								 'msg' => $added);		
					
					echo json_encode($arr);	
					exit();
				}
				
			$k=1 - $k;
			}
			
			
			$message = str_replace('%%AMOUNT%%', $this->amount, JText::_('COM_TICKETMASTER_ADDED_TO_WAITINGLIST'));
			$msg = '<div class="'.$success_class.'" style="font-size:97%;"><strong>'.$message.'</strong><br/>
					'.JText::_('COM_TICKETMASTER_GO_TO_BASKET').'</div>';
			
			## No session means no selling!
			$arr = array('status' => '666', 
						 'msg' => $msg);
						 
			echo json_encode($arr);				 
			exit();				
		
		}
		
	}
	
	function addtocart(){
		
		#########################################
		#### CHECK THE AJAX POST CAREFULLY!! ####
		#########################################
		
		## Checking if the session has been filled
		if ( $this->session == 0 ) { $result = 2; }
		## Checking if the amount has been filled
		elseif ( $this->amount == 0 ) { $result = 4;
		## Check if ordercode is ok with send session.	
		}elseif ( $this->ordercode != $this->session ){ $result = 2;
		## All Checks are OK!	
		}else{ $result = 1; }

		##############################################################
		### OK, first checks have been done, now do the last one. ####
		##############################################################
		
		if ($result == 1) {

			$db = JFactory::getDBO();
	
			$query = 'SELECT totaltickets, max_ordering AS maximum, min_ordering AS minimum
					  FROM #__ticketmaster_tickets 
					  WHERE ticketid = '.(int)$this->id.''; 			
	
			$db->setQuery($query);
			$tickets = $db->loadObject();	
			
			$query = 'SELECT COUNT(orderid) AS totalorder
					  FROM #__ticketmaster_orders 
					  WHERE ticketid = '.(int)$this->id.' 
					  AND ordercode = '.(int)$this->session.''; 			
	
			$db->setQuery($query);
			$basket = $db->loadObject();				
			
			## If max is not set, go on!
			if($tickets->maximum != 0){
				## If the amount is higher is higher than allowed.
				if ($this->amount > $tickets->maximum){
					$result = 7;		
				}
				
				## Count the orders from the table and compare with the order.
				if ($basket->totalorder >= $tickets->maximum-1){
					$result = 7;
				}
			}
			
			## If minimum is not set, go on.
			if($tickets->minimum != 0){
				## If the amount is below the minimum allowed ordering.
				if ($this->amount < $tickets->minimum){
					$result = 8;		
				}
			}						
						
		}
		
		##########################################################
		### OK, if the tests failed return and show the error ####
		##########################################################
		if ($result == 2 || $result == 4 || $result == 7 || $result == 8) {
		
			echo $result;
		
		}else{
			
			$db = JFactory::getDBO();
			
			## Getting the dropdown for make search.
			$query = 'SELECT totaltickets, max_ordering, min_ordering, parent AS parentticket 
					  FROM #__ticketmaster_tickets 
					  WHERE ticketid = '.(int)$this->id.''; 			
	
			$db->setQuery($query);
			$tickets = $db->loadObject();			
			
			if ($tickets->parentticket > 0){
				
				## Getting the dropdown for make search.
				$query = 'SELECT totaltickets 
						  FROM #__ticketmaster_tickets 
						  WHERE ticketid = '.(int)$tickets->parentticket.''; 
						  
				$db->setQuery($query);
				$t = $db->loadObject();
				
				## Get the total tickets from the parent.
				$totaltickets = $t->totaltickets;						  
			
			}else{
				
				## No parent available --> Use titckettotal.
				$totaltickets = $tickets->totaltickets;
				
			}	
			
			if ($this->amount > $totaltickets){
				
				$result = 3;
				echo $result;
			
			}else{

				$post = JRequest::get('post');
				
				## Making the query for showing all the cars in list function
				$sql = 'SELECT pro_installed FROM #__ticketmaster_config WHERE configid =1';
			 
				$db->setQuery($sql);
				$config = $db->loadObject();				
				
				if($config->pro_installed == 1){
					
					## Getting the dropdown for make search.
					$query = 'SELECT show_seatplans AS required 
							  FROM #__ticketmaster_tickets 
							  WHERE ticketid = '.(int)$this->id.''; 			
			
					$db->setQuery($query);
					$seat = $db->loadObject();
					
					if ($seat->required == 1) {
					
						## Getting the dropdown for make search.
						$query = 'SELECT * FROM #__ticketmaster_tickets_ext
								  WHERE ticketid = '.(int)$this->id.''; 			
				
						$db->setQuery($query);
						$type_seat = $db->loadObject();	
						
						if ($type_seat->type == 1){
							$post['requires_seat'] = '1';
						}else{
							$post['requires_seat'] = '0';
						}
						
					}else{
						
						$post['requires_seat'] = '0';
						
					}


				}else{
					
					$post['requires_seat'] = '0';
				
				}
				
				## GETTING THE MODEL TO SAVE
				$model	=& $this->getModel('order');
				
				## Creating a proper time stamp
				$now = time();
				## Output: $now = "1074176782";
				$orderdate = date('Y-m-d h:m:s', $now);
				$post['orderdate'] = $orderdate;
				$post['ipaddress'] = $_SERVER['REMOTE_ADDR'];	
				
				if ($this->userid) {
					$post['userid'] = $this->userid;
				}	

				$k = 0;
				for ($i = 0, $n = $this->amount; $i < $n; $i++ ){
				
					## Let's save all data now.
					if ($model->store($post)) {
						$msg = JText::_( 'ADDED TO CART' );
					} else {
						$msg = JText::_( 'ERROR DURING CART ADDING' );
					}			
					
				$k=1 - $k;
				}

				## Update the tickets-totals that where removed.
				$query = 'UPDATE #__ticketmaster_tickets'
					  . ' SET totaltickets = totaltickets-'.(int) $this->amount
					  . ' WHERE ticketid = '.(int) $tickets->parentticket.' ';

				## Do the query now	
				$db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$db->query()) {
				
					$this->setError($db->getErrorMsg());
					$result = 2;
					
				}else{
				
					$result = 1;
				}
				
				if ($result == 1){
				
					## Update the tickets-totals that where removed.
					$query = 'UPDATE #__ticketmaster_tickets'
						. ' SET totaltickets = totaltickets-'.(int) $this->amount
						. ' WHERE ticketid = '.(int) $this->id.' ';
					
					## Do the query now	
					$db->setQuery( $query );
					
					## When query goes wrong.. Show message with error.
					if (!$db->query()) {
					
						$this->setError($db->getErrorMsg());
						$result = 2;
						
					}else{
					
						$result = 1;
					}
				
				}
				
				echo $result;		
			
			}	
					
		} 
	
	}

	function remove() {
	
		$orderid = JRequest::getInt( 'orderid', 0 );
		
		## Getting the database.
		$db = JFactory::getDBO();
		$session =& JFactory::getSession();
		$ordercode = $session->get('ordercode');
		
		## Making the tickets inactive, they still be present at the database.
		$update = 'SELECT o.* , t.parent AS parentticket
				  FROM #__ticketmaster_orders  AS o, #__ticketmaster_tickets AS t
				  WHERE orderid = '.(int)$orderid.'
				  AND o.ticketid = t.ticketid
				  AND ordercode = '.$ordercode.'';
		
		$db->setQuery($update);
		$tdata = $db->loadObject();	
		
		## Update the tickets-totals that where removed.
		$query = 'UPDATE #__ticketmaster_tickets'
			. ' SET totaltickets = totaltickets+1'
			. ' WHERE ticketid = '.(int)$tdata->ticketid.' ';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		if ($tdata->parentticket != 0){
			## Update the tickets-totals that where removed.
			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET totaltickets = totaltickets+1'
				. ' WHERE ticketid = '.$tdata->parentticket.' ';

			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}							
		}
		
		if ($tdata->seat_sector != 0){
		
			$query = 'UPDATE #__ticketmaster_coords 
					  SET booked = 0, orderid = 0 
					  WHERE orderid = '.$tdata->orderid.' ';

			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError = $db->getErrorMsg();
				return false;
			}						
			
							
		}									
		
		## Now delete the selected tickets.
		$query = 'DELETE FROM #__ticketmaster_orders WHERE orderid = "'.(int)$orderid.'" 
				  AND ordercode = "'.(int)$ordercode.'" ';
		
		## Do the query now	and delete all selected invoices.
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}		
		
		## Making the query for showing all the cars in list function
		$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		$db->setQuery($sql);
		$data = $db->loadObject();
		
		## Including some functionality.
		include_once( 'components/com_ticketmaster/assets/functions.php' );

		## Including required paths to calculator.
		$path_include = JPATH_SITE.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'helpers'.DS.'get.amount.php';
		include_once( $path_include );	
		
		## Total for this order:
		$total = _getAmount($session->get('ordercode'));
		$fees = _getFees($session->get('ordercode'));
		$order = $total-$fees;
		
		$result = showprice($data->priceformat ,$order, $data->valuta);		
		
		echo $result;		
		
	}
	
	function removeWaiting() {
	
		$orderid = JRequest::getInt( 'orderid', 0 );
		
		## Getting the database.
		$db = JFactory::getDBO();
		$session =& JFactory::getSession();
		$ordercode = $session->get('ordercode');								
		
		## Now delete the selected tickets.
		$query = 'DELETE FROM #__ticketmaster_waitinglist WHERE id = "'.(int)$orderid.'" 
				  AND ordercode = "'.(int)$ordercode.'" ';
		
		## Do the query now	and delete all selected invoices.
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}		
				
		exit();		
		
	}
	


}	
?>