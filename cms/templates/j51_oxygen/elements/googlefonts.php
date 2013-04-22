<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldGoogleFonts extends JFormField

{
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'GoogleFonts';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
// 		$lines = file(JPATH_ROOT.DS.'php'.DS.'googlefonts.php');
		// Initialize variables.
		$googleFonts = array();

		foreach ($this->element->children() as $googlefonts)
		{
        $googleFonts['Arial, sans-serif'] = '--------Web Safe Fonts--------';
        $googleFonts['Arial, Helvetica, sans-serif'] = 'Arial';
		$googleFonts['Courier, monospace'] = 'Courier';
		$googleFonts['Garamond, serif'] = 'Garamond';
		$googleFonts['Georgia, serif'] = 'Georgia';
		$googleFonts['Impact, Charcoal, sans-serif'] = 'Impact';
		$googleFonts['Lucida Console, Monaco, monospace'] = 'Lucida Console';
		$googleFonts['Lucida Sans Unicode, Lucida Grande, sans-serif'] = 'Lucida Sans Unicode';
		$googleFonts['MS Sans Serif, Geneva, sans-serif'] = 'MS Sans Serif';
		$googleFonts['MS Serif, New York, sans-serif'] = 'MS Serif';
		$googleFonts['Palatino Linotype, Book Antiqua, Palatino, serif'] = 'Palatino Linotype';
		$googleFonts['Tahoma, Geneva, sans-serif'] = 'Tahoma';
		$googleFonts['Times New Roman, Times, serif'] = 'Times New Roman';
		$googleFonts['Trebuchet MS, Helvetica, sans-serif'] = 'Trebuchet MS';
		$googleFonts['Verdana, Geneva, sans-serif'] = 'Verdana';
		$googleFonts['Arial'] = '----------Google Fonts----------';
		$googleFonts['Allan'] = 'Allan';
		$googleFonts['Allerta'] = 'Allerta';
		$googleFonts['Allerta Stencil'] = 'Allerta Stencil';
		$googleFonts['Anonymous Pro'] = 'Anonymous Pro';
		$googleFonts['Anton'] = 'Anton';
		$googleFonts['Arimo'] = 'Arimo';
		$googleFonts['Arvo'] = 'Arvo';
		$googleFonts['Astloch'] = 'Astloch';
		$googleFonts['Bentham'] = 'Bentham';
		$googleFonts['Bevan'] = 'Bevan';
		$googleFonts['Buda'] = 'Buda';
		$googleFonts['Cabin'] = 'Cabin';
		$googleFonts['Calligraffitti'] = 'Calligraffitti';	
		$googleFonts['Cantarell'] = 'Cantarell';
		$googleFonts['Cardo'] = 'Cardo';	
		$googleFonts['Carme'] = 'Carme';	
		$googleFonts['Cherry Cream Soda'] = 'Cherry Cream Soda';
		$googleFonts['Chewy'] = 'Chewy';
		$googleFonts['Coda'] = 'Coda';	
		$googleFonts['Coming Soon'] = 'Coming Soon';
		$googleFonts['Comfortaa'] = 'Comfortaa';
		$googleFonts['Copse'] = 'Copse';
		$googleFonts['Corben'] = 'Corben';
		$googleFonts['Cousine'] = 'Cousine';
		$googleFonts['Covered By Your Grace'] = 'Covered By Your Grace';
		$googleFonts['Crafty Girls'] = 'Crafty Girls';
		$googleFonts['Crimson Text'] = 'Crimson Text';
		$googleFonts['Crushed'] = 'Crushed';
		$googleFonts['Cuprum'] = 'Cuprum';	
		$googleFonts['Dancing Script'] = 'Dancing Script';
		$googleFonts['Droid Sans'] = 'Droid Sans';
		$googleFonts['Droid Sans Mono'] = 'Droid Sans Mono';
		$googleFonts['Droid Serif'] = 'Droid Serif';
		$googleFonts['Expletus Sans'] = 'Expletus Sans';
		$googleFonts['Fontdiner Swanky'] = 'Fontdiner Swanky';
		$googleFonts['Geo'] = 'Geo';
		$googleFonts['Goudy Bookletter 1911'] = 'Goudy Bookletter 1911';	
		$googleFonts['Gruppo'] = 'Gruppo';			
		$googleFonts['Homemade Apple'] = 'Homemade Apple';
		$googleFonts['Helvetica'] = 'Helvetica';
		$googleFonts['IM Fell'] = 'IM Fell';
		$googleFonts['Inconsolata'] = 'Inconsolata';
		$googleFonts['Irish Growler'] = 'Irish Growler';
		$googleFonts['Josefin Slab'] = 'Josefin Slab';
		$googleFonts['Josefin Sans'] = 'Josefin Sans';
		$googleFonts['Josefin Sans Std Light'] = 'Josefin Sans Std Light';
		$googleFonts['Just Another Hand'] = 'Just Another Hand';
		$googleFonts['Just Me Again Down Here'] = 'Just Me Again Down Here';	
		$googleFonts['Kenia'] = 'Kenia';
		$googleFonts['Kranky'] = 'Kranky';
		$googleFonts['Kreon'] = 'Kreon';
		$googleFonts['Kristi'] = 'Kristi';
		$googleFonts['Lato'] = 'Lato';
		$googleFonts['Lekton'] = 'Lekton';
		$googleFonts['Lobster'] = 'Lobster';
		$googleFonts['Luckiest Guy'] = 'Luckiest Guy';
		$googleFonts['Mako'] = 'Mako';
		$googleFonts['Meddon'] = 'Meddon';
		$googleFonts['Merriweather'] = 'Merriweather';
		$googleFonts['Molengo'] = 'Molengo';
		$googleFonts['Mountains of Christmas'] = 'Mountains of Christmas';
		$googleFonts['Neucha'] = 'Neucha';
		$googleFonts['Neuton'] = 'Neuton';
		$googleFonts['Nobile'] = 'Nobile';
		$googleFonts['OFL Sorts Mill Goudy TT'] = 'OFL Sorts Mill Goudy TT';
		$googleFonts['Old Standard TT'] = 'Old Standard TT';
		$googleFonts['Open Sans'] = 'Open Sans';
		$googleFonts['Open Sans Condensed'] = 'Open Sans Condensed:300';
		$googleFonts['Orbitron'] = 'Orbitron';
		$googleFonts['Oswald'] = 'Oswald';
		$googleFonts['Permanent Marker'] = 'Permanent Marker';
		$googleFonts['Philosopher'] = 'Philosopher';
		$googleFonts['PT Sans'] = 'PT Sans';
		$googleFonts['PT Serif'] = 'PT Serif';
		$googleFonts['Puritan'] = 'Puritan';
		$googleFonts['Questrial'] = 'Questrial';
		$googleFonts['Radley'] = 'Radley';	
		$googleFonts['Raleway'] = 'Raleway';
		$googleFonts['Reenie Beanie'] = 'Reenie Beanie';
		$googleFonts['Rock Salt'] = 'Rock Salt';
		$googleFonts['Sans-Serif'] = 'Sans-Serif';
		$googleFonts['Schoolbell'] = 'Schoolbell';
		$googleFonts['Slackey'] = 'Slackey';
		$googleFonts['Sniglet'] = 'Sniglet';
		$googleFonts['Sunshiney'] = 'Sunshiney';
		$googleFonts['Syncopate'] = 'Syncopate';
		$googleFonts['Tangerine'] = 'Tangerine';
		$googleFonts['Tinos'] = 'Tinos';
		$googleFonts['Ubuntu'] = 'Ubuntu';
		$googleFonts['UnifrakturCook'] = 'UnifrakturCook';
		$googleFonts['UnifrakturMaguntia'] = 'UnifrakturMaguntia';
		$googleFonts['Unkempt'] = 'Unkempt';
		$googleFonts['Vibur'] = 'Vibur';	
		$googleFonts['Vollkorn'] = 'Vollkorn';
		$googleFonts['VT323'] = 'VT323';
		$googleFonts['Walter Turncoat'] = 'Walter Turncoat';	
		$googleFonts['Yanone Kaffeesatz'] = 'Yanone Kaffeesatz';
		
		}

		reset($googlefonts);

		return $googleFonts;
	}
}
