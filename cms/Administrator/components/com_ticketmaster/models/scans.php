<?php
/****************************************************************
 * @version				Ticketmaster 3.1.0							
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


class TicketmasterModelScans extends JModelLegacy {

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
	
		$mainframe = JFactory::getApplication();
		$db         = JFactory::getDBO();
		
		$filter_scan_result = $mainframe->getUserStateFromRequest( 'filter_scan_result', 'filter_scan_result','0','cmd' );
		$search	= $mainframe->getUserStateFromRequest( 'searchbox', 'searchbox', '', 'string' );
		$search	= JString::strtolower( $search );

		$where = array();
		
		if($filter_scan_result != 0){
			$where[] = 'scanresult = '. (int) $filter_scan_result;
		}
		
		if ($search) {
			$where[] = 'barcode = '. (int) $search;
		}		
		
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
		$query = 'SELECT * FROM #__ticketmaster_scans';
				
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
		$sql = 'SELECT * FROM #__ticketmaster_scans '.$where;
				 
         $db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
         $this->data = $db->loadObjectList();
				 
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

			$query = 'DELETE FROM #__ticketmaster_scans WHERE scanid IN ( '.$cids.' )';
			
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