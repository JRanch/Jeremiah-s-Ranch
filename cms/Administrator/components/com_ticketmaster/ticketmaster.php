<?php
/****************************************************************
 * @version		2.5.4 ticketmaster 								*
 * @package		Ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 *																* 
 ***************************************************************/

## no direct access
defined('_JEXEC') or die('Restricted access');

## For Joomla! 3.0
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

## Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	## No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

## Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.2.0', '>='))
{
	return JError::raise(E_ERROR, 500, 'PHP 4.x, 5.0 and 5.1 is no longer supported by RD-Media.','The version of PHP used on your site is obsolete and contains known security vulenrabilities. Moreover, it is missing features required by Ticketmaster to work properly or at all. Please ask your host to upgrade your server to the latest PHP 5.2 or 5.3 release. Thank you!');
}

## Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

## Require specific controller if requested.
if($controller = JRequest::getWord('controller', 'cpanel')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

## path to remover -- remove unfinished tickets functionality -- the class will detect if it's turned on.
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'ticketcleaner.class.php';

if (file_exists($path)) {
	require_once($path);

	$cleaner = new remover();  
	$cleaner->cleanup();

}

## Create the controller
$classname	= 'TicketmasterController'.ucfirst($controller);
$controller	= new $classname( );

## Perform the Request task, display will be loaded automatically.
$controller->execute( JRequest::getCmd( 'task' ) );

$controller->redirect();

?>
