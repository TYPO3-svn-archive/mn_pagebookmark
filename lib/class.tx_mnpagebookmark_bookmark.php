<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Mario Naether <mario-naether@gmx.de>
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
 * Class 'Bookmark' for the 'mn_pagebookmark' extension.
 *
 * @author	Mario Naether <mario-naether@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_mnpagebookmark
 */
class tx_mnpagebookmark_bookmark {
	
	var $id;
	var $title;
	var $parameter;
	var $BookMarkPropertys;
	
	/**
	 * constructor of class 
	 *
	 * @param int $id: BookMark-UID
	 * @return tx_mnpagebookmark_bookmark
	 */
	function tx_mnpagebookmark_bookmark( $id, $parameter ){
		
		$this->loadBookmark( (integer)$id, $parameter );
	}
	
	
	/**
	 * load Current Bookmark
	 *
	 * @param int $id: BookMark-UID
	 * @access private
	 */
	function loadBookmark( $id, $parameter ){
		
		$BookMark = $GLOBALS['TSFE']->sys_page->checkRecord('pages', $id);
		
		$this->id = $id;
		$this->title = (string)$BookMark[ 'title' ];
		$this->parameter = $parameter;
		
		if(is_array($BookMark)){
			foreach ($BookMark as $field => $value) {
				$this->BookMarkPropertys[ $field ] = $value;
			}
		}
	}
	
	
	
	
	
	
	
	/******************
	 * 
	 * GET / SET FUNCTION
	 * 
	 ******************/

	/**
	 * get the Title of Bookmark
	 *
	 * @return string
	 */
	function getTitle(){
		return $this->title;
	}
	/**
	 * set the Title of Bookmark
	 *
	 * @return string
	 */
	function setTitle($title){
		$this->title = $title;
	}
	/**
	 * get the Parameter of Bookmark
	 *
	 * @return string
	 */
	function getParameter(){
		return $this->parameter;
	}
	
	/**
	 * get ID of Bookmark
	 *
	 * @return int
	 */
	function getID(){
		return $this->id;
	}
	
	/**
	 * get value of given Fieldname
	 *
	 * @param unknown_type $fieldname
	 * @return unknown
	 */
	function getField( $fieldname )	{
		return $this->BookMarkPropertys[ $fieldname ];
	}
	
	/**
	 * get all Fields/Propertys from current Bookmark
	 *
	 * @return array
	 */
	function getAllFields()	{
		return $this->BookMarkPropertys;
	}
	
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/lib/class.tx_mnpagebookmark_bookmark.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/lib/class.tx_mnpagebookmark_bookmark.php']);
}
?>