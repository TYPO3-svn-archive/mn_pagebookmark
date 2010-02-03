<?php

class tx_mnpagebookmark_div {
	
	
	/**
	 * validate given URL, set http and end slash 
	 *
	 * @param sting $url
	 * @return string
	 */
	function validateUrl( $url ){
		
		
		if(!empty( $url )){ 
			if( substr($url, -1) != '/' ){
				$url .= '/';
			}
		
			//remove all http's Protocol, and add this at first
			$url = 'http://'.str_replace( 'http://', '', $url);
		}
		return $url;
	}
	
	/**
	 * replace Zeros from Double Values 
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	function strReplaceZeroFromDouble( $value ){
		return str_replace('.00', '', $value);
	}
	
	/**
	 * validate Value whit htmlspecialchars
	 * if $value a array, validate rekursive
	 *
	 * @param mixed $value
	 * @return string
	 */
	function htmlspecialchars_Value( $value ){
		
		if(is_array($value)){
			
			$tmp_arr = array();
			foreach ($value as $key =>  $tmp_value) {
				$tmp_arr[$key] = tx_mnpagebookmark_div::htmlspecialchars_Value( $tmp_value );
			}
			
			return $tmp_arr;
			
		}else{
			
			return htmlspecialchars( $value );
		}
		
	}
	
	function getBookmarkList_Link($label, $domain, $PageID, $additionlParams){
		
		return  $this->piBaseObj->cObj->getTypoLink( $this->piBaseObj->pi_getLL($label,'Bookmark List'),
										tx_mnpagebookmark_div::validateUrl( $domain ).
										$this->piBaseObj->pi_linkTP_keepPIvars_url(	array(),
											0,
											1,
											$PageID
											).($additionlParams?' '.$additionlParams:'')
										);
	}
	
}

?>
