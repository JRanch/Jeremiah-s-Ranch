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

class ticketmasterModelEvents extends JmodelLegacy
{
	function __construct(){
            parent::__construct();

            $config = JFactory::getConfig();

            ## Connect the previous $mainframe.
            $mainframe =& JFactory::getApplication();
            ## Get the pagination request variables
            $limit        = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
            $limitstart    = $mainframe->getUserStateFromRequest( 'products.limitstart', 'limitstart', 0, 'int' );


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
	
			$sql = 'SELECT * FROM #__ticketmaster_events';
					
            $this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }         
        
   function getList() {

        if (empty($this->data)) {

                $db = JFactory::getDBO();

                ## Making the query for showing all the cars in list function
                $sql = 'SELECT * FROM #__ticketmaster_events';

                $db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
                $this->data = $db->loadObjectList();
        }
        return $this->data;
    }
	
   function getSold() {
   
        if (empty($this->_data)) {

			$db = JFactory::getDBO();

			## Making the query for showing all the cars in list function
			$sql = 'SELECT eventid, COUNT(orderid) AS soldtickets
							FROM #__ticketmaster_orders
							GROUP BY eventid';

			$db->setQuery($sql);
			$this->data = $db->loadObjectList();
        }
        return $this->data;
    }
	
   function getAdded() {
   
        if (empty($this->_data)) {

			$db = JFactory::getDBO();

			## Making the query for showing all the cars in list function
			$sql = 'SELECT eventid, SUM(totaltickets) AS totals
							FROM #__ticketmaster_tickets
							GROUP BY eventid';

			$db->setQuery($sql);
			$this->data = $db->loadObjectList();
        }
        return $this->data;
    }	
	
   function getAvailables() {
   
        if (empty($this->_data)) {

                $db = JFactory::getDBO();

                ## Making the query for showing all the cars in list function
                $sql = 'SELECT eventid, SUM(totaltickets) AS totals
                                FROM #__ticketmaster_tickets
                                GROUP BY eventid';

                $db->setQuery($sql);
                $this->data = $db->loadObjectList();
        }
        return $this->data;
    }		

   function getData() {
   		
      if (empty($this->data))
      {
         $db = JFactory::getDBO();
		
		if ($this->id != '') {
		 ## Getting the information for just one car. 
		 ## The ID is prvided by the URL
		 $sql = 'SELECT * FROM #__ticketmaster_events WHERE eventid = '. (int) $this->id.'  ';
		 
         $db->setQuery($sql);
         $data = $db->loadObject();
		 
		}else{
			
			$data->groupname = '';
			$data->eventdate = '';	
			$data->closingdate = '';
			$data->eventdescription = '';
			$data->eventid = '';
			$data->totaltickets = '';
			$data->published = 0;
			
		}
		 
      }
      return $data;
   }   

    function publish($cid = array(), $publish = 1) {

        ## Count the cids
        if (count( $cid )) {

                ## Make cids safe, against SQL injections
                JArrayHelper::toInteger($cid);
                ## Implode cids for more actions (when more selected)
                $cids = implode( ',', $cid );

                $query = 'UPDATE #__ticketmaster_events'
                        . ' SET published = '.(int) $publish
                        . ' WHERE eventid IN ( '.$cids.' )';

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

	function remove($cid = array()){
			
            $db = JFactory::getDBO();

            ## Make cids safe, against SQL injections
            JArrayHelper::toInteger($cid);

            ## Implode cids for more actions (when more selected)
            $cids = implode( ',', $cid );

            ## Get all tickets affected with this party/event.
            $sql = 'SELECT orderid FROM #__ticketmaster_orders WHERE eventid IN ( '.$cids.' )';

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

            ## Delete all tickets in the event list.
            $query = 'DELETE FROM #__ticketmaster_events WHERE eventid IN ( '.$cids.' )';

            ## Do the query now	and delete all selected invoices.
            $db->setQuery( $query );

            ## When query goes wrong.. Show message with error.
            if (!$db->query()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
            }

            ## Deleting all tickets in ticket table..
            $query = 'DELETE FROM #__ticketmaster_tickets WHERE eventid IN ( '.$cids.' )';

            ## Do the query now	and delete all selected invoices.
            $db->setQuery( $query );

            ## When query goes wrong.. Show message with error.
            if (!$db->query()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
            }

            ## Deleting all tickets in ordertable table..
            $query = 'DELETE FROM #__ticketmaster_orders WHERE eventid IN ( '.$cids.' )';

            ## Do the query now	and delete all selected invoices.
            $db->setQuery( $query );

            ## When query goes wrong.. Show message with error.
            if (!$db->query()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
            }

            return true;

	}

    function _deleteTicket($tid){

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

}
?>