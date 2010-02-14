<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='tx_mnpagebookmark_bookmark_mode;;;;1-1-1';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:mn_pagebookmark/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

$TCA['tx_mnpagebookmark_bookmark'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tx_mnpagebookmark_bookmark',		
		'label'     => 'user_id',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_mnpagebookmark_bookmark.gif',
	),
);

$tempColumns = array (
	'tx_mnpagebookmark_bookmark_mode' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tt_content.tx_mnpagebookmark_bookmark_mode',		
		'config' => array (
			'type' => 'select',
			'items' => array (
				array('LLL:EXT:mn_pagebookmark/locallang_db.xml:tt_content.tx_mnpagebookmark_bookmark_mode.I.0', '1'),
				array('LLL:EXT:mn_pagebookmark/locallang_db.xml:tt_content.tx_mnpagebookmark_bookmark_mode.I.1', '2'),
			),
			'size' => 1,	
			'maxitems' => 1,
		)
	),
);


t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Static Bookmarklist');
?>