<?php
/**
 * @version		1.0.0 ticketmaster $
 * @package		ticketmaster
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license		GNU/GPL
 * @author		Robert Dam
 * @author mail	info@rd-media.org
 * @website		http://www.rd-media.org   
 */

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

## Starting a session.
$session =& JFactory::getSession();
## Gettig the orderid if there is one.

$ordercode = $session->get('ordercode');
## If there none.. Create a session for the order process.
if ($ordercode == ''){
	## Creating an ordernumber for the client.
	## This will be 8 numbers.
	$numbers = numbers();
	$session->set('ordercode', $numbers);
}

## Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

## Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
	
}

## Create the controller
$classname    = 'TicketmasterController'.ucfirst($controller);
$controller   = new $classname( );
## Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );


## Redirect if set by the controller
$controller->redirect();

#####################################################################################
#####################################################################################
#################### Generate a random character strings ############################
#####################################################################################
#####################################################################################

## Generate a random character string
function numbers($length = 7, $chars = '123456789'){
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
?>