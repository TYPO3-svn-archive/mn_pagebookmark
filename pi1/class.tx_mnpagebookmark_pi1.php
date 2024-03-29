<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Mario Naether <mario-naether@gmx.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_bookmarklist.php'));
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_bookmark.php'));
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_display.php'));
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_parametercontroller.php'));
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_controller.php'));
require_once(t3lib_extMgm::extPath('mn_pagebookmark','lib/class.tx_mnpagebookmark_div.php'));

/**
 * Plugin 'Show Bookmarks' for the 'mn_pagebookmark' extension.
 *
 * @author	Mario Naether <mario-naether@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_mnpagebookmark
 */
class tx_mnpagebookmark_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_mnpagebookmark_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_mnpagebookmark_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'mn_pagebookmark';	// The extension key.
	#var $pi_checkCHash = true;
	var $template;
	var $conf;
	var $mode;
	var $BMcontroller;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
		$this->init();
		if($conf[ 'templateFile' ]){
			
			$this->BMcontroller = t3lib_div::makeInstance( 'tx_mnpagebookmark_controller' );
			$this->BMcontroller->init( $this );
			
			$this->BMcontroller->start( $this->mode );
			
			$content = $this->BMcontroller->getContent();
		}else{
			$content .= 'template file not defined, use "Static Template files to include" in "Include static" '; 		
		}
		return $this->pi_wrapInBaseClass($content);
	}
	
	 
	/**
	 * init all used Values
	 *
	 */
	function init()	{
		
		//set debuging
		$this->debug = $this->conf['outputDebug'];
		
		if($this->debug){
			$GLOBALS['TYPO3_DB']->debugOutput = true;
			$GLOBALS['SYS']['devIPmask'] = '*';
		}

		
				
		// set the current view Mode
		if( $this->cObj->data[ 'tx_mnpagebookmark_bookmark_mode' ] > 0){
			$this->mode = $this->cObj->data[ 'tx_mnpagebookmark_bookmark_mode' ];
		} else {
			$this->mode = $this->conf[ 'mode' ];
		}
	
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/pi1/class.tx_mnpagebookmark_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/pi1/class.tx_mnpagebookmark_pi1.php']);
}

?>