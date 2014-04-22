<?php
/****************************************************************
 * @version				2.5.5 ticketmaster 						
 * @package				ticketmaster								
 * @copyright           Copyright © 2009 - All rights reserved.			
 * @license				GNU/GPL										
 * @author				Robert Dam									
 * @author mail         info@rd-media.org							
 * @website				http://www.rd-media.org						
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelTransactions extends JModelLegacy {

   ## Empty data variabele
   var $_data  = null;
   var $_id = null;
   var $order = null;
   var $filter_order = null;

   function __construct()
   {
      	parent::__construct();
	  
		$mainframe 	= JFactory::getApplication();
		$config 	= JFactory::getConfig();
	
		## Get the pagination request variables
		$limit      = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( 'products.limitstart', 'limitstart', 0, 'int' );
		
		## In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
				  

		  $array    = JRequest::getVar('cid', array(0), '', 'array');
		  $this->id = (int)$array[0]; 
		  $this->userid = JRequest::getInt('dealerid', 0);
		  $this->task = JRequest::getCmd( 'task' );
	  
   }

	function _buildContentOrderBy() {
	
			$mainframe =& JFactory::getApplication();
	 
			$filter_order     = $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'a.date', 'cmd' );
			$filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
	 
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir.'';
	 
			return $orderby;
	}

	function _buildContentWhere() {
	
		$mainframe =& JFactory::getApplication();
		
		$db					=& JFactory::getDBO();
		
		$search				= $mainframe->getUserStateFromRequest( 'search', 'search', '', 'string' );
		$search				= JString::strtolower( $search );

		$where = array();
		
		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}
  
   function getPagination() {
   
        if (empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }

        return $this->_pagination;
    }
    
    function getTotal(){

	 $orderby	= $this->_buildContentOrderBy();
	 $where		= $this->_buildContentWhere(); 
	
        if (empty($this->_total))
        {
		$query = 'SELECT * FROM #__ticketmaster_transactions';
				
            $this->_total = $this->_getListCount($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

   function getList()
   {
   
	 $orderby	= $this->_buildContentOrderBy();
	 $where		= $this->_buildContentWhere(); 
	   
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		## Making the query for showing all the cars in list function
		$sql = 'SELECT * FROM #__ticketmaster_transactions';
				 
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
		$sql = 'SELECT a.*, d.name FROM #__ticketmaster_transactions AS a, #__ticketmaster_clients AS d
				WHERE a.pid = '. (int) $this->id.'
				AND a.userid = d.userid';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObject();
      }
      return $this->data;
   }   

   function getConfig() {
   		
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		 $sql = 'SELECT valuta, currencytype FROM #__ticketmaster_config  WHERE configid = 1';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObject();
      }
      return $this->data;
   }  

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );

			## Making the domains inactive, they still be present at the database.

			$query = 'DELETE FROM #__ticketmaster_transactions WHERE pid IN ( '.$cids.' )';
			
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

}
?>