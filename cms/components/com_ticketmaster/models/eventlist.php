<?php
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class ticketmasterModelEventlist extends JmodelLegacy{

	function __construct(){
		parent::__construct();

		$mainframe = JFactory::getApplication();
		
		$config = JFactory::getConfig();
		
		// Get the pagination request variables
		$limit        = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart    = JRequest::getInt('limitstart', 0);
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 
		
		$this->eventid    = JRequest::getInt('id', 0);			
	}

   function getConfig() {
   
		if (empty($this->_data)) {
		
		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the cars in list function
			$sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1 ';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
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
			$sql = 'SELECT a.*, b.eventname, b.groupname, v.id AS venueid, v.venue AS venuename
					FROM #__ticketmaster_tickets AS a, #__ticketmaster_events AS b, #__ticketmaster_venues AS v'
					.$where; 
            $this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

	function _buildContentWhere() {
	
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		
		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_t','filter_ordering_t','a.ticketdate','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );

		$where = array();

		$where[] = 'a.eventid = b.eventid';
		$where[] = 'a.venue = v.id';
		
		$where[] = 'a.eventid = '.$this->eventid;
		$where[] = 'a.published = 1';	
		$where[] = 'a.parent = 0';		
		
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildContentOrderBy() {
	
		$mainframe =& JFactory::getApplication();
 
		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_ordering_t','filter_ordering_t','a.ticketdate','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
		$orderby = ' ORDER BY a.ordering ASC';
 
		return $orderby;
	}

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where		= $this->_buildContentWhere();
			$order		= $this->_buildContentOrderBy();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT a.*, b.eventname, b.groupname, v.venue AS venuename, v.id AS venueid
					FROM #__ticketmaster_tickets AS a, #__ticketmaster_events AS b, #__ticketmaster_venues AS v'
					.$where
					.$order; 					
		 
		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}

   function getData() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
			
			$where		= $this->_buildContentWhere();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT *
					FROM #__ticketmaster_events
					WHERE eventid = '.$this->eventid.'';
		 
		 	$db->setQuery($sql);
		 	$this->data = $db->loadObject();
		}
		return $this->data;
	}
	
}
?>