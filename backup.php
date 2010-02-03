<?php
//ext_tables
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='tx_mnpagebookmark_bookmark_mode;;;;1-1-1';


//ext_localconf
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tx_mnpagebookmark_pi1']['BookmarkList'][] = 'EXT:'.$_EXTKEY.'/hooks/class.tx_bookmark_hook.php:tx_bookmark_hook';

?>