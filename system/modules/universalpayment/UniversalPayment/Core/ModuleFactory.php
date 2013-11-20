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
 * Manage payment modules
 */
class ModuleFactory
{
	/**
	 * Table
	 * @var
	 */
	protected $strTable = 'tl_up_modules';
	
	/**
	 * Current object instance (Singleton)
	 * @var Database
	 */
	protected static $objInstance;
	
	/**
	 * Source Table
	 * @var
	 */
	protected $strSource;
	
	/**
	 * Type of element (mod,ffd,cte)
	 * @var
	 */
	protected $strType;
	
	/**
	 * Data array
	 * @var array
	 */
	protected $arrData = array();
	
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
	 * Save the element to database
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
		
		// add a create timestamp
		$this->arrData['createTstamp'] = time();
		
		$objDatabase = \Database::getInstance();
		$objStatement = $objDatabase->prepare("INSERT INTO ".$this->strTable." %s")->set($this->arrData);
		$objStatement->execute();
		return true;
	}
	
	/**
	 * Save the element to database
	 */
	public function update($intId, $arrSet=array() )
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
		
		$objDatabase = \Database::getInstance();
		$objStatement = $objDatabase->prepare("UPDATE ".$this->strTable." %s WHERE id=?")->set($this->arrData);
		$objStatement->execute($intId);
		return true;
	}
	
	
	/**
	 * Find an entry by the parent id
	 * @param integer	id of the element in the source table
	 * @param string	type of element
	 * @return object	Database_Result
	 */
	public function findItemByPid($intPid, $strType)
	{
		$this->strSource = UniversalPayment::getInstance()->findTableByType($strType);
		
		$objResult = \Database::getInstance()->prepare("SELECT * FROM ".$this->strTable." WHERE pid=? AND type=?")
						->limit(1)
						->execute($intPid,$strType);
		
		if($objResult->numRows < 1)
		{
			return null;
		}
		return $objResult;
	}

}