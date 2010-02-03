<?php
class tx_bookmark_hook {
	
	function addBookmark_Markers(&$markers, $BookmarkRenderObj, $BookmarkObj) {
		/*@var $BookmarkObj tx_mnpagebookmark_bookmark */;
		
		$markers[ '###MY_MARKER###' ] = "test";
		
	}

}

?>