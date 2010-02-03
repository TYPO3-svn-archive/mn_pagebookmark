<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_mnpagebookmark_bookmark'] = array (
	'ctrl' => $TCA['tx_mnpagebookmark_bookmark']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,user_id,page_id,parameter'
	),
	'feInterface' => $TCA['tx_mnpagebookmark_bookmark']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tx_mnpagebookmark_bookmark.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'user_id' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tx_mnpagebookmark_bookmark.user_id',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'page_id' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tx_mnpagebookmark_bookmark.page_id',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'pages',	
				'foreign_table_where' => 'ORDER BY pages.uid',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 100,	
				"MM" => "tx_mnpagebookmark_bookmark_page_id_mm",
			)
		),
		'parameter' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:mn_pagebookmark/locallang_db.xml:tx_mnpagebookmark_bookmark.parameter',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, user_id, page_id, parameter')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>