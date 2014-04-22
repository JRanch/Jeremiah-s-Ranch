<?php
/**
 * Joomla! 1.6 component rdautos single dealer (rdautos)
 *
 * @version $Id: subscribe.php 2010-01-25 13:12:42 svn $robert
 * @author Robert Dam
 * @package Joomla
 * @subpackage RD-Autos
 * @license GNU/GPL
 *
 * Showing vehicles at your website the easy way.
 * completely tabeless views and compatible for J1.5 and 1.6
 *
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports a modal contact picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_contact
 * @since		1.6
 */
class JFormFieldModal_Venue extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Venue';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the javascript
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectChart_'.$this->id.'(id, name, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = name;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Get the title of the linked chart
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT venue' .
			' FROM #__ticketmaster_venues' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_TICKETMASTER_SELECT_VENUE_LABEL');
		}

		$link = 'index.php?option=com_ticketmaster&amp;controller=venues&amp;task=modal&amp;tmpl=component&amp;function=jSelectChart_'.$this->id;

		$html = "\n".'<div class="fltlft"><input type="text" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="btn"><div class="blank"><a class="modal" title="'.JText::_('COM_TICKETMASTER_SELECT_BUTTON').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_TICKETMASTER_SELECT_BUTTON').'</a></div></div>'."\n";
		// The active contact id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return $html;
	}
}
