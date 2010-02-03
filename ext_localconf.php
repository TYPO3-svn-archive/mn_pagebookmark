<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_mnpagebookmark_pi1 = < plugin.tx_mnpagebookmark_pi1.CSS_editor
',43);

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_mnpagebookmark_pi1.php', '_pi1', 'list_type', 0);

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_mnpagebookmark_bookmark=1
');

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tx_mnpagebookmark_pi1']['BookmarkList'][] = 'EXT:'.$_EXTKEY.'/hooks/class.tx_bookmark_hook.php:tx_bookmark_hook';
?>