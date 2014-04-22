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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a author element
 *
 * @package 	Joomla
 * @subpackage	Articles
 * @since		1.5
 */
class JElementEventlist extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Category';

	function fetchElement($name, $value, &$node, $control_name)
	{
		return $this->_category($control_name.'['.$name.']', $value);
	}

	function _category( $name, $active = NULL, $javascript = NULL, $order = 'ordering', $size = 1, $sel_cat = 1 )
		{
			$db =& JFactory::getDBO();
	
			$query = 'SELECT eventid AS value, eventname AS text'
			. ' FROM #__ticketmaster_events'
			. ' WHERE published = 1'
			;
			
			$db->setQuery( $query );
			
			if ( $sel_cat ) {
				$categories[] = JHTML::_('select.option',  '0', '- '. JText::_( 'Select a Category' ) .'' );
				$categories = array_merge( $categories, $db->loadObjectList() );
			} else {
				$categories = $db->loadObjectList();
			}
	
			$category = JHTML::_('select.genericlist', $categories, $name, 'class="inputbox" size="'. $size .'" '. $javascript,
			 'value', 'text', $active );
			return $category;
	}
}