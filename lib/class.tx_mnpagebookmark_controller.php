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
 * Class 'Controller' for the 'mn_pagebookmark' extension.
 *
 * @author	Mario Naether <mario-naether@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_mnpagebookmark
 */
class tx_mnpagebookmark_controller {
	
	var $conf;
	var $PiBaseObj; 
	var $BookMarkList;
	var $BookMarkDisplay;
	var $content;
	var $piVars = array();
	
	
	const DELETE_MODE = 'delete';
	const ADD_MODE = 'add';
	const MOVE_MODE = 'move';
	
	/**
	 * construtor of this class
	 *
	 * @return tx_mnpagebookmark_controller
	 */
	function tx_mnpagebookmark_controller(){
	}
	
	/**
	 * set piBase und config
	 *
	 * @param Object $PiBaseObj
	 */
	function init($PiBaseObj){
		$this->conf = $PiBaseObj->conf;
		
		$this->piVars = $PiBaseObj->piVars;
		
		$this->PiBaseObj = $PiBaseObj;
		
		
		//validation for security
		$this->validatePostvars();
		
		$this->BookMarkList = t3lib_div::makeInstance( 'tx_mnpagebookmark_bookmarklist' );
				
	}
	
	/**
	 * start the controlling whit given Mode (1 shows the Link, 2 schow the BMList)
	 *
	 * @param int $mode
	 */
	function start( $mode )	{
		
				
		switch( (integer)$mode ){
			case 1:
				if(is_numeric($this->conf['PIDofBookmarkList']) && $this->conf['PIDofBookmarkList'] != $GLOBALS['TSFE']->id){
					$this->controllBookmarks();
				}
					$content .= $this->getBookmarkPage_Link();				
					break;
			
			case 2: 
					$this->controllBookmarks();
					$content .= $this->renderDisplay();
					break;
					
			default: 
					$this->controllBookmarks();
					$content .= $this->renderDisplay();
					break;
		}
	}
	
	function controllBookmarks(){
		
		
		
			//saves / delete Bookmarks from List
			$this->handleBookmarkList();
			//check Bookmarklist if is the same Bookmarks in DB and in Cookie
			$this->validateBookmarkList();
		
	}
	
	/**
	 * handle BookmarkList (add / delete) Bookmarks
	 *
	 */
	function handleBookmarkList(){
		//handling(add/delete)
		//-> controller
		if($this->piVars['URLParameter']){
			$this->piVars['URLParameter']= $this->validateAdditionURLParameter($this->piVars['URLParameter']);
		}
		
		switch ($this->piVars[ 'submitMode' ]){
			
			case self::ADD_MODE: $this->BookMarkList->addBookmark( $this->piVars[ 'RootPID' ], $this->piVars[ 'PageID' ], $this->piVars['URLParameter']  );
						$this->BookMarkList->saveBookmarkList();break;
						
			case self::DELETE_MODE:	$this->BookMarkList->deleteBookmark( $this->piVars[ 'BookmarkID' ], $this->piVars['URLParameter'] );
						$this->BookMarkList->saveBookmarkList();
						$this->content_m = $this->PiBaseObj->pi_getLL('delete-message');
						break;
							
			case self::MOVE_MODE: $this->BookMarkList->addBookmarkArray( $this->piVars[ 'PageID' ], $this->piVars['URLParameter']);
							$this->BookMarkList->saveBookmarkList();break;
		}
		#return $content;
	}
	
	/**
	 * display the BookmarkList
	 *
	 */
	function renderDisplay(){

		//init Display
		$this->BookMarkDisplay = t3lib_div::makeInstance( 'tx_mnpagebookmark_display' );
		$this->BookMarkDisplay->init( $this->BookMarkList, $this->PiBaseObj );
				
		//render BookmarkList View
		$this->BookMarkDisplay->setMessage( $this->content_m );
		$this->content .= $this->BookMarkDisplay->renderBookmarkList();

	}
	
	/**
	 * validate Boomark-List in DB whit Cookie/Session
	 *
	 */
	function validateBookmarkList()	{
		
		
		if($GLOBALS['TSFE']->loginUser && $this->conf[ 'validateBMListOnView.' ]['whitDB']){ 
		
			/* @var $this->BookMarkList tx_mnpagebookmark_bookmarklist */
			$this->BookMarkList->loadBookmarklistFromCookie( );
			$this->BookMarkList->saveBookmarkListToDB( $GLOBALS['TSFE']->fe_user->user[ 'uid' ] );
			
		}
		
		if($this->conf[ 'validateBMListOnView.' ]['whitCookie']){
			
			//save BookmarkList in Cookie
			$this->BookMarkList->saveBookmarkListToCookie( );
		}
	}
	
	/**
	 * get the "Bookmark this Page" Link
	 *
	 */
	function getBookmarkPage_Link(){
		//tx_mnbookmarklis_display::getLinkToBookmarkPage();
		
		$urlParams = $_SERVER['QUERY_STRING'];
		
		if($this->isPageBookmarked($GLOBALS['TSFE']->id, urldecode($urlParams))
			|| $this->isPageBookmarked($GLOBALS['TSFE']->id, urldecode($this->validateAdditionURLParameter( $this->PiBaseObj->piVars['URLParameter'])))
			){
			
			$this->content .=  $this->PiBaseObj->cObj->getTypoLink($this->PiBaseObj->pi_getLL( 'deleteBookmark' ),
								$this->PiBaseObj->pi_linkTP_keepPIvars_url(array(
													#'submitMode' => 'add', 
													'submitMode' => self::DELETE_MODE, 
													'BookmarkID' => $GLOBALS['TSFE']->id,
													'RootPID' => $GLOBALS['TSFE']->rootLine[0]['uid'],
													'URLParameter' => '--'.($urlParams).'--' ),
											0,
											1,
											$this->conf['PIDofBookmarkList']
											).'&'.$urlParams
											) ;
		}else{
			
			$this->content .= $this->PiBaseObj->cObj->getTypoLink( $this->PiBaseObj->pi_getLL( 'bookmarkPage' ),
								tx_mnpagebookmark_div::validateUrl( $this->conf['BookmarkLink.']['externalURL'] ).
										$this->PiBaseObj->pi_linkTP_keepPIvars_url(	array('submitMode' => 'add', 
													'PageID' => $GLOBALS['TSFE']->id, 
													'RootPID' => $GLOBALS['TSFE']->rootLine[0]['uid'],
													'URLParameter' => '--'.($urlParams).'--' ),
											0,
											1,
											$this->conf['PIDofBookmarkList']
											) 
											.'&'.$urlParams // add for sub pagees
											.' '.$this->conf['BookmarkLink.']['additionJSParams'] // add for popus
										);
		}
		
	}
	
	function isPageBookmarked($pid, $parameter){
		return (bool)$this->BookMarkList->isBookmarkOfRootSite( $this->BookMarkList->getRootPID($pid), $pid, $parameter);
	}

	/**
	 * get current Content 
	 *
	 * @return string
	 */
	function getContent(){
		return $this->content;
	}
	
	/**
	 * validate the PIVars
	 *
	 */
	function validatePostvars()	{
		
		foreach ($this->piVars as $index => $var) {
			$this->piVars[ $index ] = tx_mnpagebookmark_div::htmlspecialchars_Value( $var );
		}
	}
	
	function validateAdditionURLParameter($param) {
		
		//die letzten beiden schriche dummys abmachen
		if( strrpos($param, '--') + 2 == strlen($param) ){
			$param = substr($param,0, -2);
		}
		
		//die ersten beiden schriche dummys abmachen
		if( strpos($param, '--') == 0 ){
			$param = substr($param, 2);
		}
		$param = urldecode($param);
		$param = html_entity_decode($param);
		
		return $param;
	}
	
}

?>