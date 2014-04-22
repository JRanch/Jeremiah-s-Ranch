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

class ticketmasterModeltickets extends JmodelLegacy
{
	
	protected $_data = array();
	
	function __construct(){
		parent::__construct();

		$mainframe = JFactory::getApplication();
		
		$config = JFactory::getConfig();
		
		// Get the pagination request variables
		$limit        = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		//$limitstart    = $mainframe->getUserStateFromRequest( 'limitstart', 'limitstart', 0, 'int' );
		$limitstart    = JRequest::getInt('limitstart', 0);
		
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
		
			## Making the query for showing all the clients in list function
			$query = 'SELECT a.*, b.eventname, b.groupname
					  FROM #__ticketmaster_tickets AS a, #__ticketmaster_events AS b, #__ticketmaster_venues AS v'
					.$where; 
            $this->_total = $this->_getListCount($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

	function _buildContentWhere() {
	
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		
		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_t','filter_ordering_t','a.eventid','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );

		$where = array();

		$where[] = 'a.eventid = b.eventid';
		$where[] = 'a.venue = v.id';
		$where[] = 'a.parent = 0';
		
		
		if($filter_order == 0) {
			$where[] = 'a.eventid > 0';
		}else{
			$where[] = 'a.eventid = '.$filter_order;
		}
		
		
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where = $this->_buildContentWhere();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT a.*, b.eventname, b.groupname, v.venue, v.city, v.id AS venueid
					FROM #__ticketmaster_tickets AS a, #__ticketmaster_events AS b, #__ticketmaster_venues AS v'
					.$where.' ORDER BY a.ordering ASC'; 
		 	
		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}
	
   function getChilds() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where = $this->_buildChildsWhere();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT a.*, b.eventname, b.groupname
					FROM #__ticketmaster_tickets AS a, #__ticketmaster_events AS b'
					.$where.' ORDER BY a.ordering ASC'; 
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}	

	function _buildChildsWhere() {
	
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		
		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_t','filter_ordering_t','a.eventid','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );

		$where = array();

		$where[] = 'a.eventid = b.eventid';
		$where[] = 'a.parent != 0';
		
		if($filter_order == 0) {
			$where[] = 'a.eventid > 0';
		}else{
			$where[] = 'a.eventid = '.$filter_order;
		}
		
		
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getConfig() {
   		
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		## Getting the information for just one car. 
		## The ID is prvided by the URL
		$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1 ';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObject();
      }
      return $this->data;
   }   
	
   function getData() {
   	
	 
      if (empty($this->_data))
      {
		 
         $db = JFactory::getDBO();

		## Getting the information for just one ticket. 
		## The ID is prvided by the URL
		if($this->id > 0) {
		$sql = 'SELECT * FROM #__ticketmaster_tickets
				WHERE ticketid = '. (int) $this->id.'  ';
		}
		else
		{
			$sql = 'SELECT t.* FROM (SELECT 1 AS adummy) a LEFT JOIN (     SELECT * FROM #__ticketmaster_tickets WHERE ticketid=0 ) t ON 1=1';
		}
         $db->setQuery($sql);
         $this->data = $db->loadObject();
		
      }
	
      return $this->data;
   }   

	function update($event = 0) {
		
            ## Let's updte the tiket totals in the table events
            ## It will be done automatically.

            $db = JFactory::getDBO();

            $sql = 'SELECT SUM(totaltickets) AS totals 
                            FROM #__ticketmaster_tickets
                            WHERE eventid = '.(int)$event.'';

            $db->setQuery($sql);
            $data = $db->loadObject();

            $query = 'UPDATE #__ticketmaster_events'
                    . ' SET ticketcounter = '.(int)$data->totals
                    . ' WHERE eventid = '.(int)$event.'';

            ## Do the query now	
            $this->_db->setQuery( $query );

            ## When query goes wrong.. Show message with error.
            if (!$this->_db->query()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
            }
            return true;
	}

	function publish($cid = array(), $publish = 1) {
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET published = '.(int) $publish
				. ' WHERE ticketid IN ( '.$cids.' )';
			
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

	function store($data) {
	
		$mainframe = JFactory::getApplication();
		
		$row = $this->getTable();

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
		
		## Get the ID for the inserted ticket.
		$id = JRequest::getInt( 'ticketid', 0 ); 
				 
		if ($id != 0) {  
			$id = JRequest::getInt( 'ticketid', 0 );  
			$this->ticketid = $id;
		} else {   
			$id = $this->_db->insertid();
			$this->ticketid = $id;
		} 	
		
		$pdf = JRequest::getVar( 'pdffile', '', 'files', 'array' );
		
		if ($pdf['name']){
		
			jimport('joomla.filesystem.file');
			
			$pdf['name'] = JFile::makeSafe($pdf['name']);
			## The link to the previous saved data.
			$link = 'index.php?option=com_ticketmaster&controller=tickets&task=edit&cid[]='.$id;
	
			## Check if the image is in the of the supported extentions
			if ($pdf['type'] != 'application/pdf'){
				JError::raiseWarning(100, ''.$pdf['name'].' '.JText::_( 'COM_TICKETMASTER_ONLY_PDF_ALLOWED' ).'');
				$mainframe->redirect($link, $pdf['type']);
			}
	
			$ticket = 'eTicket-'.$id;

			$path 	= JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS;
						
			chmod ( $pdf['tmp_name'], 0755);
			## Moving the file now to the destination folder (images), offcourse with the new name.
			if (!JFile::upload($pdf['tmp_name'], $path.$ticket.'.pdf')) {
				JError::raiseWarning(100, ''.$file['name'].' '.JText::_( 'COM_TICKETMASTER_COULD_NOT_MOVE_FILE').'');
				$mainframe->redirect($link);
			}
			
			##Define the path to the image and check if it's there.
			$path 	= JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS;
			$file   = 'eTicket-'.$id.'.jpg';
				
			if (file_exists($path.$file)) {
				## Deleteing the files (image and thumbnail)
				JFile::delete( $path.$file );
			}			
			
		}
		
		$image = JRequest::getVar( 'jpgfile', '', 'files', 'array' );
		
		if ($image['name']){

		
			jimport('joomla.filesystem.file');
			
			$image['name'] = JFile::makeSafe($image['name']);
			## The link to the previous saved data.
			$link = 'index.php?option=com_ticktmaster&controller=tickets&task=edit&cid[]='.$id;		
			
			## Allowed uploads
			$allowed = array('image/pjpeg','image/jpeg','image/JPG','image/jpg');

			## Check if the image is in the of the supported extentions
			if (!in_array($image['type'], $allowed)){
				JError::raiseWarning(100, ''.$image['name'].' '.JText::_( 'COM_TICKETMASTER_ONLY_JPG_ALLOWED' ).'');
				$mainframe->redirect($link);
			}			
	
			$ticket = 'eTicket-'.$id;

			$path 	= JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'etickets'.DS;
						
			chmod ( $image['tmp_name'], 0755);
			## Moving the file now to the destination folder (images), offcourse with the new name.
			if (!JFile::upload($image['tmp_name'], $path.$ticket.'.jpg')) {
				JError::raiseWarning(100, ''.$image['name'].' '.JText::_( 'COM_TICKETMASTER_COULD_NOT_MOVE_FILE').'');
				$mainframe->redirect($link);
			}
			
			##Define the path to the image and check if it's there.
			$path 	= JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS;
			$file   = 'eTicket-'.$id.'.pdf';
			
			if (file_exists($path.$file)) {	
				## Deleteing the files (image and thumbnail)
				JFile::delete( $path.$file );	
			}		
			
		}		
				
	return true;
	}
	
	function getTicketID(){
		
		return $this->ticketid;
		
	}

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
			
			global $mainframe, $option;
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			$db = JFactory::getDBO();
			
			## Get all tickets affected with this party/event.
			$sql = 'SELECT orderid FROM #__ticketmaster_orders WHERE ticketid IN ( '.$cids.' )';
			
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
				
				$row  = &$data[$i];
							
				$this->_deleteTicket($row->orderid);
	
			}

			## Delete all tickets from DB
			$query = 'DELETE FROM #__ticketmaster_tickets WHERE ticketid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			## Delete all tickets from DB
			$query = 'DELETE FROM #__ticketmaster_orders WHERE ticketid IN ( '.$cids.' )';
			
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

	function _deleteTicket($tid){
		
		$mainframe =& JFactory::getApplication();

		## Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		## Import the file system
		jimport('joomla.filesystem.file');

		##Define the path to the image and check if it's there.
		$path 	= JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_ticketmaster'.DS.'tickets'.DS;
		$file   = 'eTicket-'.$tid.'.pdf';
			
		## Deleteing the files (image and thumbnail)
		JFile::delete( $path.$file );

	}
	
	function cleanup(){
		
		## path to remover
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'ticketcleaner.class.php';

		## Include path.
		if (file_exists($path)) {
			require_once($path);
		}else{
			return false;
		}
		
		$cleaner = new remover();  
		$cleaner->cleanup();
		
		$msg = $cleaner->error();
		$count = $cleaner->counter();
		
		if ($msg == ''){
			return $count;	
		}else{
			return false;	
		}
	
	}

	function notactivated(){

        $db = JFactory::getDBO();

		## Getting the information for just one car. 
		## The ID is prvided by the URL
		$sql = 'SELECT removal_days FROM #__ticketmaster_config WHERE configid = 1';
		 
        $db->setQuery($sql);
        $config = $db->loadObject();

		## Create a new date NOW()-2hours. (database session is not longer than 2 hours in global config.
		$cleanup = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d')-$config->removal_days, date('Y')));

		$update = 'SELECT o.* , t.parent AS parentticket
				   FROM #__ticketmaster_orders  AS o, #__ticketmaster_tickets AS t
				   WHERE o.orderdate < "'.$cleanup.'"  
				   AND o.published = 0
				   AND o.ticketid = t.ticketid';
		
		$db->setQuery($update);
		$this->data = $db->loadObjectList();	
		
		## Make a backup before everything goes wrong. // Data is saved in a csv file.
		$backup = 'SELECT * FROM #__ticketmaster_orders WHERE orderdate < "'.$cleanup.'" AND published = 0';
		
		$this->_db->setQuery($backup);	
		$rows = $db->loadAssocList();
			   
		$test = count($rows);
		$count = count($this->data);
		
		if ($count < 1){

			$mainframe =& JFactory::getApplication();		
			$msg = JText::_( 'COM_TICKETMASTER_DB_NO_ACTIVATION_UNNEEDED' );
			$link = 'index.php?option=com_ticketmaster&controller=tickets';
			$mainframe->redirect($link,$msg);				
		}			
		
		## Empty data vars
		$data = "" ;
		## We need tabbed data
		$sep = "\t"; 
		
		$fields = (array_keys($rows[0]));
		
		## Count all fields(will be the collumns
		$columns = count($fields);
		## Put the name of all fields to $out.  
		for ($i = 0; $i < $columns; $i++) {
		  $data .= $fields[$i].$sep;
		}
		
		$data .= "\n";
		
		## Counting rows and push them into a for loop
		for($k=0; $k < count( $rows ); $k++) {
			$row = $rows[$k];
			$line = '';
			
			## Now replace several things for MS Excel
			foreach ($row as $value) {
			  $value = str_replace('"', '""', $value);
			  $line .= '"' . $value . '"' . "\t";
			}
			$data .= trim($line)."\n";
		}
		
		$data = str_replace("\r","",$data);	
		
		$today = date("YmdHms");
		$filename = 'export-sync-file'.$today;
			
		## Opening file in write modus
		$handle = fopen('../administrator/components/com_ticketmaster/assets/export.xls', 'w');
		## Write $somecontent to our opened file.
		## Note: all information is be overwritten everytime.
		if (fwrite($handle, $data) === FALSE) {
			JError::raiseWarning(100, $error.' '. JText::_( 'COULD NOT OPEN EXPORTFILE' ));
			$mainframe->redirect('index.php?option=com_ticketmaster&controller=export');
		}

		## Now move the file away for security reasons
		## Import the Joomla! Filesystem.
		jimport('joomla.filesystem.file');
		
		## Copy the file to a new directory.
		$src  = '../administrator/components/com_ticketmaster/assets/export.xls';		
		
		## The new name for the exportfile
		## Creating a date for proper saving
		
		$dest = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'assets'.DS.'exports'.DS.$filename.'.xls';
		
		## Copy the file now.
		JFile::copy($src, $dest);
		
	
				

		## Now go on with the delete functions. All expired orders have been saved.
		$query = 'DELETE FROM #__ticketmaster_orders WHERE orderdate < "'.$cleanup.'"  AND published = 0';
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}	

		## Tickets have been removed successfull
		## Now we need to update the totals from the Object earlier this script.
		$k = 0;
		for ($i = 0, $n = count($this->data); $i < $n; $i++ ){
		
			$row = $this->data[$i];
			
			$query = 'UPDATE #__ticketmaster_tickets'
				. ' SET totaltickets = totaltickets+1'
				. ' WHERE ticketid = '.$row->ticketid.' ';
			
			## Do the query now	
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
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
				$this->_db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
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
		
		return $count;
	}

	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable('tickets');
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->ticketid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		return true;
	}

	function move($id, $direction)
	{
		$row =& $this->getTable('tickets');
		if (!$row->load($id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	
}
?>