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

//No direct acesss
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }
//Includes helper file
require_once (dirname(__FILE__).DS.'helper.php');

//Fetch related videos here
$rs_thumbnail	= hdflvplayer::getrecords($params);
$pid		= '';
$pid		= JRequest::getvar('pid','','get','var');

//Fetch video details based selection in param settings  
$rs_title	= hdflvplayer::gettitle($pid,$params);
$homepageaccess = false;
if(!empty($rs_title)){
$homepageaccess	= hdflvplayer::getHTMLVideoAccessLevel($rs_title->id);
}

$class		= $params->get( 'moduleclass_sfx' );

//Query to fetch Google Ads
$db 		= JFactory::getDBO();
$query 	    = 'SELECT closeadd,reopenadd,ropen,publish,showaddm FROM #__hdflvaddgoogle 
			   WHERE publish=1';
$db->setQuery($query);
$fields = $db->loadObject();

$detailmodule = array();

//set Google Ads info into array variable.  
if(!empty($fields))
{
$detailmodule = array('closeadd'	=> $fields->closeadd,
					  'reopenadd'	=> $fields->reopenadd,
					  'ropen'		=> $fields->ropen,
					  'publish'		=> $fields->publish,
				      'showaddm'	=> $fields->showaddm);
}
require(JModuleHelper::getLayoutPath('mod_hdflvplayer'));
?>