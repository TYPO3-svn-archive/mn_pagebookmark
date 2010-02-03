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
 * Class 'Display' for the 'mn_pagebookmark' extension.
 *
 * @author	Mario Naether <mario-naether@gmx.de>
 * @package	TYPO3
 * @subpackage	tx_mnpagebookmark
 */
class tx_mnpagebookmark_display {
	
	var $template;
	var $BookmarkListObj;
	var $piBaseObj;
	var $message;
	
	/**
	 * Construtor from Display Class
	 *
	 * @return tx_mnpagebookmark_display
	 */
	function tx_mnpagebookmark_display() {
		
			
	}
	
	/**
	 * init, set the piBase Object und TS-Conf Vars
	 *
	 * @param Object $BMObj: Bookmark List
	 * @param Object $piBaseObj: PiBase
	 */
	function init( $BMObj, $piBaseObj ) {
		$this->BookmarkListObj = $BMObj;
		$this->piBaseObj = $piBaseObj;
		
		
		$this->conf = $this->piBaseObj->conf;
		#debug($this->conf[ 'templateFile' ]);
		$this->template = $GLOBALS['TSFE']->cObj->FileResource( $this->conf[ 'templateFile' ] );
		
	}
	
	/**
	 * render the Bookmark-List whit additional Url's
	 *
	 * @return string
	 */
	function renderBookmarkList() {
		
		$content = $this->getBookmarkList();
		
		$markerArray = $this->getAdditionalMarkersToList();
		
		$content = $GLOBALS['TSFE']->cObj->substituteMarkerArray($content , $markerArray);
		
		return $content;
	}
	
	/**
	 * get the List of Bookmarks from current User
	 *
	 * @return string
	 */
	function getBookmarkList(){
	
		$templateBMList =  $GLOBALS['TSFE']->cObj->getSubpart( $this->template, '###BOOKMARK###');
			
		/* @var $BMObj tx_mnpagebookmark_bookmark */
		$subpart['###BOOKMARK_ENTRY_1###'] = '';
		
		/* sorting */
		/*
		$newBMList = $this->BookmarkListObj->getBookmarkList();
		foreach($this->BookmarkListObj->getBookmarkList() as $RootPID => $BMObjArr){
			foreach ($BMObjArr as $bookmkarkId => $BMArr2) {
				foreach ($BMArr2 as $bookmarkParams => $BMObj) {
					
					$parmCon = new tx_mnpagebookmark_parameterconroller($this->piBaseObj, 
						$this->piBaseObj->conf['URLparamter.'], 
						$BMObj->getParameter(), 
						$BMObj->getTitle());
						
					#debug($newBMList[ $RootPID ][ $bookmkarkId ][ $bookmarkParams ]->setTitle($parmCon->getLabel()));	
					#$newBMList[ $RootPID ][ $bookmkarkId ][ $bookmarkParams ]['displayTitle'] = $parmCon->getLabel();
		
				}
			}
		}
		#debug($newBMList);
		*/
		
		if($this->BookmarkListObj->getBookmarkListCount() > 0 ){
				
			foreach ($this->BookmarkListObj->getBookmarkList() as $RootPID =>  $BMObjArr) {
						
				$templateBM = $GLOBALS['TSFE']->cObj->getSubpart( $templateBMList, '###BOOKMARK_ENTRY_'.$RootPID.'###');
				$list = '';			
				foreach ($BMObjArr as $BMArr2) {
					foreach ($BMArr2 as $BMObj) {
						$markerArray = array();
						$markerArray = $this->renderBookmarkRow( $BMObj, $RootPID );
						
						$list .= $GLOBALS['TSFE']->cObj->substituteMarkerArray( $templateBM, $markerArray);
					}
				}
				
				$subpart['###BOOKMARK_COUNT_'.$RootPID.'###'] = $this->BookmarkListObj->getCountBookmarksOfRootPage($RootPID);
				
				$subpart['###BOOKMARK_ENTRY_'.$RootPID.'###'] = $list;
			}
		}else{
			
			$templateBMList = $GLOBALS['TSFE']->cObj->getSubpart($this->template, '###BOOKMARK_EMPTY###');
		}
		
		$content .= $GLOBALS['TSFE']->cObj->substituteMarkerArrayCached($templateBMList, $markerArray, $subpart, array());
		
		
		
		return $content;
	}
	
	/**
	 * render a Single Bookmark to MarkerArray  
	 *
	 * @param tx_mnpagebookmark_bookmarklist $BMObj
	 * @return array
	 */
	function renderBookmarkRow( $BMObj, $RootPID ){
		
		/* @var $BMObj tx_mnpagebookmark_bookmark */
		
		//add Std. Marker
		$markerArray = array();

		#$markerArray['###ID###'] = $BMObj->getID();
		//add all Fields to Marker
		foreach ($BMObj->getAllFields() as $fieldname => $value) {
			$markerArray[ '###'.strtoupper($fieldname).'###'] = $this->validateValue( $value );
		}
		
		$linkConf = array('parameter'=>$BMObj->getID(),'additionalParams'=>'&'.$BMObj->getParameter());
		$markerArray['###LINK_URL###'] = $this->piBaseObj->cObj->typoLink_URL($linkConf);
		$markerArray['###DELETE_LINK_URL###'] = $this->piBaseObj->pi_linkTP_keepPIvars_url(array( 'submitMode' => 'delete','BookmarkID' => $BMObj->getID(), 'URLParameter' => '--'.$BMObj->getParameter().'--'), 0,1);
		
		
		//add Domain-URL 
		$markerArray['###TARGET_DOMAIN###'] = $this->getDomainURL( $RootPID );
		
		$parmCon = new tx_mnpagebookmark_parameterconroller($this->piBaseObj, $this->piBaseObj->conf['URLparamter.'], $BMObj->getParameter(), $BMObj->getTitle());
		$markerArray['###TITLE###'] = $parmCon->getLabel();
		$markerArray['###A_TAG_TITLE###'] = $parmCon->getFullLabel();
		
		
		//add Hook
		if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->piBaseObj->extKey][$this->piBaseObj->prefixId]['BookmarkList'])) {
            foreach  ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->piBaseObj->extKey][$this->piBaseObj->prefixId]['BookmarkList'] as $classRef) {
                $hookObj= &t3lib_div::getUserObj($classRef);
                if (method_exists($hookObj, 'addBookmark_Markers')) {
                    $hookObj->addBookmark_Markers($markerArray, $this->piBaseObj, $BMObj);
                }
            }
        }
        
           
		return $markerArray;
	}
	
	/**
	 * get additionl makers for BM-Lists
	 *
	 * @return array
	 */
	function getAdditionalMarkersToList(){
		
		$markerArray = array();
		$markerArray['###BOOKMARK_LIST_COUNT###'] = $this->BookmarkListObj->getBookmarkListCount();
		$markerArray['###BOOKMARK_LABEL_LINK###'] = $this->piBaseObj->cObj->getTypoLink( $this->piBaseObj->pi_getLL('bookmark_header_label','Bookmark List'),
								tx_mnpagebookmark_div::validateUrl( $this->conf['BookmarkLink.']['externalURL'] ).
										$this->piBaseObj->pi_linkTP_keepPIvars_url(	array(),
											0,
											1,
											$this->conf['PIDofBookmarkList']
											).' '.$this->conf['BookmarkLink.']['additionJSParams']
										);

		/**
		 * URLs
		 */
		//target Form ID
		$markerArray['###ACTION_URL###'] = $this->piBaseObj->pi_linkTP_keepPIvars_url(	array(),
											0,
											1,
											$this->conf['formular.']['MoveToPID']
											);
		$markerArray['###MESSAGE###'] = $this->message;
		return $markerArray;
	}
	
	
	/**
	 * Validate the Value
	 *
	 * @param mixed $value
	 */
	function validateValue($value) {
		
		//replace all Zeros after Point in Double Values
		$value = tx_mnpagebookmark_div::strReplaceZeroFromDouble ( $value );
		
		return $value;
	}
	

	
	/**
	 * get Domain-Url from given RootID
	 *
	 * @param int $RootPID
	 * @return string
	 */
	function getDomainURL( $RootPID ){
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
											'sys_domain',
											'pid = '.$RootPID.$GLOBALS['TSFE']->cObj->enableFields('sys_domain'),
											'1');
		
		if( $GLOBALS['TYPO3_DB']->sql_num_rows( $res ) ){
			
			while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
				return tx_mnpagebookmark_div::validateUrl( $row['domainName'] );
			}
		}
		
	}
	
	function setMessage( $message ){
		$this->message  = $message;
	}
}

?>