<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		universalpayment
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespaces
 */
namespace UniversalPayment\Core;

/**
 * Imports
 */
use UniversalPayment\Factory as UniversalPayment;


/**
 * Class file
 * Manage order related actions
 */
class OrderFactory
{
	/**
	 * Table
	 * @var
	 */
	protected $strTable = 'tl_up_orders';
	
	/**
	 * Data array
	 * @var array
	 */
	protected $arrData = array();

	
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
	 * Setter
	 */
	public function set($strKey,$strValue)
	{
		$this->arrData[$strKey] = $strValue;
	}
	
	/**
	 * Getter
	 */
	public function get($strKey)
	{
		return $this->arrData[$strKey];
	}
	
	/**
	 * Get data array
	 * @param array
	 */
	public function getData()
	{
		return $this->arrData;
	}

	/**
	 * Set data array
	 * @param array
	 */
	public function setData($arrData)
	{
		if($arrData['type']){$this->strType = $arrData['type'];}
		if($arrData['source']){$this->strSource = $arrData['source'];}
		
		$this->arrData = $arrData;
	}
	
	/**
	 * Save the payment order to database
	 * @param array		optional set array for database
	 * @return boolean
	 */
	public function save($arrSet=array())
	{
		if(count($this->arrData) < 1 && count($arrSet) < 1)
		{
			return false;
		}
		
		// update data array if a set array is set
		if(count($arrSet) > 0)
		{
			$this->setData($arrSet);
		}
		
		$this->arrData['tstamp'] = time();
		
		$objDatabase = \Database::getInstance();
		$objStatement = $objDatabase->prepare("INSERT INTO ".$this->strTable." %s")->set($this->arrData);
		$objStatement->execute();
		return true;
	}

}