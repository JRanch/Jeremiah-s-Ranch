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

class TicketmasterModelCoupons extends JmodelLegacy{
	
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
		
		$sql = 'SELECT * FROM #__ticketmaster_coupons'; 
							
		$this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));

        return $this->_total;

	}

   function getList() {

		$db = JFactory::getDBO();
	
		$sql = 'SELECT * FROM #__ticketmaster_coupons'; 

		$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		$this->data = $db->loadObjectList();

		return $this->data;
	}
	
   function getData() {

		$db = JFactory::getDBO();
	
		$sql = 'SELECT * FROM #__ticketmaster_coupons WHERE coupon_id = '.(int)$this->id.'';

		$db->setQuery($sql);
		$this->data = $db->loadObject();

		return $this->data;
	}	
	
   function getConfig() {
   		
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

		 $sql = 'SELECT * FROM #__ticketmaster_config WHERE configid = 1';
		 
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

			$query = 'UPDATE #__ticketmaster_coupons'
				. ' SET published = '.(int) $publish
				. ' WHERE coupon_id IN ( '.$cids.' )';

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

	function remove($cid){
		
		## Count the cids
		if (count( $cid )) {
		
			## Make cids safe, against SQL injections
			JArrayHelper::toInteger($cid);
			
			## Implode cids for more actions (when more selected)
			$cids = implode( ',', $cid );
			
			$db = JFactory::getDBO();
			
			## Delete all categories from DB
			$query = 'DELETE FROM #__ticketmaster_coupons WHERE coupon_id IN ( '.$cids.' )';
			
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