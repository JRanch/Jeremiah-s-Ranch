<?php
/**
 * @name 	        hdflvplayer
 ** @version	        2.1.0.1
 * @package	        Apptha
 * @since	        Joomla 1.5
 * @subpackage	        hdflvplayer
 * @author      	Apptha - http://www.apptha.com/
 * @copyright 		Copyright (C) 2011 Powered by Apptha
 * @license 		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @abstract      	com_hdflvplayer installation file.
 ** @Creation Date	23 Feb 2011
 ** @modified Date	28 Aug 2013
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


jimport('joomla.html.html');

//imports for param fields
jimport('joomla.form.formfield');

$moduleBaseurl = str_replace('administrator/','',JURI::base());
$document = JFactory::getDocument();
$document->addScript($moduleBaseurl.'modules/mod_hdflvplayer/js/helper_js.js');

/*
 * Class for playlist, videos form fields
 */
class JFormFieldHdflvplayer extends JFormField
{

	protected $type = 'hdflvplayer';
	
	//Function for input to playlist parameter
	function getInput() {
		return $this->fetchElement($this->element['name'], $this->value, $this->element, $this->name);
	}
	
	//Function to fetch playlist info from database
	function fetchElement($name, $value, &$node, $control_name)
	{
				
		$db = JFactory::getDBO();
		
		$videocat = '';
		$style = 'display:block;';
		
		//query to fetch the playlist records
		$query = 'SELECT a.id,a.name'
		. ' FROM #__hdflvplayername AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.id';

		$db->setQuery( $query );
		$options = $db->loadObjectList();
		
		//Fetch module id 
                $moduleId=$get_id="";
                $get_id = JRequest::getVar('id');
                if(isset($get_id))
                $moduleId =  $get_id;
		
		//Check If module id available 
		if($moduleId != '')
		{
			//Fetch params from module table. 
			$qry = 'SELECT params from #__modules WHERE id='.$moduleId;
			$db->setQuery( $qry );
			$rs_params = $db->loadObject();
			
			$paramdecode = json_decode($rs_params->params, true);
			 
			$videocat = $paramdecode['videocat']['videocat'];
			
			$style = 'display:block;';
			
			//If Video category 1, show playlists
			if($videocat == '1')
			{

				echo '<script>window.onload = hidelbl;</script>';
			}
			//else show videos list
			else
			{

				echo '<script>window.onload = hideplaylistvideo;</script>';
			}

		}
                if (version_compare(JVERSION, '3.0', 'ge')) {
		echo "<style> #jformparamsplaylistidplaylistid_chzn, #jformparamsvideoidvideoid_chzn { display:none;  }</style>";
                }
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Playlist').' -', 'id', 'name'));

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox" ', 'id', 'name', $value, $control_name.$name );
	}
}
