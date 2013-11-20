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
use \Session;

/**
 * Class file
 * Provide various function to handle sessions
 */
class SessionFactory
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
	 * Session node name
	 * @var string
	 */
	protected $strSessionName = 'universalpayment';
	
	/**
	 * Insert or update a payment item in the session
	 * @param array
	 */
	public function setItem($arrItem)
	{
		if(empty($arrItem['id']) || empty($arrItem['type']))
		{
			return false;
		}
		
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		$intId = $arrItem['id'];
		$strType = strtolower($arrItem['type']);
		
		$arrSession[$strType][$intId] = $arrItem;
		
		$objSession->set($this->strSessionName,$arrSession);
	}
	
	/**
	 * Find a unique id in the source and return it
	 * @param integer
	 * @param string 	Source Type (mod,ffd,ce) 
	 */
	public function findUniqueByIdAndType($intId,$strType)
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		if(empty($arrSession))
		{
			return null;
		}
		
		return $arrSession[$strType][$intId]['uniqueId'];
	}
	
	/**
	 * Find a payment session node by its id and type
	 * @param integer
	 * @param string 	Source Type (mod,ffd,ce) 
	 */
	public function findItemByIdAndType($intId,$strType)
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		if(empty($arrSession))
		{
			return null;
		}
		
		return $arrSession[$strType][$intId];
	}
	
	/**
	 * Set a unique id to the session
	 * @param integer
	 * @param string 	Source Type (mod,ffd,ce) 
	 */
	public function setUniqueById($strUniqueId, $intId, $strType)
	{
		$arrSet = array();
		$arrSet[$strType][$intId]['id']		  = $intId;
		$arrSet[$strType][$intId]['type']	  = $strType;
		$arrSet[$strType][$intId]['uniqueId'] = $strUniqueId;
		$this->setSession($arrSet);
	}
	
	/**
	 * Find a payment information array by a unique id
	 * @param string
	 * @param string	Source Type (mod,ffd,ce)
	 * @return array
	 */
	public function findItemByUnique($strUniqueId)
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		if(empty($arrSession))
		{
			return null;
		}
		
		foreach($arrSession as $strType => $arrItems)
		{
			foreach($arrItems as $arrItem)
			{
				if($arrItem['uniqueId'] == $strUniqueId)
				{
					return $arrItem;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Find a payment information array by a unique id
	 * @param string
	 * @param string	Source Type (mod,ffd,ce)
	 * @return boolean
	 */
	public function removeItemByUnique($strUniqueId)
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		if(empty($arrSession))
		{
			return null;
		}
		
		foreach($arrSession as $strType => $arrItems)
		{
			foreach($arrItems as $intId => $arrItem)
			{
				if($arrItem['uniqueId'] == $strUniqueId)
				{
					unset($arrSession[$strType][$intId]);
				}
			}
		}
		
		$objSession->set($this->strSessionName,$arrSession);
		
		return true;
	}

	/**
	 * Set payment session
	 * @param array
	 * @return boolean
	 */
	public function setSession($arrSet)
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		if(!is_array($arrSet) || count($arrSet) < 1)
		{
			return false;
		}
		
		if(empty($arrSession) || !isset($arrSession))
		{
			$arrSession = array();
		}
		
		foreach($arrSet as $type => $arrItems)
		{
			foreach($arrItems as $itemId => $arrItem)
			{
				$arrSession[$type][$itemId] = $arrItem;
			}
		}
		
		$objSession->set($this->strSessionName,$arrSession);
		
		return true;
	}
	
	/**
	 * Get payment session array
	 * @return array
	 */
	public function getSession()
	{
		$objSession = \Session::getInstance();
		$arrSession = $objSession->get($this->strSessionName);
		
		return $arrSession;
	}
	
	/**
	 * Remove the whole payment session
	 * @param array
	 * @return boolean
	 */
	public function removeSession()
	{
		$objSession = \Session::getInstance();
		$objSession->remove($this->strSessionName);
	}
	
	/**
	 * Purge payment session
	 * @param array
	 * @return boolean
	 */
	public function purgeSession()
	{
		$objSession = \Session::getInstance();
		$objSession->set($this->strSessionName,array());
	}
		
}