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
 * Class 'BookmarkList' for the 'mn_pagebookmark' extension.
 *
 * @author	Mario Naether <mario-naether@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_mnpagebookmark
 */
class tx_mnpagebookmark_bookmarklist {
	
	var $bookmarks = array();
	var $cookieName = 'bookmark_list';
	var $cookieLifeTime = 99999999;
	var $CookieDomains;
	var $conf = array();
	var $storagePID = 0;
	
	/**
	 * constructor of class
	 *
	 * @return tx_mnpagebookmark_bookmarklist
	 */
	function tx_mnpagebookmark_bookmarklist( ) {
		
		$this->init();
		
		$this->loadBookmarklist();
	}
	
	function init( )	{
		//set TSConf
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_mnpagebookmark_pi1.'];
		
		//set 
		if( (integer)$this->conf['cookieLifeTime'] > 0 ){
			$this->cookieLifeTime = (integer)$this->conf['cookieLifeTime'];
		}
		
		//set Cookie Domains
		if( is_array( $this->conf[ 'CookieDomain.' ] ) ){
			
			$this->CookieDomains = $this->conf[ 'CookieDomain.' ];
		}
		
		//set Bookmark Array  
		if(is_array($this->conf['RootPIDs.'])){
			foreach ($this->conf['RootPIDs.'] as $RootID => $enable) {
				if($enable){
					$this->bookmarks[ $RootID ] = array();
				}
			}
		}
		
		if( strlen( $this->conf[ 'cookieName' ] ) > 0 ){
			$this->cookieName = $this->conf[ 'cookieName' ];
		}
		
		if( is_numeric( $this->conf[ 'storagePID' ] ) ){
			$this->storagePID = (integer)$this->conf[ 'storagePID' ];
		}
	}
	
	
	
	/***********************************
	 * 
	 * LOAD FUNCTIONS: to load Bookmarks 
	 * 
	 ***********************************/
	
	/**
	 * load all Bookmarks, if logon from DB else from Cookie
	 *
	 */
	function loadBookmarklist(  ){
		
		//load all Bookmarks from Database
		if($GLOBALS['TSFE']->loginUser){
			
			$this->loadBookmarklistFromDB( $GLOBALS['TSFE']->fe_user->user[ 'uid' ] );
			
		}else{	
			//load all Bookmarks from Cookie / Session	
			$this->loadBookmarklistFromCookie();
		}
		
	}
	
	/**
	 * load all Bookmarks from DB
	 *
	 * @param int $UserID: FEUserUID
	 */
	function loadBookmarklistFromDB( $UserID ){
		
		$this->bookmarks = array();
		if(is_numeric($UserID)){
			 $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(' tx_mnpagebookmark_bookmark.*,tx_mnpagebookmark_bookmark_page_id_mm.uid_foreign, tx_mnpagebookmark_bookmark_page_id_mm.parameter'
							,'tx_mnpagebookmark_bookmark 
								RIGHT JOIN tx_mnpagebookmark_bookmark_page_id_mm ON (tx_mnpagebookmark_bookmark.uid = tx_mnpagebookmark_bookmark_page_id_mm.uid_local)
								',
							
							'tx_mnpagebookmark_bookmark.user_id = '.$GLOBALS['TYPO3_DB']->quoteStr( $UserID,'tx_mnpagebookmark_bookmark' ).
								' AND tx_mnpagebookmark_bookmark.pid = '.
								$this->storagePID. $GLOBALS['TSFE']->cObj->enableFields('tx_mnpagebookmark_bookmark'),
							'',
							'tx_mnpagebookmark_bookmark.uid ASC');
							
			if($GLOBALS['TYPO3_DB']->sql_num_rows( $res ) ){
								
				while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ){
					if(is_numeric($row[ 'uid_foreign' ])){
						#debug('load BM from db'); 
						$this->addBookmark($this->getRootPID( $row[ 'uid_foreign' ] ), $row[ 'uid_foreign' ], $row[ 'parameter' ]);
						#$this->bookmarks[ $this->getRootPID( $row[ 'uid_foreign' ] ) ][ $row[ 'uid_foreign' ] ][ ] = new tx_mnpagebookmark_bookmark( $row['uid_foreign'], $row['parameter']);
						
					}
				}
				
				$GLOBALS['TYPO3_DB']->sql_free_result( $res );
			}
		}
	}
	
	/**
	 * load all Bookmarks from Cookie
	 *
	 */
	function loadBookmarklistFromCookie(){

		//get from cookie
		//$BookMark_IDs = unserialize( $_COOKIE[ $this->cookieName ] );
	
		//cookie disabled use Session
		if( !is_array( $BookMark_IDs ) ){
			
			$BookMark_IDs = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->cookieName);
		}
				
		//add to object
		if( is_array( $BookMark_IDs ) ){

			foreach ($BookMark_IDs as $RootID => $BMArr) {
				
				foreach ($BMArr as $key => $BMID) {
					$this->addBookmark((integer)$RootID, (integer)$BMID, '');
					#$this->bookmarks[ (integer)$RootID ][ (integer)$BMID ][] = new tx_mnpagebookmark_bookmark( $BMID, '' );
					
				}
			
			}
		}
		
	}
	
	/**
	 * reload the BookMark List
	 * @access public
	 */
	function resetBookmarkList(){
		$this->loadBookmarklist();
	}
	
	
	
	
	
	/*************************************
	 * 
	 * SAVE FUNCTIONS: to save the Bookmark
	 * 
	 *************************************/
	
	/**
	 * save The BookmarkList in DB or Cookie
	 *
	 */
	function saveBookmarkList( ){
		
		if( $GLOBALS['TSFE']->loginUser ){
			$this->saveBookmarkListToDB( $GLOBALS['TSFE']->fe_user->user['uid'] );
		}else {
		}
		$this->saveBookmarkListToCookie( );
	}
	
	
	/**
	 * save current BookmarkList in DB
	 *
	 */
	function saveBookmarkListToDB( $user_id){
		
		if(is_numeric($user_id)){
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_mnpagebookmark_bookmark','user_id = '.$GLOBALS['TYPO3_DB']->quoteStr( $user_id,'tx_mnpagebookmark_bookmark' ).' AND pid = '.$this->storagePID );
			
			//if Bookmark List set from current user, delete her Bookmarks from MM-Table
			if( $GLOBALS['TYPO3_DB']->sql_num_rows( $res ) == 1 ){
				
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					
					$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mnpagebookmark_bookmark_page_id_mm','uid_local = '.$GLOBALS['TYPO3_DB']->quoteStr( $row['uid'],'tx_mnpagebookmark_bookmark_page_id_mm' ) );
					$BMUID = (integer)$row['uid'];
				}
				
			}else{ //else add a empty Bookmarklist
				
				$BMUID = $this->insertBookmarkListToDB( $GLOBALS['TSFE']->fe_user->user );
			}
		
			foreach ($this->bookmarks as $BookmarkArr) {
				foreach ($BookmarkArr as $BookmarkArr2) {
					foreach ($BookmarkArr2 as $param =>  $BookmarkObj) {
						$this->insertBookmarkToDB($BMUID, $BookmarkObj->getID(), $param );
					}					
				}
				
			}
		}
	}
	
	/**
	 * save given Bookmark in MM-DB 
	 *
	 * @param int $Bookmark_DB_UID: uid of BookmarkList
	 * @param int $Bookmark_obj_ID: uid of Bookmark
	 * @return int: affeced Rows
	 */
	function insertBookmarkToDB($Bookmark_DB_UID, $Bookmark_obj_ID, $BookmarkParams){
		
		$insertArray = array(
			'uid_local' => $Bookmark_DB_UID,
			'uid_foreign' => $Bookmark_obj_ID,
			'parameter' => $BookmarkParams,
		);
		#debug($Bookmark_obj_ID);
					
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mnpagebookmark_bookmark_page_id_mm', $insertArray);
		
		return  ( $GLOBALS['TYPO3_DB']->sql_affected_rows() > 0 );
	}
	
	/**
	 * save a new BookmarkList in DB
	 *
	 * @param int $FE_User
	 * @return int: inserted uid
	 */
	function insertBookmarkListToDB($FE_User){
		
		$insArr = array(
		'pid' => (integer)$this->storagePID,
		'tstamp' => time(),
		'crdate' => time(),
		'page_id' => 0,
		'user_id' => (integer)$FE_User['uid'],
		'name' => (integer)$FE_User['username'],
		);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mnpagebookmark_bookmark', $insArr);
		
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	
	
	/**
	 * save BookmarkList in Cookie and Typo-Session, if user has disabled cookies
	 *
	 */
	function saveBookmarkListToCookie( ){
		
		foreach ($this->bookmarks as $RootPageID => $BMArr) {
			
			foreach ($BMArr as $BMarr2) {
				
				foreach ($BMarr2 as $BMObj) {
					$cookieArr[(integer)$RootPageID][] = $BMObj->getID();
				}
			}
			
		}
		
		//setcookie( $this->cookieName, serialize( $cookieArr ),time() + $this->cookieLifeTime );
				
		//save in Session
		$GLOBALS['TSFE']->fe_user->setKey('ses', $this->cookieName,  $cookieArr );
		
		
	}
	
	
	
	
	
	
	/*******************
	 * 
	 * HANDLE BOOKMARKS: Handle the given Bookmarks, add / delete to List
	 * 
	 *******************/
	
	/**
	 * add given Bookmark to current List 
	 *
	 * @param int $PageID
	 * @param int $RootPID
	 */
	function addBookmark( $RootPID, $PageID, $Parameter = '')	{
		if(is_numeric($RootPID) && is_numeric($PageID) ){
			$this->bookmarks[ (integer)$RootPID ][ (integer)$PageID ][ $Parameter ] = new tx_mnpagebookmark_bookmark($PageID, $Parameter);
		}
	}
	
	function addBookmarkArray( $PageIDArr){
		
		if( is_array( $PageIDArr ) ){
			
			foreach ( $PageIDArr as $BookMarkID ){
				
				$this->addBookmark($this->getRootPID( $BookMarkID ), $BookMarkID);
				#$this->bookmarks[ (integer)$RootPID ][ (integer)$BookMarkID ] = new tx_mnpagebookmark_bookmark( $BookMarkID );
			}
		}
	}
	
	
	/**
	 * delete given Bookmark from given RootSite
	 *
	 * @param int $RootPID
	 * @param inr $BookmarkID
	 * 
	 * @return bool
	 */
	function deleteBookmark( $BookmarkID, $parameter ){
		
	 	$RUID = $this->getRootPIDByBookmarkID( $BookmarkID, $parameter );
	 	
		if($this->isBookmarkofRootSite($RUID, $BookmarkID, $parameter)){
	
		
			unset( $this->bookmarks[ $RUID ][ $BookmarkID ][ $parameter ] ); 
			
			//checks again
			if( !$this->isBookmarkofRootSite($RUID, $BookmarkID, $parameter)){
				return true;
			}
			
		}
		return false;
	}
	
	
	
	
	
	/**
	 * checks if given Bookmark a Bookmark of given RootSite 
	 *
	 * @param unknown_type $RootPID
	 * @param unknown_type $BookmarkID
	 * @return unknown
	 */
	function isBookmarkOfRootSite( $RootPID, $BookmarkID, $parameter){
		#debug($parameter);
		#debug($this->bookmarks[ $RootPID ][ $BookmarkID ][ $parameter ]);
		return isset($this->bookmarks[ $RootPID ][ $BookmarkID ][ trim($parameter) ]);
	}
	
	
	/**
	 * get the RootPID from current BookmarkList by given BookmarkID
	 *
	 * @param int $BookmarkID
	 * @return int
	 */
	function getRootPIDByBookmarkID( $BookmarkID, $parameter ){
		
		foreach ($this->bookmarks as $RootPID => $BookMarkArr) {
			
				if($this->isBookmarkOfRootSite($RootPID, $BookmarkID, $parameter)){
					return (integer)$RootPID;
				}
						
		}
		
	}
	
	/**
	 * get the PID of RootSite
	 *
	 * @param int $BookmarkID
	 * @return int
	 */
	function getRootPID( $BookmarkID ){
		
		$RootlinePID = 0;
		
		$Rootline = $GLOBALS['TSFE']->sys_page->getRootLine( $BookmarkID );
		
		//check in TS the Root PID's 
		if(is_array($this->conf['RootPIDs.'])){
			
			foreach ($Rootline as $key => $Page) {
				if( in_array( $Page['uid'], array_keys( $this->conf['RootPIDs.'] ) ) ){
					$RootlinePID = $Page['uid'];
				}
			}
		}else { //else get the 0 Rootline
			$RootlinePID = $Rootline[0][ 'uid' ];
		}
		return (integer)$RootlinePID;
		
		/* code replaced by typo function
		$Page = $GLOBALS['TSFE']->sys_page->checkRecord('pages', $BookmarkID);
		$pid = $Page['pid'];
		$found = false;
		
		while ( !$found )  {
			$gpid = $this->getParent( $pid );
			
			if($gpid['pid'] == 0 ){
				#echo  $gpid['uid'];
				return $gpid['uid'];
				#$found = true;
			} else {
				$pid = $gpid['pid'];
			}
		}
		*/
		return ;
	}

	
	/**
	 * get a complete List of all Bookmarks
	 *
	 * @return array
	 */
	function getBookmarkList(){
		return $this->bookmarks;
	}
	
	/**
	 * get count of Boomarks from given Root-Page
	 *
	 * @param int $RootPid
	 * @return int
	 */
	function getCountBookmarksOfRootPage( $RootPid ){
		return count( $this->bookmarks[ $RootPid ] );
	}
	
	
	/**
	 * get count of all Bookmarks in the List
	 *
	 * @return int
	 */
	function getBookmarkListCount(){
		
		$count = 0;
		foreach ($this->bookmarks as $rootID => $BMArr) {
			$count += $this->getCountBookmarksOfRootPage( $rootID );		
		}
		return (integer)$count;
	}
	
	
	
	
	
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/lib/class.tx_mnpagebookmark_bookmarklist.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mn_pagebookmark/lib/class.tx_mnpagebookmark_bookmarklist.php']);
}
?>