<?php 
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

## Check if the file is included in the Joomla Framework
defined('_JEXEC') or die ('No Acces to this file!');

## Let's check if the system has a payment_type
$payment_type =  JRequest::getVar('payment_type');

## Getting the URL
JPluginHelper::importPlugin( 'rdmedia' );
$dispatcher = JDispatcher::getInstance();
$results = $dispatcher->trigger( $payment_type );

		
?>	

