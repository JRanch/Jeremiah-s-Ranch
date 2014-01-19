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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('contushdflvplayerController', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/controller.php');
JLoader::register('hdflvplayerView', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/view.php');
JLoader::register('hdflvplayerModel', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/model.php');
if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

// Require specific controller if requested
if ($controller = JRequest::getWord('controller')) {
       $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname    = 'hdflvplayerController'.$controller;
$controller   = new $classname();

// Perform the Request task
$taskconfig = '';
$taskconfig = JRequest::getvar('taskconfig','','get','var');

if($taskconfig)
{
	$controller->configxml();
}
else
{
	$controller->execute( JRequest::getVar( 'task' ) );
}


// Redirect if set by the controller
$controller->redirect();

?>