<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		universal_payment
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespaces
 */
namespace UniversalPayment;


/**
 * Class file
 * Provide various functions
 */
class Factory
{
	/**
	 * Current object instance (Singleton)
	 * @var Database
	 */
	protected static $objInstance;
	
	/**
	 * Instantiate this class and return it (Factory)
	 * @return FormPayment
	 * @throws Exception
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new self();
		}

		return self::$objInstance;
	}
	
	
	/**
	 * Get registered payment modules
	 * @return array
	 */
	public function getPaymentModules()
	{
		if(empty($GLOBALS['UNIVERSALPAYMENT']['PAYMENT_METHOD']))
		{
			return array();
		}
		
		$arrReturn = array();
		foreach($GLOBALS['UNIVERSALPAYMENT']['PAYMENT_METHOD'] as $name => $class)
		{
			$strLabel = $name;
			if(strlen($GLOBALS['TL_LANG']['UNIVERSALPAYMENT']['PAYMENT_METHOD'][$name]))
			{
				$strLabel = $GLOBALS['TL_LANG']['UNIVERSALPAYMENT']['PAYMENT_METHOD'][$name];
			}
			
			$arrReturn[$name] = $strLabel;
		}
		
		return $arrReturn;
	}
	
	
	/**
	 * Get the translated payment module name
	 * @param string
	 * @return string
	 */
	public function getPaymentModuleName($strName)
	{
		if(strlen($GLOBALS['TL_LANG']['UNIVERSALPAYMENT']['PAYMENT_METHOD'][$strName]))
		{
			$strName = $GLOBALS['TL_LANG']['UNIVERSALPAYMENT']['PAYMENT_METHOD'][$strName];
		}
		return $strName;
	}
	
	
	/**
	 * Generate a unique string
	 * @param integer
	 * @return string
	 */
	public function generatePassword($intLength=8,$strCharSet='')
	{
		if(!$strCharSet)
		{
			$strCharSet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		
		$max = strlen($strCharSet);
		$rand = rand(0,$max);
		
		$return = '';
		for($i = 0; $i <= $intLength-1; $i++)
		{
			$rand = rand(0,$max);
			$return .= substr($strCharSet, $rand,1);
		}
		
		return $return;
	}
	
	/**
	 * Generate unique id
	 * @param integer
	 * @param string
	 */
	public function generateUnique($intLength=8,$strCharSet='')
	{
		if($intLength < 0)
		{
			$intLength = 8;
		}
		
		return $this->generatePassword($intLength,$strCharSet);
	}
	
	/**
	 * Get the source tables for each element type
	 * @param string
	 * @return string
	 */
	public function findTableByType($strType)
	{
		return $GLOBALS['UNIVERSALPAYMENT']['typeToTableReference'][$strType];
	}
	
	/**
	 * Get the element type by the parent table
	 * @param string
	 * @return string
	 */
	public function findTypeByTable($strTable)
	{
		$arrRef = array_flip($GLOBALS['UNIVERSALPAYMENT']['typeToTableReference']);
		return $arrRef[$strTable];
	}
	
}