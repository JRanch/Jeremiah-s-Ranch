<?php
## No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_ticketmasterInstallerScript
{
	/*
	 * The release value would ideally be extracted from <version> in the manifest file,
	 * but at preflight, the manifest file exists only in the uploaded temp folder.
	 */
	private $release = '3.1.3';
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		## this component does not work with Joomla releases prior to 1.6
		## abort if the current Joomla release is older
		$jversion = new JVersion();
		if( version_compare( $jversion->getShortVersion(), '1.6', 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install Ticketmaster in a Joomla release prior to 1.6');
			return false;
		}
 
		## abort if the release being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
 
		echo '<p>' . JText::_('' . $type . ' ' . $rel) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		echo '<p>' . JText::_('Thank you for installing Ticketmaster ' . $this->release) . '</p>';
		## You can have the backend jump directly to the newly installed component configuration page
		$parent->getParent()->setRedirectURL('index.php?option=com_ticketmaster');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		echo '<p>' . JText::_('Ticketmasters has been upgraded to ' . $this->release) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		## set initial values for component parameters
		$params['my_param0'] = 'Component version ' . $this->release;
		## $params['my_param1'] = 'Another value';
		## $params['my_param2'] = 'Still yet another value';
		$this->setParams( $params );
 
		echo '<p>' . JText::_('Extention has been registered in the database: ' . $type . ' to ' . $this->release) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		echo '<p>' . JText::_('Removed Ticketmaster Version: ' . $this->release) . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_ticketmaster"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			## read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_ticketmaster"');
			$params = json_decode( $db->loadResult(), true );
			## add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			## store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_ticketmaster"' );
				$db->query();
		}
	}
}