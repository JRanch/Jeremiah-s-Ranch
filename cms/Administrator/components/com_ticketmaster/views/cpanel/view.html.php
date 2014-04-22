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

## No Direct Access - Kill this Script!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class TicketmasterViewCpanel extends JViewLegacy {

	function display($tpl = null) {
		
		$db         = JFactory::getDBO();
        $app        = JFactory::getApplication();
        $document   = JFactory::getDocument();
                
        ## Check if we're running Joomla 3.0
		$isJ30 = version_compare(JVERSION, '3.0.0', 'ge');
		
        ## Getting the desired info from the configuration table
		$sql = "SELECT * FROM #__ticketmaster_config WHERE configid = 1 ";
		 
		$db->setQuery($sql);
		$config = $db->loadObject();
				 
		## Model is defined in the controller
		$model	= $this->getModel();
                
		$data = $this->get('data');
		
		if ($config->scan_api == 0){
			
			$api_code = self::numbers(6);
			
			$query = 'UPDATE #__ticketmaster_config'
				. ' SET scan_api = '.(int) $api_code
				. ' WHERE configid = 1';
			
			## Do the query now	
			$db->setQuery( $query );
			
			## When query goes wrong.. Show message with error.
			if (!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			}

		}
		
		$this->assignRef('data', $data);     
		$this->assignRef('config', $config);	

		parent::display($tpl);

	}
	
	## Generate a random character string
	function numbers($length = 6, $chars = '123456789'){
		## Length of character list
		$chars_length = (strlen($chars) - 1);
		## Start our string
		$string = $chars{rand(0, $chars_length)};
		## Generate random string
		for ($i = 1; $i < $length; $i = strlen($string)){
			## Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};
			## Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		## Return the string
		return $string;
	}	
  
}
?>
