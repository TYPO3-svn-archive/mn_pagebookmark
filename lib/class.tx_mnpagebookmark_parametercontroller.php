<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Mario Naether <mario-naether@gmx.de>
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
class tx_mnpagebookmark_parameterconroller{
	
	private $piBase;
	private $tableConfig;
	private $defaultLabel;
	private $URLparmeter;
	private $label;
	
	function __construct($piBase, $tableConfig, $URLparmeter, $defaultLabel){
		$this->piBase = $piBase;
		$this->tableConfig = $tableConfig;
		$this->defaultLabel = $defaultLabel;
		$this->URLparmeter = $URLparmeter;
		
		$this->doControll();
	}
	
	function doControll(){
		$paramArr = explode('&', $this->URLparmeter);
		
		foreach($paramArr as $key => $parameter){
			foreach($this->tableConfig as $tsKey => $tsconfig){
				$parameterValues = explode('=', $parameter);
				
				if( is_array($tsconfig) && strlen($parameterValues[0]) > 0){
					if(strpos($tsconfig['getvar'], $parameterValues[0]) === 0){
						$NewTsConfig = $tsconfig;
						$NewTsConfig['source'] = $parameterValues[1];
						unset($NewTsConfig['getvar']);
						
						$labels[] = $this->piBase->cObj->RECORDS( $NewTsConfig );
						
					}
				}
			}
		}
		if(!empty($labels)){
			$this->label = implode(' / ', $labels); 
		}else{
			$this->label = $this->defaultLabel;
		}
		$this->fullLabel = $this->label;
		
		$this->label = $this->piBase->cObj->stdWrap($this->label, $this->tableConfig['stdWrap.']);
		
	}
	
	function getLabel(){
		return $this->label;
	}
	
	function getFullLabel(){
		return $this->fullLabel;
	}
}
?>