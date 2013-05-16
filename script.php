<?php
/**
 * AMCPortfolio Update script
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_amcportfolioInstallerScript {
	
	private $release = '4.2';
	private $minimum_update_release = '4.0';
	private $minimum_joomla_release = '1.7.0';
	
	function update($parent) {
		$jversion = new JVersion();
		echo 'Upgraded from '.$this->getParam('version').' to '.$this->release;
	}
	
	/**
	* Pre-installation function
	* runs before any installation
	* @param string $type	Installation type (new, update, discover_install, but not uninstall)
	* @param object $parent Class calling this method
	* @return boolean False on failure cancels installation
	*/
	function preflight( $type, $parent ) {
		// This component requires Joomla 1.7 or later
		$jversion = new JVersion();
	
		//Component version
		$this->release = $parent->get("manifest")->version;
	
		//Joomla minimum
		$this->minimum_joomla_release=$parent->get("manifest")->attributes()->version;
	
		//Verify this is a new enough version of Joomla
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install AMCPortfolio in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
	
		//Verify that we are updating from a safe version of AMCPortfolio
		if( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			if( version_compare( $oldRelease, $this->minimum_update_release, 'lt' ) ) {
				Jerror::raiseWarning(null, 'The existing installation is to old to upgrade. Cannot update AMCPortfolio '. $oldRelease . ' to '. $this->release);
				return false;
			}
		}
	
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'The existing version of AMCPortfolio is the same or newer than the one being installed. Cannot update ' . $rel);
				return false;
			}
		} else {
			$rel = $this->release;
		}
	
		echo '<p>Preinstallation checks complete. Installing AMCPortvolio v ' . $rel . '</p>';
	}
	
	/**
	 * Get a parameter from the manifest cache
	 * @param string $name Parameter name
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_amcportfolio"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
}