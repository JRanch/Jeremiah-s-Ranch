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

class ticketmasterModelCPanel extends JmodelLegacy
{
	function __construct(){
		parent::__construct();

		$mainframe = JFactory::getApplication();
		$config    = JFactory::getConfig();
		
		## Get the pagination request variables
		$limit        = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart   = JRequest::getInt('limitstart', 0);
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 		
	}

   function getData()
   {
      if (empty($this->_data)){

         $db = JFactory::getDBO();
         $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_ticketmaster"');
         $this->data = json_decode( $db->loadResult(), true );

      }
      return $this->data;
   }
   
	function changeSettingPro() {

        $query = 'UPDATE #__ticketmaster_config'
                . ' SET pro_installed = 0'
                . ' WHERE configid = 1';

        ## Do the query now	
        $this->_db->setQuery( $query );

        ## When query goes wrong.. Show message with error.
        if (!$this->_db->query()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
        }
        return true;
	}   
	
}
?>