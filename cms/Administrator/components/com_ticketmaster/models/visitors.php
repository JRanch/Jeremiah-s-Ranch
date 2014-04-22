<?php
/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class ticketmasterModelVisitors extends JmodelLegacy
{
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
			$orderby	= $this->_buildContentOrderBy();
		
			## Making the query for showing all the clients in list function
			$sql='SELECT * FROM #__ticketmaster_clients AS a, #__users AS u, #__ticketmaster_country AS c'
					.$where
					.' ORDER BY a.clientid ASC'; 
            $this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

	function _buildContentOrderBy() {
	
			$mainframe =& JFactory::getApplication();
	 
			$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering', 'filter_ordering', 'a.clientid', 'cmd' );
			$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
	 
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir.'';
	 
			return $orderby;
	}

	function _buildContentWhere() {
	
		$mainframe 	=& JFactory::getApplication();
		$db			=& JFactory::getDBO();
		
		$filter_order		= $mainframe->getUserStateFromRequest( 'filter_ordering', 'filter_ordering', 'a.name', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '', 'string' );
		$search				= JString::strtolower( $search );

		$where = array();

		$where[] = 'a.userid = u.id';
		$where[] = 'a.country_id = c.country_id';
		
		if ($search) {
			$where[] = $filter_order.' LIKE "%' .  $search. '%"' ;
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where		= $this->_buildContentWhere();
			$orderby	= $this->_buildContentOrderBy();
		
			## Making the query for showing all the clients in list function
			$sql='SELECT * FROM #__ticketmaster_clients AS a, #__users AS u, #__ticketmaster_country AS c'
					.$where
					.' ORDER BY a.clientid ASC'; 
						
		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}

   function getData() {
   		
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		## Getting the information for just one car. 
		## The ID is prvided by the URL
		$sql = 'SELECT * FROM #__ticketmaster_clients
				WHERE clientid = '. (int) $this->id.'  ';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObject();
      }
      return $this->data;
   }   

	function publish($cid = array(), $publish = 1) {
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__ticketmaster_clients'
				. ' SET published = '.(int) $publish
				. ' WHERE clientid IN ( '.$cids.' )';
			
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
	
		global $mainframe;
		
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

			## Getting the information for the deleted customers. 
			$sql = 'SELECT userid FROM #__ticketmaster_clients
					WHERE clientid IN ( '.$cids.' ) ';
			 
			 $this->_db->setQuery($sql);
			 $data = $this->_db->loadObjectList();
			
			## Making the domains inactive, they still be present at the database.
			$query = 'DELETE FROM #__ticketmaster_clients WHERE clientid IN ( '.$cids.' )';
			
			## Do the query now	and delete all selected invoices.
			$this->_db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

	   		for ($i = 0, $n = count($data); $i < $n; $i++ ){
				
				$row  = &$data[$i];
				## block = 1 for removed users.
				## Login is disabled for them.
				$publish = 1;
				
				$query = 'UPDATE #__users'
					. ' SET block = 1'
					. ' WHERE id = '.$row->userid.'';
				
				## Do the query now	
				$this->_db->setQuery( $query );

				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}	
				
				$query = 'DELETE FROM #__ticketmaster_orders WHERE userid = '.$row->userid.'';
				
				## Do the query now	and delete all selected invoices.
				$this->_db->setQuery( $query );
				
				## When query goes wrong.. Show message with error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}				
				
			}
		
		return true;
		
		}
	}


   function getOrderList() {

		$db = JFactory::getDBO();
		
		## Getting the information for the deleted customers. 
		$sql = 'SELECT userid FROM #__ticketmaster_clients WHERE clientid = '.(int)$this->id.'';
		 
		 $db->setQuery($sql);
		 $uid = $db->loadObject();		
	
		## Making the query for showing all the clients in list function
		$sql='SELECT a.*, t.ticketname, e.eventname, c.address, c.name, c.city, SUM(t.ticketprice) AS orderprice, COUNT(a.orderid) AS totaltickets 
			  FROM #__ticketmaster_orders AS a, #__ticketmaster_clients AS c, #__ticketmaster_events AS e, #__ticketmaster_tickets AS t 
			  WHERE a.userid = c.userid 
			  AND a.eventid = e.eventid 
			  AND a.ticketid = t.ticketid 
			  AND a.userid  = '. (int) $uid->userid.'
			  GROUP BY a.ordercode 
			  ORDER BY orderid ASC'; 
		
		$db->setQuery($sql);
		$this->data = $db->loadObjectList();

		return $this->data;
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

}
?>

