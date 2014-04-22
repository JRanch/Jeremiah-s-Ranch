<?php
/**
 * Joomla! 2.5 Ticketmaster
 *
 * @version $Id: subscribe.php 2010-01-25 13:12:42 svn $robert
 * @author Robert Dam
 * @package Joomla
 * @subpackage Ticketmaster
 * @license GNU/GPL
 *
 */

private function salutation($value){

	if ($value == 0) { $value = JText::_( 'COM_TICKETMASTER_NA' ); }
	if ($value == 1) { $value = JText::_( 'COM_TICKETMASTER_MR' ); } 
	if ($value == 2) { $value = JText::_( 'COM_TICKETMASTER_MRS' ); }
	if ($value == 3) { $value = JText::_( 'COM_TICKETMASTER_MISS' ); }
	if ($value == 4) { $value = JText::_( 'COM_TICKETMASTER_FAMILY' ); }
	
		
    return $value;    
} 
