<?php
defined('_JEXEC') or die();

/****************************************************************
 * @version		1.0.0 ticketmaster $							*
 * @package		ticketmaster									*
 * @copyright	Copyright © 2009 - All rights reserved.			*
 * @license		GNU/GPL											*
 * @author		Robert Dam										*
 * @author mail	info@rd-media.org								*
 * @website		http://www.rd-media.org							*
 ***************************************************************/

class JElementEventlist extends JElement
{

	var	$_name = 'Eventlist';

	function fetchElement($name, $value, &$node, $control_name)
	{

		$db =& JFactory::getDBO();
		
		$query = 'SELECT eventid AS value, eventname AS text '
		.' FROM  #__ticketmaster_events '
		.' WHERE published > 0 '
		;
		
		$db->setQuery( $query );
		
		$options = array();
		$options[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select An Event To Display' ) .' -' );
		$options = array_merge($options, $db->loadObjectList());
		
		return JHTML::_('select.genericlist', $options, 'urlparams['.$name.']', 'class="inputbox" size="1" ', 'value', 'text', $value);

	}
	
}