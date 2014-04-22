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
 
## Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class TicketmasterModelConfiguration extends JModelLegacy
{
   var $_data  = null;
   var $config_id = 0;

   function __construct()
   {
      parent::__construct();

      $this->id = 1; // hard-coded for now
   }

   function getData()
   {
      if (empty($this->_data))
      {
         $db = JFactory::getDBO();

         $query = 'SELECT * FROM #__ticketmaster_config WHERE configid = '.$this->id;

         $db->setQuery($query);
         $this->data = $db->loadObject();
      }
      return $this->data;
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
   
}
?>