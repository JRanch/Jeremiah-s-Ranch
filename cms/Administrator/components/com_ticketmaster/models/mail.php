<?php
/****************************************************************
 * @version			2.5.5											
 * @package			com_ticketmaster									
 * @copyright		Copyright © 2009 - All rights reserved.			
 * @license			GNU/GPL											
 * @author			Robert Dam										
 * @author mail		info@rd-media.org								
 * @website			http://www.rd-media.org							
 ***************************************************************/

## Direct access is not allowed.
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class TicketmasterModelMail extends JModelLegacy {

   ## Empty data variabele
   var $_data  = null;
   var $_id = null;

   function __construct() {
   
		parent::__construct();
		
		$mainframe = JFactory::getApplication();		
		
		$config = JFactory::getConfig();
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 
		  
   }

	function store($data)
	{
	
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
  
   function getList()
   {
      if (empty($this->_data)) {
	  
         $db = JFactory::getDBO();
		 $mainframe = JFactory::getApplication();	

		 $sql = 'SELECT * FROM #__ticketmaster_emails ORDER BY emailid ASC';
		 
         $db->setQuery($sql);
         $this->data = $db->loadObjectList();
      }
      return $this->data;
   }

   function getData()
   {
      if (empty($this->_data)) {
	  
         $db = JFactory::getDBO();
		 $mainframe = JFactory::getApplication();	

		 $sql = 'SELECT * FROM #__ticketmaster_emails WHERE emailid = '.(int)$this->id.'';
		 
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

			$query = ' UPDATE #__ticketmaster_emails'
				. '    SET published = '.(int) $publish
				. '    WHERE emailid IN ( '.$cids.' )';
			
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

	function remove($cid  = array()) {
		
		$mainframe  =& JFactory::getApplication();;
		$db  		=  JFactory::getDBO();
		
		## If someone is trying to delete without $cid
		## If there is no cid provided, redirect the component.
		if(count($cid) < 1 ) {
		
			$mainframe->redirect('index.php?option=com_ticketmaster');
		}
		
		JArrayHelper::toInteger($cid);
		$cids = implode( ',', $cid );
		
		## Make the query to delete one of the cars.
		$query = 'DELETE FROM #__ticketmaster_emails WHERE emailid IN ( '.$cids.' )';
		$db->setQuery($query);
		
		## If the query doesn't work..
		if (!$db->query() ){
			echo "<script>alert('The query didn't run.. Please report your problem. (Code: Application-Model-123)');
			window.history.go(-1);</script>\n";		 
		}
		
		## Nothing went wrong, redirect now to the overview.
		return true;	
			
	}
		
}
?>